<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function approve(User $user, Appointment $appointment): bool
    {
        if ((int)($user->role ?? 0) === 4) return true; // admin
        return (int)($user->role ?? 0) === 2 && (int)$appointment->assigned_staff_id === (int)$user->id;
    }

    public function decline(User $user, Appointment $appointment): bool
    {
        return $this->approve($user, $appointment);
    }

    public function reschedule(User $user, Appointment $appointment): bool
    {
        if ((int)($user->role ?? 0) === 4) return true; // admin
        return (int)($user->role ?? 0) === 2 && (int)$appointment->assigned_staff_id === (int)$user->id;
    }
}


