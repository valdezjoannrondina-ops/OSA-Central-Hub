<?php
namespace App\Http\Controllers\Admin;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Helpers\EmailHelper;

class StudentController extends Controller
{
    public function index()
    {
        // Fetch students with all relationships, including user data
        $students = \App\Models\Student::with(['user', 'department', 'course', 'organization', 'scholarship'])
            ->get()
            ->map(function($student) {
                // Merge user data into student for easier access
                if ($student->user) {
                    $student->user_id_display = $student->user->user_id;
                    // Sync any missing data from user to student for display
                    if (!$student->email && $student->user->email) {
                        $student->email = $student->user->email;
                    }
                    if (!$student->contact_number && $student->user->contact_number) {
                        $student->contact_number = $student->user->contact_number;
                    }
                }
                return $student;
            })
            ->sortBy(function($student) {
                // Sort alphabetically by last name, then first name (case-insensitive)
                $lastName = strtolower($student->last_name ?? $student->user->last_name ?? '');
                $firstName = strtolower($student->first_name ?? $student->user->first_name ?? '');
                return $lastName . ' ' . $firstName;
            })
            ->values();
        $departments = \App\Models\Department::all();
        $courses = \App\Models\Course::all();
        $organizations = \App\Models\Organization::all();
        $scholarships = \App\Models\Scholarship::all();
        return view('admin.staff.dashboard.AdmissionServicesOfficer.student-management', compact('students', 'departments', 'courses', 'organizations', 'scholarships'));
    }

    public function store(Request $request)
    {
        // Check for duplicates before validation
        $existingUserByUserId = \App\Models\User::where('user_id', $request->user_id)->first();
        $existingUserByEmail = \App\Models\User::where('email', $request->email)->first();
        
        if ($existingUserByUserId || $existingUserByEmail) {
            // Find the student associated with the duplicate user
            $duplicateUser = $existingUserByUserId ?? $existingUserByEmail;
            $duplicateStudent = Student::where('user_id', $duplicateUser->id)->with(['department', 'course', 'organization', 'scholarship'])->first();
            
            if ($duplicateStudent) {
                return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')
                    ->withInput()
                    ->with('duplicate_student_id', $duplicateStudent->id)
                    ->with('duplicate_message', 'A student with this Student ID or Email has already been added. Do you want to see the student\'s details?');
            } else {
                return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')
                    ->withInput()
                    ->with('error', 'A user with this Student ID or Email already exists, but no associated student record was found.');
            }
        }
        
        $validated = $request->validate([
            // A. NAME
            'user_id' => 'required|unique:users,user_id',
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable',
            'ext_name' => 'nullable|string|max:50',
            // B. HOME ADDRESS
            'street' => 'nullable|string',
            'barangay' => 'nullable|string',
            'city_municipality' => 'nullable|string',
            'province' => 'nullable|string',
            'zip_code' => 'nullable|string|max:20',
            // C. PERSONAL DETAILS
            'age' => 'required|integer|min:1|max:100',
            'birth_date' => 'required|date',
            'place_of_birth' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'civil_status' => 'required|in:single,married,divorced,widowed',
            'nationality' => 'nullable|string',
            // D. Other
            'religion' => 'nullable|string',
            'contact_number' => 'required|string',
            'tel_no' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users',
            'spouse_name' => 'nullable|string',
            'spouse_contact_no' => 'nullable|string|max:50',
            // E. SPECIAL SKILLS AND TALENTS
            'sport' => 'nullable|string',
            'arts' => 'nullable|string',
            'technical' => 'nullable|string',
            // F. EDUCATION BACKGROUND
            'junior_high_school_name' => 'nullable|string',
            'junior_high_school_year_completed' => 'nullable|string|max:20',
            'junior_high_school_address' => 'nullable|string',
            'junior_high_school_honors_awards' => 'nullable|string',
            'senior_high_school_name' => 'nullable|string',
            'senior_high_school_year_graduated' => 'nullable|string|max:20',
            'senior_high_school_track_strand' => 'nullable|string',
            'senior_high_school_lrn' => 'nullable|string|max:50',
            'senior_high_school_address' => 'nullable|string',
            'senior_high_school_honors_awards' => 'nullable|string',
            'last_school_attended' => 'nullable|string',
            'last_school_course' => 'nullable|string',
            'last_school_address' => 'nullable|string',
            'last_school_year_attended' => 'nullable|string|max:20',
            // G. FAMILY BACKGROUND
            'father_name' => 'nullable|string',
            'father_contact_number' => 'nullable|string|max:50',
            'father_occupation' => 'nullable|string',
            'father_workplace' => 'nullable|string',
            'father_monthly_income' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'mother_contact_number' => 'nullable|string|max:50',
            'mother_occupation' => 'nullable|string',
            'mother_workplace' => 'nullable|string',
            'mother_monthly_income' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_relationship' => 'nullable|string',
            'guardian_contact_number' => 'nullable|string|max:50',
            'guardian_occupation' => 'nullable|string',
            'guardian_workplace' => 'nullable|string',
            'guardian_monthly_income' => 'nullable|string',
            // H. OTHER INFORMATION
            'is_active_scholar' => 'nullable|boolean',
            'scholarship_grant_name' => 'nullable|string',
            'is_indigenous_group_member' => 'nullable|boolean',
            'indigenous_group_specify' => 'nullable|string',
            'is_pwd' => 'nullable|boolean',
            'pwd_id_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:102400', // 100MB max
            'is_government_member' => 'nullable|in:no,yes',
            'government_level' => 'nullable|in:barangay,municipal_city,provincial',
            'government_role_position' => 'nullable|string',
            'living_arrangement' => 'nullable|in:home,boarding_house,relatives,working_student,others',
            'living_arrangement_others_specify' => 'nullable|string',
            'is_single_parent' => 'nullable|boolean',
            'fraternity_sorority_name' => 'nullable|string',
            'fraternity_sorority_position' => 'nullable|string',
            'has_criminal_record' => 'nullable|boolean',
            // Legacy fields
            'complete_home_address' => 'nullable|string',
            'maiden_name' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string',
            'emergency_relation' => 'nullable|string',
            'parent_spouse_guardian' => 'nullable|string',
            'parent_spouse_guardian_address' => 'nullable|string',
            'personal_data_sheet_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'elementary_school' => 'nullable|string',
            'elementary_address' => 'nullable|string',
            'elementary_year_graduated' => 'nullable|string',
            'high_school' => 'nullable|string',
            'high_school_address' => 'nullable|string',
            'high_school_year_graduated' => 'nullable|string',
            'college_name' => 'nullable|string',
            'college_address' => 'nullable|string',
            'college_course' => 'nullable|string',
            'college_year' => 'nullable|string',
            // Academic information
            'school_year' => 'nullable|string',
            'semester' => 'nullable|string',
            'student_type' => 'nullable|in:new,old',
            'department_id' => 'required|integer',
            'course_id' => 'required|integer',
            'organization_id' => 'nullable|integer',
            'scholarship_id' => 'nullable|integer',
            'year_level' => 'required|integer',
            'student_type1' => 'required|in:regular,irregular,transferee',
            'student_type2' => 'required|in:paying,scholar',
            // Entrance credentials
            'form_137_presented' => 'nullable|boolean',
            'tor_presented' => 'nullable|boolean',
            'good_moral_cert_presented' => 'nullable|boolean',
            'birth_cert_presented' => 'nullable|boolean',
            'marriage_cert_presented' => 'nullable|boolean',
        ]);

        // Use the email provided in the form
        $email = $validated['email'] ?? null;

        // Generate username and temp password
        $username = $email; // Username is the email address
        // Generate temporary password: last_name@user_id (lowercase), e.g., datu@2023304529
        $tempPassword = strtolower($validated['last_name']) . '@' . $validated['user_id'];

        // Guarantee only valid enum values for gender
        $genderMap = [
            'M' => 'male', 'F' => 'female', 'O' => 'other',
            'm' => 'male', 'f' => 'female', 'o' => 'other',
            'male' => 'male', 'female' => 'female', 'other' => 'other'
        ];
        $genderValue = $validated['gender'] ?? null;
        $gender = $genderMap[$genderValue] ?? 'other';

        // Automatically assign department-related organization
        // Find organization with matching department_id
        $departmentOrganization = \App\Models\Organization::where('department_id', $validated['department_id'])->first();
        $organizationId = null;
        
        if ($departmentOrganization) {
            // Automatically assign department-related organization
            $organizationId = $departmentOrganization->id;
        } else {
            // If no department-related organization exists, use manually selected one (for non-academic)
            $organizationId = $validated['organization_id'] ?? null;
        }

        try {
            // Handle PWD ID image upload if provided
            $pwdIdImagePath = null;
            if ($request->hasFile('pwd_id_image')) {
                $pwdImage = $request->file('pwd_id_image');
                $pwdImageName = 'pwd_id_' . $validated['user_id'] . '_' . time() . '.' . $pwdImage->getClientOriginalExtension();
                $pwdIdImagePath = $pwdImage->storeAs('students/pwd_ids', $pwdImageName, 'public');
            }

            // Create user without triggering observer (we'll manually create student record)
            $user = \App\Models\User::withoutEvents(function() use ($validated, $email, $gender, $organizationId, $tempPassword, $pwdIdImagePath) {
                return \App\Models\User::create([
            'user_id' => $validated['user_id'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? '',
            'last_name' => $validated['last_name'],
            'ext_name' => $validated['ext_name'] ?? null,
            'email' => $email,
            'gender' => $gender,
            'birth_date' => $validated['birth_date'] ?? null,
            'age' => $validated['age'] ?? null,
            'civil_status' => $validated['civil_status'] ?? null,
            'maiden_name' => $validated['maiden_name'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'contact_number' => $validated['contact_number'] ?? '',
            'tel_no' => $validated['tel_no'] ?? null,
            'spouse_name' => $validated['spouse_name'] ?? null,
            'spouse_contact_no' => $validated['spouse_contact_no'] ?? null,
            'sport' => $validated['sport'] ?? null,
            'arts' => $validated['arts'] ?? null,
            'technical' => $validated['technical'] ?? null,
            'street' => $validated['street'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
            'city_municipality' => $validated['city_municipality'] ?? null,
            'province' => $validated['province'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
            'complete_home_address' => $validated['complete_home_address'] ?? null,
            'junior_high_school_name' => $validated['junior_high_school_name'] ?? null,
            'junior_high_school_year_completed' => $validated['junior_high_school_year_completed'] ?? null,
            'junior_high_school_address' => $validated['junior_high_school_address'] ?? null,
            'junior_high_school_honors_awards' => $validated['junior_high_school_honors_awards'] ?? null,
            'senior_high_school_name' => $validated['senior_high_school_name'] ?? null,
            'senior_high_school_year_graduated' => $validated['senior_high_school_year_graduated'] ?? null,
            'senior_high_school_track_strand' => $validated['senior_high_school_track_strand'] ?? null,
            'senior_high_school_lrn' => $validated['senior_high_school_lrn'] ?? null,
            'senior_high_school_address' => $validated['senior_high_school_address'] ?? null,
            'senior_high_school_honors_awards' => $validated['senior_high_school_honors_awards'] ?? null,
            'last_school_attended' => $validated['last_school_attended'] ?? null,
            'last_school_course' => $validated['last_school_course'] ?? null,
            'last_school_address' => $validated['last_school_address'] ?? null,
            'last_school_year_attended' => $validated['last_school_year_attended'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'father_contact_number' => $validated['father_contact_number'] ?? null,
            'father_occupation' => $validated['father_occupation'] ?? null,
            'father_workplace' => $validated['father_workplace'] ?? null,
            'father_monthly_income' => $validated['father_monthly_income'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'mother_contact_number' => $validated['mother_contact_number'] ?? null,
            'mother_occupation' => $validated['mother_occupation'] ?? null,
            'mother_workplace' => $validated['mother_workplace'] ?? null,
            'mother_monthly_income' => $validated['mother_monthly_income'] ?? null,
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'guardian_contact_number' => $validated['guardian_contact_number'] ?? null,
            'guardian_occupation' => $validated['guardian_occupation'] ?? null,
            'guardian_workplace' => $validated['guardian_workplace'] ?? null,
            'guardian_monthly_income' => $validated['guardian_monthly_income'] ?? null,
            'is_active_scholar' => isset($validated['is_active_scholar']) ? (bool)$validated['is_active_scholar'] : false,
            'scholarship_grant_name' => $validated['scholarship_grant_name'] ?? null,
            'is_indigenous_group_member' => isset($validated['is_indigenous_group_member']) ? (bool)$validated['is_indigenous_group_member'] : false,
            'indigenous_group_specify' => $validated['indigenous_group_specify'] ?? null,
            'is_pwd' => isset($validated['is_pwd']) ? (bool)$validated['is_pwd'] : false,
            'pwd_id_image' => $pwdIdImagePath,
            'is_government_member' => $validated['is_government_member'] ?? null,
            'government_level' => $validated['government_level'] ?? null,
            'government_role_position' => $validated['government_role_position'] ?? null,
            'living_arrangement' => $validated['living_arrangement'] ?? null,
            'living_arrangement_others_specify' => $validated['living_arrangement_others_specify'] ?? null,
            'is_single_parent' => isset($validated['is_single_parent']) ? (bool)$validated['is_single_parent'] : false,
            'fraternity_sorority_name' => $validated['fraternity_sorority_name'] ?? null,
            'fraternity_sorority_position' => $validated['fraternity_sorority_position'] ?? null,
            'has_criminal_record' => isset($validated['has_criminal_record']) ? (bool)$validated['has_criminal_record'] : false,
            'department_id' => $validated['department_id'],
            'course_id' => $validated['course_id'],
            'organization_id' => $organizationId,
            'scholarship_id' => $validated['scholarship_id'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'student_type1' => $validated['student_type1'] ?? null,
            'student_type2' => $validated['student_type2'] ?? null,
            'student_type' => $validated['student_type'] ?? null,
            'school_year' => $validated['school_year'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
            'emergency_relation' => $validated['emergency_relation'] ?? null,
            'parent_spouse_guardian' => $validated['parent_spouse_guardian'] ?? null,
            'parent_spouse_guardian_address' => $validated['parent_spouse_guardian_address'] ?? null,
            'elementary_school' => $validated['elementary_school'] ?? null,
            'elementary_address' => $validated['elementary_address'] ?? null,
            'elementary_year_graduated' => $validated['elementary_year_graduated'] ?? null,
            'high_school' => $validated['high_school'] ?? null,
            'high_school_address' => $validated['high_school_address'] ?? null,
            'high_school_year_graduated' => $validated['high_school_year_graduated'] ?? null,
            'college_name' => $validated['college_name'] ?? null,
            'college_address' => $validated['college_address'] ?? null,
            'college_course' => $validated['college_course'] ?? null,
            'college_year' => $validated['college_year'] ?? null,
            'form_137_presented' => isset($validated['form_137_presented']) ? (bool)$validated['form_137_presented'] : false,
            'tor_presented' => isset($validated['tor_presented']) ? (bool)$validated['tor_presented'] : false,
            'good_moral_cert_presented' => isset($validated['good_moral_cert_presented']) ? (bool)$validated['good_moral_cert_presented'] : false,
            'birth_cert_presented' => isset($validated['birth_cert_presented']) ? (bool)$validated['birth_cert_presented'] : false,
            'marriage_cert_presented' => isset($validated['marriage_cert_presented']) ? (bool)$validated['marriage_cert_presented'] : false,
            'role' => 1,
            'password' => bcrypt($tempPassword),
                ]);
            });

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('personal_data_sheet_image')) {
            $image = $request->file('personal_data_sheet_image');
            $imageName = 'personal_data_sheet_' . $validated['user_id'] . '_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('students/personal_data_sheets', $imageName, 'public');
        }

        // Create Student record
        $student = Student::create([
            'user_id' => $user->id, // foreign key to users.id
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? '',
            'last_name' => $validated['last_name'],
            'ext_name' => $validated['ext_name'] ?? null,
            'email' => $email,
            'gender' => $gender,
            'birth_date' => $validated['birth_date'] ?? null,
            'age' => $validated['age'] ?? null,
            'civil_status' => $validated['civil_status'] ?? null,
            'maiden_name' => $validated['maiden_name'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'contact_number' => $validated['contact_number'] ?? '',
            'tel_no' => $validated['tel_no'] ?? null,
            'spouse_name' => $validated['spouse_name'] ?? null,
            'spouse_contact_no' => $validated['spouse_contact_no'] ?? null,
            'sport' => $validated['sport'] ?? null,
            'arts' => $validated['arts'] ?? null,
            'technical' => $validated['technical'] ?? null,
            'street' => $validated['street'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
            'city_municipality' => $validated['city_municipality'] ?? null,
            'province' => $validated['province'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
            'complete_home_address' => $validated['complete_home_address'] ?? null,
            'junior_high_school_name' => $validated['junior_high_school_name'] ?? null,
            'junior_high_school_year_completed' => $validated['junior_high_school_year_completed'] ?? null,
            'junior_high_school_address' => $validated['junior_high_school_address'] ?? null,
            'junior_high_school_honors_awards' => $validated['junior_high_school_honors_awards'] ?? null,
            'senior_high_school_name' => $validated['senior_high_school_name'] ?? null,
            'senior_high_school_year_graduated' => $validated['senior_high_school_year_graduated'] ?? null,
            'senior_high_school_track_strand' => $validated['senior_high_school_track_strand'] ?? null,
            'senior_high_school_lrn' => $validated['senior_high_school_lrn'] ?? null,
            'senior_high_school_address' => $validated['senior_high_school_address'] ?? null,
            'senior_high_school_honors_awards' => $validated['senior_high_school_honors_awards'] ?? null,
            'last_school_attended' => $validated['last_school_attended'] ?? null,
            'last_school_course' => $validated['last_school_course'] ?? null,
            'last_school_address' => $validated['last_school_address'] ?? null,
            'last_school_year_attended' => $validated['last_school_year_attended'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'father_contact_number' => $validated['father_contact_number'] ?? null,
            'father_occupation' => $validated['father_occupation'] ?? null,
            'father_workplace' => $validated['father_workplace'] ?? null,
            'father_monthly_income' => $validated['father_monthly_income'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'mother_contact_number' => $validated['mother_contact_number'] ?? null,
            'mother_occupation' => $validated['mother_occupation'] ?? null,
            'mother_workplace' => $validated['mother_workplace'] ?? null,
            'mother_monthly_income' => $validated['mother_monthly_income'] ?? null,
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'guardian_contact_number' => $validated['guardian_contact_number'] ?? null,
            'guardian_occupation' => $validated['guardian_occupation'] ?? null,
            'guardian_workplace' => $validated['guardian_workplace'] ?? null,
            'guardian_monthly_income' => $validated['guardian_monthly_income'] ?? null,
            'is_active_scholar' => isset($validated['is_active_scholar']) ? (bool)$validated['is_active_scholar'] : false,
            'scholarship_grant_name' => $validated['scholarship_grant_name'] ?? null,
            'is_indigenous_group_member' => isset($validated['is_indigenous_group_member']) ? (bool)$validated['is_indigenous_group_member'] : false,
            'indigenous_group_specify' => $validated['indigenous_group_specify'] ?? null,
            'is_pwd' => isset($validated['is_pwd']) ? (bool)$validated['is_pwd'] : false,
            'pwd_id_image' => $pwdIdImagePath,
            'is_government_member' => $validated['is_government_member'] ?? null,
            'government_level' => $validated['government_level'] ?? null,
            'government_role_position' => $validated['government_role_position'] ?? null,
            'living_arrangement' => $validated['living_arrangement'] ?? null,
            'living_arrangement_others_specify' => $validated['living_arrangement_others_specify'] ?? null,
            'is_single_parent' => isset($validated['is_single_parent']) ? (bool)$validated['is_single_parent'] : false,
            'fraternity_sorority_name' => $validated['fraternity_sorority_name'] ?? null,
            'fraternity_sorority_position' => $validated['fraternity_sorority_position'] ?? null,
            'has_criminal_record' => isset($validated['has_criminal_record']) ? (bool)$validated['has_criminal_record'] : false,
            'department_id' => $validated['department_id'],
            'course_id' => $validated['course_id'],
            'organization_id' => $organizationId,
            'scholarship_id' => $validated['scholarship_id'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'student_type1' => $validated['student_type1'] ?? null,
            'student_type2' => $validated['student_type2'] ?? null,
            'student_type' => $validated['student_type'] ?? null,
            'school_year' => $validated['school_year'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
            'emergency_relation' => $validated['emergency_relation'] ?? null,
            'parent_spouse_guardian' => $validated['parent_spouse_guardian'] ?? null,
            'parent_spouse_guardian_address' => $validated['parent_spouse_guardian_address'] ?? null,
            'elementary_school' => $validated['elementary_school'] ?? null,
            'elementary_address' => $validated['elementary_address'] ?? null,
            'elementary_year_graduated' => $validated['elementary_year_graduated'] ?? null,
            'high_school' => $validated['high_school'] ?? null,
            'high_school_address' => $validated['high_school_address'] ?? null,
            'high_school_year_graduated' => $validated['high_school_year_graduated'] ?? null,
            'college_name' => $validated['college_name'] ?? null,
            'college_address' => $validated['college_address'] ?? null,
            'college_course' => $validated['college_course'] ?? null,
            'college_year' => $validated['college_year'] ?? null,
            'form_137_presented' => isset($validated['form_137_presented']) ? (bool)$validated['form_137_presented'] : false,
            'tor_presented' => isset($validated['tor_presented']) ? (bool)$validated['tor_presented'] : false,
            'good_moral_cert_presented' => isset($validated['good_moral_cert_presented']) ? (bool)$validated['good_moral_cert_presented'] : false,
            'birth_cert_presented' => isset($validated['birth_cert_presented']) ? (bool)$validated['birth_cert_presented'] : false,
            'marriage_cert_presented' => isset($validated['marriage_cert_presented']) ? (bool)$validated['marriage_cert_presented'] : false,
            'personal_data_sheet_image' => $imagePath,
        ]);

            // Send email notification
            try {
                Mail::raw(
                    "Welcome to OSA Hub!\n\nYour account has been created.\n\nUser Name: $username\nTemporary Password: $tempPassword\n\nPlease log in and change your password.",
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('OSA Hub Student Account Created');
                    }
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to send email notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue even if email fails
            }

            \Illuminate\Support\Facades\Log::info('Student created successfully', [
                'student_id' => $student->id,
                'user_id' => $user->id,
                'created_by' => auth()->id(),
                'timestamp' => now(),
            ]);

            return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')->with('success', 'Student added and notified successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create student', [
                'user_id' => $request->user_id ?? null,
                'email' => $request->email ?? null,
                'error' => $e->getMessage(),
                'created_by' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create student. Please try again or contact support if the problem persists.');
        }
    }

    // Show edit form for a student
    public function edit($student)
    {
        $student = Student::with(['user', 'department', 'course', 'organization', 'scholarship'])->findOrFail($student);
        $departments = \App\Models\Department::all();
        $courses = \App\Models\Course::all();
        $organizations = \App\Models\Organization::all();
        $scholarships = \App\Models\Scholarship::all();
        return view('admin.staff.edit-student', compact('student', 'departments', 'courses', 'organizations', 'scholarships'));
    }

    // Update student details
    public function update(Request $request, $student)
    {
        $student = Student::with(['user', 'department', 'course', 'organization', 'scholarship'])->findOrFail($student);
        $user = $student->user;
        
        if (!$user) {
            return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')
                ->with('error', 'Associated user record not found.');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|unique:users,user_id,' . $user->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'nullable',
            // Email validation - required and must be unique (except for current user)
            'email' => 'required|email|unique:users,email,' . $user->id,
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:100',
            'civil_status' => 'nullable|in:single,married,divorced,widowed',
            'maiden_name' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'place_of_birth' => 'nullable|string',
            'complete_home_address' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'parent_spouse_guardian' => 'nullable|string',
            'parent_spouse_guardian_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string',
            'emergency_relation' => 'nullable|string',
            'personal_data_sheet_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'elementary_school' => 'nullable|string',
            'elementary_address' => 'nullable|string',
            'elementary_year_graduated' => 'nullable|string',
            'high_school' => 'nullable|string',
            'high_school_address' => 'nullable|string',
            'high_school_year_graduated' => 'nullable|string',
            'college_name' => 'nullable|string',
            'college_address' => 'nullable|string',
            'college_course' => 'nullable|string',
            'college_year' => 'nullable|string',
            'school_year' => 'nullable|string',
            'semester' => 'nullable|string',
            'student_type' => 'nullable|in:new,old',
            'department_id' => 'required|integer',
            'course_id' => 'required|integer',
            'organization_id' => 'nullable|integer',
            'scholarship_id' => 'nullable|integer',
            'year_level' => 'nullable|integer',
            'student_type1' => 'nullable|in:regular,irregular,transferee',
            'student_type2' => 'nullable|in:paying,scholar',
            'form_137_presented' => 'nullable|boolean',
            'tor_presented' => 'nullable|boolean',
            'good_moral_cert_presented' => 'nullable|boolean',
            'birth_cert_presented' => 'nullable|boolean',
            'marriage_cert_presented' => 'nullable|boolean',
        ]);

        // Use the email provided in the form
        $email = $validated['email'] ?? $user->email;
        
        // Check if email has changed
        $emailChanged = strtolower(trim($user->email)) !== strtolower(trim($email));
        
        // Check if name or user_id changed (which would change the email)
        $nameOrIdChanged = (
            strtolower(trim($user->first_name)) !== strtolower(trim($validated['first_name'])) ||
            strtolower(trim($user->last_name)) !== strtolower(trim($validated['last_name'])) ||
            $user->user_id !== $validated['user_id']
        );
        
        // Check if resend verification is requested
        $resendVerification = $request->has('resend_verification') && $request->input('resend_verification') == '1';
        
        // Check if department has changed
        $departmentChanged = $user->department_id != $validated['department_id'];
        
        // Get current organization
        $currentOrganization = $user->organization;
        $isDepartmentRelatedOrg = $currentOrganization && $currentOrganization->department_id !== null;
        
        // Handle organization assignment
        $organizationId = null;
        
        if ($departmentChanged) {
            // Department changed - find new department-related organization
            $newDepartmentOrganization = \App\Models\Organization::where('department_id', $validated['department_id'])->first();
            if ($newDepartmentOrganization) {
                $organizationId = $newDepartmentOrganization->id;
            } else {
                // No department-related organization for new department, allow manual selection (for non-academic)
                $organizationId = $validated['organization_id'] ?? null;
            }
        } else {
            // Department hasn't changed
            if ($isDepartmentRelatedOrg) {
                // Current organization is department-related - keep it (cannot be manually edited)
                $organizationId = $user->organization_id;
            } else {
                // Current organization is not department-related - allow manual editing
                $organizationId = $validated['organization_id'] ?? null;
            }
        }
        
        // Generate temporary password if email changed OR resend verification is requested
        $tempPassword = null;
        if ($emailChanged || $resendVerification || $nameOrIdChanged) {
            // Generate temporary password: last_name@user_id (lowercase), e.g., datu@2023304529
            $tempPassword = strtolower($validated['last_name']) . '@' . $validated['user_id'];
        }
        
        // Generate username (email address)
        $username = $email;

        // Guarantee only valid enum values for gender
        $genderMap = [
            'M' => 'male', 'F' => 'female', 'O' => 'other',
            'm' => 'male', 'f' => 'female', 'o' => 'other',
            'male' => 'male', 'female' => 'female', 'other' => 'other'
        ];
        $genderValue = $validated['gender'] ?? null;
        $gender = $genderMap[$genderValue] ?? 'other';

        // Handle image upload if provided
        $imagePath = $student->personal_data_sheet_image; // Keep existing image if not updated
        if ($request->hasFile('personal_data_sheet_image')) {
            // Delete old image if it exists
            if ($student->personal_data_sheet_image && Storage::disk('public')->exists($student->personal_data_sheet_image)) {
                Storage::disk('public')->delete($student->personal_data_sheet_image);
            }
            
            $image = $request->file('personal_data_sheet_image');
            $imageName = 'personal_data_sheet_' . $validated['user_id'] . '_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('students/personal_data_sheets', $imageName, 'public');
        }

        try {
            // Prepare user update data
            $userUpdateData = [
            'user_id' => $validated['user_id'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? '',
            'last_name' => $validated['last_name'],
                'email' => $email,
            'gender' => $gender,
            'birth_date' => $validated['birth_date'] ?? null,
            'age' => $validated['age'] ?? null,
            'civil_status' => $validated['civil_status'] ?? null,
            'maiden_name' => $validated['maiden_name'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'contact_number' => $validated['contact_number'] ?? '',
            'complete_home_address' => $validated['complete_home_address'] ?? null,
            'department_id' => $validated['department_id'],
            'course_id' => $validated['course_id'],
                'organization_id' => $organizationId,
            'scholarship_id' => $validated['scholarship_id'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'student_type1' => $validated['student_type1'] ?? null,
            'student_type2' => $validated['student_type2'] ?? null,
            'student_type' => $validated['student_type'] ?? null,
            'school_year' => $validated['school_year'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
            'emergency_relation' => $validated['emergency_relation'] ?? null,
            'parent_spouse_guardian' => $validated['parent_spouse_guardian'] ?? null,
            'parent_spouse_guardian_address' => $validated['parent_spouse_guardian_address'] ?? null,
            'elementary_school' => $validated['elementary_school'] ?? null,
            'elementary_address' => $validated['elementary_address'] ?? null,
            'elementary_year_graduated' => $validated['elementary_year_graduated'] ?? null,
            'high_school' => $validated['high_school'] ?? null,
            'high_school_address' => $validated['high_school_address'] ?? null,
            'high_school_year_graduated' => $validated['high_school_year_graduated'] ?? null,
            'college_name' => $validated['college_name'] ?? null,
            'college_address' => $validated['college_address'] ?? null,
            'college_course' => $validated['college_course'] ?? null,
            'college_year' => $validated['college_year'] ?? null,
            'form_137_presented' => isset($validated['form_137_presented']) ? (bool)$validated['form_137_presented'] : false,
            'tor_presented' => isset($validated['tor_presented']) ? (bool)$validated['tor_presented'] : false,
            'good_moral_cert_presented' => isset($validated['good_moral_cert_presented']) ? (bool)$validated['good_moral_cert_presented'] : false,
            'birth_cert_presented' => isset($validated['birth_cert_presented']) ? (bool)$validated['birth_cert_presented'] : false,
            'marriage_cert_presented' => isset($validated['marriage_cert_presented']) ? (bool)$validated['marriage_cert_presented'] : false,
            ];
            
            // Add password update if email changed OR resend verification is requested
            if (($emailChanged || $resendVerification) && $tempPassword) {
                $userUpdateData['password'] = bcrypt($tempPassword);
            }
            
            // Update User table without triggering observer (we'll update Student manually)
            \App\Models\User::withoutEvents(function() use ($user, $userUpdateData) {
                $user->update($userUpdateData);
            });

        // Update Student table
        $student->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? '',
            'last_name' => $validated['last_name'],
                'email' => $email,
            'gender' => $gender,
            'birth_date' => $validated['birth_date'] ?? null,
            'age' => $validated['age'] ?? null,
            'civil_status' => $validated['civil_status'] ?? null,
            'maiden_name' => $validated['maiden_name'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'contact_number' => $validated['contact_number'] ?? '',
            'complete_home_address' => $validated['complete_home_address'] ?? null,
            'department_id' => $validated['department_id'],
            'course_id' => $validated['course_id'],
            'organization_id' => $organizationId,
            'scholarship_id' => $validated['scholarship_id'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'student_type1' => $validated['student_type1'] ?? null,
            'student_type2' => $validated['student_type2'] ?? null,
            'student_type' => $validated['student_type'] ?? null,
            'school_year' => $validated['school_year'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
            'emergency_relation' => $validated['emergency_relation'] ?? null,
            'parent_spouse_guardian' => $validated['parent_spouse_guardian'] ?? null,
            'parent_spouse_guardian_address' => $validated['parent_spouse_guardian_address'] ?? null,
            'elementary_school' => $validated['elementary_school'] ?? null,
            'elementary_address' => $validated['elementary_address'] ?? null,
            'elementary_year_graduated' => $validated['elementary_year_graduated'] ?? null,
            'high_school' => $validated['high_school'] ?? null,
            'high_school_address' => $validated['high_school_address'] ?? null,
            'high_school_year_graduated' => $validated['high_school_year_graduated'] ?? null,
            'college_name' => $validated['college_name'] ?? null,
            'college_address' => $validated['college_address'] ?? null,
            'college_course' => $validated['college_course'] ?? null,
            'college_year' => $validated['college_year'] ?? null,
            'form_137_presented' => isset($validated['form_137_presented']) ? (bool)$validated['form_137_presented'] : false,
            'tor_presented' => isset($validated['tor_presented']) ? (bool)$validated['tor_presented'] : false,
            'good_moral_cert_presented' => isset($validated['good_moral_cert_presented']) ? (bool)$validated['good_moral_cert_presented'] : false,
            'birth_cert_presented' => isset($validated['birth_cert_presented']) ? (bool)$validated['birth_cert_presented'] : false,
            'marriage_cert_presented' => isset($validated['marriage_cert_presented']) ? (bool)$validated['marriage_cert_presented'] : false,
            'personal_data_sheet_image' => $imagePath,
        ]);
            
            // Send email with temporary password if email changed OR resend verification is requested
            if (($emailChanged || $resendVerification) && $tempPassword) {
                try {
                    $emailSubject = $emailChanged 
                        ? 'OSA Hub - Email Updated - Temporary Password'
                        : 'OSA Hub - Verification Email - Temporary Password';
                    
                    $emailBody = $emailChanged
                        ? "Hello {$user->first_name},\n\nYour email address has been updated in OSA Hub.\n\nYour login credentials:\nUser Name: $username\nTemporary Password: $tempPassword\n\nPlease log in with these credentials and change your password for security.\n\nThis is a temporary password for email validation purposes."
                        : "Hello {$user->first_name},\n\nThis is a resend of your verification email for OSA Hub.\n\nYour login credentials:\nUser Name: $username\nTemporary Password: $tempPassword\n\nPlease log in with these credentials and change your password for security.\n\nThis is a temporary password for email validation purposes.";
                    
                    Mail::raw(
                        $emailBody,
                        function ($message) use ($user, $emailSubject) {
                            $message->to($user->email)
                                ->subject($emailSubject);
                        }
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to send email notification', [
                        'user_id' => $user->id,
                        'email_changed' => $emailChanged,
                        'resend_verification' => $resendVerification,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue even if email fails
                }
            }

            \Illuminate\Support\Facades\Log::info('Student updated successfully', [
                'student_id' => $student->id,
                'user_id' => $user->id,
                'updated_by' => auth()->id(),
                'timestamp' => now(),
            ]);

            $successMessage = 'Student updated successfully.';
            if ($resendVerification) {
                $successMessage .= ' Verification email with temporary password has been sent.';
            } elseif ($emailChanged) {
                $successMessage .= ' Verification email with temporary password has been sent to the new email address.';
            }
            
            return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update student', [
                'student_id' => $student->id,
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update student. Please try again or contact support if the problem persists.');
        }
    }

    // Delete a student
    public function destroy($student)
    {
        $student = Student::findOrFail($student);
        $student->delete();
    return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')->with('success', 'Student deleted successfully.');
    }

    // Show student details
    public function show($id)
    {
        $student = Student::with(['user', 'department', 'course', 'organization', 'scholarship'])->findOrFail($id);
        return view('admin.staff.dashboard.AdmissionServicesOfficer.student-details', compact('student'));
    }
}
