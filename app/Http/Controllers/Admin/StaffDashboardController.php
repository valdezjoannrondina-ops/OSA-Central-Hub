<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Designation;
use App\Models\User;
use App\Models\Staff;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Use Staff records so we have designation and organization set from admin/add-staff
        $staff = Staff::with(['department','organization'])->get();
        $user = auth()->user();
        $currentUserDesignation = null;
        $currentUserStaffRecord = null;
        
        if ($user) {
            // Try to find the staff record for the current user by email (case-insensitive)
            $currentUserStaffRecord = Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($user->email))])
                ->with(['department','organization'])
                ->first();
            
            $currentUserDesignation = $user->designation
                ?? optional($user->staffProfile)->designation
                ?? ($currentUserStaffRecord ? $currentUserStaffRecord->designation : null);
        }
        $isAdmin = $user && (int) $user->role === 4;
        $isStaff = $user && (int) $user->role === 2;

        return view('admin.staff.dashboard', compact('staff', 'currentUserDesignation', 'currentUserStaffRecord', 'isAdmin', 'isStaff'));
    }

    public function showByDesignation(Request $request, string $designation)
    {
        $designationRecord = Designation::where('name', $designation)->first();
        if (!$designationRecord) {
            abort(404, 'Designation not found');
        }
        // Access control: staff (role=2) can only view their own designation
        $user = auth()->user();
        if ($user && (int) $user->role === 2) {
            // Try to find staff record by email (case-insensitive)
            $staffRecord = Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($user->email))])->first();
            
            $userDesignation = $user->designation
                ?? optional($user->staffProfile)->designation
                ?? ($staffRecord ? $staffRecord->designation : null);
            
            if (!$userDesignation || strcasecmp($userDesignation, $designation) !== 0) {
                $prev = url()->previous();
                if ($prev && $prev !== url()->current()) {
                    return redirect()->to($prev)->with('error', 'not allowed');
                }
                // Fallback to main staff dashboard
                return redirect()->route('admin.staff.dashboard')->with('error', 'not allowed');
            }
        }
        // If no worksheet in session, try to load from user's last_imported_worksheet
    if ($user && strcasecmp(str_replace(' ', '', $designation), 'AdmissionServicesOfficer') === 0 && !session('worksheetHtml')) {
            $lastFile = $user->last_imported_worksheet;
            if ($lastFile && file_exists(public_path($lastFile))) {
                $rows = [];
                $structured = [];
                $ext = pathinfo($lastFile, PATHINFO_EXTENSION);
                $fullPath = public_path($lastFile);
                try {
                    if ($ext === 'csv') {
                        $rows = array_map('str_getcsv', file($fullPath));
                    } else {
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
                        $sheet = $spreadsheet->getActiveSheet();
                        foreach ($sheet->toArray() as $row) {
                            $rows[] = $row;
                        }
                    }
                    $header = array_map('trim', $rows[0] ?? []);
                    for ($i = 1; $i < count($rows); $i++) {
                        $entry = [];
                        foreach ($header as $idx => $col) {
                            $entry[$col] = $rows[$i][$idx] ?? '';
                        }
                        $structured[] = $entry;
                    }
                    $html = '<table class="table table-bordered" contenteditable="true">';
                    foreach ($rows as $row) {
                        $html .= '<tr>';
                        foreach ($row as $cell) {
                            $html .= '<td contenteditable="true">' . htmlspecialchars((string)$cell) . '</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</table>';
                    session(['importedWorksheetData' => $structured]);
                    session(['worksheetHtml' => $html, 'worksheetFilePath' => $lastFile]);
                } catch (\Exception $e) {
                    // Ignore parse errors, just don't show worksheet
                }
            }
        }
        // Use Staff model to filter by designation and include relations
        $staff = \App\Models\Staff::where('designation', $designation)
            ->with(['department','organization'])
            ->paginate(15);

        $user = auth()->user();
        $isAdmin = $user && (int) $user->role === 4;
        $isStaff = $user && (int) $user->role === 2;
        
        // For Admission Services Officer, also pass all students with filters
        $students = collect([]);
        $departments = collect([]);
        if (strcasecmp(str_replace(' ', '', $designation), 'AdmissionServicesOfficer') === 0) {
            // Show students if form was submitted (search button clicked) - this allows showing all when no filters
            // Check if any filter parameter exists in the request (even if empty)
            $formSubmitted = $request->has('search') || $request->has('department_id') || $request->has('year_level');
            if ($formSubmitted) {
                $search = $request->input('search', '');
                $hasSearch = $request->filled('search') && !empty(trim($search));
                $hasDepartment = $request->filled('department_id');
                $hasYearLevel = $request->filled('year_level');
                
                // Fetch ALL students from Student table (with user relationship)
                $studentsQuery = \App\Models\Student::with(['user', 'department','course','organization','scholarship']);
                
                // Only apply search filter if search term is provided and not empty
                if ($hasSearch) {
                    $studentsQuery->where(function($q) use ($search) {
                        // Search in students table
                        $q->where('first_name', 'like', '%' . $search . '%')
                          ->orWhere('last_name', 'like', '%' . $search . '%')
                          ->orWhere('middle_name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('contact_number', 'like', '%' . $search . '%')
                          // Also search in related users table
                          ->orWhereHas('user', function($userQuery) use ($search) {
                              $userQuery->where('first_name', 'like', '%' . $search . '%')
                                        ->orWhere('last_name', 'like', '%' . $search . '%')
                                        ->orWhere('middle_name', 'like', '%' . $search . '%')
                                        ->orWhere('email', 'like', '%' . $search . '%')
                                        ->orWhere('user_id', 'like', '%' . $search . '%')
                                        ->orWhere('contact_number', 'like', '%' . $search . '%');
                          });
                    });
                }
                
                // Department filter - check both tables (only if department_id is provided and not empty)
                if ($hasDepartment) {
                    $studentsQuery->where(function($q) use ($request) {
                        $q->where('department_id', $request->department_id)
                          ->orWhereHas('user', function($userQuery) use ($request) {
                              $userQuery->where('department_id', $request->department_id);
                          });
                    });
                }
                
                // Year level filter - check both tables (only if year_level is provided and not empty)
                if ($hasYearLevel) {
                    $studentsQuery->where(function($q) use ($request) {
                        $q->where('year_level', $request->year_level)
                          ->orWhereHas('user', function($userQuery) use ($request) {
                              $userQuery->where('year_level', $request->year_level);
                          });
                    });
                }
                
                $studentsFromStudentTable = $studentsQuery->get();
                
                // Fetch ALL students from User table (role = 1) that don't have Student records yet
                // This ensures we get students that exist in users table but not yet in students table
                $usersQuery = \App\Models\User::with(['department','course','organization','scholarship'])
                    ->where('role', 1) // Only students
                    ->whereDoesntHave('student'); // Exclude users that already have Student records (to avoid duplicates)
                
                // Only apply search filter if search term is provided and not empty
                if ($hasSearch) {
                    $usersQuery->where(function($q) use ($search) {
                        $q->where('first_name', 'like', '%' . $search . '%')
                          ->orWhere('last_name', 'like', '%' . $search . '%')
                          ->orWhere('middle_name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('user_id', 'like', '%' . $search . '%')
                          ->orWhere('contact_number', 'like', '%' . $search . '%');
                    });
                }
                
                // Department filter for users (only if department_id is provided and not empty)
                if ($hasDepartment) {
                    $usersQuery->where('department_id', $request->department_id);
                }
                
                // Year level filter for users (only if year_level is provided and not empty)
                if ($hasYearLevel) {
                    $usersQuery->where('year_level', $request->year_level);
                }
                
                $usersFromUserTable = $usersQuery->get();
                
                // Automatically sync User records to Student table
                foreach ($usersFromUserTable as $user) {
                    $this->syncUserToStudent($user);
                }
                
                // Re-fetch to include newly synced students (now they have Student records)
                // We need to re-query Students instead since users now have student records
                if ($usersFromUserTable->isNotEmpty()) {
                    $syncedStudents = \App\Models\Student::with(['user', 'department','course','organization','scholarship'])
                        ->whereIn('user_id', $usersFromUserTable->pluck('id'))
                        ->get();
                    
                    // Add synced students to the main collection (avoid duplicates)
                    $existingUserIds = $studentsFromStudentTable->pluck('user_id')->toArray();
                    $newStudents = $syncedStudents->reject(function($student) use ($existingUserIds) {
                        return in_array($student->user_id, $existingUserIds);
                    });
                    $studentsFromStudentTable = $studentsFromStudentTable->merge($newStudents);
                }
                
                // Combine and format results
                $students = $studentsFromStudentTable
                    ->map(function($student) {
                        // Mark as Student model
                        $student->isStudentModel = true;
                        $student->isUserModel = false;
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
                            if (!$student->user_id && $student->user->user_id) {
                                $student->user_id = $student->user->user_id;
                            }
                        }
                        return $student;
                    })
                    ->sortBy(function($item) {
                        // Sort alphabetically by last name, then first name (case-insensitive)
                        $lastName = strtolower($item->last_name ?? $item->user->last_name ?? '');
                        $firstName = strtolower($item->first_name ?? $item->user->first_name ?? '');
                        return $lastName . ' ' . $firstName;
                    })
                    ->values();
            }
            
            // Get departments for filter dropdown
            $departments = \App\Models\Department::orderBy('name')->get();
        }
        
        // Get all approved events for QR scanner dropdown
        $events = \App\Models\Event::where('status', 'approved')
            ->orderBy('event_date', 'desc')
            ->get();
        
        return view('admin.staff.designation-dashboard', [
            'designation' => $designationRecord,
            'staff' => $staff,
            'isAdmin' => $isAdmin,
            'isStaff' => $isStaff,
            'students' => $students,
            'departments' => $departments,
            'events' => $events,
            'filters' => $request->only(['search', 'department_id', 'year_level']),
        ]);
    }
    
    /**
     * Sync a User record (role = 1) to Student table
     * This ensures all students exist in both tables
     */
    private function syncUserToStudent(\App\Models\User $user)
    {
        // Check if Student record already exists
        $existingStudent = \App\Models\Student::where('user_id', $user->id)->first();
        
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
            ]);
            
            // Add personal_data_sheet_image only if column exists
            if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'personal_data_sheet_image')) {
                $existingStudent->update([
                    'personal_data_sheet_image' => $user->image ?? $existingStudent->personal_data_sheet_image,
                ]);
            }
            return $existingStudent;
        }
        
        // Prepare student data array
        $studentData = [
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
        ];
        
        // Add personal_data_sheet_image only if column exists
        if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'personal_data_sheet_image')) {
            $studentData['personal_data_sheet_image'] = $user->image ?? null;
        }
        
        // Create new Student record from User data
        $student = \App\Models\Student::create($studentData);
        
        return $student;
    }
    /**
     * Handle Excel/CSV import for Admission Services Officer
     */
    public function importWorksheet(Request $request)
    {
        $user = auth()->user();
        // Try all possible sources for designation
        $designation = $user?->designation
            ?? optional($user?->staffProfile)->designation
            ?? \App\Models\Staff::where('email', $user?->email)->value('designation')
            ?? '';
        // Only allow Admission Services Officer or Admin
    if (strcasecmp(str_replace(' ', '', $designation), 'AdmissionServicesOfficer') !== 0 && (int)($user?->role ?? 0) !== 4) {
            return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')
                ->withErrors(['worksheetFile' => 'You are not allowed to import files.']);
        }
        $request->validate([
            'worksheetFile' => 'required|file|mimes:xlsx,csv',
        ]);
        $file = $request->file('worksheetFile');
        // Save the uploaded file to public/staff/sidebar/report/
        $filename = 'worksheet_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('staff/sidebar/report'), $filename);
        $rows = [];
        $structured = [];
        // Use PhpSpreadsheet for parsing
        try {
            $ext = $file->getClientOriginalExtension();
            $fullPath = public_path('staff/sidebar/report/' . $filename);
            if ($ext === 'csv') {
                $rows = array_map('str_getcsv', file($fullPath));
            } else {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->toArray() as $row) {
                    $rows[] = $row;
                }
            }
            // Assume first row is header
            $header = array_map('trim', $rows[0] ?? []);
            for ($i = 1; $i < count($rows); $i++) {
                $entry = [];
                foreach ($header as $idx => $col) {
                    $entry[$col] = $rows[$i][$idx] ?? '';
                }
                $structured[] = $entry;
            }
        } catch (\Exception $e) {
            return back()->withErrors(['worksheetFile' => 'Failed to parse file: ' . $e->getMessage()]);
        }
        // Render as editable HTML table
        $html = '<table class="table table-bordered" contenteditable="true">';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td contenteditable="true">' . htmlspecialchars((string)$cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        // Debug: log import process
        \Log::info('Excel import processed for user: ' . ($user->email ?? 'unknown'));
        \Log::info('Imported rows count: ' . count($rows));
        // Store structured data in session for search/edit
        session(['importedWorksheetData' => $structured]);
        // Save file path to user for persistence
        $user->last_imported_worksheet = 'staff/sidebar/report/' . $filename;
        $user->save();
    // Always redirect to the Admission Services Officer student-management page so the table displays
    return redirect()->route('admin.staff.dashboard.AdmissionServicesOfficer.student-management')
            ->with(['worksheetHtml' => $html, 'worksheetFilePath' => 'staff/sidebar/report/' . $filename]);
    }
}
