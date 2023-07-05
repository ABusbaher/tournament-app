<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Tournament;
use ScheduleBuilder;

class GameService
{
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
                // if not even number of teams make sure that always host team has free game.
                $game = [
                    'fixture' => $index,
                    'host_team_id' => ($hostTeamId !== null) ? $hostTeamId : $guestTeamId,
                    'guest_team_id' => ($hostTeamId !== null) ? $guestTeamId : null,
                    'tournament_id' => $tournament->id,
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
}
