<?php

namespace Tests\Feature\Game;

use App\Enums\TournamentTypeEnum;
use App\Models\FixturePassword;
use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_not_create_a_games_by_league(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        Team::factory()->times(6)->create([
            'tournament_id' => $tournament->id,
        ]);

        $response = $this->post(route('games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(403);
    }

    public function test_regular_user_can_not_create_a_games_by_league(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        $this->signInUser();
        Team::factory()->times(6)->create([
            'tournament_id' => $tournament->id,
        ]);

        $response = $this->post(route('games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(403);
    }

    public function test_games_by_league_and_fixture_passwords_can_be_created_by_admin(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
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
        $this->assertDatabaseHas('fixture_passwords', [
            'tournament_id' => $tournament->id,
            'fixture' => 10 // assert that 10 fixture_passwords is created with 6 teams and 2 rounds of matches.
        ]);
        // assert 30 games is created with 6 teams and 2 rounds of matches.
        $this->assertEquals(30, \DB::table('games')->count());
        // assert 10 fixture_passwords is created with 6 teams and 2 rounds of matches.
        $this->assertEquals(10, \DB::table('fixture_passwords')->count());
    }

    public function test_league_games_can_not_be_created_if_already_been_created_before(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
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
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        Game::factory()->count(6)->create(['tournament_id' => $tournament->id]);
        $response = $this->put(route('tournament.updateAll', ['tournament' => $tournament->id]), [
            'name' => 'PES updated',
            'rounds' => 3,
            'type' => TournamentTypeEnum::ELIMINATION->value,
            'tournament_id' => $tournament->id
        ]);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Fixtures for this tournament already exist!',
        ]);
    }

    public function test_league_games_can_not_be_created_if_less_than_four_teams_created(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        Team::factory()->count(3)->create(['tournament_id' => $tournament->id]);
        $response = $this->post(route('games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);

        $response->assertStatus(422);
        $response->assertInvalid(['tournament_id']);
    }

    public function test_games_can_be_fetched_by_fixtures(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        Game::factory()->count(4)->create(['tournament_id' => $tournament->id, 'fixture' => 1]);
        FixturePassword::factory()->create(['tournament_id' => $tournament->id, 'fixture' => 1]);
        $response = $this->get(route('games.by_fixture', [
            'tournament' => $tournament->id,
            'fixture' => 1,
        ]));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'max_fixture' => 1,
            'fixture' => 1,
            'prev_fixture' => NULL,
            'next_fixture' => NULL
        ]);
        $response->assertJsonFragment([
            'isPasswordProtected' => false,
        ]);
        $response->assertJsonCount(4, $key = 'games');
    }

    public function test_games_return_404_if_not_existing_fixture_is_provided(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        Game::factory()->count(4)->create(['tournament_id' => $tournament->id, 'fixture' => 1]);
        $response = $this->get(route('games.by_fixture', [
            'tournament' => $tournament->id,
            'fixture' => 'non-existing-fixture'
        ]));
        $response->assertStatus(404);
    }

    public function test_game_can_be_fetched_by_tournament_id_and_game_id(): void
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

    public function test_game_can_not_be_fetched_if_wrong_tournament_id_or_game_id(): void
    {
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->get(route('game.show', [
            'tournament' => 1,
            'game' => 'none-existing-game-id',
        ]));
        $response->assertStatus(404);
    }

    public function test_game_score_and_time_can_be_updated_by_admin(): void
    {
        $this->signInAdmin();
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['host_goals' => 2, 'guest_goals' => 0, 'game_time' => $gameTime]
        );
        $response->assertStatus(200);
        $response->assertJson([
            'host_goals' => 2,
            'guest_goals' => 0,
            'game_time' => $gameTime->toISOString(),
        ]);
    }

    public function test_game_score_and_time_can_not_be_updated_by_guest(): void
    {
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['host_goals' => 2, 'guest_goals' => 0, 'game_time' => $gameTime]
        );
        $response->assertStatus(403);
    }

    public function test_game_score_and_time_can_not_be_updated_by_regular_user(): void
    {
        $this->signInUser();
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $gameTime = new Carbon('2024-05-26');
        $gameTime->setTimeFromTimeString('16:00:00');
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['host_goals' => 2, 'guest_goals' => 0, 'game_time' => $gameTime]
        );
        $response->assertStatus(403);
    }


    public function test_game_score_can_not_be_updated_if_scores_are_not_correct_format(): void
    {
        $this->signInAdmin();
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['host_goals' => -2, 'guest_goals' => 'not-a-goal']
        );
        $response->assertStatus(422);
        $response->assertInvalid(['guest_goals']);
        $response->assertInvalid(['host_goals']);
    }

    public function test_game_score_can_not_be_updated_if_game_time_is_not_provided()
    {
        $this->signInAdmin();
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 1]),
            ['host_goals' => 2, 'guest_goals' => 0]
        );
        $response->assertStatus(422);
        $response->assertInvalid(['game_time']);
    }

    public function test_game_score_can_not_be_updated_if_wrong_game_id_is_provided(): void
    {
        $this->signInAdmin();
        Game::factory()->count(4)->create(['tournament_id' => 1]);
        $response = $this->patch(route('game.updateScore', ['tournament' => 1, 'game' => 'not-valid-game-id']),
            ['host_goals' => 2, 'guest_goals' => 0]
        );
        $response->assertStatus(404);
    }

    public function test_game_score_table_can_be_fetched(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        Team::factory()->times(4)->create([
            'tournament_id' => $tournament->id,
        ]);
        $this->post(route('games.create.all', ['tournament' => $tournament->id]),
            ['tournament_id' => $tournament->id]);
        $response = $this->get(route('games.table', [
            'tournament' => $tournament->id,
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(4);
    }

    public function test_game_score_table_can_not_be_fetched_with_invalid_tournament_id(): void
    {
        Game::factory()->withScore()->count(4)->create(['tournament_id' => 1, 'fixture' => 1]);
        $response = $this->get(route('games.table', [
            'tournament' => 'non-valid-tournament-id',
        ]));
        $response->assertStatus(404);
    }

    public function test_game_score_table_with_valid_results_can_be_fetched(): void
    {
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE->value, 'rounds' => 2]);
        $teamWithNegativePoints = Team::factory()->create([
            'id' => 1,
            'name' => 'eliminated_team',
            'tournament_id' => $tournament->id,
            'negative_points' => -2
        ]);
        Team::factory()->times(3)->create([
            'tournament_id' => $tournament->id,
        ]);
        Game::factory()->create([
            'tournament_id' => $tournament->id,
            'host_team_id' => $teamWithNegativePoints->id,
            'host_goals' => 2,
            'guest_team_id' => 2,
            'guest_goals' => 5,
        ]);

        Game::factory()->create([
            'tournament_id' => $tournament->id,
            'host_team_id' => 2,
            'host_goals' => 2,
            'guest_team_id' => 3,
            'guest_goals' => 0,
        ]);

        Game::factory()->create([
            'tournament_id' => $tournament->id,
            'host_team_id' => 3,
            'host_goals' => 1,
            'guest_team_id' => 4,
            'guest_goals' => 1,
        ]);

        $response = $this->get(route('games.table', [
            'tournament' => $tournament->id,
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(4);

        $response->assertJsonFragment([
            'ID' => 1,
            'team' => $teamWithNegativePoints->name,
            'tournament_id' => $tournament->id,
            'image_path' => null,
            'negative_points' => $teamWithNegativePoints->negative_points,
            'Wins' => 0,
            'Draws' => 0,
            'Losses' => 1,
            'Points' => $teamWithNegativePoints->negative_points,
            'GamesPlayed' => 1,
            'Ranking' => 4
        ]);

        $response->assertJsonFragment([
            'ID' => 2,
            'tournament_id' => $tournament->id,
            'image_path' => null,
            'negative_points' => null,
            'Wins' => 2,
            'Draws' => 0,
            'Losses' => 0,
            'Points' => 6,
            'GamesPlayed' => 2,
            'Ranking' => 1
        ]);

        $response->assertJsonFragment([
            'ID' => 3,
            'tournament_id' => $tournament->id,
            'image_path' => null,
            'negative_points' => null,
            'Wins' => 0,
            'Draws' => 1,
            'Losses' => 1,
            'Points' => 1,
            'GamesPlayed' => 2,
            'Ranking' => 3
        ]);

        $response->assertJsonFragment([
            'ID' => 4,
            'tournament_id' => $tournament->id,
            'image_path' => null,
            'negative_points' => null,
            'Wins' => 0,
            'Draws' => 1,
            'Losses' => 0,
            'Points' => 1,
            'GamesPlayed' => 2,
            'Ranking' => 2
        ]);
    }

}
