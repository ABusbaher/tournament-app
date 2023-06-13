<?php

namespace Tests\Unit;

use App\Models\Tournament;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_can_be_created_without_image(): void
    {
        $teamData = [
            'name' => 'Test team',
            'tournament_id' => Tournament::factory()->create()->id
        ];
        $teamService = new TeamService();
        $teamService->createTeam($teamData);

        $this->assertDatabaseHas('teams', [
            'name' => 'Test team',
            'tournament_id' => 1,
        ]);
    }
}
