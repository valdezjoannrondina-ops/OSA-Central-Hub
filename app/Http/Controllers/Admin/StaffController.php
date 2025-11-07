<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\User;
use App\Models\Department;
use App\Models\Organization;

class StaffController extends Controller
{
    public function updateEmployeeId(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|string|unique:staff,user_id,' . $id
        ]);
        $staff = Staff::findOrFail($id);
        $staff->user_id = $request->employee_id;
        $staff->save();
        
        // Sync user_id to the users table
        $user = User::where('email', $staff->email)->first();
        if ($user) {
            $user->user_id = $request->employee_id;
            $user->save();
        }
        
        return back()->with('success', 'Employee ID updated successfully!');
    }
    public function profile()
    {
        $user = auth()->user();
        $staff = null;
        
        // Get Staff record by email if user has email
        if ($user && $user->email) {
            $staff = Staff::where('email', $user->email)
                ->with(['department', 'organization', 'organizations'])
                ->first();
        }
        
        // If staff not found, use user data
        if (!$staff) {
            $staff = $user;
        }
        
        return view('staff.profile', compact('staff', 'user'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|unique:staff,user_id',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:staff',
            'password' => 'required|min:8',
            'department_id' => 'nullable|integer',
            'designation' => 'required|string',
            'organization_ids' => 'array',
            'organization_ids.*' => 'integer|exists:organizations,id',
            'contact_number' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'service_order' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'age' => 'nullable|integer',
            'middle_name' => 'nullable|string',
            'contract_end_at' => 'nullable|string',
        ]);

        $data = $request->only([
            'user_id', 'first_name', 'last_name', 'middle_name', 'email', 'designation', 'department_id', 'contact_number', 'birth_date', 'gender', 'age'
        ]);
        $data['password'] = bcrypt($request->password);
        // Auto-calc age from birth_date if provided
        if (!empty($data['birth_date'])) {
            try {
                $data['age'] = \Carbon\Carbon::parse($data['birth_date'])->age;
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }
        $data['admin_id'] = auth()->id();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('staff_images', 'public');
        }
        if ($request->hasFile('service_order')) {
            $data['service_order'] = $request->file('service_order')->store('service_orders', 'public');
        }
        // Store human-readable LOS if provided (numeric from add form)
        if ($request->filled('length_of_service')) {
            $data['length_of_service'] = (string) $request->input('length_of_service');
        }
        // Normalize End of Contract from MM/DD/YYYY to Y-m-d H:i:s (end of day)
        if ($request->filled('contract_end_at')) {
            try {
                $dt = \Carbon\Carbon::createFromFormat('m/d/Y', $request->input('contract_end_at'));
                $data['contract_end_at'] = $dt->endOfDay()->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // keep as given if parsing fails
                $data['contract_end_at'] = $request->input('contract_end_at');
            }
        }
        $staff = Staff::create($data);
        // Attach organizations
        if ($request->has('organization_ids')) {
            $staff->organizations()->sync($request->organization_ids);
        }

        // Create matching User record for staff login
        \App\Models\User::create([
            'user_id' => $data['user_id'] ?? null,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'], // already hashed
            'role' => 2,
            'department_id' => $data['department_id'] ?? null,
            'organization_id' => $data['organization_id'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'image' => $data['image'] ?? null,
            'email_verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Staff member added successfully!');
    }


    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $departments = Department::all();
        // Pre-populate organizations: if staff has a department, show that department's orgs + unassigned; else show unassigned + current org
        if ($staff->department_id) {
            $organizations = Organization::where(function($q) use ($staff){
                $q->whereNull('department_id')
                  ->orWhere('department_id', $staff->department_id);
            })->orderBy('name')->get();
        } else {
            $organizations = Organization::whereNull('department_id')
                ->orWhere('id', $staff->organization_id)
                ->orderBy('name')
                ->get();
        }
        return view('admin.edit-staff', compact('staff', 'departments', 'organizations'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $data = $request->only([
            'first_name', 'last_name', 'middle_name', 'user_id', 'email', 'designation', 'department_id', 'contact_number', 'birth_date', 'gender', 'age', 'employment_status'
        ]);
        // Sync organizations
        if ($request->has('organization_ids')) {
            $staff->organizations()->sync($request->organization_ids);
        }
        // Optional admin-set password
        if ($request->filled('new_password')) {
            $request->validate([
                'new_password' => 'required|string|min:8|confirmed',
            ]);
        }
        // Save human-friendly LOS string if provided
        $losRaw = trim((string) $request->input('length_of_service', ''));
        $data['length_of_service'] = ($losRaw === '') ? null : $losRaw;

        // Auto-calc age from birth_date if provided
        if (!empty($data['birth_date'])) {
            try {
                $data['age'] = \Carbon\Carbon::parse($data['birth_date'])->age;
            } catch (\Exception $e) {
                // ignore
            }
        }

        // Prefer explicit End of Contract date if provided; otherwise compute from LOS only when units exist
        $contractEndRaw = trim((string) $request->input('contract_end_at', ''));
        $losInput = trim((string) $request->input('length_of_service', ''));

        if ($contractEndRaw !== '') {
            try {
                // Expect MM/DD/YYYY from the UI; set to end of day for consistency
                $dt = \Carbon\Carbon::createFromFormat('m/d/Y', $contractEndRaw)->endOfDay();
                $data['contract_end_at'] = $dt->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                try {
                    // Fallback: attempt a generic parse
                    $dt = \Carbon\Carbon::parse($contractEndRaw)->endOfDay();
                    $data['contract_end_at'] = $dt->format('Y-m-d H:i:s');
                } catch (\Exception $e2) {
                    // As a last resort, store the raw string
                    $data['contract_end_at'] = $contractEndRaw;
                }
            }
        } elseif ($losInput !== '') {
            // Compute only if LOS has units (e.g., 1y, 6mo, 2w, 3d, 5h, 30m)
            preg_match_all('/(\d+)\s*(y|yr|yrs|year|years|mo|month|months|w|wk|wks|week|weeks|d|day|days|h|hr|hrs|hour|hours|m|min|mins|minute|minutes)\b/i', $losInput, $matches, PREG_SET_ORDER);
            if (!empty($matches)) {
                $dt = now();
                foreach ($matches as $m) {
                    $val = (int) $m[1];
                    $unit = strtolower($m[2]);
                    switch ($unit) {
                        case 'y': case 'yr': case 'yrs': case 'year': case 'years':
                            $dt = $dt->addYears($val); break;
                        case 'mo': case 'month': case 'months':
                            $dt = $dt->addMonths($val); break;
                        case 'w': case 'wk': case 'wks': case 'week': case 'weeks':
                            $dt = $dt->addWeeks($val); break;
                        case 'd': case 'day': case 'days':
                            $dt = $dt->addDays($val); break;
                        case 'h': case 'hr': case 'hrs': case 'hour': case 'hours':
                            $dt = $dt->addHours($val); break;
                        case 'm': case 'min': case 'mins': case 'minute': case 'minutes':
                            $dt = $dt->addMinutes($val); break;
                    }
                }
                $data['contract_end_at'] = $dt->format('Y-m-d H:i:s');
            }
        }

        // Handle image and service order uploads with improved validation
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,jpg,png|max:2048|dimensions:min_width=100,min_height=100',
            ]);
            $image = $request->file('image');
            // Sanitize filename
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $image->getClientOriginalName());
            $data['image'] = $image->storeAs('staff_images', $filename, 'public');
        }
        if ($request->hasFile('service_order')) {
            $request->validate([
                'service_order' => 'file|mimes:pdf,doc,docx|max:10240',
            ]);
            $serviceOrder = $request->file('service_order');
            // Sanitize filename
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $serviceOrder->getClientOriginalName());
            $data['service_order'] = $serviceOrder->storeAs('service_orders', $filename, 'public');
        }

        try {
            // Get the user BEFORE updating staff (in case email changes)
            $oldEmail = $staff->email;
            $newEmail = $data['email'] ?? $oldEmail;
            
            // Try to find user by multiple methods (case-insensitive)
            $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])->first();
            
            // If not found by old email, try new email (case-insensitive)
            if (!$user && $newEmail !== $oldEmail) {
                $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim($newEmail))])->first();
            }
            
            // If still not found, try by user_id
            if (!$user && !empty($data['user_id'])) {
                $user = User::where('user_id', $data['user_id'])->first();
            }
            
            // Update staff first
            $staff->update($data);
            
            // Refresh staff to get updated values
            $staff->refresh();
            
            // If email changed, update ALL staff records with the old email to the new email
            if ($newEmail !== $oldEmail && $oldEmail) {
                Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($oldEmail))])
                    ->where('id', '!=', $staff->id)
                    ->update(['email' => $newEmail]);
            }
            
            // Sync all changes to the User table
            if ($user) {
                // Update all user fields that match staff fields
                // Use data from request if available, otherwise use updated staff values
                $user->first_name = $data['first_name'] ?? $staff->first_name;
                $user->last_name = $data['last_name'] ?? $staff->last_name;
                $user->middle_name = $data['middle_name'] ?? $staff->middle_name ?? '';
                $user->user_id = $data['user_id'] ?? $staff->user_id;
                $user->email = $data['email'] ?? $staff->email; // Update email if changed
                $user->contact_number = $data['contact_number'] ?? $staff->contact_number ?? '';
                $user->birth_date = $data['birth_date'] ?? $staff->birth_date;
                $user->gender = $data['gender'] ?? $staff->gender;
                $user->age = $data['age'] ?? $staff->age;
                $user->department_id = $data['department_id'] ?? $staff->department_id;
                
                // Update image and service_order if they exist in data
                if (isset($data['image'])) {
                    $user->image = $data['image'];
                }
                if (isset($data['service_order'])) {
                    $user->service_order = $data['service_order'];
                }
                
                // Update designation if it exists in User model
                if (isset($data['designation'])) {
                    $user->designation = $data['designation'];
                } elseif ($staff->designation) {
                    $user->designation = $staff->designation;
                }
                
                // Handle employment status
                $employmentStatus = $data['employment_status'] ?? $staff->employment_status;
                if (in_array($employmentStatus, ['ended','inactive'])) {
                    // Block access without mutating the user's role to avoid DB NOT NULL constraint issues
                    // Optionally, we could set a separate flag on users if available.
                } elseif ($employmentStatus === 'active' && $user->role !== 2) {
                    // Reactivate as Staff
                    $user->role = 2;
                    if (is_null($user->email_verified_at)) {
                        $user->email_verified_at = now();
                    }
                }
                
                // Update password if provided
                if ($request->filled('new_password')) {
                    $user->password = bcrypt($request->input('new_password'));
                }
                
                // Save user changes
                $user->save();
            } else {
                // If user not found by email, try to find by user_id
                if (!empty($data['user_id'])) {
                    $user = User::where('user_id', $data['user_id'])->first();
                    if ($user) {
                        // Update user with staff data
                        $user->first_name = $data['first_name'] ?? $staff->first_name;
                        $user->last_name = $data['last_name'] ?? $staff->last_name;
                        $user->middle_name = $data['middle_name'] ?? $staff->middle_name ?? '';
                        $user->email = $data['email'] ?? $staff->email;
                        $user->contact_number = $data['contact_number'] ?? $staff->contact_number ?? '';
                        $user->birth_date = $data['birth_date'] ?? $staff->birth_date;
                        $user->gender = $data['gender'] ?? $staff->gender;
                        $user->age = $data['age'] ?? $staff->age;
                        $user->department_id = $data['department_id'] ?? $staff->department_id;
                        if (isset($data['designation'])) {
                            $user->designation = $data['designation'];
                        } elseif ($staff->designation) {
                            $user->designation = $staff->designation;
                        }
                        if (isset($data['image'])) {
                            $user->image = $data['image'];
                        }
                        if (isset($data['service_order'])) {
                            $user->service_order = $data['service_order'];
                        }
                        $user->save();
                    }
                }
            }

            \Illuminate\Support\Facades\Log::info('Staff updated successfully', [
                'staff_id' => $staff->id,
                'user_id' => $user->id ?? null,
                'updated_by' => auth()->id(),
                'timestamp' => now(),
            ]);

            return redirect()->to(route('admin.show-staff') . '#staff-' . $staff->id)
                ->with('success', 'Staff details updated successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update staff', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update staff. Please try again or contact support if the problem persists.');
        }
    }

    public function addAssistant(Request $request)
    {
        $count = User::where('role', 3)->count();
        if ($count >= 11) {
            return back()->withErrors(['limit' => 'Maximum of 11 assistant staff allowed.']);
        }
        $request->validate([
            'user_id' => 'required|unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        User::create([
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 3,
            'email_verified_at' => now(),
        ]);
        return back()->with('success', 'Assistant staff added.');
    }

    public function listAssistants()
    {
        $assistants = User::where('role', 3)->get();
        return view('admin.assistants', compact('assistants'));
    }

    public function editAssistant($id)
    {
        $assistant = User::where('role', 3)->findOrFail($id);
        return view('admin.assistants.edit', compact('assistant'));
    }

    public function updateAssistant(Request $request, $id)
    {
        $assistant = User::where('role', 3)->findOrFail($id);
        $request->validate([
            'user_id' => 'required|unique:users,user_id,' . $assistant->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $assistant->id,
            'password' => 'nullable|min:8',
        ]);
        $assistant->user_id = $request->user_id;
        $assistant->first_name = $request->first_name;
        $assistant->last_name = $request->last_name;
        $assistant->email = $request->email;
        if ($request->filled('password')) {
            $assistant->password = bcrypt($request->password);
        }
        $assistant->save();
        return redirect()->route('admin.assistants.index')->with('success', 'Assistant updated.');
    }

    public function destroyAssistant($id)
    {
        $assistant = User::where('role', 3)->findOrFail($id);
        $assistant->delete();
        return back()->with('success', 'Assistant deleted.');
    }

    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        $staff->delete();
        return back()->with('success', 'Staff member deleted successfully!');
    }
}