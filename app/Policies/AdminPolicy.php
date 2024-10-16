<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    public function adminAccess(User $user): bool
    {
        return $user->isAdmin();
    }
}
