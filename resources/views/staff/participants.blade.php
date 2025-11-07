@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Participant Management</h2>

    <!-- Filter Form -->
    <div class="card mb-4">
        @if(auth()->user()->role == 4)
        <div class="col-md-2 mb-3">
            <a href="{{ route('admin.participants.export') }}?{{ http_build_query(request()->all()) }}" 
                class="btn btn-success w-100">Export</a>
        </div>
        @endif

        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Filter Participants</h5>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <select name="event_id" class="form-control">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select name="department_id" class="form-control" id="department-filter">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <select name="year_level" class="form-control">
                            <option value="">All Years</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('year_level') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('staff.participants.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Filtered Participants ({{ $participants->total() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Event</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participants as $part)
                        <tr>
                            <td>{{ $part->user->user_id }}</td>
                            <td>{{ $part->user->first_name }} {{ $part->user->last_name }}</td>
                            <td>{{ $part->user->department->name ?? 'N/A' }}</td>
                            <td>{{ $part->user->course->name ?? 'N/A' }}</td>
                            <td>{{ $part->user->year_level }}</td>
                            <td>{{ $part->event->title }}</td>
                            <td>
                                @if($part->qr_scanned)
                                    <span class="badge bg-success">Attended</span>
                                @else
                                    <span class="badge bg-warning text-dark">Registered</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No participants found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $participants->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection