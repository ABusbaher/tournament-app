<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Seeder;
use ScheduleBuilder;

class TeamSeeder extends Seeder
{
    public function run()
    {
        $tournamentIds = Tournament::pluck('id')->toArray();; // IDs of the tournaments

        foreach ($tournamentIds as $tournamentId) {
            // Create 6 teams for each tournament
            for ($i = 1; $i <= 6; $i++) {
                Team::create([
                    'tournament_id' => $tournamentId,
                    'name' => "Team $i",
                    'short_name' => "AB$i",
                    'image_path' => null,
                ]);
            }
        }
    }
}
