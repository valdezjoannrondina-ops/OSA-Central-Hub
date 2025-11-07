<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    // List assistants belonging to the current staff
    public function index(Request $request)
    {
        $status = $request->query('status', 'all'); // all | active | suspended

        $query = User::where('role', 3)
            ->where('supervisor_id', auth()->id());

        if ($status === 'active') {
            $query->where(function ($q) {
                $q->whereNull('suspended')->orWhere('suspended', false);
            });
        } elseif ($status === 'suspended') {
            $query->where('suspended', true);
        }

        $assistants = $query->orderBy('first_name')->get();

        return view('staff.assistants.index', compact('assistants', 'status'));
    }

    public function create(Request $request)
    {
        $departments = \App\Models\Department::all();
        $organizations = \App\Models\Organization::orderBy('name')->get();
        $selectedOrganizationId = $request->query('organization_id');
        return view('staff.assistants.create', compact('departments', 'organizations', 'selectedOrganizationId'));
    }

    public function store(Request $request)
    {
        // Optional per-staff cap
        $count = User::where('role', 3)->where('supervisor_id', auth()->id())->count();
        if ($count >= 11) {
            return back()->withErrors(['limit' => 'Maximum of 11 assistant staff allowed per staff.'])->withInput();
        }

        // Check if user already exists by email or user_id
        $existingUser = User::where('email', $request->email)
            ->orWhere('user_id', $request->user_id)
            ->first();

        // Validation: unique checks only needed if creating new user
        $validationRules = [
            'user_id' => 'required|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'contact_number' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'organization_id' => 'required|exists:organizations,id',
            'position' => 'nullable|string|max:200',
            'image' => 'nullable|image|max:5120',
            'service_order' => 'nullable|file|mimes:pdf,doc,docx|max:15360',
            'length_of_service' => 'nullable|integer|min:0',
            'contract_end_at' => ['nullable','regex:/^\d{2}\/\d{2}\/\d{4}$/'],
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:100',
            'civil_status' => 'nullable|in:single,married,divorced,widowed,separated',
            'course_id' => 'nullable|exists:courses,id',
            'year_level' => 'nullable|integer|min:1|max:10',
            'student_type1' => 'nullable|in:regular,irregular,transferee',
            'student_type2' => 'nullable|in:paying,scholar',
            'scholarship_id' => 'nullable|exists:scholarships,id',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:50',
            'emergency_relation' => 'nullable|string|max:100',
            'complete_home_address' => 'nullable|string|max:500',
            'academic_year' => 'nullable|string|max:20',
            'leadership_org' => 'nullable|array',
            'leadership_position' => 'nullable|array',
            'leadership_year' => 'nullable|array',
        ];

        // Only require password if creating new user
        if (!$existingUser) {
            $validationRules['password'] = 'required|string|min:6|confirmed';
            $validationRules['user_id'] = 'required|string|max:50|unique:users,user_id';
            $validationRules['email'] = 'required|email|max:255|unique:users,email';
        }

        $data = $request->validate($validationRules);

        // Check if existing user is already an assistant
        if ($existingUser && $existingUser->role == 3) {
            return back()->withErrors(['email' => 'This user is already an assistant staff member.'])->withInput();
        }

        if ($existingUser) {
            // Update existing user to be an assistant
            $assistant = $existingUser;
        } else {
            // Create new user
            $assistant = new User();
        }

        // Update/create assistant fields
        // Only set user_id for new users (preserve existing user_id for students)
        if (!$existingUser) {
            $assistant->user_id = $data['user_id'];
        }
        $assistant->first_name = $data['first_name'];
        $assistant->middle_name = $data['middle_name'] ?? null;
        $assistant->last_name = $data['last_name'];
        $assistant->email = $data['email'];
        $assistant->role = 3; // assistant
        $assistant->supervisor_id = auth()->id();
        
        // Only set password if provided (creating new user or updating password)
        if (isset($data['password'])) {
            $assistant->password = bcrypt($data['password']);
        }
        
        $assistant->contact_number = $data['contact_number'] ?? ($assistant->contact_number ?? null);
        $assistant->department_id = $data['department_id'] ?? ($assistant->department_id ?? null);
        $assistant->organization_id = $data['organization_id']; // Use organization from form
        $assistant->position = $data['position'] ?? null;
        
        if (!$existingUser) {
            $assistant->email_verified_at = now();
        }
        
        $assistant->birth_date = $data['birth_date'] ?? ($assistant->birth_date ?? null);
        $assistant->gender = $data['gender'] ?? ($assistant->gender ?? null);
        $assistant->age = $data['age'] ?? ($assistant->age ?? null);
        $assistant->civil_status = $data['civil_status'] ?? ($assistant->civil_status ?? null);
        $assistant->course_id = $data['course_id'] ?? ($assistant->course_id ?? null);
        $assistant->year_level = $data['year_level'] ?? ($assistant->year_level ?? null);
        $assistant->student_type1 = $data['student_type1'] ?? ($assistant->student_type1 ?? null);
        $assistant->student_type2 = $data['student_type2'] ?? ($assistant->student_type2 ?? null);
        $assistant->scholarship_id = $data['scholarship_id'] ?? ($assistant->scholarship_id ?? null);
        $assistant->emergency_contact_name = $data['emergency_contact_name'] ?? ($assistant->emergency_contact_name ?? null);
        $assistant->emergency_contact_number = $data['emergency_contact_number'] ?? ($assistant->emergency_contact_number ?? null);
        $assistant->emergency_relation = $data['emergency_relation'] ?? ($assistant->emergency_relation ?? null);
        $assistant->complete_home_address = $data['complete_home_address'] ?? ($assistant->complete_home_address ?? null);
        $assistant->academic_year = $data['academic_year'] ?? ($assistant->academic_year ?? null);

        if ($request->hasFile('image')) {
            $assistant->image = $request->file('image')->store('profile_images', 'public');
        }
        if ($request->hasFile('service_order')) {
            $assistant->service_order = $request->file('service_order')->store('service_orders', 'public');
        }

        // Contract end date resolution
        $contractEndAt = null;
        if (!empty($data['contract_end_at'])) {
            // MM/DD/YYYY
            $dt = \DateTime::createFromFormat('m/d/Y', $data['contract_end_at']);
            if ($dt) {
                $contractEndAt = $dt->format('Y-m-d');
            }
        } elseif (!empty($data['length_of_service'])) {
            // Compute from now + length_of_service years
            $contractEndAt = now()->addYears((int)$data['length_of_service'])->toDateString();
        }
        $assistant->length_of_service = $data['length_of_service'] ?? null;
        $assistant->contract_end_at = $contractEndAt;
        $assistant->save();

        // Save leadership background data
        if (!empty($data['leadership_org'])) {
            $leadershipOrgs = $data['leadership_org'];
            $leadershipPositions = $data['leadership_position'] ?? [];
            $leadershipYears = $data['leadership_year'] ?? [];
            
            foreach ($leadershipOrgs as $index => $org) {
                if (!empty(trim($org))) {
                    \App\Models\AssistantLeadershipBackground::create([
                        'user_id' => $assistant->id,
                        'organization' => trim($org),
                        'position' => !empty($leadershipPositions[$index]) ? trim($leadershipPositions[$index]) : null,
                        'year' => !empty($leadershipYears[$index]) ? trim($leadershipYears[$index]) : null,
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('staff.assistants.index')->with('success', 'Assistant staff added.');
    }

    public function edit($id)
    {
        $assistant = User::where('role', 3)
            ->where('supervisor_id', auth()->id())
            ->findOrFail($id);
        return view('staff.assistants.edit', compact('assistant'));
    }

    public function update(Request $request, $id)
    {
        $assistant = User::where('role', 3)
            ->where('supervisor_id', auth()->id())
            ->findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|string|max:50|unique:users,user_id,' . $assistant->id,
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $assistant->id,
            'password' => 'nullable|string|min:6|confirmed',
            'contact_number' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'position' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120|dimensions:min_width=100,min_height=100',
            'service_order' => 'nullable|file|mimes:pdf,doc,docx|max:15360',
            'length_of_service' => 'nullable|integer|min:0',
            'contract_end_at' => ['nullable','regex:/^\d{2}\/\d{2}\/\d{4}$/'],
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:100',
            'civil_status' => 'nullable|in:single,married,divorced,widowed,separated',
            'course_id' => 'nullable|exists:courses,id',
            'year_level' => 'nullable|integer|min:1|max:10',
            'student_type1' => 'nullable|in:regular,irregular,transferee',
            'student_type2' => 'nullable|in:paying,scholar',
            'scholarship_id' => 'nullable|exists:scholarships,id',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:50',
            'emergency_relation' => 'nullable|string|max:100',
            'complete_home_address' => 'nullable|string|max:500',
            'academic_year' => 'nullable|string|max:20',
            'leadership_org' => 'nullable|array',
            'leadership_position' => 'nullable|array',
            'leadership_year' => 'nullable|array',
        ]);

        $assistant->user_id = $data['user_id'];
        $assistant->first_name = $data['first_name'];
        $assistant->middle_name = $data['middle_name'] ?? null;
        $assistant->last_name = $data['last_name'];
        $assistant->email = $data['email'];
        if (!empty($data['password'])) {
            $assistant->password = bcrypt($data['password']);
        }
        $assistant->contact_number = $data['contact_number'] ?? null;
        $assistant->department_id = $data['department_id'] ?? null;
        $assistant->organization_id = $data['organization_id'] ?? $assistant->organization_id; // Use organization from form or keep existing
        $assistant->position = $data['position'] ?? null;
        $assistant->birth_date = $data['birth_date'] ?? null;
        $assistant->gender = $data['gender'] ?? null;
        $assistant->age = $data['age'] ?? null;
        $assistant->civil_status = $data['civil_status'] ?? null;
        $assistant->course_id = $data['course_id'] ?? null;
        $assistant->year_level = $data['year_level'] ?? null;
        $assistant->student_type1 = $data['student_type1'] ?? null;
        $assistant->student_type2 = $data['student_type2'] ?? null;
        $assistant->scholarship_id = $data['scholarship_id'] ?? null;
        $assistant->emergency_contact_name = $data['emergency_contact_name'] ?? null;
        $assistant->emergency_contact_number = $data['emergency_contact_number'] ?? null;
        $assistant->emergency_relation = $data['emergency_relation'] ?? null;
        $assistant->complete_home_address = $data['complete_home_address'] ?? null;
        $assistant->academic_year = $data['academic_year'] ?? null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Sanitize filename
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $image->getClientOriginalName());
            $assistant->image = $image->storeAs('profile_images', $filename, 'public');
        }
        if ($request->hasFile('service_order')) {
            $serviceOrder = $request->file('service_order');
            // Sanitize filename
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $serviceOrder->getClientOriginalName());
            $assistant->service_order = $serviceOrder->storeAs('service_orders', $filename, 'public');
        }

        $contractEndAt = null;
        if (!empty($data['contract_end_at'])) {
            $dt = \DateTime::createFromFormat('m/d/Y', $data['contract_end_at']);
            if ($dt) {
                $contractEndAt = $dt->format('Y-m-d');
            }
        } elseif (!empty($data['length_of_service'])) {
            $contractEndAt = now()->addYears((int)$data['length_of_service'])->toDateString();
        }
        $assistant->length_of_service = $data['length_of_service'] ?? null;
        $assistant->contract_end_at = $contractEndAt;
        $assistant->save();

        // Update leadership background data (delete existing and recreate)
        \App\Models\AssistantLeadershipBackground::where('user_id', $assistant->id)->delete();
        
        if (!empty($data['leadership_org'])) {
            $leadershipOrgs = $data['leadership_org'];
            $leadershipPositions = $data['leadership_position'] ?? [];
            $leadershipYears = $data['leadership_year'] ?? [];
            
            foreach ($leadershipOrgs as $index => $org) {
                if (!empty(trim($org))) {
                    \App\Models\AssistantLeadershipBackground::create([
                        'user_id' => $assistant->id,
                        'organization' => trim($org),
                        'position' => !empty($leadershipPositions[$index]) ? trim($leadershipPositions[$index]) : null,
                        'year' => !empty($leadershipYears[$index]) ? trim($leadershipYears[$index]) : null,
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('staff.assistants.index')->with('success', 'Assistant staff updated.');
    }

    public function destroy($id)
    {
        $assistant = User::where('role', 3)
            ->where('supervisor_id', auth()->id())
            ->findOrFail($id);
        $assistant->delete();
        return redirect()->route('staff.assistants.index')->with('success', 'Assistant staff deleted.');
    }

    public function suspend($id)
    {
        $assistant = User::where('role', 3)
            ->where('supervisor_id', auth()->id())
            ->findOrFail($id);
        $assistant->suspended = true;
        $assistant->save();
        return redirect()->route('staff.assistants.index')->with('success', 'Assistant suspended.');
    }

    public function resume($id)
    {
        $assistant = User::where('role', 3)
            ->where('supervisor_id', auth()->id())
            ->findOrFail($id);
        $assistant->suspended = false;
        $assistant->save();
        return redirect()->route('staff.assistants.index')->with('success', 'Assistant resumed.');
    }

    // Show all organizations for the current staff with assistant management
    public function organizations()
    {
        $user = auth()->user();
        
        // Get staff record by email
        $staff = \App\Models\Staff::where('email', $user->email)->first();
        
        $organizations = collect();
        
        if ($staff) {
            // Get single organization
            if ($staff->organization_id) {
                $org = \App\Models\Organization::with('department')->find($staff->organization_id);
                if ($org) {
                    $organizations->push($org);
                }
            }
            
            // Get additional organizations from many-to-many relationship
            $additionalOrgs = $staff->organizations()->with('department')->get();
            foreach ($additionalOrgs as $org) {
                // Avoid duplicates
                if (!$organizations->contains('id', $org->id)) {
                    $organizations->push($org);
                }
            }
        }
        
        // Also check if user has organizations directly (for staff who are users)
        if ($user->organization_id) {
            $org = \App\Models\Organization::with('department')->find($user->organization_id);
            if ($org && !$organizations->contains('id', $org->id)) {
                $organizations->push($org);
            }
        }
        
        // Get additional organizations from organization_user table
        if (method_exists($user, 'otherOrganizations')) {
            $userOrgs = $user->otherOrganizations()->with('department')->get();
            foreach ($userOrgs as $org) {
                if (!$organizations->contains('id', $org->id)) {
                    $organizations->push($org);
                }
            }
        }
        
        // For each organization, fetch membership statistics
        $organizationsWithStats = $organizations->map(function ($org) {
            // Refresh organization to ensure department_id is current
            $org->refresh();
            
            // Get all students (role = 1) who belong to this organization
            // For department-related organizations (academic), automatically include all students from that department
            // Match using department_id from the users table with organization's department_id from the organizations table
            
            $orgDepartmentId = $org->department_id;
            
            if ($orgDepartmentId) {
                // If organization is department-related (academic), include ALL students from that department
                // Match students' department_id (from users table) with organization's department_id (from organizations table)
                // This makes students under Information Technology automatically belong to Student Council of Information Technology
                $members = \App\Models\User::where('role', 1)
                    ->whereNotNull('department_id')
                    ->where('department_id', $orgDepartmentId)
                    ->get();
            } else {
                // For non-academic organizations (no department_id), only count explicit assignments
                $members = \App\Models\User::where('role', 1)
                    ->where(function ($query) use ($org) {
                        // Direct organization assignment
                        $query->where('organization_id', $org->id)
                            // Organization via pivot table
                            ->orWhereHas('otherOrganizations', function ($q) use ($org) {
                                $q->where('organizations.id', $org->id);
                            });
                    })
                    ->get();
            }
            
            // Calculate total members
            $totalMembers = $members->count();
            
            // Calculate by gender
            $maleCount = $members->where('gender', 'male')->count();
            $femaleCount = $members->where('gender', 'female')->count();
            $otherCount = $members->where('gender', 'other')->count();
            
            // Calculate by year level (1st to 5th year)
            $yearLevelCounts = [];
            for ($year = 1; $year <= 5; $year++) {
                $yearLevelCounts[$year] = [
                    'total' => $members->where('year_level', $year)->count(),
                    'male' => $members->where('gender', 'male')->where('year_level', $year)->count(),
                    'female' => $members->where('gender', 'female')->where('year_level', $year)->count(),
                    'other' => $members->where('gender', 'other')->where('year_level', $year)->count(),
                ];
            }
            
            return [
                'organization' => $org,
                'total_members' => $totalMembers,
                'male_count' => $maleCount,
                'female_count' => $femaleCount,
                'other_count' => $otherCount,
                'year_level_counts' => $yearLevelCounts,
            ];
        });
        
        // Get all approved events for QR scanner dropdown
        $events = \App\Models\Event::where('status', 'approved')
            ->orderBy('event_date', 'desc')
            ->get();
        
        return view('staff.organizations.index', compact('organizationsWithStats', 'organizations', 'events'));
    }

    // List assistants for a specific organization
    public function organizationAssistants($organizationId)
    {
        $organization = \App\Models\Organization::findOrFail($organizationId);
        
        // Verify the user has access to this organization
        $user = auth()->user();
        $staff = \App\Models\Staff::where('email', $user->email)->first();
        
        $hasAccess = false;
        
        if ($staff) {
            $hasAccess = ($staff->organization_id == $organizationId) || 
                         $staff->organizations()->where('organizations.id', $organizationId)->exists();
        }
        
        if (!$hasAccess && $user->organization_id == $organizationId) {
            $hasAccess = true;
        }
        
        if (!$hasAccess && method_exists($user, 'otherOrganizations')) {
            $hasAccess = $user->otherOrganizations()->where('organizations.id', $organizationId)->exists();
        }
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this organization.');
        }
        
        // Get assistants for this organization
        $assistants = User::where('role', 3)
            ->where('supervisor_id', auth()->id())
            ->where(function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId)
                  ->orWhereHas('otherOrganizations', function($oq) use ($organizationId) {
                      $oq->where('organizations.id', $organizationId);
                  });
            })
            ->orderBy('first_name')
            ->get();
        
        return view('staff.organizations.assistants', compact('organization', 'assistants'));
    }
}
