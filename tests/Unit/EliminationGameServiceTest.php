<?php
namespace Tests\Unit;

use App\Models\Team;
use App\Models\Tournament;
use App\Services\EliminationGameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminationGameServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_games_by_elimination_can_be_created(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'elimination', 'rounds' => 1]);
        Team::factory()->times(8)->create([
            'tournament_id' => $tournament->id,
        ]);
        $this->assertDatabaseMissing('elimination_games', [
            'tournament_id' => $tournament->id,
        ]);
        $eliminationGameService = new EliminationGameService();
        $games = $eliminationGameService->createAllEliminationGames($tournament);

        // assert 7 games is created with 8 teams.
        $this->assertCount(7, $games);
        // assert 4 games are created in round 1.
        $this->assertCount(4, array_filter($games, fn($item) => $item['round'] === 1));
        // assert 2 games are created in round 2.
        $this->assertCount(2, array_filter($games, fn($item) => $item['round'] === 2));
        // assert 1 game are created in round 3.
        $this->assertCount(1, array_filter($games, fn($item) => $item['round'] === 3));
        // assert that 3 rounds is created with 8 teams.
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 3
        ]);
    }

    public function test_games_by_elimination_can_be_created_with_home_away_games(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'elimination', 'rounds' => 2]);
        Team::factory()->times(8)->create([
            'tournament_id' => $tournament->id,
        ]);
        $this->assertDatabaseMissing('elimination_games', [
            'tournament_id' => $tournament->id,
        ]);
        $eliminationGameService = new EliminationGameService();
        $games = $eliminationGameService->createAllEliminationGames($tournament);

        // assert 14 games is created with 8 teams.
        $this->assertCount(14, $games);
        // assert 8 games are created in round 1.
        $this->assertCount(8, array_filter($games, fn($item) => $item['round'] === 1));
        // assert 4 games are created in round 2.
        $this->assertCount(4, array_filter($games, fn($item) => $item['round'] === 2));
        // assert 2 game are created in round 3.
        $this->assertCount(2, array_filter($games, fn($item) => $item['round'] === 3));
        // assert that 3 rounds is created with 8 teams.
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 3
        ]);
    }


    public function test_games_by_league_can_be_created_with_non_elimination_number_of_teams_different_than_4_8_16_or_32(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'elimination', 'rounds' => 1]);
        Team::factory()->times(5)->create([
            'tournament_id' => $tournament->id,
        ]);
        $eliminationGameService = new EliminationGameService();
        $games = $eliminationGameService->createAllEliminationGames($tournament);

        // assert 4 games is created with 5 teams.
        $this->assertCount(4, $games);
        // assert 1 game are created in round 1.
        $this->assertCount(1, array_filter($games, fn($item) => $item['round'] === 1));
        // assert 2 games are created in round 2.
        $this->assertCount(2, array_filter($games, fn($item) => $item['round'] === 2));
        // assert 1 game are created in round 3.
        $this->assertCount(1, array_filter($games, fn($item) => $item['round'] === 3));
        // assert that 3 rounds is created with 8 teams.
        $this->assertDatabaseHas('elimination_games', [
            'tournament_id' => $tournament->id,
            'round' => 3
        ]);
    }

}
