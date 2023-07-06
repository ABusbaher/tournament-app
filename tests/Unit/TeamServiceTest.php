<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Models\Tournament;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    private string $testFilename;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testFilename = "test_image.jpg";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $filename = public_path('storage/team_images/' . $this->testFilename);
        if(file_exists($filename)) {
            unlink($filename);
        }
    }


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
            'tournament_id' => $teamData['tournament_id'],
        ]);
    }

    public function test_team_can_be_created_with_image(): void
    {
        $file = UploadedFile::fake()->image($this->testFilename);
        $teamData = [
            'name' => 'Test team',
            'tournament_id' => Tournament::factory()->create()->id,
            'image' => $file
        ];
        $teamService = new TeamService();
        $team = $teamService->createTeam($teamData);

        $this->assertDatabaseHas('teams', [
            'name' => 'Test team',
            'tournament_id' => $teamData['tournament_id'],
        ]);

        $this->assertNotNull($team->image_path);
        $this->testFilename = substr($team->image_path, strrpos($team->image_path, '/') + 1);


        Storage::disk('public')->assertExists('team_images/' . $this->testFilename);
    }

    public function test_team_can_be_deleted(): void
    {
        $teamData = [
            'name' => 'Test team',
            'tournament_id' => Tournament::factory()->create()->id
        ];
        $teamService = new TeamService();
        $team = $teamService->createTeam($teamData);
        $teamService->deleteTeam($team);

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_update_team_with_image(): void
    {
        $team = Team::factory()->withImage()->create();
        $oldImage =  substr($team->image_path, strrpos($team->image_path, '/') + 1);

        $imagePath = UploadedFile::fake()->image($this->testFilename);
        $data = [
            'name' => 'Updated Team Name',
            'image' => $imagePath,
        ];
        $teamService = new TeamService();
        $updatedTeam = $teamService->updateTeam($team, $data);
        $this->testFilename = substr($updatedTeam->image_path, strrpos($updatedTeam->image_path, '/') + 1);

        $this->assertEquals('Updated Team Name', $updatedTeam->name);
        $this->assertNotNull($updatedTeam->image_path);
        Storage::disk('public')->assertExists('team_images/' . $this->testFilename);
        Storage::disk('public')->assertMissing('team_images/' . $oldImage);
    }

    public function test_update_team_without_image(): void
    {
        $team = Team::factory()->create();
        $data = [
            'name' => 'Updated Team Name',
        ];
        $teamService = new TeamService();
        $updatedTeam = $teamService->updateTeam($team, $data);

        $this->assertEquals('Updated Team Name', $updatedTeam->name);
        $this->assertNull($updatedTeam->image_path);
    }

}
