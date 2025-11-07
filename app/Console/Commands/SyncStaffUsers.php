<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncStaffUsers extends Command
{
    protected $signature = 'staff:sync-users {--dry-run : Show what would change without writing}';
    protected $description = 'Sync Staff records to User records (role=2, email/designation aligned)';

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $staffRecords = \App\Models\Staff::all();
        $created = 0; $updated = 0; $skipped = 0;

        foreach ($staffRecords as $staff) {
            $user = \App\Models\User::where('email', $staff->email)->first();
            if (!$user) {
                if ($dry) {
                    $this->line("[DRY] Create user for {$staff->email} ({$staff->designation})");
                    $created++; continue;
                }
                $user = new \App\Models\User();
                $user->first_name = $staff->first_name ?? '';
                $user->last_name = $staff->last_name ?? '';
                $user->email = $staff->email;
                $user->birth_date = $staff->birth_date ?? '2000-01-01';
                // Use staff password (hash if not already hashed)
                $staffPassword = $staff->password;
                // If password is already hashed (starts with $2y$), use as is; else hash it
                if (is_string($staffPassword) && str_starts_with($staffPassword, '$2y$')) {
                    $user->password = $staffPassword;
                } else {
                    $user->password = bcrypt($staffPassword);
                }
                $user->role = 2; // staff
                $user->designation = $staff->designation;
                $user->department_id = $staff->department_id ?? null;
                $user->save();
                $created++;
                $this->info("Created user for {$staff->email}");
                continue;
            }

            // Update existing user to align role and designation
            $changes = [];
            if ($user->role !== 2) { $changes['role'] = 2; }
            if ($user->designation !== $staff->designation) { $changes['designation'] = $staff->designation; }
            if (($user->department_id ?? null) !== ($staff->department_id ?? null)) { $changes['department_id'] = $staff->department_id; }

            if (empty($changes)) {
                $skipped++;
                continue;
            }

            if ($dry) {
                $this->line("[DRY] Update user {$staff->email}: " . json_encode($changes));
                $updated++; continue;
            }

            foreach ($changes as $field => $value) {
                $user->{$field} = $value;
            }
            $user->save();
            $updated++;
            $this->info("Updated user {$staff->email}");
        }

        $this->info("Sync complete. Created: {$created}, Updated: {$updated}, Unchanged: {$skipped}");
        return 0;
    }
}
