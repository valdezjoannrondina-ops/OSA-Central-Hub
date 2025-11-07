<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaffMessage;
use App\Models\StaffMessageAttachment;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        // Only staff can access via middleware in routes
        $messages = StaffMessage::with(['user', 'replies.user', 'attachments'])
            ->whereNull('parent_id')
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return view('staff.community.index', compact('messages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:announcement,inquiry',
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:staff_messages,id',
            'mentions' => 'array',
            'mentions.*' => 'integer|exists:users,id',
            'attachments.*' => 'file|max:5120', // 5MB per file
        ]);

        // Temporary CSV mentions support until UI is upgraded
        if ($request->filled('mentions_csv') && empty($validated['mentions'])) {
            $ids = collect(explode(',', $request->input('mentions_csv')))
                ->map(fn($v) => (int) trim($v))
                ->filter();
            // Keep only existing user IDs
            $validated['mentions'] = \App\Models\User::whereIn('id', $ids)->pluck('id')->all();
        }

        $message = StaffMessage::create([
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'type' => $validated['type'],
            'content' => $validated['content'],
        ]);

        // Mentions
        if (!empty($validated['mentions'])) {
            $message->mentions()->sync($validated['mentions']);
        }

        // Attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('staff-community', 'public');
                StaffMessageAttachment::create([
                    'message_id' => $message->id,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return back()->with('success', 'Message posted.');
    }
}
