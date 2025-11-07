<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        // Registration is disabled
        abort(403, 'Student self-registration is disabled. Please contact the Admission Services Officer.');
    }

    /**
     * Handle account creation.
     */
    public function store(Request $request)
    {
        // Optional: Disable public registration
        // abort(403, 'Registration is closed.');

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Generate email in format: first_name.last_name+student_id@gmail.com
        $generatedEmail = \App\Helpers\EmailHelper::generateStudentEmail(
            $request->first_name,
            $request->last_name,
            $request->user_id
        );
        
        $user = User::create([
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $generatedEmail,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'department_id' => $request->department_id,
            'course_id' => $request->course_id,
            'organization_id' => $request->organization_id,
            'year_level' => $request->year_level,
            'student_type1' => $request->student_type1,
            'student_type2' => $request->student_type2,
            'scholarship_id' => $request->scholarship_id,
            'contact_number' => $request->contact_number,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_relation' => $request->emergency_relation,
            'password' => Hash::make($request->password),
            'role' => 1, // Default: student
        ]);

        // One-time QR code generation using user details
        $qrPayload = [
            'student_id' => $user->user_id,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'department' => optional($user->department)->name,
            'course' => optional($user->course)->name,
            'year_level' => $user->year_level,
            'generated_at' => now()->toIso8601String(),
        ];
        $qrData = json_encode($qrPayload);
        // Store PNG in public storage under qr-codes/{user_id}.png
    $svg = \QrCode::format('svg')->size(300)->generate($qrData);
    \Storage::disk('public')->put("qr-codes/{$user->user_id}.svg", $svg);

        // Automatically create Student record from User record
        $this->syncUserToStudent($user);

        // Log the user in automatically
        auth()->login($user);

        return redirect('/student/dashboard')->with('success', 'Account created successfully!');
    }
    
    /**
     * Sync a User record (role = 1) to Student table
     */
    private function syncUserToStudent(User $user)
    {
        if ($user->role != 1) {
            return; // Only sync students
        }
        
        // Check if Student record already exists
        $existingStudent = \App\Models\Student::where('user_id', $user->id)->first();
        
        if ($existingStudent) {
            // Update existing Student record with User data
            $existingStudent->update([
                'first_name' => $user->first_name ?? $existingStudent->first_name,
                'middle_name' => $user->middle_name ?? $existingStudent->middle_name,
                'last_name' => $user->last_name ?? $existingStudent->last_name,
                'email' => $user->email ?? $existingStudent->email,
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
            return $existingStudent;
        }
        
        // Create new Student record from User data
        $student = \App\Models\Student::create([
            'user_id' => $user->id,
            'first_name' => $user->first_name ?? '',
            'middle_name' => $user->middle_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $generatedEmail,
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
        
        return $student;
    }
}