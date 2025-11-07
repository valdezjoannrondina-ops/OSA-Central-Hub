<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Load dashboard data for assistant-staff (role 3)
        // Example: events, appointments, files, calendar, participants, etc.
        return view('assistant-staff-dashboard');
    }
}
