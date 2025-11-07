@extends('layouts.app')

@section('title', 'Assistant Staff Dashboard')

@section('content')
<div class="mb-3">
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">Back to Student Dashboard</a>
    </div>
<div class="container">
    <div class="dashboard-header text-center">
        <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
        <p>Assistant Staff Dashboard</p>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>My Events</h5>
                    <a href="{{ route('assistant.events.index') }}" class="btn btn-secondary mt-2">View Events</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Participants History</h5>
                    <a href="{{ route('assistant.participants.history') }}" class="btn btn-info mt-2">Open History</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Messages</h5>
                    <a href="{{ route('assistant.messages.index') }}" class="btn btn-success mt-2">Open Inbox</a>
                </div>
            </div>
        </div>
    </div>

    <!-- End of content -->
</div>

@endsection
