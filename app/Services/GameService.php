<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Team;
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

    public function updateTeam(Team $team, array $data)
    {
//        $old_image = $team->image_path;
//        $team->name = $data['name'];
//        if (isset($data['image'])) {
//            $imagePath = $this->uploads($data['image'], 'team_images');
//            $team->image_path = $imagePath;
//            // Delete the old image if exists
//            if ($old_image) {
//                $parsed_path = parse_url($old_image);
//                File::delete(public_path($parsed_path['path']));
//            }
//        }
//        $team->save();
//        return $team;
    }

    public function deleteTeam(Team $team): void
    {
//        $team->delete();
    }

    private function setLeagueRounds(Tournament $tournament): int
    {
        return (($count = count($tournament->teams)) % 2 === 0 ? $count - 1 : $count) * $tournament->rounds;
    }
}
