<?php

namespace App\Services;

use App\Exceptions\TournamentExistsException;
use App\Helpers\ImageManager;
use App\Models\EliminationGame;
use App\Models\Game;
use App\Models\Team;
use App\Models\Tournament;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class TeamService
{
    use ImageManager;

    public function getAllTeamsByTournament(Tournament $tournament): Collection|array
    {
        return Team::with('tournament')->where('tournament_id', $tournament->id)->get();
    }

    public function getTeam(Tournament $tournament,Team $team): Model|Builder|Team
    {
        return Team::where('tournament_id', $tournament->id)
            ->where('id', $team->id)
            ->firstOrFail();
    }

    /**
     * @throws TournamentExistsException
     */
    public function createTeam(array $data, Tournament $tournament): Team
    {
        if ($this->gamesAlreadyCreated($tournament)) {
            throw new TournamentExistsException("Cannot add a team because an active tournament games already exists.", Response::HTTP_CONFLICT);
        }
        $team = new Team();
        $team->name = $data['name'];
        $team->shorten_name = $data['shorten_name'];
        $team->tournament_id = $data['tournament_id'];

        if (isset($data['image'])) {
            $image = $data['image'];
            $imagePath = $this->uploads($image, 'team_images');
            $team->image_path = $imagePath;
        }

        $team->save();
        return $team;
    }

    public function updateTeam(Team $team, array $data): Team
    {
        $old_image = $team->image_path;
        $team->name = $data['name'];
        $team->shorten_name = $data['shorten_name'];
        if (isset($data['image'])) {
            $imagePath = $this->uploads($data['image'], 'team_images');
            $team->image_path = $imagePath;
            // Delete the old image if exists
            if ($old_image) {
                $parsed_path = parse_url($old_image);
                File::delete(public_path($parsed_path['path']));
            }
        }
        $team->save();
        return $team;
    }

    /**
     * @throws TournamentExistsException
     */
    public function deleteTeam(Team $team, Tournament $tournament): void
    {
        if ($this->gamesAlreadyCreated($tournament)) {
            throw new TournamentExistsException("Cannot delete a team because an active tournament games already exists.", Response::HTTP_CONFLICT);
        }
        $team->delete();
    }


    private function gamesAlreadyCreated(Tournament $tournament): bool
    {
        $model = $tournament->type === 'league' ? Game::class : ($tournament->type === 'elimination' ? EliminationGame::class : null);

        if ($model) {
            return $model::where('tournament_id', $tournament->id)->exists();
        }

        return false;
    }
}
