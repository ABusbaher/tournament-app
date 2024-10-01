<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ScheduleBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameService
{
    public function getGamesByFixture(Tournament $tournament, $fixture): array
    {
        if (!is_numeric($fixture)) {
            throw new NotFoundHttpException('Fixture must be a number');
        }
//        $games = Game::join('teams as host_team', 'games.host_team_id', '=', 'host_team.id')
//            ->join('teams as guest_team', 'games.guest_team_id', '=', 'guest_team.id')
//            ->where('games.tournament_id', $tournament->id)
//            ->where('games.fixture', $fixture)
//            ->select('games.*', 'host_team.name as host_team_name', 'host_team.image_path as host_team_image', 'guest_team.name as guest_team_name', 'guest_team.image_path as guest_team_image')
//            ->get();
        $prevFixture = (int) $fixture - 1;
        $nextFixture = (int) $fixture + 1;
        $tournamentId = $tournament->id;

        $games = Game::select('games.*', 'host_team.name as host_team_name', 'guest_team.name as guest_team_name',
            'host_team.shorten_name as host_team_shortname', 'guest_team.shorten_name as guest_team_shortname',
            'host_team.image_path as host_team_image', 'guest_team.image_path as guest_team_image')
            ->leftJoin('teams as guest_team', 'games.guest_team_id', '=', 'guest_team.id')
            ->join('teams as host_team', 'games.host_team_id', '=', 'host_team.id')
            ->where('games.tournament_id', $tournamentId)
            ->where('games.fixture', $fixture)
            ->orderByRaw('CASE WHEN guest_team.id IS NULL THEN 1 ELSE 0 END, guest_team.id ASC')
            ->get();

        $fixtures = DB::table('games as g')
            ->selectRaw('MAX(g.fixture) as max_fixture, '. $fixture . ' as fixture')
            ->selectSub(function ($query) use ($nextFixture, $tournamentId) {
                $query->selectRaw('CASE WHEN COUNT(g2.fixture = '. $nextFixture . ') > 0 THEN ' . $nextFixture . ' ELSE NULL END')
                    ->from('games as g2')
                    ->where('g2.tournament_id', $tournamentId)
                    ->whereRaw('g2.fixture = ?', $nextFixture);
            }, 'next_fixture')
            ->selectSub(function ($query) use ($prevFixture, $tournamentId) {
                $query->selectRaw('CASE WHEN COUNT(g1.fixture = ' . $prevFixture . ') > 0 THEN '. $prevFixture . ' ELSE NULL END')
                    ->from('games as g1')
                    ->where('g1.tournament_id', $tournamentId)
                    ->whereRaw('g1.fixture = ?', $prevFixture);
            }, 'prev_fixture')
            ->where('g.tournament_id', $tournamentId)
            ->first();

        if ($games->isEmpty()) {
            throw new NotFoundHttpException('No games found for the given tournament and fixture.');
        }

        return [
            'games' => $games,
            'fixtures' => $fixtures
        ];
    }

    public function createAllLeagueGames(Tournament $tournament): array
    {
        $teams = $tournament->teams;
        $rounds = $this->setLeagueRounds($tournament);

        $scheduleBuilder = new ScheduleBuilder();
        $scheduleBuilder->setTeams($teams->pluck('id')->toArray());
        $scheduleBuilder->setRounds($rounds);
        $schedule = $scheduleBuilder->build();

        $games = [];
        foreach ($schedule as $index => $tournamentTeams) {
            foreach ($tournamentTeams as $team) {
                $hostTeamId = $team[0];
                $guestTeamId = $team[1] ?? null;
                // if not even number of teams make sure that always host team has free games.
                $game = [
                    'fixture' => $index,
                    'host_team_id' => ($hostTeamId !== null) ? $hostTeamId : $guestTeamId,
                    'guest_team_id' => ($hostTeamId !== null) ? $guestTeamId : null,
                    'tournament_id' => $tournament->id,
                    'created_at' => Carbon::now()
                ];
                $games[] = $game;
            }
        }
        // Batch insert the games
        Game::insert($games);
        return $games;
    }

    private function setLeagueRounds(Tournament $tournament): int
    {
        return (($count = count($tournament->teams)) % 2 === 0 ? $count - 1 : $count) * $tournament->rounds;
    }

    public function getGame(Tournament $tournament, Game $game): Model|Builder|Game
    {
        return Game::with('hostTeam:id,name', 'guestTeam:id,name')
            ->where('tournament_id', $tournament->id)
            ->where('id', $game->id)
            ->firstOrFail();
    }

    public function updateGameScore(Tournament $tournament, Game $game, array $data): Model|Builder|Game
    {
        $game = Game::where('tournament_id', $tournament->id)
            ->where('id', $game->id)
            ->firstOrFail();
        $game->host_goals = $data['host_goals'];
        $game->guest_goals = $data['guest_goals'];
        $game->game_time = $data['game_time'];
        $game->save();
        return $game;
    }

    public function getGameTable(Tournament $tournament): Collection
    {
        // calculate points between games.
        $pointsTable = DB::table(function ($query) {
            $query->select('host_team_id AS Team', 'host_goals', 'guest_goals')
                ->selectRaw('CASE WHEN host_goals > guest_goals THEN 3
                  WHEN host_goals = guest_goals THEN 1 ELSE 0
             END AS Points')
                ->from('games')
                ->whereNotNull('host_goals')
                ->whereNotNull('guest_goals')
                ->unionAll(function ($query) {
                    $query->select('guest_team_id AS Team', 'guest_goals', 'host_goals')
                        ->selectRaw('CASE WHEN guest_goals > host_goals THEN 3
                          WHEN guest_goals = host_goals THEN 1 ELSE 0
                            END AS Points')
                        ->from('games')
                        ->whereNotNull('host_goals')
                        ->whereNotNull('guest_goals');
                });
        });

        // calculate games played, goal scored, goal received and goal difference between teams
        $resultsWithPoints = Team::select('teams.id AS ID', 'teams.name AS team', 'teams.tournament_id', 'teams.image_path')
            ->selectRaw('COUNT(pt.team) AS GamesPlayed')
            ->selectRaw('SUM(CASE WHEN pt.Points = 3 THEN 1 ELSE 0 END) as Wins')
            ->selectRaw('SUM(CASE WHEN pt.Points = 1 THEN 1 ELSE 0 END) as Draws')
            ->selectRaw('SUM(CASE WHEN pt.Points = 0 THEN 1 ELSE 0 END) as Losses')
            ->selectRaw('SUM(pt.Points) AS Points')
            ->selectRaw('SUM(pt.host_goals) AS GoalsScored')
            ->selectRaw('SUM(pt.guest_goals) AS GoalsReceived')
            ->selectRaw('SUM(pt.host_goals - pt.guest_goals) AS GoalDiff')
            ->leftJoinSub($pointsTable, 'pt', 'teams.id', '=', 'pt.Team')
            ->where('teams.tournament_id', $tournament->id)
            ->groupBy('teams.id', 'teams.name')
        ;

        // calculate head to head between teams
        $hthTable = DB::table(function ($query) use ($resultsWithPoints) {
            $query->select('team AS hth_team')
                ->selectRaw('SUM(hth) AS hth')
                ->from(function ($query) use ($resultsWithPoints) {
                    $query->select('g.host_team_id AS team')
                        ->selectRaw('CASE WHEN g.host_goals > g.guest_goals THEN 1 ELSE 0 END AS hth')
                        ->from('games AS g')
                        ->joinSub($resultsWithPoints, 'w1', 'g.host_team_id', '=', 'w1.ID')
                        ->joinSub($resultsWithPoints, 'w2', 'g.host_team_id', '=', 'w2.ID')
                        ->whereColumn('w1.Points', '=', 'w2.Points')
                        ->unionAll(function ($query) use ($resultsWithPoints) {
                            $query->select('g.guest_team_id AS team')
                                ->selectRaw('CASE WHEN g.guest_goals > g.host_goals THEN 1 ELSE 0 END AS hth')
                                ->from('games AS g')
                                ->joinSub($resultsWithPoints, 'w1', 'g.host_team_id', '=', 'w1.ID')
                                ->joinSub($resultsWithPoints, 'w2', 'g.host_team_id', '=', 'w2.ID')
                                ->whereColumn('w1.Points', '=', 'w2.Points');
                        });
                })
                ->groupBy('team');
        });

        // final results and sorting
        return DB::query()->fromSub($resultsWithPoints, 'rwp')
            ->select('rwp.*', 'b.hth', )
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY rwp.Points DESC, b.hth DESC, rwp.GoalDiff DESC, rwp.GoalsScored DESC) AS `Ranking`')
            ->leftJoinSub($hthTable, 'b', 'b.hth_team', '=', 'rwp.ID')
            ->orderByDesc('rwp.Points')
            ->orderByDesc('b.hth')
            ->orderByDesc('rwp.GoalDiff')
            ->orderByDesc('rwp.GoalsScored')
            ->get();
    }
}
