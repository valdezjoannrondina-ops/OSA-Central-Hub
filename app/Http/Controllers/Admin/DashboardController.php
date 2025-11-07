<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function showAssistantStaff(Request $request)
    {
        // Build base query for assistants: role in (1,3) or NULL
        $with = ['department', 'organization'];
        if (Schema::hasTable('organization_user')) {
            $with[] = 'otherOrganizations';
        }
        $query = \App\Models\User::with($with)
            ->where(function ($q) {
                $q->whereIn('role', [1, 3])
                  ->orWhereNull('role');
            });

        // Filters
        if ($request->filled('role_type')) {
            $roleType = $request->role_type;
            if ($roleType === 'assistant') {
                $query->where('role', 3);
            } elseif ($roleType === 'student') {
                $query->where('role', 1);
            } elseif ($roleType === 'none') {
                $query->whereNull('role');
            }
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('organization_id')) {
            $orgId = $request->organization_id;
            $query->where(function ($qq) use ($orgId) {
                $qq->where('organization_id', $orgId);
                if (Schema::hasTable('organization_user')) {
                    $qq->orWhereHas('otherOrganizations', function ($q2) use ($orgId) {
                        $q2->where('organizations.id', $orgId);
                    });
                }
            });
        }

        $assistantStaff = $query->orderBy('last_name')->paginate(15)->appends($request->query());
        $departments = \App\Models\Department::orderBy('name')->get();
        $organizations = \App\Models\Organization::orderBy('name')->get();

        return view('admin.show-assistant-staff', [
            'assistantStaff' => $assistantStaff,
            'departments' => $departments,
            'organizations' => $organizations,
            'filters' => $request->only(['role_type','department_id','organization_id'])
        ]);
    }

    public function showStudentsList(Request $request)
    {
        $query = \App\Models\User::with(['department', 'course'])
            ->where('role', 1);

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('last_name')->orderBy('first_name')->paginate(15)->appends($request->query());

        // Reference lists for filters
        $departments = \App\Models\Department::orderBy('name')->get();
        $courses = \App\Models\Course::orderBy('name')->get();

        return view('admin.show-students-list', [
            'students' => $students,
            'departments' => $departments,
            'courses' => $courses,
            'filters' => $request->only(['department_id','course_id','year_level','status'])
        ]);
    }

    public function showAdmins(Request $request)
    {
        $currentUser = auth()->user();
        
        $query = \App\Models\User::with(['department', 'organization'])
            ->where('role', 4)
            // Hide admin001 and all admin accounts from other admins/staff
            ->where('user_id', '!=', 'admin001');
        
        // If current user is not admin001, hide all admin accounts
        if (!$currentUser || $currentUser->user_id !== 'admin001') {
            // Hide all admin accounts (role 4) from non-admin001 users
            $query->whereRaw('1 = 0'); // This will return no results
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        $admins = $query->orderBy('last_name')->paginate(15)->appends($request->query());
        $departments = \App\Models\Department::orderBy('name')->get();
        $organizations = \App\Models\Organization::orderBy('name')->get();

        return view('admin.show-admins', [
            'admins' => $admins,
            'departments' => $departments,
            'organizations' => $organizations,
            'filters' => $request->only(['department_id','organization_id'])
        ]);
    }
    // ...existing code...
    public function profile()
    {
        $admin = auth()->user();
        return view('admin.profile', compact('admin'));
    }
    /**
     * Export filtered participants as CSV.
     */
    public function exportParticipants(Request $request)
    {
        $query = \App\Models\EventParticipant::with(['user', 'event']);
        if ($request->filled('date')) {
            $query->whereHas('event', function($q) use ($request) {
                $q->whereDate('event_date', $request->date);
            });
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('course_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('year_level')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('year_level', $request->year_level);
            });
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $participants = $query->orderBy('created_at', 'desc')->get();

        $csv = "Event,Participant,Date,Status\n";
        foreach ($participants as $p) {
            $csv .= sprintf(
                '"%s","%s %s","%s","%s"\n',
                $p->event->title ?? '-',
                $p->user->first_name ?? '-',
                $p->user->last_name ?? '',
                $p->event->event_date->format('Y-m-d') ?? '-',
                ucfirst($p->status)
            );
        }
        $filename = 'participants_export_' . now()->format('Ymd_His') . '.csv';
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=' . $filename);
    }
    /**
     * Show participant history with filters.
     */
    public function participantsHistory(Request $request)
    {
        $query = \App\Models\EventParticipant::with(['user', 'event']);
        if ($request->filled('date')) {
            $query->whereHas('event', function($q) use ($request) {
                $q->whereDate('event_date', $request->date);
            });
        }
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('course_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->filled('year_level')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('year_level', $request->year_level);
            });
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $participants = $query->orderBy('created_at', 'desc')->paginate(15);
        $events = \App\Models\Event::all();
        $users = \App\Models\User::all();
        $departments = \App\Models\Department::all();
        $courses = \App\Models\Course::all();
        return view('admin.participants-history', compact('participants', 'events', 'users', 'departments', 'courses'));
    }
    /**
     * Update event details (date, start time, end time).
     */
    public function updateEvent($id, Request $request)
    {
        $event = \App\Models\Event::findOrFail($id);
        // Ensure only the creator can update
        if ($event->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
        ]);

        $description = $request->description;

        // Normalize time values to HH:MM:SS for MySQL strict mode
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        if (is_string($startTime) && preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime .= ':00';
        }
        if (is_string($endTime) && preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            $endTime .= ':00';
        }

        // Build start/end as DATETIME
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $startDateTime = $startDate . ' ' . ($startTime ?: '00:00:00');
        $endDateTime = $endDate . ' ' . ($endTime ?: ($startTime ?: '23:59:59'));

        $event->update([
            'name' => $request->name,
            'description' => $description,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'location' => $request->location,
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroyEvent($id)
    {
        $event = \App\Models\Event::findOrFail($id);
        // Ensure only the creator can delete
        if ($event->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }
    /**
     * Show calendar with events that have all requirements approved.
     */
    public function calendar(Request $request)
    {
        // Get all approved events for the calendar
        $events = \App\Models\Event::where('status', 'approved')
            ->orderBy('start_time')
            ->get();
        
        // Group events by date for easier display
        $eventsByDate = $events->groupBy(function($event) {
            return \Carbon\Carbon::parse($event->start_time)->format('Y-m-d');
        });
        
        // Get year from request or use current year
        $year = $request->get('year', now()->year);
        
        // Organize events by semester for list view
        $firstSemStart = \Carbon\Carbon::createFromDate($year, 8, 31); // August 31
        $firstSemEnd = \Carbon\Carbon::createFromDate($year + 1, 1, 24); // January 24
        $secondSemStart = \Carbon\Carbon::createFromDate($year + 1, 2, 2); // February 2
        $secondSemEnd = \Carbon\Carbon::createFromDate($year + 1, 6, 18); // June 18
        $midyearStart = \Carbon\Carbon::createFromDate($year + 1, 7, 1); // July 1
        $midyearEnd = \Carbon\Carbon::createFromDate($year + 1, 8, 10); // August 10
        
        // Filter events for this academic year
        $yearEvents = $events->filter(function($event) use ($year) {
            $eventYear = \Carbon\Carbon::parse($event->start_time)->year;
            return $eventYear == $year || $eventYear == $year + 1;
        });
        
        // Organize events by name/description and semester
        $eventsByActivity = [];
        foreach ($yearEvents as $event) {
            $eventStart = \Carbon\Carbon::parse($event->start_time);
            $eventEnd = \Carbon\Carbon::parse($event->end_time);
            $activityKey = $event->name; // Use event name as activity key
            
            if (!isset($eventsByActivity[$activityKey])) {
                $eventsByActivity[$activityKey] = [
                    'name' => $event->name,
                    'description' => $event->description,
                    'first_sem' => [],
                    'second_sem' => [],
                    'midyear' => []
                ];
            }
            
            // Determine which semester(s) this event belongs to
            // First Semester: Aug 31 - Jan 24
            if ($eventStart->lte($firstSemEnd) && $eventEnd->gte($firstSemStart)) {
                $eventsByActivity[$activityKey]['first_sem'][] = [
                    'event' => $event,
                    'start' => $eventStart,
                    'end' => $eventEnd
                ];
            }
            
            // Second Semester: Feb 2 - Jun 18
            if ($eventStart->lte($secondSemEnd) && $eventEnd->gte($secondSemStart)) {
                $eventsByActivity[$activityKey]['second_sem'][] = [
                    'event' => $event,
                    'start' => $eventStart,
                    'end' => $eventEnd
                ];
            }
            
            // Midyear Term: Jul 1 - Aug 10
            if ($eventStart->lte($midyearEnd) && $eventEnd->gte($midyearStart)) {
                $eventsByActivity[$activityKey]['midyear'][] = [
                    'event' => $event,
                    'start' => $eventStart,
                    'end' => $eventEnd
                ];
            }
        }
        
        return view('admin.calendar', [
            'events' => $events,
            'eventsByDate' => $eventsByDate,
            'eventsByActivity' => $eventsByActivity,
            'year' => $year
        ]);
    }
    /**
     * Bulk upload event requirement files.
     */
    public function bulkUploadRequirements(Request $request)
    {
        $request->validate([
            'bulk_files' => 'required',
            'bulk_files.*' => 'file',
        ]);
        $uploaded = [];
        foreach ($request->file('bulk_files') as $file) {
            $path = $file->store('event_requirements', 'public');
            $uploaded[] = $path;
            // Optionally, associate with EventRequirement model here
        }
        return back()->with('success', count($uploaded) . ' files uploaded successfully.');
    }

    /**
     * Bulk download all event requirement files as a zip.
     */
    public function bulkDownloadRequirements()
    {
        $requirements = \App\Models\EventRequirement::whereNotNull('file_path')->get();
        if ($requirements->isEmpty()) {
            return back()->with('error', 'No event requirement files found to download.');
        }
        $zip = new \ZipArchive();
        $zipFile = storage_path('app/public/event_requirements.zip');
        $added = false;
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($requirements as $req) {
                $filePath = storage_path('app/public/' . $req->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                    $added = true;
                }
            }
            $zip->close();
        }
        if (!$added || !file_exists($zipFile)) {
            return back()->with('error', 'No event requirement files found to download.');
        }
        return response()->download($zipFile)->deleteFileAfterSend(true);
    }
    public function index()
    {
        $pendingEvents = \App\Models\Event::where('status', 'pending')->with('creator')->get();
        $approvedEvents = \App\Models\Event::where('status', 'approved')->with('creator')->get();
        $staff = \App\Models\User::where('role', 2)->get();
        $appointments = \App\Models\Appointment::where('status', 'pending')->with('user', 'assignedStaff')->get();
        
        return view('admin.dashboard', compact('pendingEvents', 'approvedEvents', 'staff', 'appointments'));
    }
    
    /**
     * Display organizational structure page
     */
    public function organizationalStructure(Request $request)
    {
        $organizationId = $request->get('organization_id');
        
        if ($organizationId) {
            // Show Staff → Assistants structure for specific organization
            $organization = \App\Models\Organization::findOrFail($organizationId);
            $orgStructure = $this->buildOrgStaffAssistantsStructure($organizationId);
            $structureType = 'organization';
            
            return view('admin.organizational-structure', compact('orgStructure', 'organization', 'structureType'));
        } else {
            // Show Admin → Staff structure
            $orgStructure = $this->buildAdminStaffOrgStructure();
            $structureType = 'admin';
            
            return view('admin.organizational-structure', compact('orgStructure', 'structureType'));
        }
    }
    
    /**
     * Update organizational structure configuration
     */
    public function updateOrgStructureConfig(Request $request)
    {
        $request->validate([
            'staff_selections' => 'required|array',
            'staff_selections.*' => 'array',
            'staff_selections.*.*' => 'required|integer|exists:staff,id',
            'max_levels' => 'required|integer|min:1|max:10'
        ]);
        
        $config = \App\Models\OrgStructureConfig::getDefaultConfig();
        $config->staff_selections = $request->staff_selections;
        $config->max_levels = $request->max_levels;
        $config->save();
        
        return redirect()->back()->with('success', 'Organizational structure configuration updated successfully!');
    }

    public function showStaff()
    {
        $currentUser = auth()->user();
        
    $staff = \App\Models\Staff::with(['department', 'organizations', 'admin'])->get();
        $assistants = \App\Models\User::where('role', 3)->orderBy('last_name')->orderBy('first_name')->get();
        $students = \App\Models\User::where('role', 1)->orderBy('last_name')->orderBy('first_name')->get();
        
        // Hide admin accounts from other admins/staff
        $adminsQuery = \App\Models\User::where('role', 4);
        if (!$currentUser || $currentUser->user_id !== 'admin001') {
            // Hide all admin accounts (including admin001) from non-admin001 users
            $adminsQuery->whereRaw('1 = 0'); // This will return no results
        }
        $admins = $adminsQuery->orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.show-staff', compact('staff', 'assistants', 'students', 'admins'));
    }

    /**
     * Show organizations management page
     */
    public function organizations()
    {
        $organizations = \App\Models\Organization::with('department')->orderBy('name')->get();
        return view('admin.organizations', compact('organizations'));
    }

    /**
     * Update organization official email
     */
    public function updateOrganizationEmail(Request $request, $id)
    {
        $request->validate([
            'official_email' => 'required|email|max:255',
        ]);

        $organization = \App\Models\Organization::findOrFail($id);
        $organization->update([
            'official_email' => $request->official_email,
        ]);

        return back()->with('success', 'Official email updated successfully.');
    }

    /**
     * Show organization profile with student count
     */
    public function organizationProfile($id)
    {
        $organization = \App\Models\Organization::with(['department', 'users', 'otherUsers'])->findOrFail($id);
        
        // Count all students (role 1) who belong to this organization
        // This includes both direct users and users via pivot table
        $directStudents = $organization->users()->where('role', 1)->get();
        $pivotStudents = $organization->otherUsers()->where('role', 1)->get();
        
        // Combine and get unique students
        $allStudents = $directStudents->merge($pivotStudents)->unique('id');
        
        $studentCount = $allStudents->count();
        
        // Get organization files grouped by category
        $files = \App\Models\OrganizationFile::where('organization_id', $id)
            ->with('uploader')
            ->get()
            ->groupBy('file_category');
        
        // Define required file categories
        $requiredFileCategories = [
            'accreditation_checklist' => 'Accreditation of New Organization Checklist',
            'application_letter' => 'Application Letter for Student Organization',
            'accreditation_form' => 'FM-USTP-OSA-02 Application for Accreditation/Reaccreditation of School Organization',
            'concept_paper' => 'Concept Paper_Student Organization',
            'constitution' => 'Constitution and By-laws_for (Org.Name)',
            'organizational_profile' => 'FM-USTP-OSA-03 Organizational Profile',
            'officers_members_list' => 'List of Officers and Members',
            'personal_data_sheet_assistant' => 'Personal Data Sheet (each assistant staff)',
            'personal_data_sheet' => 'Organizational Structure',
            'moderatorship_letter' => 'Moderatorship-Acceptance Letter',
        ];
        
        return view('admin.organization-profile', compact('organization', 'studentCount', 'allStudents', 'files', 'requiredFileCategories'));
    }

    public function approveEvent($id)
    {
        $event = \App\Models\Event::with('organization')->findOrFail($id);
        $event->update(['status' => 'approved']);
        
        // Notify assigned staff and admin
        // Get staff/admins for notifications - hide admin accounts from non-admin001 users
        $currentUser = auth()->user();
        $staffQuery = \App\Models\User::where('id', $event->created_by);
        // Only include admins if current user is admin001
        if ($currentUser && $currentUser->user_id === 'admin001') {
            $staffQuery->orWhere('role', 4);
        }
        $staff = $staffQuery->get();
        foreach ($staff as $user) {
            $user->notify(new \App\Notifications\EventApprovedNotification($event));
        }
        
        // Send email to organization's official email
        if ($event->organization && $event->organization->official_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($event->organization->official_email)
                    ->send(new \App\Mail\EventApprovedMail($event));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send event approval email to organization', [
                    'event_id' => $event->id,
                    'organization_id' => $event->organization->id,
                    'email' => $event->organization->official_email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return back()->with('success', 'Event approved and notifications sent.');
    }

    public function declineEvent(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $event = \App\Models\Event::with('organization')->findOrFail($id);
        $event->update([
            'status' => 'declined',
            'decline_reason' => $request->reason,
        ]);
        
        // Send email to organization's official email
        if ($event->organization && $event->organization->official_email) {
            try {
                \Illuminate\Support\Facades\Mail::to($event->organization->official_email)
                    ->send(new \App\Mail\EventDeclinedMail($event));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send event decline email to organization', [
                    'event_id' => $event->id,
                    'organization_id' => $event->organization->id,
                    'email' => $event->organization->official_email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return redirect()->route('admin.events.show', $event->id)->with('success', 'Event declined successfully. The event is now closed and cannot be edited or updated.');
    }

    public function addRequirement(Request $request, $id)
    {
        $request->validate([
            'requirement_name' => 'required|string|max:255',
        ]);

        $event = \App\Models\Event::with('organization')->findOrFail($id);
        
        // Prevent adding requirements if event is declined
        if ($event->status === 'declined') {
            return back()->with('error', 'Cannot add requirements to a declined event.');
        }

        \App\Models\EventRequirement::create([
            'event_id' => $event->id,
            'requirement_name' => $request->requirement_name,
            'is_uploaded' => false,
        ]);

        // Notify organization about missing requirements
        $this->notifyMissingRequirements($event);

        return back()->with('success', 'Requirement added successfully.');
    }

    /**
     * Notify organization about missing requirements for an event
     */
    private function notifyMissingRequirements($event)
    {
        if (!$event->organization || !$event->organization->official_email) {
            return;
        }

        // Default requirements list
        $defaultRequirements = [
            'Student Activity Request Form',
            'Program Flow',
            'Letters of Approval',
            'Financial Report',
            'Accomplishment Report'
        ];

        // Get forwarded requirements for this event
        $forwardedRequirements = $event->requirements()->pluck('requirement_name')->toArray();
        
        // Check which default requirements are missing
        $missingRequirements = collect($defaultRequirements)->filter(function($req) use ($forwardedRequirements) {
            return !in_array($req, $forwardedRequirements);
        });

        // Also check if admin-added requirements are not uploaded
        $adminRequirements = $event->requirements()
            ->whereNotIn('requirement_name', $defaultRequirements)
            ->where('is_uploaded', false)
            ->pluck('requirement_name');

        $allMissing = $missingRequirements->merge($adminRequirements);

        // Send email if there are missing requirements
        if ($allMissing->isNotEmpty()) {
            try {
                \Illuminate\Support\Facades\Mail::to($event->organization->official_email)
                    ->send(new \App\Mail\EventRequirementsMissingMail($event, $allMissing));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send missing requirements email to organization', [
                    'event_id' => $event->id,
                    'organization_id' => $event->organization->id,
                    'email' => $event->organization->official_email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function appointments(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user && (int) $user->role === 4;
        $isStaff = $user && (int) $user->role === 2;
        
        // Get user designation
        $userDesignation = $user->designation 
            ?? optional($user->staffProfile)->designation 
            ?? \App\Models\Staff::where('email', $user->email)->value('designation');
        
        $isOSAStaff = $userDesignation && strcasecmp($userDesignation, 'OSA Staff') === 0;
        
        $query = \App\Models\Appointment::with(['user', 'assignedStaff'])
            ->orderBy('created_at', 'desc');

        // Initialize filterAssigned to avoid undefined variable error
        $filterAssigned = null;

        // If staff (including Admission Services Officer or OSA Staff), show only appointments assigned to them
        if ($isStaff && !$isAdmin) {
            $query->where('assigned_staff_id', $user->id);
        } else {
            // Filter by assigned staff (for admins only)
        $filterAssigned = $request->query('assigned_staff_id');
        if ($request->has('assigned_staff_id') && $filterAssigned !== null && $filterAssigned !== '') {
            if ($filterAssigned === 'unassigned') {
                $query->whereNull('assigned_staff_id');
            } elseif (is_numeric($filterAssigned)) {
                $query->where('assigned_staff_id', (int) $filterAssigned);
                }
            }
        }

        $appointments = $query->paginate(10)->appends($request->query());
        $staffList = \App\Models\User::where('role', 2)->orderBy('first_name')->get();
        return view('admin.appointments', compact('appointments', 'staffList', 'filterAssigned', 'isOSAStaff', 'isAdmin', 'isStaff', 'userDesignation'));
    }

    public function approveAppointment($id, Request $request)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        
        // Check if user has permission to approve this appointment
        $user = auth()->user();
        $isAdmin = $user && (int) $user->role === 4;
        $userDesignation = $user->designation 
            ?? optional($user->staffProfile)->designation 
            ?? \App\Models\Staff::where('email', $user->email)->value('designation');
        $isOSAStaff = $userDesignation && strcasecmp($userDesignation, 'OSA Staff') === 0;
        
        // Only allow if admin or if staff (including Admission Services Officer or OSA Staff) and appointment is assigned to them
        $isStaff = $user && (int) $user->role === 2;
        if (!$isAdmin && !($isStaff && $appointment->assigned_staff_id == $user->id)) {
            return back()->with('error', 'Unauthorized: You do not have permission to approve this appointment.');
        }
        
        $appointment->update([
            'status' => 'approved',
            'action_taken' => 'approve',
        ]);

        // Refresh the appointment model to ensure we have the latest data
        $appointment->refresh();

        // Send email notification to the appointment email address
        if (!empty($appointment->email)) {
            try {
                // Send email to the appointment email address
                \Illuminate\Support\Facades\Mail::to($appointment->email)->send(new \App\Mail\AppointmentApprovedMail($appointment));
                
                \Illuminate\Support\Facades\Log::info('Approval email sent successfully', [
                    'appointment_id' => $appointment->id,
                    'email' => $appointment->email,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send approval email', [
                    'appointment_id' => $appointment->id,
                    'email' => $appointment->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        } else {
            \Illuminate\Support\Facades\Log::warning('Cannot send approval email: appointment email is empty', [
                'appointment_id' => $appointment->id,
            ]);
        }

        return back()->with('success', 'Appointment approved and email notification sent.');
    }

    public function declineAppointment($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:500',
        ]);

        $appointment = \App\Models\Appointment::findOrFail($id);
        
        // Check if user has permission to decline this appointment
        $user = auth()->user();
        $isAdmin = $user && (int) $user->role === 4;
        $userDesignation = $user->designation 
            ?? optional($user->staffProfile)->designation 
            ?? \App\Models\Staff::where('email', $user->email)->value('designation');
        $isOSAStaff = $userDesignation && strcasecmp($userDesignation, 'OSA Staff') === 0;
        
        // Only allow if admin or if staff (including Admission Services Officer or OSA Staff) and appointment is assigned to them
        $isStaff = $user && (int) $user->role === 2;
        if (!$isAdmin && !($isStaff && $appointment->assigned_staff_id == $user->id)) {
            return back()->with('error', 'Unauthorized: You do not have permission to decline this appointment.');
        }
        
        $appointment->update([
            'status' => 'declined',
            'action_taken' => 'decline',
            'action_reason' => $request->reason,
        ]);

        // Send email notification to the appointment email address
        if (!empty($appointment->email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->email)->send(new \App\Mail\AppointmentDeclinedMail($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send decline email', [
                    'appointment_id' => $appointment->id,
                    'email' => $appointment->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', 'Appointment declined and email notification sent.');
    }

    public function cancelAppointment($id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        $appointment->update(['status' => 'cancelled']);
        return back()->with('success', 'Appointment cancelled.');
    }

    public function reassignAppointment($id, Request $request)
    {
        $request->validate([
            'assigned_staff_id' => 'required|exists:users,id'
        ]);

        $appointment = \App\Models\Appointment::findOrFail($id);
        $previousStaffId = $appointment->assigned_staff_id;
        $appointment->update([
            'assigned_staff_id' => $request->assigned_staff_id,
            'status' => 'pending',
        ]);

        // Notify previous staff if exists and is different from new
        if ($previousStaffId && $previousStaffId != $request->assigned_staff_id) {
            $previousStaff = \App\Models\User::find($previousStaffId);
            if ($previousStaff) {
                $previousStaff->notify(new \App\Notifications\AppointmentReassignedNotification($appointment));
            }
        }

        return back()->with('success', 'Appointment reassigned. Previous staff notified.');
    }

    public function rescheduleAppointment($id, Request $request)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        $request->validate([
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'reschedule_reason' => 'nullable|string|max:500',
        ]);

        // Check if user has permission to reschedule this appointment
        $user = auth()->user();
        $isAdmin = $user && (int) $user->role === 4;
        $userDesignation = $user->designation 
            ?? optional($user->staffProfile)->designation 
            ?? \App\Models\Staff::where('email', $user->email)->value('designation');
        $isOSAStaff = $userDesignation && strcasecmp($userDesignation, 'OSA Staff') === 0;
        
        // Only allow if admin or if staff (including Admission Services Officer or OSA Staff) and appointment is assigned to them
        $isStaff = $user && (int) $user->role === 2;
        if (!$isAdmin && !($isStaff && $appointment->assigned_staff_id == $user->id)) {
            return back()->with('error', 'Unauthorized: You do not have permission to reschedule this appointment.');
        }

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'rescheduled', // Set status to rescheduled when rescheduling is successful
            'action_taken' => 'reschedule',
            'rescheduled_date' => $request->appointment_date,
            'rescheduled_time' => $request->appointment_time,
            'action_reason' => $request->reschedule_reason,
        ]);

        // Send email notification to the appointment email address
        if (!empty($appointment->email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($appointment->email)->send(new \App\Mail\AppointmentRescheduledMail($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send reschedule email', [
                    'appointment_id' => $appointment->id,
                    'email' => $appointment->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', 'Appointment rescheduled successfully.');
    }

    public function events(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user && (int) $user->role === 4;
        
        // Get all staff user IDs (role 2)
        $staffUserIds = \App\Models\User::where('role', 2)->pluck('id');
        
        // Base query for staff-created events
        $baseQuery = \App\Models\Event::whereIn('created_by', $staffUserIds)
            ->with(['creator', 'organization', 'requirements', 'participants']);
        
        // Apply filters if provided
        $filteredQuery = clone $baseQuery;
        
        if ($request->filled('search')) {
            $search = $request->search;
            $filteredQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('description')) {
            $filteredQuery->where('description', $request->description);
        }
        
        if ($request->filled('organization_id')) {
            $filteredQuery->where('organization_id', $request->organization_id);
        }
        
        // 1. Pending Events - events created by staff but still need approval
        $pendingEvents = (clone $filteredQuery)
            ->where('status', 'pending')
            ->orderBy('start_time', 'asc')
            ->get();
        
        // 2. Upcoming Events - approved events, arranged by date (soonest first)
        $upcomingEvents = (clone $filteredQuery)
            ->where('status', 'approved')
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();
        
        // 3. Most Recent Events - events that were conducted most recently
        // Get events that have concluded (end_time < now()), ordered by most recent first
        $mostRecentEvents = (clone $filteredQuery)
            ->where('end_time', '<', now())
            ->whereNotNull('end_time')
            ->orderBy('end_time', 'desc')
            ->get();
        
        // 4. Created Events - categorized into approved and declined
        $approvedCreatedEvents = (clone $filteredQuery)
            ->where('status', 'approved')
            ->orderBy('start_time', 'desc')
            ->get();
        
        $declinedCreatedEvents = (clone $filteredQuery)
            ->where('status', 'declined')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $organizations = \App\Models\Organization::all();
        
        // Get descriptions from staff-created events only
        $descriptions = \App\Models\Event::whereIn('created_by', $staffUserIds)
            ->distinct()
            ->pluck('description')
            ->filter()
            ->sort();
        
        return view('admin.events', compact(
            'pendingEvents',
            'upcomingEvents',
            'mostRecentEvents',
            'approvedCreatedEvents',
            'declinedCreatedEvents',
            'organizations',
            'descriptions',
            'isAdmin'
        ));
    }

    /**
     * Helper method to build filtered query
     */
    private function buildFilteredQuery(Request $request)
    {
        $staffUserIds = \App\Models\User::where('role', 2)->pluck('id');
        
        $query = \App\Models\Event::whereIn('created_by', $staffUserIds)
            ->with(['creator', 'organization', 'requirements', 'participants']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('description')) {
            $query->where('description', $request->description);
        }
        
        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }
        
        return $query;
    }

    public function pendingEvents(Request $request)
    {
        $isAdmin = auth()->user() && (int) auth()->user()->role === 4;
        $query = $this->buildFilteredQuery($request);
        
        $events = $query->where('status', 'pending')
            ->orderBy('start_time', 'asc')
            ->paginate(15)
            ->withQueryString();
        
        $organizations = \App\Models\Organization::all();
        $staffUserIds = \App\Models\User::where('role', 2)->pluck('id');
        $descriptions = \App\Models\Event::whereIn('created_by', $staffUserIds)
            ->where('status', 'pending')
            ->distinct()
            ->pluck('description')
            ->filter()
            ->sort();
        
        return view('admin.events.pending-events', compact('events', 'organizations', 'descriptions', 'isAdmin'));
    }

    public function upcomingEvents(Request $request)
    {
        $isAdmin = auth()->user() && (int) auth()->user()->role === 4;
        $query = $this->buildFilteredQuery($request);
        
        $events = $query->where('status', 'approved')
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->paginate(15)
            ->withQueryString();
        
        $organizations = \App\Models\Organization::all();
        $staffUserIds = \App\Models\User::where('role', 2)->pluck('id');
        $descriptions = \App\Models\Event::whereIn('created_by', $staffUserIds)
            ->where('status', 'approved')
            ->distinct()
            ->pluck('description')
            ->filter()
            ->sort();
        
        return view('admin.events.upcoming-events', compact('events', 'organizations', 'descriptions', 'isAdmin'));
    }

    public function recentEvents(Request $request)
    {
        $isAdmin = auth()->user() && (int) auth()->user()->role === 4;
        $query = $this->buildFilteredQuery($request);
        
        $events = $query->where('end_time', '<', now())
            ->whereNotNull('end_time')
            ->orderBy('end_time', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        $organizations = \App\Models\Organization::all();
        $staffUserIds = \App\Models\User::where('role', 2)->pluck('id');
        $descriptions = \App\Models\Event::whereIn('created_by', $staffUserIds)
            ->whereNotNull('end_time')
            ->where('end_time', '<', now())
            ->distinct()
            ->pluck('description')
            ->filter()
            ->sort();
        
        return view('admin.events.recent-events', compact('events', 'organizations', 'descriptions', 'isAdmin'));
    }

    public function createdEvents(Request $request)
    {
        $isAdmin = auth()->user() && (int) auth()->user()->role === 4;
        $query = $this->buildFilteredQuery($request);
        
        $approvedQuery = clone $query;
        $declinedQuery = clone $query;
        
        $approvedEvents = $approvedQuery->where('status', 'approved')
            ->orderBy('start_time', 'desc')
            ->paginate(15, ['*'], 'approved_page')
            ->withQueryString();
        
        $declinedEvents = $declinedQuery->where('status', 'declined')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'declined_page')
            ->withQueryString();
        
        $organizations = \App\Models\Organization::all();
        $staffUserIds = \App\Models\User::where('role', 2)->pluck('id');
        $descriptions = \App\Models\Event::whereIn('created_by', $staffUserIds)
            ->distinct()
            ->pluck('description')
            ->filter()
            ->sort();
        
        return view('admin.events.created-events', compact('approvedEvents', 'declinedEvents', 'organizations', 'descriptions', 'isAdmin'));
    }

    public function createEvent()
    {
        // Simple form for admin to add events directly as approved
        return view('admin.events-create');
    }

    public function showEvent($id)
    {
        $event = \App\Models\Event::with(['creator', 'organization', 'requirements.uploader'])
            ->findOrFail($id);
        
        // Default requirements list
        $defaultRequirements = [
            'Student Activity Request Form',
            'Program Flow',
            'Letters of Approval',
            'Financial Report',
            'Accomplishment Report'
        ];
        
        // Get forwarded requirements for this event
        $forwardedRequirements = $event->requirements()->orderBy('created_at', 'asc')->get();
        
        // Check if event is declined
        $isDeclined = $event->status === 'declined';
        
        return view('admin.events.show', compact('event', 'defaultRequirements', 'forwardedRequirements', 'isDeclined'));
    }

    /**
     * Notify organization about missing requirements (public method for manual notification)
     */
    public function notifyOrganizationRequirements(Request $request, $id)
    {
        $event = \App\Models\Event::with('organization')->findOrFail($id);
        
        // Prevent notifying if event is declined
        if ($event->status === 'declined') {
            return back()->with('error', 'Cannot notify about missing requirements for a declined event.');
        }

        $this->notifyMissingRequirements($event);

        return back()->with('success', 'Notification sent to organization about missing requirements.');
    }

    public function editEvent($id)
    {
        $event = \App\Models\Event::findOrFail($id);
        
        // Prevent editing if event is declined
        if ($event->status === 'declined') {
            return redirect()->route('admin.events.show', $event->id)
                ->with('error', 'Cannot edit a declined event. The event is considered closed.');
        }
        
        // Ensure only the creator can edit
        if ($event->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        $organizations = \App\Models\Organization::all();
        return view('admin.events-edit', compact('event', 'organizations'));
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:255',
        ]);

        $description = $request->description;

        // Normalize time values to HH:MM:SS for MySQL strict mode
        $startTime = $request->start_time;
        $endTime = $request->end_time;
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
        $endDateTime = $endDate . ' ' . ($endTime ?: ($startTime ?: '23:59:59'));

        // Check for duplicate events (same name with overlapping dates)
        $duplicates = \App\Models\Event::where('name', $request->name)
            ->where(function($query) use ($startDateTime, $endDateTime) {
                // Check for overlapping date ranges - two date ranges overlap if:
                // start1 <= end2 AND start2 <= end1
                $query->where('start_time', '<=', $endDateTime)
                      ->where('end_time', '>=', $startDateTime);
            })
            ->get();

        // If duplicates found, show selection page
        if ($duplicates->isNotEmpty()) {
            // Store the new event data in session for later use
            session([
                'pending_event' => [
                    'name' => $request->name,
                    'description' => $description,
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'location' => $request->location,
                ]
            ]);
            
            return view('admin.events-duplicate', [
                'duplicates' => $duplicates,
                'newEvent' => [
                    'name' => $request->name,
                    'description' => $description,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => $request->location,
                    'start_datetime' => $startDateTime,
                    'end_datetime' => $endDateTime,
                ]
            ]);
        }

        $event = new \App\Models\Event();
        $event->fill([
            'name' => $request->name,
            'description' => $description,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'location' => $request->location,
            'qr_code_path' => '',
            'status' => 'approved',
            'created_by' => auth()->id(),
        ]);
        $event->save();
        // Generate QR code for the event and store path
        try {
            $payload = [
                'event_id' => $event->id,
                'name' => $event->name,
                'start_date' => \Carbon\Carbon::parse($event->start_time)->format('Y-m-d'),
                'end_date' => \Carbon\Carbon::parse($event->end_time)->format('Y-m-d'),
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'location' => $event->location,
                'created_at' => now()->toIso8601String(),
            ];
            $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate(json_encode($payload));
            $path = 'qr/events/'.$event->id.'.svg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $svg);
            $event->qr_code_path = $path;
            $event->save();
        } catch (\Throwable $e) {
            // Non-fatal: continue without blocking event creation
        }

        return redirect()->route('admin.events.create')->with('success', 'Event created and approved successfully.');
    }

    /**
     * Handle duplicate event resolution - user chooses which event to keep
     */
    public function resolveDuplicate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:keep_new,keep_existing,keep_both',
            'existing_event_ids' => 'required_if:action,keep_existing|array',
            'existing_event_ids.*' => 'exists:events,id',
        ]);

        $pendingEvent = session('pending_event');
        
        if (!$pendingEvent) {
            return redirect()->route('admin.events.create')
                ->with('error', 'Session expired. Please try creating the event again.');
        }

        if ($request->action === 'keep_new') {
            // Delete all duplicate existing events
            $duplicateIds = $request->input('existing_event_ids', []);
            if (!empty($duplicateIds)) {
                \App\Models\Event::whereIn('id', $duplicateIds)->delete();
            }
            
            // Create the new event
            $event = new \App\Models\Event();
            $event->fill([
                'name' => $pendingEvent['name'],
                'description' => $pendingEvent['description'],
                'start_time' => $pendingEvent['start_time'],
                'end_time' => $pendingEvent['end_time'],
                'location' => $pendingEvent['location'],
                'qr_code_path' => '',
                'status' => 'approved',
                'created_by' => auth()->id(),
            ]);
            $event->save();
            
            // Generate QR code
            try {
                $payload = [
                    'event_id' => $event->id,
                    'name' => $event->name,
                    'start_date' => \Carbon\Carbon::parse($event->start_time)->format('Y-m-d'),
                    'end_date' => \Carbon\Carbon::parse($event->end_time)->format('Y-m-d'),
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'location' => $event->location,
                    'created_at' => now()->toIso8601String(),
                ];
                $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate(json_encode($payload));
                $path = 'qr/events/'.$event->id.'.svg';
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $svg);
                $event->qr_code_path = $path;
                $event->save();
            } catch (\Throwable $e) {
                // Non-fatal
            }
            
            session()->forget('pending_event');
            return redirect()->route('admin.events.create')
                ->with('success', 'Event created successfully. Duplicate events were removed.');
        }
        
        if ($request->action === 'keep_existing') {
            // Don't create new event, just keep existing ones
            session()->forget('pending_event');
            return redirect()->route('admin.events.create')
                ->with('info', 'Event creation cancelled. Existing events were kept.');
        }
        
        if ($request->action === 'keep_both') {
            // Create new event even though duplicates exist
            $event = new \App\Models\Event();
            $event->fill([
                'name' => $pendingEvent['name'],
                'description' => $pendingEvent['description'],
                'start_time' => $pendingEvent['start_time'],
                'end_time' => $pendingEvent['end_time'],
                'location' => $pendingEvent['location'],
                'qr_code_path' => '',
                'status' => 'approved',
                'created_by' => auth()->id(),
            ]);
            $event->save();
            
            // Generate QR code
            try {
                $payload = [
                    'event_id' => $event->id,
                    'name' => $event->name,
                    'start_date' => \Carbon\Carbon::parse($event->start_time)->format('Y-m-d'),
                    'end_date' => \Carbon\Carbon::parse($event->end_time)->format('Y-m-d'),
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'location' => $event->location,
                    'created_at' => now()->toIso8601String(),
                ];
                $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate(json_encode($payload));
                $path = 'qr/events/'.$event->id.'.svg';
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $svg);
                $event->qr_code_path = $path;
                $event->save();
            } catch (\Throwable $e) {
                // Non-fatal
            }
            
            session()->forget('pending_event');
            return redirect()->route('admin.events.create')
                ->with('success', 'Event created successfully. Both existing and new events are kept.');
        }

        return redirect()->route('admin.events.create')
            ->with('error', 'Invalid action selected.');
    }

    public function eventsHistory(Request $request)
    {
        $query = \App\Models\Event::with(['creator', 'requirements', 'participants']);
        if ($request->filled('date')) {
            $query->whereDate('event_date', $request->date);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $events = $query->orderBy('event_date', 'desc')->paginate(15);
        $departments = \App\Models\Department::all();
        $courses = \App\Models\Course::all();
        return view('admin.events-history', compact('events', 'departments', 'courses'));
    }

    /**
     * Build organizational structure: Admin → Staff
     * Structure:
     * - Level 0: Admin (admin002)
     * - Level 1: OSA Staff (directly below admin)
     * - Level 2: Staff designations (Guidance Counsellor, Admission Services Officer, Prefect of Discipline, Nurse, Librarian, Carriers Management Officer, EMT Coordinator) - directly connected to admin
     * - Level 3: Student Org. Moderators - directly connected to admin
     */
    private function buildAdminStaffOrgStructure()
    {
        $nodes = [];
        $edges = [];

        // Get admin002 as the head of the structure
        $adminHead = \App\Models\User::where('role', 4)
            ->where('user_id', 'admin002')
            ->first();
        
        // Get all staff (Staff model) with relationships
        $allStaff = \App\Models\Staff::with(['organizations', 'department', 'admin'])->get();
        
        // Define designation categories
        $designationCategories = [
            'OSA Staff',
            'Guidance Counsellor',
            'Admission Services Officer',
            'Prefect of Discipline',
            'Nurse',
            'Librarian',
            'Carriers Management Officer',
            'EMT Coordinator',
            'Student Org. Moderator'
        ];
        
        // Categorize staff by designation
        $osaStaff = $allStaff->filter(function($staff) {
            return strcasecmp($staff->designation ?? '', 'OSA Staff') === 0;
        });
        
        $designationStaff = $allStaff->filter(function($staff) {
            $designation = $staff->designation ?? '';
            return in_array(strtolower($designation), [
                'guidance counsellor',
                'admission services officer',
                'prefect of discipline',
                'nurse',
                'librarian',
                'carriers management officer',
                'safety officer'
            ], true);
        });
        
        $studentOrgModerators = $allStaff->filter(function($staff) {
            return strcasecmp($staff->designation ?? '', 'Student Org. Moderator') === 0;
        });

        // Add admin002 as top-level node (Level 0)
        if ($adminHead) {
            $adminName = trim(($adminHead->first_name ?? '') . ' ' . ($adminHead->last_name ?? ''));
            if (empty($adminName)) {
                $adminName = 'Admin 002';
            }
            $nodes[] = [
                'id' => 'admin-' . $adminHead->id,
                'label' => $adminName . '\n(Administrator)',
                'level' => 0,
                'group' => 'admin',
                'title' => $adminName . ' - Administrator'
            ];
            $adminNodeId = 'admin-' . $adminHead->id;
        } else {
            // Fallback: generic admin node
            $nodes[] = [
                'id' => 'admin-root',
                'label' => 'Administrator\n(Admin)',
                'level' => 0,
                'group' => 'admin',
                'title' => 'Administrator'
            ];
            $adminNodeId = 'admin-root';
        }
        
        // Helper function to create staff node
        $createStaffNode = function($staff, $level) use (&$nodes, &$edges, $adminNodeId) {
            $staffName = trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));
            if (empty($staffName)) {
                return null;
            }
            
            $designation = $staff->designation ?? 'Staff';
            $department = $staff->department ? $staff->department->name : 'No Department';
            
            // Get all affiliated organizations with IDs for clickable links
            $orgs = [];
            if ($staff->organizations && $staff->organizations->count() > 0) {
                foreach ($staff->organizations as $org) {
                    $orgs[] = [
                        'id' => $org->id,
                        'name' => $org->name
                    ];
                }
            }
            if ($staff->organization && $staff->organization->name) {
                $orgExists = false;
                foreach ($orgs as $org) {
                    if ($org['id'] == $staff->organization->id) {
                        $orgExists = true;
                        break;
                    }
                }
                if (!$orgExists) {
                    $orgs[] = [
                        'id' => $staff->organization->id,
                        'name' => $staff->organization->name
                    ];
                }
            }
            
            // Build organization names string for label
            $orgNamesString = !empty($orgs) ? implode(', ', array_column($orgs, 'name')) : 'No Organization';
            
            // Get image URL - use same pattern as other views
            $imageUrl = null;
            if ($staff->image) {
                // Use Storage::url() directly like other views do
                $imageUrl = \Illuminate\Support\Facades\Storage::url($staff->image);
            }
            
            // Build label with name and designation (will be styled with HTML)
            $label = $staffName . '\n' . $designation;
            
            // Build detailed title with all information for tooltip
            $title = $staffName . '\n\n' . 
                     'Designation: ' . $designation . '\n' . 
                     'Department: ' . $department . '\n' . 
                     'Organizations: ' . $orgNamesString;
            
            $nodeData = [
                'id' => 'staff-' . $staff->id,
                'label' => $label,
                'level' => $level,
                'group' => 'staff',
                'title' => $title,
                'staff_id' => $staff->id,
                'organizations' => $orgs, // Store org data for clickable links
                'image' => $imageUrl, // Always store image URL (even if null)
                'department' => $department // Store department for display
            ];
            
            $nodes[] = $nodeData;
            
            // Connect directly to admin
            $edges[] = [
                'from' => $adminNodeId,
                'to' => 'staff-' . $staff->id
            ];
            
            return 'staff-' . $staff->id;
        };
        
        // Level 1: OSA Staff
        foreach ($osaStaff as $staff) {
            $createStaffNode($staff, 1);
        }
        
        // Level 2: Staff with specific designations (directly connected to admin)
        foreach ($designationStaff as $staff) {
            $createStaffNode($staff, 2);
        }
        
        // Level 3: Student Org. Moderators (directly connected to admin)
        foreach ($studentOrgModerators as $staff) {
            $createStaffNode($staff, 3);
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges
        ];
    }

    /**
     * Build organizational structure: Staff → Assistants (for specific organization)
     */
    public function buildOrgStaffAssistantsStructure($organizationId)
    {
        $nodes = [];
        $edges = [];

        $organization = \App\Models\Organization::findOrFail($organizationId);

        // Get staff assigned to this organization
        $staffRecords = \App\Models\Staff::where('organization_id', $organizationId)
            ->orWhereHas('organizations', function($q) use ($organizationId) {
                $q->where('organizations.id', $organizationId);
            })
            ->with(['organizations', 'department'])
            ->get();

        // Get assistant assignments for this organization
        $assistants = \App\Models\AssistantAssignment::where('organization_id', $organizationId)
            ->where('active', true)
            ->with(['user', 'supervisor', 'organization'])
            ->get();

        // Add organization as root node
        $nodes[] = [
            'id' => 'org-' . $organization->id,
            'label' => $organization->name . '\n(Organization)',
            'level' => 0,
            'group' => 'organization',
            'title' => $organization->name . ' - Organization'
        ];

        // Add staff nodes
        foreach ($staffRecords as $staff) {
            $staffName = trim(($staff->first_name ?? '') . ' ' . ($staff->last_name ?? ''));
            if (empty($staffName)) {
                continue;
            }
            $designation = $staff->designation ?? 'Staff';
            
            $nodes[] = [
                'id' => 'staff-' . $staff->id,
                'label' => $staffName . '\n' . $designation,
                'level' => 1,
                'group' => 'staff',
                'title' => $staffName . ' - ' . $designation
            ];

            // Connect staff to organization
            $edges[] = [
                'from' => 'org-' . $organization->id,
                'to' => 'staff-' . $staff->id
            ];
        }

        // Add assistant nodes with hierarchy
        foreach ($assistants as $assistant) {
            if (!$assistant->user) {
                continue;
            }
            
            $assistantName = trim(($assistant->user->first_name ?? '') . ' ' . ($assistant->user->last_name ?? ''));
            if (empty($assistantName)) {
                continue;
            }
            
            $position = $assistant->position ?? 'Member';
            
            $nodes[] = [
                'id' => 'asst-' . $assistant->id,
                'label' => $assistantName . '\n' . $position,
                'level' => 2,
                'group' => 'assistant',
                'title' => $assistantName . ' - ' . $position
            ];

            // Connect to supervisor
            if ($assistant->supervisor_id) {
                // Check if supervisor is a Staff (by email matching)
                $supervisorUser = \App\Models\User::find($assistant->supervisor_id);
                $supervisorStaff = null;
                
                if ($supervisorUser) {
                    // Find staff by email match
                    $supervisorStaff = \App\Models\Staff::whereRaw('LOWER(email) = ?', [strtolower(trim($supervisorUser->email))])
                        ->where(function($q) use ($organizationId) {
                            $q->where('organization_id', $organizationId)
                              ->orWhereHas('organizations', function($q2) use ($organizationId) {
                                  $q2->where('organizations.id', $organizationId);
                              });
                        })
                        ->first();
                }

                if ($supervisorStaff) {
                    $edges[] = [
                        'from' => 'staff-' . $supervisorStaff->id,
                        'to' => 'asst-' . $assistant->id
                    ];
                } else {
                    // Check if supervisor is another assistant
                    $supervisorAsst = \App\Models\AssistantAssignment::where('user_id', $assistant->supervisor_id)
                        ->where('organization_id', $organizationId)
                        ->where('active', true)
                        ->first();
                    
                    if ($supervisorAsst) {
                        $edges[] = [
                            'from' => 'asst-' . $supervisorAsst->id,
                            'to' => 'asst-' . $assistant->id
                        ];
                    } else {
                        // Connect to organization if no supervisor found
                        $edges[] = [
                            'from' => 'org-' . $organization->id,
                            'to' => 'asst-' . $assistant->id
                        ];
                    }
                }
            } else {
                // Connect to organization if no supervisor
                $edges[] = [
                    'from' => 'org-' . $organization->id,
                    'to' => 'asst-' . $assistant->id
                ];
            }
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges
        ];
    }
}