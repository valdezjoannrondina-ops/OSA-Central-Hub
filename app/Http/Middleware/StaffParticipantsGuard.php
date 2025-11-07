<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffParticipantsGuard
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            // Not logged in: fall back to login
            return redirect('/login');
        }

        // Allow staff (role 2)
        if ((int) $user->role === 2) {
            return $next($request);
        }

        // Redirect others to their own dashboard with an error message
        $route = match ((int) $user->role) {
            4 => 'admin.dashboard',
            3 => 'assistant.dashboard',
            1 => 'student.dashboard',
            default => 'home',
        };

        return redirect()->route($route)->with('error', 'You are not allowed to access this part');
    }
}
