<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class SyncStudentUsers extends Command
{
    protected $signature = 'students:sync-users {--dry-run : Show what would change without writing}';
    protected $description = 'Sync Student records between users and students tables (role=1)';

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $this->info('Starting student data synchronization...');
        $this->newLine();

        $created = 0;
        $updated = 0;
        $synced = 0;
        $skipped = 0;
        $errors = 0;

        // Step 1: Find users with role=1 (students) that don't have corresponding student records
        $this->info('Step 1: Creating student records for users without student records...');
        $usersWithoutStudents = User::where('role', 1)
            ->whereDoesntHave('student')
            ->get();

        foreach ($usersWithoutStudents as $user) {
            try {
                $studentData = $this->getStudentDataFromUser($user);
                
                if ($dry) {
                    $this->line("[DRY] Would create student record for user: {$user->email} (ID: {$user->id})");
                    $created++;
                    continue;
                }

                $student = Student::create([
                    'user_id' => $user->id,
                    ...$studentData
                ]);
                
                $this->info("✓ Created student record for user: {$user->email} (ID: {$user->id})");
                $created++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to create student for {$user->email}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();

        // Step 2: Find student records that don't have corresponding user records
        $this->info('Step 2: Creating user records for students without user records...');
        $studentsWithoutUsers = Student::where(function($query) {
                $query->whereNull('user_id')
                    ->orWhereDoesntHave('user');
            })
            ->get();

        foreach ($studentsWithoutUsers as $student) {
            try {
                // Try to find user by email first
                $user = User::where('email', $student->email)->first();
                
                if (!$user) {
                    // Create new user
                    $userData = $this->getUserDataFromStudent($student);
                    
                    if ($dry) {
                        $this->line("[DRY] Would create user record for student: {$student->email}");
                        $created++;
                        continue;
                    }

                    $user = User::create([
                        'role' => 1,
                        'password' => bcrypt('temp_password_' . $student->email),
                        ...$userData
                    ]);

                    // Link student to user
                    $student->user_id = $user->id;
                    $student->save();

                    $this->info("✓ Created user record for student: {$student->email} (ID: {$user->id})");
                    $created++;
                } else {
                    // Link student to existing user
                    if ($dry) {
                        $this->line("[DRY] Would link student to existing user: {$student->email} (User ID: {$user->id})");
                    } else {
                        $student->user_id = $user->id;
                        $student->save();
                        $this->info("✓ Linked student to existing user: {$student->email} (User ID: {$user->id})");
                    }
                    $synced++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Failed to create/link user for student {$student->email}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();

        // Step 3: Sync data between users and students for existing records
        $this->info('Step 3: Synchronizing data between users and students tables...');
        $students = Student::whereNotNull('user_id')
            ->with('user')
            ->get();

        foreach ($students as $student) {
            if (!$student->user) {
                $skipped++;
                continue;
            }

            $user = $student->user;
            $userUpdated = false;
            $studentUpdated = false;

            // Sync from user to student
            $userToStudentFields = $this->getSyncFieldsFromUser($user);
            foreach ($userToStudentFields as $field => $value) {
                $currentValue = $student->{$field};
                // Normalize dates for comparison
                if ($field === 'birth_date' && $currentValue && $value) {
                    $currentDate = $currentValue instanceof \DateTime ? $currentValue->format('Y-m-d') : (string)$currentValue;
                    $newDate = $value instanceof \DateTime ? $value->format('Y-m-d') : (string)$value;
                    if ($currentDate !== $newDate) {
                        if ($dry) {
                            $this->line("[DRY] Would update student.{$field} from '{$currentValue}' to '{$value}' for {$student->email}");
                        } else {
                            $student->{$field} = $value;
                            $studentUpdated = true;
                        }
                    }
                } elseif ($currentValue !== $value) {
                    if ($dry) {
                        $this->line("[DRY] Would update student.{$field} from '{$currentValue}' to '{$value}' for {$student->email}");
                    } else {
                        $student->{$field} = $value;
                        $studentUpdated = true;
                    }
                }
            }

            // Sync from student to user
            $studentToUserFields = $this->getSyncFieldsFromStudent($student);
            foreach ($studentToUserFields as $field => $value) {
                $currentValue = $user->{$field};
                // Normalize dates for comparison
                if ($field === 'birth_date' && $currentValue && $value) {
                    $currentDate = $currentValue instanceof \DateTime ? $currentValue->format('Y-m-d') : (string)$currentValue;
                    $newDate = $value instanceof \DateTime ? $value->format('Y-m-d') : (string)$value;
                    if ($currentDate !== $newDate) {
                        if ($dry) {
                            $this->line("[DRY] Would update user.{$field} from '{$currentValue}' to '{$value}' for {$user->email}");
                        } else {
                            $user->{$field} = $value;
                            $userUpdated = true;
                        }
                    }
                } elseif ($currentValue !== $value) {
                    if ($dry) {
                        $this->line("[DRY] Would update user.{$field} from '{$currentValue}' to '{$value}' for {$user->email}");
                    } else {
                        $user->{$field} = $value;
                        $userUpdated = true;
                    }
                }
            }

            if ($studentUpdated || $userUpdated) {
                if (!$dry) {
                    if ($studentUpdated) {
                        $student->save();
                    }
                    if ($userUpdated) {
                        $user->save();
                    }
                    $this->info("✓ Synchronized data for: {$user->email}");
                    $synced++;
                } else {
                    $synced++;
                }
            } else {
                $skipped++;
            }
        }

        $this->newLine();
        $this->info('=== Synchronization Summary ===');
        $this->info("Created: {$created}");
        $this->info("Updated/Synced: {$synced}");
        $this->info("Skipped (already in sync): {$skipped}");
        if ($errors > 0) {
            $this->error("Errors: {$errors}");
        }
        $this->newLine();

        if ($dry) {
            $this->warn('DRY RUN MODE - No changes were made. Run without --dry-run to apply changes.');
        } else {
            $this->info('✓ Synchronization complete!');
        }

        return 0;
    }

    /**
     * Get student data from user record
     */
    private function getStudentDataFromUser(User $user): array
    {
        return [
            'first_name' => $user->first_name ?? '',
            'middle_name' => $user->middle_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $user->email,
            'gender' => $user->gender ?? 'other',
            'birth_date' => $user->birth_date,
            'age' => $user->age,
            'civil_status' => $user->civil_status,
            'maiden_name' => $user->maiden_name,
            'place_of_birth' => $user->place_of_birth,
            'contact_number' => $user->contact_number ?? '',
            'complete_home_address' => $user->complete_home_address,
            'department_id' => $user->department_id,
            'course_id' => $user->course_id,
            'organization_id' => $user->organization_id,
            'scholarship_id' => $user->scholarship_id,
            'year_level' => $user->year_level,
            'student_type1' => $user->student_type1,
            'student_type2' => $user->student_type2,
            'student_type' => $user->student_type,
            'school_year' => $user->school_year,
            'semester' => $user->semester,
            'emergency_contact_name' => $user->emergency_contact_name,
            'emergency_contact_number' => $user->emergency_contact_number,
            'emergency_relation' => $user->emergency_relation,
            'parent_spouse_guardian' => $user->parent_spouse_guardian,
            'parent_spouse_guardian_address' => $user->parent_spouse_guardian_address,
            'elementary_school' => $user->elementary_school,
            'elementary_address' => $user->elementary_address,
            'elementary_year_graduated' => $user->elementary_year_graduated,
            'high_school' => $user->high_school,
            'high_school_address' => $user->high_school_address,
            'high_school_year_graduated' => $user->high_school_year_graduated,
            'college_name' => $user->college_name,
            'college_address' => $user->college_address,
            'college_course' => $user->college_course,
            'college_year' => $user->college_year,
            'form_137_presented' => $user->form_137_presented ?? false,
            'tor_presented' => $user->tor_presented ?? false,
            'good_moral_cert_presented' => $user->good_moral_cert_presented ?? false,
            'birth_cert_presented' => $user->birth_cert_presented ?? false,
            'marriage_cert_presented' => $user->marriage_cert_presented ?? false,
        ];
    }

    /**
     * Get user data from student record
     */
    private function getUserDataFromStudent(Student $student): array
    {
        return [
            'user_id' => $student->user_id ?? null,
            'first_name' => $student->first_name ?? '',
            'middle_name' => $student->middle_name ?? '',
            'last_name' => $student->last_name ?? '',
            'email' => $student->email,
            'gender' => $student->gender ?? 'other',
            'birth_date' => $student->birth_date,
            'age' => $student->age,
            'civil_status' => $student->civil_status,
            'maiden_name' => $student->maiden_name,
            'place_of_birth' => $student->place_of_birth,
            'contact_number' => $student->contact_number ?? '',
            'complete_home_address' => $student->complete_home_address,
            'department_id' => $student->department_id,
            'course_id' => $student->course_id,
            'organization_id' => $student->organization_id,
            'scholarship_id' => $student->scholarship_id,
            'year_level' => $student->year_level,
            'student_type1' => $student->student_type1,
            'student_type2' => $student->student_type2,
            'student_type' => $student->student_type,
            'school_year' => $student->school_year,
            'semester' => $student->semester,
            'emergency_contact_name' => $student->emergency_contact_name,
            'emergency_contact_number' => $student->emergency_contact_number,
            'emergency_relation' => $student->emergency_relation,
            'parent_spouse_guardian' => $student->parent_spouse_guardian,
            'parent_spouse_guardian_address' => $student->parent_spouse_guardian_address,
            'elementary_school' => $student->elementary_school,
            'elementary_address' => $student->elementary_address,
            'elementary_year_graduated' => $student->elementary_year_graduated,
            'high_school' => $student->high_school,
            'high_school_address' => $student->high_school_address,
            'high_school_year_graduated' => $student->high_school_year_graduated,
            'college_name' => $student->college_name,
            'college_address' => $student->college_address,
            'college_course' => $student->college_course,
            'college_year' => $student->college_year,
            'form_137_presented' => $student->form_137_presented ?? false,
            'tor_presented' => $student->tor_presented ?? false,
            'good_moral_cert_presented' => $student->good_moral_cert_presented ?? false,
            'birth_cert_presented' => $student->birth_cert_presented ?? false,
            'marriage_cert_presented' => $student->marriage_cert_presented ?? false,
        ];
    }

    /**
     * Get fields to sync from user to student
     */
    private function getSyncFieldsFromUser(User $user): array
    {
        return array_filter([
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'gender' => $user->gender,
            'birth_date' => $user->birth_date,
            'age' => $user->age,
            'civil_status' => $user->civil_status,
            'maiden_name' => $user->maiden_name,
            'place_of_birth' => $user->place_of_birth,
            'contact_number' => $user->contact_number,
            'complete_home_address' => $user->complete_home_address,
            'department_id' => $user->department_id,
            'course_id' => $user->course_id,
            'organization_id' => $user->organization_id,
            'scholarship_id' => $user->scholarship_id,
            'year_level' => $user->year_level,
            'student_type1' => $user->student_type1,
            'student_type2' => $user->student_type2,
            'student_type' => $user->student_type,
            'school_year' => $user->school_year,
            'semester' => $user->semester,
            'emergency_contact_name' => $user->emergency_contact_name,
            'emergency_contact_number' => $user->emergency_contact_number,
            'emergency_relation' => $user->emergency_relation,
            'parent_spouse_guardian' => $user->parent_spouse_guardian,
            'parent_spouse_guardian_address' => $user->parent_spouse_guardian_address,
            'elementary_school' => $user->elementary_school,
            'elementary_address' => $user->elementary_address,
            'elementary_year_graduated' => $user->elementary_year_graduated,
            'high_school' => $user->high_school,
            'high_school_address' => $user->high_school_address,
            'high_school_year_graduated' => $user->high_school_year_graduated,
            'college_name' => $user->college_name,
            'college_address' => $user->college_address,
            'college_course' => $user->college_course,
            'college_year' => $user->college_year,
            'form_137_presented' => $user->form_137_presented,
            'tor_presented' => $user->tor_presented,
            'good_moral_cert_presented' => $user->good_moral_cert_presented,
            'birth_cert_presented' => $user->birth_cert_presented,
            'marriage_cert_presented' => $user->marriage_cert_presented,
        ], function($value) {
            return $value !== null;
        });
    }

    /**
     * Get fields to sync from student to user
     */
    private function getSyncFieldsFromStudent(Student $student): array
    {
        return array_filter([
            'first_name' => $student->first_name,
            'middle_name' => $student->middle_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'gender' => $student->gender,
            'birth_date' => $student->birth_date,
            'age' => $student->age,
            'civil_status' => $student->civil_status,
            'maiden_name' => $student->maiden_name,
            'place_of_birth' => $student->place_of_birth,
            'contact_number' => $student->contact_number,
            'complete_home_address' => $student->complete_home_address,
            'department_id' => $student->department_id,
            'course_id' => $student->course_id,
            'organization_id' => $student->organization_id,
            'scholarship_id' => $student->scholarship_id,
            'year_level' => $student->year_level,
            'student_type1' => $student->student_type1,
            'student_type2' => $student->student_type2,
            'student_type' => $student->student_type,
            'school_year' => $student->school_year,
            'semester' => $student->semester,
            'emergency_contact_name' => $student->emergency_contact_name,
            'emergency_contact_number' => $student->emergency_contact_number,
            'emergency_relation' => $student->emergency_relation,
            'parent_spouse_guardian' => $student->parent_spouse_guardian,
            'parent_spouse_guardian_address' => $student->parent_spouse_guardian_address,
            'elementary_school' => $student->elementary_school,
            'elementary_address' => $student->elementary_address,
            'elementary_year_graduated' => $student->elementary_year_graduated,
            'high_school' => $student->high_school,
            'high_school_address' => $student->high_school_address,
            'high_school_year_graduated' => $student->high_school_year_graduated,
            'college_name' => $student->college_name,
            'college_address' => $student->college_address,
            'college_course' => $student->college_course,
            'college_year' => $student->college_year,
            'form_137_presented' => $student->form_137_presented,
            'tor_presented' => $student->tor_presented,
            'good_moral_cert_presented' => $student->good_moral_cert_presented,
            'birth_cert_presented' => $student->birth_cert_presented,
            'marriage_cert_presented' => $student->marriage_cert_presented,
        ], function($value) {
            return $value !== null;
        });
    }
}
