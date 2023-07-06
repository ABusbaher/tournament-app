<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_games_by_league_can_be_created(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Team::factory()->times(6)->create([
            'tournament_id' => $tournament->id,
        ]);
        $response = $this->post(route('game.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('games', [
            'tournament_id' => $tournament->id,
            'fixture' => 10 // assert that 10 fixtures is created with 6 teams and 2 rounds of matches.
        ]);
        // assert 30 games is created with 6 teams and 2 rounds of matches.
        $this->assertEquals(30, DB::table('games')->count());
    }

    public function test_league_games_can_not_be_created_if_already_been_created_before(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Game::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $response = $this->post(route('game.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Fixtures for this tournament already exist!',
        ]);
    }

    public function test_tournament_can_not_be_updated_if_league_games_are_already_created(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Game::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $response = $this->put(route('tournament.updateAll', ['tournament' => $tournament->id]), [
            'name' => 'PES updated',
            'rounds' => 3,
            'type' => 'elimination',
            'tournament_id' => $tournament->id
        ]);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Fixtures for this tournament already exist!',
        ]);
    }

    public function test_league_games_can_not_be_created_if_less_than_four_teams_created(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Team::factory()->count(3)->create(['tournament_id' => $tournament->id]);
        $response = $this->post(route('game.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(422);
        $response->assertInvalid(['tournament_id']);
    }

}
