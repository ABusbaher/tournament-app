<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Services\GameService;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(GameService $gameService)
    {
        // Create league games for tournament 1 with 6 teams
        $tournament = Tournament::first();
        $gameService->createAllLeagueGames($tournament);
    }
}
