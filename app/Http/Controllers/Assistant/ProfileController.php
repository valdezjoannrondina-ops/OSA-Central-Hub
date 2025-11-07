<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        return view('assistant.profile');
    }
}
