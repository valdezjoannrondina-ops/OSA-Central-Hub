<?php

namespace App\Policies;

use App\Models\EventRequirement;
use App\Models\User;

class EventRequirementPolicy
{
    public function approve(User $user, EventRequirement $requirement): bool
    {
        // Only admin can approve requirements
        return (int)($user->role ?? 0) === 4;
    }
}


