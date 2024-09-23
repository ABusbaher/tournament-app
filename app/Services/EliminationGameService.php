<?php

namespace App\Services;

use App\Models\EliminationGame;
use App\Models\Tournament;
use Carbon\Carbon;
class EliminationGameService
{
    public function createAllEliminationGames(Tournament $tournament): array
    {
        $teams = $tournament->teams->pluck('id')->toArray();
        $teamsCount = count($teams);
        shuffle($teams); // Randomize the team order
        $rounds = (int)ceil(log($teamsCount, 2));
        $teamsToAdvance = 2 ** $rounds - $teamsCount;

        // if we do not have exactly 4,8,16 or 32 teams some of them will already be in round 2.
        $round1Teams = array_slice($teams, 0, $teamsCount - $teamsToAdvance);
        $round2Teams = array_slice($teams, $teamsCount - $teamsToAdvance);
        $games = [];

        for ($round = 1; $round <= $rounds; $round++) {
            $nextRound = $round + 1;
            if ($round === 1) {
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
                $advancingTeams = ${"round{$round}Teams"};

                for ($i = 0; $i < count($advancingTeams); $i += 2) {
                    $teamA = $advancingTeams[$i];
                    $teamB = $advancingTeams[$i + 1];
                    $nextMatch =  $round === $rounds ? null : "{$nextRound}-" . ceil(($i + 1) / 2);
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
}
