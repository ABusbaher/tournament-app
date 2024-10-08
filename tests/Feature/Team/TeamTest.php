<?php

namespace Tests\Feature\Team;

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

    public function test_team_without_image_can_be_created(): void
    {
        $response = $this->createTeam();

        $response->assertStatus(201);
        $this->assertDatabaseHas('teams', [
            'name' => 'Team 1',
            'tournament_id' => 1,
            'shorten_name' => 'Voša',
        ]);
    }

    public function test_team_can_be_deleted(): void
    {
        $team = Team::factory()->create();
        $response = $this->delete(route('team.destroy', ['tournament' => $team->tournament_id, 'team' => $team->id]));

        $response->assertStatus(204);
        $this->assertDatabaseEmpty('teams');
    }

    public function test_team_can_not_be_deleted_if_wrong_team_id_is_provided(): void
    {
        $team = Team::factory()->create();
        $response = $this->delete(route('team.destroy', ['tournament' => $team->tournament_id, 'team' => 'not-valid-team-id']));

        $response->assertStatus(404);
    }

    public function test_team_can_not_be_deleted_if_wrong_tournament_id_is_provided(): void
    {
        $team = Team::factory()->create();
        $response = $this->delete(route('team.destroy', ['tournament' => 'not-valid-tournament-id', 'team' => $team->id]));

        $response->assertStatus(404);
    }

    public function test_team_can_be_edited_without_image(): void
    {
        $team = Team::factory()->create();
        $response = $this->put(route('team.update', ['tournament' => $team->tournament_id, 'team' => $team->id]),
        ['name' => 'Updated Team', 'shorten_name' => 'Voša']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('teams', [
            'name' => 'Updated Team',
            'shorten_name' => 'Voša',
            'tournament_id' => $team->tournament_id
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
