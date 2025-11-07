@extends('layouts.app')

@section('title', 'Admin Messages')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('admin.partials.sidebar')
        <main class="col-md-10 py-4">
        <h2>Messages to Staff</h2>
    {{-- Messaging UI for admin to all staff --}}
    <form method="POST" action="{{ route('admin.messages.send') }}">
        @csrf
        <div class="mb-3">
            <label for="recipient_id" class="form-label">Select Staff</label>
            <select name="recipient_id" id="recipient_id" class="form-select">
                @foreach($staff as $user)
                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Message</label>
            <textarea name="content" id="content" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
    <hr>
    <h3>Message History <span id="notif-badge" class="badge bg-danger" style="display:none;">New</span></h3>
    <div id="messages-list">
        @foreach($messages as $msg)
            <div class="mb-2">
                <strong>From:</strong> {{ $msg->sender->first_name }} {{ $msg->sender->last_name }}<br>
                <strong>To:</strong> {{ $msg->recipient->first_name }} {{ $msg->recipient->last_name }}<br>
                <span>{{ $msg->content }}</span><br>
                <small class="text-muted">{{ $msg->created_at->format('M d, Y H:i') }}</small>
                <form method="POST" action="{{ route('admin.messages.send') }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $msg->sender_id }}">
                    <input type="hidden" name="reply_to" value="{{ $msg->id }}">
                    <input type="text" name="content" class="form-control mb-1" placeholder="Reply...">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Reply</button>
                </form>
                @if($msg->replies)
                    <div class="ms-4 mt-1">
                        @foreach($msg->replies as $reply)
                            <div class="mb-1">
                                <strong>Reply:</strong> {{ $reply->content }} <small class="text-muted">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <script>
    // Simple AJAX polling for real-time updates
    setInterval(function() {
        fetch("{{ route('admin.messages.index') }}", { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                // Compare message count for notification
                let currentCount = document.querySelectorAll('#messages-list > div').length;
                if (data.messages.length > currentCount) {
                    document.getElementById('notif-badge').style.display = '';
                }
                // Optionally, update message list (for full real-time)
                // ...
            });
    }, 5000);
        </script>
        </main>
    </div>
</div>
@endsection
