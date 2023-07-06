<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;


class GamePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the games can be created.
     */
    public function create(User $user, Tournament $tournament): bool
    {
        $minTeams = 4;
        $teamCount = Team::where('tournament_id', $tournament->id)->count();
        return $teamCount >= $minTeams;
    }

}
