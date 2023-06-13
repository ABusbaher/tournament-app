<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamService
{
    use RefreshDatabase;
    public function createTeam(array $data): Team
    {
        $team = new Team();
        $team->name = $data['name'];
        $team->tournament_id = $data['tournament_id'];

        if (isset($data['image'])) {
            $image = $data['image'];
            $imagePath = $image->store('team_images', 'public');
            $team->image_path = $imagePath;
        }

        $team->save();

        return $team;
    }
}
