<?php

namespace Tests\Unit;

use App\Models\Tournament;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\Assert;
use Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

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
            'tournament_id' => 1,
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
            'tournament_id' => 1,
        ]);

        $this->assertNotNull($team->image_path);
        $this->testFilename = substr($team->image_path, strrpos($team->image_path, '/') + 1);


        Storage::disk('public')->assertExists('team_images/' . $this->testFilename);
    }



}
