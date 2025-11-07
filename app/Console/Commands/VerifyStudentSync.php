<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;

class VerifyStudentSync extends Command
{
    protected $signature = 'students:verify-sync';
    protected $description = 'Verify that all role 1 users have corresponding student records';

    public function handle(): int
    {
        $this->info('Verifying student data synchronization...');
        $this->newLine();

        // Get all role 1 users
        $role1Users = User::where('role', 1)->get();
        $totalRole1Users = $role1Users->count();
        
        $this->info("Total role 1 users in users table: {$totalRole1Users}");
        $this->newLine();

        // Check which users have student records
        $usersWithStudents = 0;
        $usersWithoutStudents = [];
        
        foreach ($role1Users as $user) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                $usersWithStudents++;
            } else {
                $usersWithoutStudents[] = [
                    'id' => $user->id,
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'name' => ($user->first_name ?? '') . ' ' . ($user->last_name ?? ''),
                ];
            }
        }

        // Get all student records
        $totalStudents = Student::count();
        $studentsWithoutUsers = [];
        
        $allStudents = Student::all();
        foreach ($allStudents as $student) {
            if (!$student->user_id || !User::where('id', $student->user_id)->exists()) {
                $studentsWithoutUsers[] = [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'email' => $student->email,
                    'name' => ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                ];
            }
        }

        $this->info("Users with student records: {$usersWithStudents}/{$totalRole1Users}");
        $this->info("Total students in students table: {$totalStudents}");
        $this->newLine();

        // Report users without student records
        if (count($usersWithoutStudents) > 0) {
            $this->warn("⚠ Found " . count($usersWithoutStudents) . " role 1 users WITHOUT student records:");
            $this->table(
                ['User ID', 'Student ID', 'Email', 'Name'],
                array_map(function($u) {
                    return [$u['id'], $u['user_id'] ?? 'N/A', $u['email'] ?? 'N/A', $u['name']];
                }, $usersWithoutStudents)
            );
            $this->newLine();
            $this->warn("Run 'php artisan students:sync-users' to create missing student records.");
        } else {
            $this->info("✓ All role 1 users have corresponding student records!");
        }

        // Report students without user records
        if (count($studentsWithoutUsers) > 0) {
            $this->warn("⚠ Found " . count($studentsWithoutUsers) . " student records WITHOUT user records:");
            $this->table(
                ['Student ID', 'User ID', 'Email', 'Name'],
                array_map(function($s) {
                    return [$s['id'], $s['user_id'] ?? 'N/A', $s['email'] ?? 'N/A', $s['name']];
                }, $studentsWithoutUsers)
            );
            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info('=== Verification Summary ===');
        $this->info("Total role 1 users: {$totalRole1Users}");
        $this->info("Users with student records: {$usersWithStudents}");
        $this->info("Users without student records: " . count($usersWithoutStudents));
        $this->info("Total students: {$totalStudents}");
        $this->info("Students without user records: " . count($studentsWithoutUsers));
        
        if (count($usersWithoutStudents) === 0 && count($studentsWithoutUsers) === 0) {
            $this->newLine();
            $this->info('✓ All role 1 users are properly synced with student records!');
            return 0;
        } else {
            $this->newLine();
            $this->warn('⚠ Some records need synchronization. Run "php artisan students:sync-users" to fix.');
            return 1;
        }
    }
}

