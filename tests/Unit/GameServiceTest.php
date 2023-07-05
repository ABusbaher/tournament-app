<?php
namespace Tests\Unit;

use App\Models\Team;
use App\Models\Tournament;
use App\Services\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_correct_fixtures_by_league_rounds_has_been_set(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Team::factory()->times(6)->create([
                'tournament_id' => $tournament->id,
            ]);
        // Call the private method setLeagueRounds using Reflection
        $reflectionMethod = new \ReflectionMethod(GameService::class, 'setLeagueRounds');
        $result = $reflectionMethod->invoke(new GameService(), $tournament);

        $this->assertEquals(10, $result);
    }

    public function test_games_by_league_can_be_created(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Team::factory()->times(6)->create([
            'tournament_id' => $tournament->id,
        ]);

        $this->assertDatabaseMissing('games', [
            'tournament_id' => $tournament->id,
        ]);

        $gameService = new GameService();
        $games = $gameService->createAllLeagueGames($tournament);

        $this->assertCount(30, $games); // assert 30 games is created with 6 teams and 2 rounds of matches.
        $this->assertDatabaseHas('games', [
            'tournament_id' => $tournament->id,
            'fixture' => 10 // assert that 10 fixtures is created with 6 teams and 2 rounds of matches.
        ]);
    }

    public function test_games_by_league_can_be_created_with_odd_number_of_teams(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Team::factory()->times(5)->create([
            'tournament_id' => $tournament->id,
        ]);
        $gameService = new GameService();
        $games = $gameService->createAllLeagueGames($tournament);

        $this->assertCount(30, $games);
        $this->assertContains(null, array_column($games, 'guest_team_id'));
        $this->assertDatabaseMissing('games', [
            'host_team_id' => null,
        ]);
        $this->assertDatabaseHas('games', [
            'tournament_id' => $tournament->id,
            'fixture' => 10
        ]);
    }

}
