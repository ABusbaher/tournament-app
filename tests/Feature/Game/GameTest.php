<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_games_by_league_can_be_created(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Team::factory()->times(6)->create([
            'tournament_id' => $tournament->id,
        ]);
        $response = $this->post(route('games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('games', [
            'tournament_id' => $tournament->id,
            'fixture' => 10 // assert that 10 fixtures is created with 6 teams and 2 rounds of matches.
        ]);
        // assert 30 games is created with 6 teams and 2 rounds of matches.
        $this->assertEquals(30, \DB::table('games')->count());
    }

    public function test_league_games_can_not_be_created_if_already_been_created_before(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Game::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $response = $this->post(route('games.create.all', ['tournament' => $tournament->id]),
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
        $response = $this->post(route('games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(422);
        $response->assertInvalid(['tournament_id']);
    }

    public function test_games_can_be_fetched_by_fixtures(): void
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Game::factory()->count(4)->create(['tournament_id' => $tournament->id, 'fixture' => 1]);
        $response = $this->get(route('games.by_fixture', [
            'tournament' => $tournament->id,
            'fixture' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'max_fixture' => 1,
            'prev_fixture' => NULL,
            'next_fixture' => NULL
        ]);
        $response->assertJsonCount(4);
    }

    public function test_games_return_404_if_not_existing_fixture_is_provided()
    {
        $tournament = Tournament::factory()->create(['type' => 'league', 'rounds' => 2]);
        Game::factory()->count(4)->create(['tournament_id' => $tournament->id, 'fixture' => 1]);
        $response = $this->get(route('games.by_fixture', [
            'tournament' => $tournament->id,
            'fixture' => 'non-existing-fixture'
        ]));
        $response->assertStatus(404);
    }

    public function test_game_can_be_fetched_by_tournament_id_and_game_id()
    {
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->get(route('game.show', [
            'tournament' => 1,
            'game' => 1,
        ]));
        $response->assertStatus(200);
        $response->assertJson([
            'id' => 1,
            'tournament_id' => 1
        ]);
    }

    public function test_game_can_not_be_fetched_if_wrong_tournament_id_or_game_id()
    {
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->get(route('game.show', [
            'tournament' => 1,
            'game' => 'none-existing-game-id',
        ]));
        $response->assertStatus(404);
    }

    public function test_game_score_can_be_updated()
    {
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['host_goals' => 2, 'guest_goals' => 0]
        );
        $response->assertStatus(200);
        $response->assertJson([
            'host_goals' => 2,
            'guest_goals' => 0
        ]);
    }

    public function test_game_score_can_not_be_updated_if_scores_are_not_correct_format()
    {
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['host_goals' => -2, 'guest_goals' => 'not-a-goal']
        );
        $response->assertStatus(422);
        $response->assertInvalid(['guest_goals']);
        $response->assertInvalid(['host_goals']);
    }

    public function test_game_score_can_not_be_updated_if_wrong_game_id_is_provided()
    {
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 'not-valid-game-id']),
            ['host_goals' => 2, 'guest_goals' => 0]
        );
        $response->assertStatus(404);
    }

}
