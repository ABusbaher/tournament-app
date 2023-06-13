<?php

namespace Tests\Feature\Team;

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
        ]);
    }
}
