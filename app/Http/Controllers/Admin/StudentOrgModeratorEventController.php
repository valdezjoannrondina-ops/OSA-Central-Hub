<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Organization;

class StudentOrgModeratorEventController extends Controller
{
    
    public function create(Request $request)
    {
        $currentUser = auth()->user();
        $isAdmin = $currentUser && (int) $currentUser->role === 4;
        
        // If admin is viewing, check for staff email or ID in query parameter
        $targetStaff = null;
        $targetUser = null;
        
        if ($isAdmin && $request->has('staff_email')) {
            // Admin viewing a specific staff member's dashboard
            $targetStaff = \App\Models\Staff::with(['organizations', 'organization'])->where('email', $request->staff_email)->first();
            if ($targetStaff) {
                $targetUser = \App\Models\User::where('email', $targetStaff->email)->first();
            }
        } elseif ($isAdmin && $request->has('staff_id')) {
            // Admin viewing a specific staff member's dashboard by ID
            $targetStaff = \App\Models\Staff::with(['organizations', 'organization'])->find($request->staff_id);
            if ($targetStaff) {
                $targetUser = \App\Models\User::where('email', $targetStaff->email)->first();
            }
        }
        
        // Use target staff/user if admin is viewing, otherwise use current user
        $staff = $targetStaff ?? \App\Models\Staff::with(['organizations', 'organization'])->where('email', $currentUser->email)->first();
        $user = $targetUser ?? $currentUser;
        
        // Access control: If not admin, verify that the user is a Student Org. Moderator
        if (!$isAdmin) {
            $userDesignation = $currentUser->designation
                ?? optional($currentUser->staffProfile)->designation
                ?? ($staff ? $staff->designation : null);
            
            if (!$userDesignation || strcasecmp($userDesignation, 'Student Org. Moderator') !== 0) {
                abort(403, 'Unauthorized: Only Student Org. Moderator can access this dashboard.');
            }
        }
        
        $userOrganizations = collect();
        
        if ($staff) {
            // Get single organization from organization_id (if set)
            if ($staff->organization_id) {
                $org = $staff->organization ?? Organization::find($staff->organization_id);
                if ($org) {
                    $userOrganizations->push($org);
                }
            }
            
            // Get additional organizations from many-to-many relationship (organization_staff table)
            $additionalOrgs = $staff->organizations()->get();
            foreach ($additionalOrgs as $org) {
                // Avoid duplicates
                if (!$userOrganizations->contains('id', $org->id)) {
                    $userOrganizations->push($org);
                }
            }
        }
        
        // Also check if user has organizations directly (for staff who are users)
        if ($user && $user->organization_id) {
            $org = Organization::find($user->organization_id);
            if ($org && !$userOrganizations->contains('id', $org->id)) {
                $userOrganizations->push($org);
            }
        }
        
        // Get additional organizations from organization_user table
        if ($user && method_exists($user, 'otherOrganizations')) {
            $userOrgs = $user->otherOrganizations()->get();
            foreach ($userOrgs as $org) {
                if (!$userOrganizations->contains('id', $org->id)) {
                    $userOrganizations->push($org);
                }
            }
        }
        
        // Get organization_id from request if provided
        $selectedOrganizationId = $request->query('organization_id');
        
        return view('admin.staff.dashboard.StudentOrgModerator.create-event', compact('userOrganizations', 'selectedOrganizationId'));
    }

    public function show(Event $event)
    {
    return view('admin.staff.dashboard.StudentOrgModerator.event-details', compact('event'));
    }
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isAdmin = $currentUser && (int) $currentUser->role === 4;
        
        // If admin is viewing, check for staff email or ID in query parameter
        $targetStaff = null;
        $targetUser = null;
        
        if ($isAdmin && $request->has('staff_email')) {
            // Admin viewing a specific staff member's dashboard
            $targetStaff = \App\Models\Staff::with(['organizations', 'organization'])->where('email', $request->staff_email)->first();
            if ($targetStaff) {
                $targetUser = \App\Models\User::where('email', $targetStaff->email)->first();
            }
        } elseif ($isAdmin && $request->has('staff_id')) {
            // Admin viewing a specific staff member's dashboard by ID
            $targetStaff = \App\Models\Staff::with(['organizations', 'organization'])->find($request->staff_id);
            if ($targetStaff) {
                $targetUser = \App\Models\User::where('email', $targetStaff->email)->first();
            }
        }
        
        // Use target staff/user if admin is viewing, otherwise use current user
        $staff = $targetStaff ?? \App\Models\Staff::with(['organizations', 'organization'])->where('email', $currentUser->email)->first();
        $user = $targetUser ?? $currentUser;
        $targetUserId = $user ? $user->id : auth()->id();
        
        // Access control: If not admin, verify that the user is a Student Org. Moderator
        if (!$isAdmin) {
            $userDesignation = $currentUser->designation
                ?? optional($currentUser->staffProfile)->designation
                ?? ($staff ? $staff->designation : null);
            
            if (!$userDesignation || strcasecmp($userDesignation, 'Student Org. Moderator') !== 0) {
                abort(403, 'Unauthorized: Only Student Org. Moderator can access this dashboard.');
            }
        }
        
        $userOrganizations = collect();
        
        if ($staff) {
            // Get single organization from organization_id (if set)
            if ($staff->organization_id) {
                $org = $staff->organization ?? Organization::find($staff->organization_id);
                if ($org) {
                    $userOrganizations->push($org);
                }
            }
            
            // Get additional organizations from many-to-many relationship (organization_staff table)
            $additionalOrgs = $staff->organizations()->get();
            foreach ($additionalOrgs as $org) {
                // Avoid duplicates
                if (!$userOrganizations->contains('id', $org->id)) {
                    $userOrganizations->push($org);
                }
            }
        }
        
        // Also check if user has organizations directly (for staff who are users)
        if ($user && $user->organization_id) {
            $org = Organization::find($user->organization_id);
            if ($org && !$userOrganizations->contains('id', $org->id)) {
                $userOrganizations->push($org);
            }
        }
        
        // Get additional organizations from organization_user table
        if ($user && method_exists($user, 'otherOrganizations')) {
            $userOrgs = $user->otherOrganizations()->get();
            foreach ($userOrgs as $org) {
                if (!$userOrganizations->contains('id', $org->id)) {
                    $userOrganizations->push($org);
                }
            }
        }
        
        // Get all events for this user, grouped by organization
        $events = Event::where('created_by', $targetUserId)
            ->where('organization_id', '!=', null)
            ->with('organization')
            ->orderByDesc('event_date')
            ->get();
        
        // Pre-compute student counts for each organization
        $userOrganizations->each(function($org) {
            $orgDepartmentId = $org->department_id;
            
            if ($orgDepartmentId) {
                // If organization is department-related (academic), include ALL students from that department
                // Match students' department_id (from users table) with organization's department_id (from organizations table)
                // This makes students under Information Technology automatically belong to Student Council of Information Technology
                $allStudents = \App\Models\User::where('role', 1)
                    ->whereNotNull('department_id')
                    ->where('department_id', $orgDepartmentId)
                    ->get();
            } else {
                // For non-academic organizations (no department_id), only count explicit assignments
            $directStudents = $org->users()->where('role', 1)->get();
            $pivotStudents = $org->otherUsers()->where('role', 1)->get();
            $allStudents = $directStudents->merge($pivotStudents)->unique('id');
            }
            
            $org->studentCount = $allStudents->count();
        });
            
        return view('admin.staff.dashboard.StudentOrgModerator.index', compact('userOrganizations', 'events'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isAdmin = $currentUser && (int) $currentUser->role === 4;
        
        // Access control: If not admin, verify that the user is a Student Org. Moderator
        if (!$isAdmin) {
            $staff = \App\Models\Staff::where('email', $currentUser->email)->first();
            $userDesignation = $currentUser->designation
                ?? optional($currentUser->staffProfile)->designation
                ?? ($staff ? $staff->designation : null);
            
            if (!$userDesignation || strcasecmp($userDesignation, 'Student Org. Moderator') !== 0) {
                abort(403, 'Unauthorized: Only Student Org. Moderator can create events.');
            }
        }
        
        $request->validate([
            'title' => 'required|string|max:200',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        // Normalize time values to HH:MM:SS for MySQL strict mode
        $startTime = $request->start_time ?? '00:00';
        $endTime = $request->end_time ?? '23:59';
        if (is_string($startTime) && preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime .= ':00';
        }
        if (is_string($endTime) && preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            $endTime .= ':00';
        }

        // Build start/end as DATETIME (use date + time)
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $startDateTime = $startDate . ' ' . ($startTime ?: '00:00:00');
        $endDateTime = $endDate . ' ' . ($endTime ?: '23:59:59');

        // Create event first (without QR code)
        $event = Event::create([
            'name' => $request->title,
            'event_date' => $startDate, // Use start_date as event_date
            'end_date' => $endDate,
            'location' => $request->location,
            'description' => $request->description,
            'organization_id' => $request->organization_id,
            'created_by' => auth()->id(),
            'status' => 'pending',
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'qr_code_path' => '', // will update after QR code is generated
        ]);

        // Generate QR code for attendance (handle errors gracefully)
        try {
            // Use request host for mobile accessibility (works with local IP like 192.168.x.x)
            $baseUrl = request()->getSchemeAndHttpHost();
            $qrData = $baseUrl . '/admin/staff/dashboard/StudentOrgModerator/event-management?event_id=' . $event->id;
            $qrFileName = 'event_qr_' . $event->id . '.svg';
            $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($qrData);
            \Illuminate\Support\Facades\Storage::disk('public')->put("qrcodes/{$qrFileName}", $svg);
            $event->update(['qr_code_path' => 'storage/qrcodes/' . $qrFileName]);
        } catch (\Exception $e) {
            Log::error('QR code generation failed for event ID ' . $event->id . ': ' . $e->getMessage());
            // Event is still created, but no QR code
        }
        return redirect()->route('admin.staff.dashboard.StudentOrgModerator.view-events')
            ->with('success', 'Event created! Awaiting approval.');
    }

    public function edit(Event $event)
    {
        $organizations = Organization::all();
        return view('admin.staff.dashboard.StudentOrgModerator.edit', compact('event', 'organizations'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'event_date' => 'required|date|after:today',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        // Combine date and time for start_time and end_time
        $startDateTime = $request->event_date . ' ' . $request->start_time;
        $endDateTime = $request->event_date . ' ' . $request->end_time;

        $event->update([
            'name' => $request->title,
            'event_date' => $request->event_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'description' => $request->description,
            'organization_id' => $request->organization_id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
        ]);

        // Regenerate QR code for attendance after update
        try {
            // Use request host for mobile accessibility (works with local IP like 192.168.x.x)
            $baseUrl = request()->getSchemeAndHttpHost();
            $qrData = $baseUrl . '/admin/staff/dashboard/StudentOrgModerator/event/' . $event->id . '/attendance';
            $qrFileName = 'event_qr_' . $event->id . '.svg';
            $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($qrData);
            \Illuminate\Support\Facades\Storage::disk('public')->put("qrcodes/{$qrFileName}", $svg);
            $event->update(['qr_code_path' => 'storage/qrcodes/' . $qrFileName]);
        } catch (\Exception $e) {
            Log::error('QR code regeneration failed for event ID ' . $event->id . ': ' . $e->getMessage());
            // Event is still updated, but no QR code
        }
        return redirect()->route('admin.staff.dashboard.StudentOrgModerator.view-events')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.staff.dashboard.StudentOrgModerator.view-events')
            ->with('success', 'Event deleted successfully.');
    }
    // ...existing code...
    public function qrcode(Event $event)
    {
        // Generate QR code data (URL or event info)
        // Use request host for mobile accessibility (works with local IP like 192.168.x.x)
        $baseUrl = request()->getSchemeAndHttpHost();
        $qrData = $baseUrl . '/admin/staff/dashboard/StudentOrgModerator/event/' . $event->id . '/attendance';
        // Use SVG format (doesn't require imagick extension)
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($qrData);
        return view('admin.staff.dashboard.StudentOrgModerator.qrcode', compact('event', 'qrCode'));
    }
}
