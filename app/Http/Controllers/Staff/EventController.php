<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function create()
    {
        return view('staff.events.create');
    }
    // Staff event index: show events created by this staff
    public function index()
    {
        $staff = auth()->user();
        $events = \App\Models\Event::where('created_by', $staff->id)
            ->withCount('participants')
            ->latest('event_date')
            ->get();

        // Pending events for approval (status = 'pending')
        $pendingEvents = \App\Models\Event::where('status', 'pending')
            ->with('creator')
            ->latest('event_date')
            ->get();

        // Recent participants (for quick view)
        $participants = \App\Models\EventParticipant::whereHas('event', function($q) use ($staff) {
                $q->where('created_by', $staff->id);
            })
            ->with('user', 'event')
            ->latest()
            ->take(10)
            ->get();

        return view('staff.events.index', compact('events', 'pendingEvents', 'participants'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'event_date' => 'required|date|after:today',
            'location' => 'nullable|string|max:200',
            'description' => 'nullable|string',
        ]);

        $event = \App\Models\Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'created_by' => auth()->id(),
            'status' => 'pending',
        ]);

        // Add default requirements
        $requirements = ['Parent Consent', 'ID Picture', 'Registration Form'];
        foreach ($requirements as $req) {
            \App\Models\EventRequirement::create([
                'event_id' => $event->id,
                'requirement_name' => $req,
            ]);
        }

        return redirect()->back()->with('success', 'Event created! Awaiting admin approval.');
    }

    public function uploadFile($id, Request $request)
    {
        $event = \App\Models\Event::findOrFail($id);
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx,xls|max:10240', // 10MB
        ]);
        
        $file = $request->file('file');
        
        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $filename = time() . '_' . $filename; // Add timestamp to prevent conflicts
        
        // Use event ID instead of title for security
        $path = $file->storeAs('events/' . $event->id, $filename, 'public');
        
        \App\Models\EventRequirement::create([
            'event_id' => $event->id,
            'requirement_name' => $file->getClientOriginalName(), // Store original name
            'file_path' => $path,
        ]);
        
        return back()->with('success', 'File uploaded.');
    }

    public function downloadFile($id, $file)
    {
        $event = \App\Models\Event::findOrFail($id);
        
        // Sanitize filename to prevent path traversal
        $file = basename($file);
        
        // Use event ID instead of title for path to prevent directory traversal
        $path = storage_path('app/public/events/' . $event->id . '/' . $file);
        
        // Verify file exists and is within the allowed directory
        if (!file_exists($path)) {
            abort(404);
        }
        
        // Additional security: verify path is within expected directory
        $realPath = realpath($path);
        $basePath = realpath(storage_path('app/public/events/' . $event->id));
        
        if (!$realPath || !$basePath || !str_starts_with($realPath, $basePath)) {
            abort(404);
        }
        
        return response()->download($realPath, $file);
    }

    public function history(Request $request)
    {
        $query = \App\Models\Event::where('created_by', auth()->id());
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
        $events = $query->orderBy('event_date', 'desc')->paginate(10);
        $departments = \App\Models\Department::all();
        $courses = \App\Models\Course::all();
        return view('staff.events-history', compact('events', 'departments', 'courses'));
    }
}