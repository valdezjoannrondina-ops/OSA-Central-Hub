<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // ✅ Added
use App\Models\User;         // ✅ Added

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // ✅ Remove $redirectTo entirely
    protected function authenticated(Request $request, User $user)
    {
        switch ($user->role) {
            case 4: return redirect()->intended('/admin/dashboard');
            case 2: return redirect()->intended('/staff/dashboard');
            case 3: return redirect()->intended('/assistant/dashboard');
            default: return redirect()->intended('/student/dashboard');
        }
    }
}