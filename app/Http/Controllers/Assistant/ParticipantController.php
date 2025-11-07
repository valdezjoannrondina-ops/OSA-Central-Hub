<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function history()
    {
        // Show event participants history filtered by department/courses
        // $participants = ...
        return view('assistant.participants.history');
    }
}
