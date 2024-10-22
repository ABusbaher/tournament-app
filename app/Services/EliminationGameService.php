<?php

namespace App\Services;

use App\Enums\TournamentTypeEnum;
use App\Models\EliminationGame;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EliminationGameService
{
    public function createAllEliminationGames(Tournament $tournament): array
    {
        if (!$tournament->isElimination()) {
            throw new InvalidArgumentException('Not valid tournament type', 422);
        }
        $teams = $tournament->teams->pluck('id')->toArray();
        $teamsCount = count($teams);
        shuffle($teams); // Randomize the team order
        $rounds = (int)ceil(log($teamsCount, 2));
        $teamsToAdvance = 2 ** $rounds - $teamsCount;

        // if we do not have exactly 4,8,16 or 32 teams some of them will already be in round 2.
        $round1Teams = array_slice($teams, 0, $teamsCount - $teamsToAdvance);
        $round2Teams = array_slice($teams, $teamsCount - $teamsToAdvance);
        $games = [];

        $nextRound = 1;
        for ($round = $rounds; $round > 0; $round--) {
            $nextRound++;
            if ($round === $rounds) {
                for ($i = 0; $i < count($round1Teams); $i += 2) {
                    $teamA = $round1Teams[$i];
                    $teamB = $round1Teams[$i + 1];
                    $nextMatch = "{$nextRound}-" . ceil(($i + 1) / 2);
                    $game = $this->createSingleEliminationGame(
                        $round, $teamA, $teamB, $tournament->id, $nextMatch
                    );
                    // Add winners to the next round
                    $round2Teams[] = $game['next_match'];
                    $games[] = $game;
                    // Create one more game if tournament rounds is 2 (home and away match)
                    if ($tournament->rounds === 2) {
                        $secondGame =  $this->createSingleEliminationGame(
                            $round, $teamB, $teamA, $tournament->id, $nextMatch
                        );
                        $games[] =  $secondGame;
                    }
                }
            } else {
                $currentRoundTeams = $nextRound - 1;
                $advancingTeams = ${"round{$currentRoundTeams}Teams"};

                for ($i = 0; $i < count($advancingTeams); $i += 2) {
                    $teamA = $advancingTeams[$i];
                    $teamB = $advancingTeams[$i + 1];
                    $nextMatch =  $currentRoundTeams === $rounds ? null : "{$nextRound}-" . ceil(($i + 1) / 2);
                    $game = $this->createSingleEliminationGame(
                        $round, $teamA, $teamB, $tournament->id, $nextMatch
                    );
                    ${"round{$nextRound}Teams"}[] = $game['next_match'];
                    $games[] = $game;
                    if ($tournament->rounds === 2) {
                        $secondGame =  $this->createSingleEliminationGame(
                            $round, $teamB, $teamA, $tournament->id, $nextMatch
                        );
                        $games[] =  $secondGame;
                    }
                }
            }
        }

        EliminationGame::insert($games);
        return $games;
    }

    private function createSingleEliminationGame($round, $teamA, $teamB, $tournamentId, $nextMatch): array
    {
        return [
            'round' => $round,
            'team1_id' => str_contains($teamA, '-') ? null : $teamA,
            'team2_id' => str_contains($teamB, '-') ? null : $teamB,
            'team1_prev' => str_contains($teamA, '-') ? $teamA : null,
            'team2_prev' => str_contains($teamB, '-') ? $teamB : null,
            'tournament_id' => $tournamentId,
            'created_at' => Carbon::now(),
            'next_match' => $nextMatch
        ];
    }

    public function getEliminationGames(Tournament $tournament): array
    {
        $tournamentId = $tournament->id;
        $games =  EliminationGame::select(
            'elimination_games.*',
            'team1.name as team1_name',
            'team1.shorten_name as team1_shorten_name',
            'team1.image_path as team1_image',
            'team2.name as team2_name',
            'team2.shorten_name as team2_shorten_name',
            'team2.image_path as team2_image',
            DB::raw('MAX(elimination_games.round) OVER (PARTITION BY elimination_games.tournament_id) as max_round')
        )
            ->leftJoin('teams as team1', 'elimination_games.team1_id', '=', 'team1.id')
            ->leftJoin('teams as team2', 'elimination_games.team2_id', '=', 'team2.id')
            ->where('elimination_games.tournament_id', $tournamentId)
            ->orderBy('elimination_games.round', 'desc')
            ->orderBy('elimination_games.next_match')
            ->get();

        if ($games->isEmpty()) {
            throw new NotFoundHttpException('No elimination games found for the given tournament.');
        }

        $maxRound = $games->max('round');
        $countOfMaxRoundGames = $games->where('round', $maxRound)->count();
        $expectedNumberOfGames = match($maxRound) {
            2 => 2,
            3 => 4,
            4 => 8,
            5 => 16,
            default => 0
        };
        $nonPlayedGames = $expectedNumberOfGames - $countOfMaxRoundGames;

        return [
            'games' => $games,
            'max_round' => $maxRound,
            'non_played_games' => $nonPlayedGames
        ];
    }

    public function getGame(Tournament $tournament, EliminationGame $game): Model|Builder|EliminationGame
    {
        return EliminationGame::with('firstTeam:id,name', 'secondTeam:id,name')
            ->where('tournament_id', $tournament->id)
            ->where('id', $game->id)
            ->firstOrFail();
    }

    public function updateGameScore(Tournament $tournament, EliminationGame $game, array $data): Model|Builder|EliminationGame
    {
        $game = EliminationGame::where('tournament_id', $tournament->id)
            ->where('id', $game->id)
            ->firstOrFail();

        $game->team1_goals = $data['team1_goals'];
        $game->team2_goals = $data['team2_goals'];
        $game->game_time = $data['game_time'];
        $game->save();

        // Finale game do not have next match.
        if (!$game->next_match) {
            return $game;
        }

        if ($data['team1_goals'] === $data['team2_goals']) {
            throw new InvalidArgumentException("Teams can not have same score", 422);
        }

        $this->updateNextMatch($tournament, $game);

        return $game;
    }

    private function updateNextMatch(Tournament $tournament, EliminationGame $game): void
    {
        $nextGame = EliminationGame::where('tournament_id', $tournament->id)
            ->where(function ($query) use ($game) {
                $query->where('team1_prev', $game->next_match)
                    ->orWhere('team2_prev', $game->next_match);
            })
            ->first();

        if ($game->team1_goals !== null && $game->team2_goals !== null) {
            $winner = $game->team1_goals > $game->team2_goals ? $game->team1_id : $game->team2_id;
        } else {
            $winner = null;
        }

        // update next match team by winner and reset goals to null
        if ($nextGame->team1_prev === $game->next_match) {
            $nextGame->team1_id = $winner;
        } elseif ($nextGame->team2_prev === $game->next_match) {
            $nextGame->team2_id = $winner;
        }

        $nextGame->team1_goals = null;
        $nextGame->team2_goals = null;

        $nextGame->save();

        // go out of recursive function when reach last round
        if (!$nextGame->next_match) {
            return;
        }

        $this->updateNextMatch($tournament, $nextGame);
    }
}
