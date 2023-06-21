<?php

namespace App\Services;

use App\Helpers\ImageManager;
use App\Models\Team;

class TeamService
{
    use ImageManager;
    public function createTeam(array $data): Team
    {
        $team = new Team();
        $team->name = $data['name'];
        $team->tournament_id = $data['tournament_id'];

        if (isset($data['image'])) {
            $image = $data['image'];
            $imagePath = $this->uploads($image, 'team_images');
            $team->image_path = $imagePath;
        }

        $team->save();

        return $team;
    }

    public function deleteTeam(Team $team): void
    {
        $team->delete();
    }
}
