<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $staff = auth()->user();

        // Appointments assigned to this staff
        $appointments = \App\Models\Appointment::where('assigned_staff_id', $staff->id)
            ->with('user')
            ->latest('appointment_date')
            ->get();


        // Events created by this staff
        $events = \App\Models\Event::where('created_by', $staff->id)
            ->withCount('participants')
            ->latest()
            ->get();

        // Pending events for approval (status = 'pending')
        $pendingEvents = \App\Models\Event::where('status', 'pending')
            ->with('creator')
            ->latest('event_date')
            ->get();

        // Recent participants (for quick view)
        $participants = \App\Models\EventParticipant::whereHas('event', function($q) use ($staff) {
                $q->where('created_by', $staff->id);
            })
            ->with('user', 'event')
            ->latest()
            ->take(10)
            ->get();

    return view('staff.dashboard', compact('appointments', 'events', 'participants', 'pendingEvents'));
    }
}