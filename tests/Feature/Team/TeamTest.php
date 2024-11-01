<?php

namespace Tests\Feature\Team;

use App\Enums\TournamentTypeEnum;
use App\Models\EliminationGame;
use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    private  function createTeam(): TestResponse
    {
        return $this->postWithCsrfToken(route('team.store', ['tournament' => 1]),[
            'name' => 'Team 1',
            'shorten_name' => 'Voša',
            'tournament_id' => Tournament::factory()->create()->id,
        ]);
    }

    public function test_team_without_image_and_negative_points_can_be_created_by_admin(): void
    {
        $this->signInAdmin();
        $response = $this->createTeam();

        $response->assertStatus(201);
        $this->assertDatabaseHas('teams', [
            'name' => 'Team 1',
            'tournament_id' => 1,
            'shorten_name' => 'Voša',
            'negative_points' => null
        ]);
    }

    public function test_team_without_image_can_be_created_edited_deleted_by_guest(): void
    {
        $team = Team::factory()->create();
        $response = $this->createTeam();
        $response->assertStatus(403);

        $deleteResponse = $this->delete(route('team.destroy', ['tournament' => $team->tournament_id, 'team' => $team->id]));
        $deleteResponse->assertStatus(403);

        $editResponse = $this->put(route('team.update', ['tournament' => $team->tournament_id, 'team' => $team->id]),
            ['name' => 'Updated Team', 'shorten_name' => 'Voša']);
        $editResponse->assertStatus(403);
    }

    public function test_team_without_image_can_be_created_edited_deleted_by_regular_user(): void
    {
        $this->signInUser();
        $team = Team::factory()->create();

        $response = $this->createTeam();
        $response->assertStatus(403);

        $deleteResponse = $this->delete(route('team.destroy', ['tournament' => $team->tournament_id, 'team' => $team->id]));
        $deleteResponse->assertStatus(403);

        $editResponse = $this->put(route('team.update', ['tournament' => $team->tournament_id, 'team' => $team->id]),
            ['name' => 'Updated Team', 'shorten_name' => 'Voša']);
        $editResponse->assertStatus(403);
    }

    public function test_team_can_be_deleted_by_admin(): void
    {
        $this->signInAdmin();
        $team = Team::factory()->create();
        $response = $this->delete(route('team.destroy', ['tournament' => $team->tournament_id, 'team' => $team->id]));

        $response->assertStatus(204);
        $this->assertDatabaseEmpty('teams');
    }

    public function test_team_can_not_be_deleted_if_wrong_team_id_is_provided(): void
    {
        $this->signInAdmin();
        $team = Team::factory()->create();
        $response = $this->delete(route('team.destroy', ['tournament' => $team->tournament_id, 'team' => 'not-valid-team-id']));

        $response->assertStatus(404);
    }

    public function test_team_can_not_be_deleted_if_wrong_tournament_id_is_provided(): void
    {
        $this->signInAdmin();
        $team = Team::factory()->create();
        $response = $this->delete(route('team.destroy', ['tournament' => 'not-valid-tournament-id', 'team' => $team->id]));

        $response->assertStatus(404);
    }

    public function test_team_can_not_be_added_if_fixture_games_already_created(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE]);
        $team = Team::factory()->create(['tournament_id' => $tournament->id]);
        Game::factory()->create(['tournament_id' => $tournament->id, 'host_team_id' => $team->id]);
        $response = $this->post(route('team.store', ['tournament' => $tournament->id]), [
            'name' => 'Team 1',
            'shorten_name' => 'Voša',
            'tournament_id' => $tournament->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Cannot add a team because an active tournament games already exists.',
        ]);
    }

    public function test_team_can_not_be_added_if_cup_games_already_created(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION]);
        $team = Team::factory()->create(['tournament_id' => $tournament->id]);
        EliminationGame::factory()->create(['tournament_id' => $tournament->id, 'team1_id' => $team->id]);
        $response = $this->post(route('team.store', ['tournament' => $tournament->id]), [
            'name' => 'Team 1',
            'shorten_name' => 'Voša',
            'tournament_id' => $tournament->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Cannot add a team because an active tournament games already exists.',
        ]);
    }

    public function test_team_can_not_be_deleted_if_fixture_games_already_created(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::LEAGUE]);
        $team = Team::factory()->create(['tournament_id' => $tournament->id]);
        Game::factory()->create(['tournament_id' => $tournament->id, 'host_team_id' => $team->id]);
        $response = $this->delete(route('team.destroy', ['tournament' => $tournament->id, 'team' => $team->id]));

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Cannot delete a team because an active tournament games already exists.',
        ]);
    }

    public function test_team_can_not_be_deleted_if_cup_games_already_created(): void
    {
        $this->signInAdmin();
        $tournament = Tournament::factory()->create(['type' => TournamentTypeEnum::ELIMINATION]);
        $team = Team::factory()->create(['tournament_id' => $tournament->id]);
        EliminationGame::factory()->create(['tournament_id' => $tournament->id, 'team1_id' => $team->id]);
        $response = $this->delete(route('team.destroy', ['tournament' => $tournament->id, 'team' => $team->id]));

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Cannot delete a team because an active tournament games already exists.',
        ]);
    }

    public function test_team_can_be_edited_without_image(): void
    {
        $this->signInAdmin();
        $team = Team::factory()->create();
        $response = $this->put(route('team.update', ['tournament' => $team->tournament_id, 'team' => $team->id]),
        ['name' => 'Updated Team', 'shorten_name' => 'Voša', 'negative_points' => -2]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('teams', [
            'name' => 'Updated Team',
            'shorten_name' => 'Voša',
            'negative_points' => -2,
            'tournament_id' => $team->tournament_id
        ]);
    }

    public function test_team_can_be_edited_with_positive_points(): void
    {
        $this->signInAdmin();
        $team = Team::factory()->create();
        $response = $this->put(route('team.update', ['tournament' => $team->tournament_id, 'team' => $team->id]),
            ['name' => 'Updated Team', 'shorten_name' => 'Voša', 'negative_points' => 2]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'negative_points' => ['The negative points field must not be greater than 0.'],
        ]);
    }

    public function test_team_can_be_fetched_with_proper_tournament_id_and_team_id(): void
    {
        $team = Team::factory()->create();
        $response = $this->get(route('team.update', ['tournament' => $team->tournament_id, 'team' => $team->id]));
        $response->assertStatus(200);
    }

    public function test_team_can_not_be_fetched_with_invalid_team_id(): void
    {
        $team = Team::factory()->create();
        $response = $this->get(route('team.update', ['tournament' => $team->tournament_id, 'team' => 'invalid-id']));
        $response->assertStatus(404);
    }
}
