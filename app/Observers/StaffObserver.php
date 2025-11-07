<?php

namespace App\Observers;

use App\Models\Staff;
use App\Models\User;

class StaffObserver
{
    private static $syncing = false;

    /**
     * Handle the Staff "updated" event.
     * Sync email changes to User table when staff email is updated.
     */
    public function updated(Staff $staff)
    {
        // Prevent infinite recursion
        if (self::$syncing) {
            return;
        }
        
        // Check if email was changed
        if ($staff->wasChanged('email')) {
            self::$syncing = true;
            
            try {
                $oldEmail = $staff->getOriginal('email');
                $newEmail = $staff->email;
                
                // Find user by old email (case-insensitive)
                $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])->first();
                
                // If not found by old email, try new email (case-insensitive)
                if (!$user) {
                    $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim($newEmail))])->first();
                }
                
                // If still not found, try by user_id
                if (!$user && $staff->user_id) {
                    $user = User::where('user_id', $staff->user_id)->first();
                }
                
                // Update user email if found and different
                if ($user && strtolower(trim($user->email)) !== strtolower(trim($newEmail))) {
                    $user->email = $newEmail;
                    $user->saveQuietly(); // Use saveQuietly to prevent observer recursion
                }
                
                // Update ALL other staff records with the old email to the new email
                if ($oldEmail && strtolower(trim($newEmail)) !== strtolower(trim($oldEmail))) {
                    Staff::withoutEvents(function() use ($oldEmail, $newEmail, $staff) {
                        Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])
                            ->where('id', '!=', $staff->id)
                            ->update(['email' => $newEmail]);
                    });
                }
                
                // Update ALL user records with the old email to the new email
                if ($oldEmail && strtolower(trim($newEmail)) !== strtolower(trim($oldEmail))) {
                    User::withoutEvents(function() use ($oldEmail, $newEmail, $user) {
                        User::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])
                            ->where('id', '!=', $user->id ?? 0)
                            ->update(['email' => $newEmail]);
                    });
                }
            } finally {
                self::$syncing = false;
            }
        }
    }
}

