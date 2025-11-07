<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $staff = \App\Models\User::where('role', 2)->get();
        $adminId = auth()->id();
        $messages = \App\Models\Message::where(function($q) use ($adminId) {
            $q->where('sender_id', $adminId)->orWhere('recipient_id', $adminId);
        })->orderBy('created_at', 'desc')->get();

        // Mark unread messages as read
        \App\Models\Message::where('recipient_id', $adminId)->where('is_read', false)->update(['is_read' => true]);

        // For AJAX polling (real-time updates)
        if ($request->ajax()) {
            return response()->json(['messages' => $messages]);
        }

        return view('admin.messages.index', compact('staff', 'messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'reply_to' => 'nullable|exists:messages,id',
        ]);
        $message = new \App\Models\Message();
        $message->sender_id = auth()->id();
        $message->recipient_id = $request->recipient_id;
        $message->content = $request->content;
        $message->reply_to = $request->reply_to;
        $message->save();

        // Notification logic (simple database notification)
        $recipient = \App\Models\User::find($request->recipient_id);
        if ($recipient) {
            $recipient->notify(new \App\Notifications\NewMessageNotification($message));
        }

        return back()->with('success', 'Message sent.');
    }
}
