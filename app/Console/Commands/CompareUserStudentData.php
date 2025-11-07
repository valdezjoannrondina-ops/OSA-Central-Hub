<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class CompareUserStudentData extends Command
{
    protected $signature = 'students:compare-data {--detailed : Show detailed field-by-field comparison}';
    protected $description = 'Compare data between users and students tables for all role 1 users';

    public function handle(): int
    {
        $detailed = $this->option('detailed');
        
        $this->info('Comparing data between users and students tables for role 1 users...');
        $this->newLine();

        // Get all role 1 users
        $role1Users = User::where('role', 1)->get();
        $totalRole1Users = $role1Users->count();
        
        $this->info("Total role 1 users: {$totalRole1Users}");
        $this->newLine();

        if ($totalRole1Users === 0) {
            $this->warn('No role 1 users found in the database.');
            return 0;
        }

        $usersWithStudents = 0;
        $usersWithoutStudents = [];
        $mismatchedData = [];
        $matchingData = 0;

        // Fields to compare (excluding timestamps and IDs)
        $fieldsToCompare = [
            'first_name', 'middle_name', 'last_name', 'email', 'contact_number',
            'gender', 'birth_date', 'age', 'civil_status', 'maiden_name',
            'place_of_birth', 'complete_home_address', 'department_id', 'course_id',
            'organization_id', 'scholarship_id', 'year_level', 'student_type1',
            'student_type2', 'student_type', 'school_year', 'semester',
            'emergency_contact_name', 'emergency_contact_number', 'emergency_relation',
            'parent_spouse_guardian', 'parent_spouse_guardian_address',
            'elementary_school', 'elementary_address', 'elementary_year_graduated',
            'high_school', 'high_school_address', 'high_school_year_graduated',
            'college_name', 'college_address', 'college_course', 'college_year',
            'form_137_presented', 'tor_presented', 'good_moral_cert_presented',
            'birth_cert_presented', 'marriage_cert_presented'
        ];

        foreach ($role1Users as $user) {
            $student = Student::where('user_id', $user->id)->first();
            
            if (!$student) {
                $usersWithoutStudents[] = [
                    'user_id' => $user->user_id,
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                ];
                continue;
            }

            $usersWithStudents++;
            
            // Compare fields
            $mismatches = [];
            foreach ($fieldsToCompare as $field) {
                $userValue = $user->$field;
                $studentValue = $student->$field;
                
                // Normalize values for comparison
                $userValue = $this->normalizeValue($userValue);
                $studentValue = $this->normalizeValue($studentValue);
                
                if ($userValue !== $studentValue) {
                    $mismatches[] = [
                        'field' => $field,
                        'user_value' => $userValue ?? '(null)',
                        'student_value' => $studentValue ?? '(null)',
                    ];
                }
            }
            
            if (count($mismatches) > 0) {
                $mismatchedData[] = [
                    'user_id' => $user->user_id,
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                    'mismatches' => $mismatches,
                ];
            } else {
                $matchingData++;
            }
        }

        // Display results
        $this->info("=== Comparison Results ===");
        $this->info("Total role 1 users: {$totalRole1Users}");
        $this->info("Users with student records: {$usersWithStudents}");
        $this->info("Users without student records: " . count($usersWithoutStudents));
        $this->info("Users with matching data: {$matchingData}");
        $this->info("Users with mismatched data: " . count($mismatchedData));
        $this->newLine();

        // Report users without student records
        if (count($usersWithoutStudents) > 0) {
            $this->warn("⚠ Found " . count($usersWithoutStudents) . " role 1 users WITHOUT student records:");
            $this->table(
                ['User ID', 'ID', 'Email', 'Name'],
                array_map(function($u) {
                    return [$u['user_id'] ?? 'N/A', $u['id'], $u['email'] ?? 'N/A', $u['name']];
                }, $usersWithoutStudents)
            );
            $this->newLine();
        }

        // Report mismatched data
        if (count($mismatchedData) > 0) {
            $this->warn("⚠ Found " . count($mismatchedData) . " users with mismatched data:");
            
            foreach ($mismatchedData as $mismatch) {
                $this->line("User: {$mismatch['name']} ({$mismatch['email']}) - User ID: {$mismatch['user_id']}");
                
                if ($detailed) {
                    $this->table(
                        ['Field', 'Users Table Value', 'Students Table Value'],
                        array_map(function($m) {
                            return [$m['field'], $m['user_value'], $m['student_value']];
                        }, $mismatch['mismatches'])
                    );
                } else {
                    $mismatchFields = implode(', ', array_column($mismatch['mismatches'], 'field'));
                    $this->line("  Mismatched fields: {$mismatchFields}");
                }
                $this->newLine();
            }
        }

        // Summary
        $this->newLine();
        if (count($usersWithoutStudents) === 0 && count($mismatchedData) === 0) {
            $this->info('✓ All role 1 users have matching data in both users and students tables!');
            return 0;
        } else {
            $this->warn('⚠ Some discrepancies found. Run "php artisan students:sync-users" to sync data.');
            return 1;
        }
    }

    /**
     * Normalize values for comparison
     */
    private function normalizeValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
        
        if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d');
        }
        
        return (string) $value;
    }
}

