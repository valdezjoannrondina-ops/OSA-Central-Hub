<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array((int)($user->role ?? 0), [2,3,4], true);
    }

    public function update(User $user, Event $event): bool
    {
        if ((int)($user->role ?? 0) === 4) return true;
        return (int)($user->id) === (int)($event->created_by);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function approve(User $user, Event $event): bool
    {
        // Only admin approves/declines events
        return (int)($user->role ?? 0) === 4;
    }

    public function decline(User $user, Event $event): bool
    {
        return $this->approve($user, $event);
    }
}


