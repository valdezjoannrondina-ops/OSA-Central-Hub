<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStaffDesignation
{
    public function handle(Request $request, Closure $next, string $requiredDesignation)
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Admins always permitted
        if ((int)($user->role ?? 0) === 4) {
            return $next($request);
        }

        // Allow role 2 staff whose designation matches either on user, staff profile, or Staff table
        $userDesignation = $user->designation 
            ?? optional($user->staffProfile)->designation 
            ?? \App\Models\Staff::where('email', $user->email)->value('designation');
        
        // Normalize "Safety Officer" to "EMT Coordinator" for backward compatibility
        $normalizedUserDesignation = trim($userDesignation ?? '');
        if (strcasecmp($normalizedUserDesignation, 'Safety Officer') === 0) {
            $normalizedUserDesignation = 'EMT Coordinator';
        }
        
        $normalizedRequiredDesignation = trim($requiredDesignation);
        if (strcasecmp($normalizedRequiredDesignation, 'Safety Officer') === 0) {
            $normalizedRequiredDesignation = 'EMT Coordinator';
        }
        
        if ((int)($user->role ?? 0) === 2 && $normalizedUserDesignation && strcasecmp($normalizedUserDesignation, $normalizedRequiredDesignation) === 0) {
            return $next($request);
        }

        return abort(403, 'Unauthorized: insufficient designation.');
    }
}

