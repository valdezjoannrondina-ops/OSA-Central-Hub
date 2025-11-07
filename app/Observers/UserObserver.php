<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Student;

class UserObserver
{
    private static $syncing = false;

    /**
     * Handle the User "created" event.
     * Sync role 1 users (students) to Student table.
     */
    public function created(User $user)
    {
        // Prevent infinite recursion
        if (self::$syncing) {
            return;
        }
        
        // Sync role 1 users (students) to Student table
        if ($user->role === 1) {
            $this->syncUserToStudent($user);
        }
    }

    /**
     * Handle the User "updated" event.
     * Sync email changes to Staff table when user email is updated (for staff users only).
     * Sync role 1 users (students) to Student table.
     */
    public function updated(User $user)
    {
        // Prevent infinite recursion
        if (self::$syncing) {
            return;
        }
        
        // Sync role 1 users (students) to Student table
        if ($user->role === 1) {
            $this->syncUserToStudent($user);
        }
        
        // Sync role 2 users (staff) to Staff table
        if ($user->role === 2) {
            // Check if email was changed
            if ($user->wasChanged('email')) {
                self::$syncing = true;
                
                try {
                    $oldEmail = $user->getOriginal('email');
                    $newEmail = $user->email;
                    
                    // Find staff by old email (case-insensitive)
                    $staff = Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])->first();
                    
                    // If not found by old email, try new email (case-insensitive)
                    if (!$staff) {
                        $staff = Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($newEmail))])->first();
                    }
                    
                    // If still not found, try by user_id
                    if (!$staff && $user->user_id) {
                        $staff = Staff::where('user_id', $user->user_id)->first();
                    }
                    
                    // Update staff email if found and different
                    if ($staff && strtolower(trim($staff->email)) !== strtolower(trim($newEmail))) {
                        $staff->email = $newEmail;
                        $staff->saveQuietly(); // Use saveQuietly to prevent observer recursion
                    }
                    
                    // Update ALL other user records with the old email to the new email
                    if ($oldEmail && strtolower(trim($newEmail)) !== strtolower(trim($oldEmail))) {
                        User::withoutEvents(function() use ($oldEmail, $newEmail, $user) {
                            User::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])
                                ->where('id', '!=', $user->id)
                                ->update(['email' => $newEmail]);
                        });
                    }
                    
                    // Update ALL staff records with the old email to the new email
                    if ($oldEmail && strtolower(trim($newEmail)) !== strtolower(trim($oldEmail))) {
                        Staff::withoutEvents(function() use ($oldEmail, $newEmail, $staff) {
                            Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])
                                ->where('id', '!=', $staff->id ?? 0)
                                ->update(['email' => $newEmail]);
                        });
                    }
                } finally {
                    self::$syncing = false;
                }
            }
        }
    }

    /**
     * Sync a User record (role = 1) to Student table
     * This ensures all students exist in both tables
     */
    private function syncUserToStudent(User $user)
    {
        if ($user->role !== 1) {
            return; // Only sync students
        }
        
        // Prevent infinite recursion
        if (self::$syncing) {
            return;
        }
        
        self::$syncing = true;
        
        try {
            // Check if Student record already exists
            $existingStudent = Student::where('user_id', $user->id)->first();
            
            // Use the email from the user record
            $email = $user->email ?? null;
            
            if ($existingStudent) {
                // Update existing Student record with User data
                $existingStudent->update([
                    'first_name' => $user->first_name ?? $existingStudent->first_name,
                    'middle_name' => $user->middle_name ?? $existingStudent->middle_name,
                    'last_name' => $user->last_name ?? $existingStudent->last_name,
                    'email' => $email,
                    'contact_number' => $user->contact_number ?? $existingStudent->contact_number,
                    'gender' => $user->gender ?? $existingStudent->gender,
                    'birth_date' => $user->birth_date ?? $existingStudent->birth_date,
                    'age' => $user->age ?? $existingStudent->age,
                    'civil_status' => $user->civil_status ?? $existingStudent->civil_status,
                    'maiden_name' => $user->maiden_name ?? $existingStudent->maiden_name,
                    'place_of_birth' => $user->place_of_birth ?? $existingStudent->place_of_birth,
                    'complete_home_address' => $user->complete_home_address ?? $existingStudent->complete_home_address,
                    'department_id' => $user->department_id ?? $existingStudent->department_id,
                    'course_id' => $user->course_id ?? $existingStudent->course_id,
                    'organization_id' => $user->organization_id ?? $existingStudent->organization_id,
                    'scholarship_id' => $user->scholarship_id ?? $existingStudent->scholarship_id,
                    'year_level' => $user->year_level ?? $existingStudent->year_level,
                    'student_type1' => $user->student_type1 ?? $existingStudent->student_type1,
                    'student_type2' => $user->student_type2 ?? $existingStudent->student_type2,
                    'student_type' => $user->student_type ?? $existingStudent->student_type,
                    'school_year' => $user->school_year ?? $existingStudent->school_year,
                    'semester' => $user->semester ?? $existingStudent->semester,
                    'emergency_contact_name' => $user->emergency_contact_name ?? $existingStudent->emergency_contact_name,
                    'emergency_contact_number' => $user->emergency_contact_number ?? $existingStudent->emergency_contact_number,
                    'emergency_relation' => $user->emergency_relation ?? $existingStudent->emergency_relation,
                    'parent_spouse_guardian' => $user->parent_spouse_guardian ?? $existingStudent->parent_spouse_guardian,
                    'parent_spouse_guardian_address' => $user->parent_spouse_guardian_address ?? $existingStudent->parent_spouse_guardian_address,
                    'elementary_school' => $user->elementary_school ?? $existingStudent->elementary_school,
                    'elementary_address' => $user->elementary_address ?? $existingStudent->elementary_address,
                    'elementary_year_graduated' => $user->elementary_year_graduated ?? $existingStudent->elementary_year_graduated,
                    'high_school' => $user->high_school ?? $existingStudent->high_school,
                    'high_school_address' => $user->high_school_address ?? $existingStudent->high_school_address,
                    'high_school_year_graduated' => $user->high_school_year_graduated ?? $existingStudent->high_school_year_graduated,
                    'college_name' => $user->college_name ?? $existingStudent->college_name,
                    'college_address' => $user->college_address ?? $existingStudent->college_address,
                    'college_course' => $user->college_course ?? $existingStudent->college_course,
                    'college_year' => $user->college_year ?? $existingStudent->college_year,
                    'form_137_presented' => $user->form_137_presented ?? $existingStudent->form_137_presented,
                    'tor_presented' => $user->tor_presented ?? $existingStudent->tor_presented,
                    'good_moral_cert_presented' => $user->good_moral_cert_presented ?? $existingStudent->good_moral_cert_presented,
                    'birth_cert_presented' => $user->birth_cert_presented ?? $existingStudent->birth_cert_presented,
                    'marriage_cert_presented' => $user->marriage_cert_presented ?? $existingStudent->marriage_cert_presented,
                    'personal_data_sheet_image' => $user->image ?? $existingStudent->personal_data_sheet_image,
                ]);
            } else {
                // Create new Student record from User data
                Student::create([
                    'user_id' => $user->id,
                    'first_name' => $user->first_name ?? '',
                    'middle_name' => $user->middle_name ?? '',
                    'last_name' => $user->last_name ?? '',
                    'email' => $email,
                    'contact_number' => $user->contact_number ?? '',
                    'gender' => $user->gender ?? 'other',
                    'birth_date' => $user->birth_date ?? null,
                    'age' => $user->age ?? null,
                    'civil_status' => $user->civil_status ?? null,
                    'maiden_name' => $user->maiden_name ?? null,
                    'place_of_birth' => $user->place_of_birth ?? null,
                    'complete_home_address' => $user->complete_home_address ?? null,
                    'department_id' => $user->department_id ?? null,
                    'course_id' => $user->course_id ?? null,
                    'organization_id' => $user->organization_id ?? null,
                    'scholarship_id' => $user->scholarship_id ?? null,
                    'year_level' => $user->year_level ?? null,
                    'student_type1' => $user->student_type1 ?? null,
                    'student_type2' => $user->student_type2 ?? null,
                    'student_type' => $user->student_type ?? null,
                    'school_year' => $user->school_year ?? null,
                    'semester' => $user->semester ?? null,
                    'emergency_contact_name' => $user->emergency_contact_name ?? null,
                    'emergency_contact_number' => $user->emergency_contact_number ?? null,
                    'emergency_relation' => $user->emergency_relation ?? null,
                    'parent_spouse_guardian' => $user->parent_spouse_guardian ?? null,
                    'parent_spouse_guardian_address' => $user->parent_spouse_guardian_address ?? null,
                    'elementary_school' => $user->elementary_school ?? null,
                    'elementary_address' => $user->elementary_address ?? null,
                    'elementary_year_graduated' => $user->elementary_year_graduated ?? null,
                    'high_school' => $user->high_school ?? null,
                    'high_school_address' => $user->high_school_address ?? null,
                    'high_school_year_graduated' => $user->high_school_year_graduated ?? null,
                    'college_name' => $user->college_name ?? null,
                    'college_address' => $user->college_address ?? null,
                    'college_course' => $user->college_course ?? null,
                    'college_year' => $user->college_year ?? null,
                    'form_137_presented' => $user->form_137_presented ?? false,
                    'tor_presented' => $user->tor_presented ?? false,
                    'good_moral_cert_presented' => $user->good_moral_cert_presented ?? false,
                    'birth_cert_presented' => $user->birth_cert_presented ?? false,
                    'marriage_cert_presented' => $user->marriage_cert_presented ?? false,
                    'personal_data_sheet_image' => $user->image ?? null,
                ]);
            }
        } finally {
            self::$syncing = false;
        }
    }
}

