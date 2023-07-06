<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Tournament;
use Illuminate\Database\Seeder;
use ScheduleBuilder;

class GameSeeder extends Seeder
{
    public function run()
    {
        // Create league games for tournament 1 with 6 teams
        $tournament = Tournament::first();
        $teams = $tournament->teams()->get();
        $teamIds = $teams->pluck('id')->toArray();
        $rounds = 10;

        $scheduleBuilder = new ScheduleBuilder();
        $scheduleBuilder->setTeams($teamIds);
        $scheduleBuilder->setRounds($rounds);
        $schedule = $scheduleBuilder->build();

        $games = [];
        foreach ($schedule as $index => $tournamentTeams) {
            foreach ($tournamentTeams as $team) {
                $hostTeamId = $team[0];
                $guestTeamId = $team[1] ?? null;
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
    }
}
