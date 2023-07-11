<?php

namespace App\Services;

use App\Models\Game;
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
    public function getGamesByFixture(Tournament $tournament, $fixture): Collection
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

        $games = Game::select('games.*', 'host_team.name as host_team_name', 'guest_team.name as guest_team_name',
            'host_team.image_path as host_team_image', 'guest_team.image_path as guest_team_image')
            ->join('teams as guest_team', 'games.guest_team_id', '=', 'guest_team.id')
            ->join('teams as host_team', 'games.host_team_id', '=', 'host_team.id')
            ->where('games.tournament_id', $tournament->id)
            ->where('games.fixture', $fixture)
            ->addSelect(DB::raw('(select max(fixture) from games g2 where g2.tournament_id = ' . $tournament->id . ') as max_fixture'))
            ->addSelect(DB::raw('(SELECT CASE WHEN COUNT('. $prevFixture . ') > 0 THEN '. $prevFixture . ' ELSE NULL END
            FROM games WHERE tournament_id = ' . $tournament->id . ' AND fixture = ' . $prevFixture .  ') as prev_fixture'))
            ->addSelect(DB::raw('(SELECT CASE WHEN COUNT('. $nextFixture .') > 0 THEN ' . $nextFixture . ' ELSE NULL END
            FROM games WHERE tournament_id = ' . $tournament->id . ' AND fixture = '. $nextFixture . ') as next_fixture'))
            ->get();

        if ($games->isEmpty()) {
            throw new NotFoundHttpException('No games found for the given tournament and fixture.');
        }

        return $games;
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
        $game->save();
        return $game;
    }
}
