@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar: Quick Actions -->
        <div class="col-md-3 d-flex align-items-start">
            <div class="card mb-4 w-100" style="margin-top: 3.5rem;">
                <div class="card-header bg-primary text-white" style="text-align: center; font-size: 1.5rem; padding-top: 0.7rem; padding-bottom: 0.7rem;">Quick Actions</div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Book Appointment</h5>
                        <a href="{{ route('student.make-appointment') }}" class="btn btn-primary w-100">Book an Appointment</a>
                    </div>
                    <div class="mb-3">
                        <h5>View Events</h5>
                        <a href="{{ route('student.events.index') }}" class="btn btn-secondary w-100">See Upcoming</a>
                    </div>
                    <div class="mb-3">
                        <h5>Organization Registration Request</h5>
                        <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#orgRegModal">Request Organization Registration</button>
                    </div>
                    @if(auth()->user()->designation === 'assistant-staff')
                    <div class="mb-3">
                        <h5>Organizational Dashboard</h5>
                        <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#assistantSwitchModal">Open</button>
                    </div>
                    @endif
                    <div class="mb-3">
                        <h5>My QR Code</h5>
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#qrModal">View QR</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-header text-center">
                <h1>My Profile</h1>
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary mt-3">&larr; Return to Dashboard</a>
            </div>
            <div class="page-section">
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">My Profile</h4>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Student ID</dt>
                            <dd class="col-sm-9">{{ $user->user_id }}</dd>
                            <dt class="col-sm-3">Name</dt>
                            <dd class="col-sm-9">{{ $user->first_name }} {{ $user->last_name }}</dd>
                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9">{{ $user->email }}</dd>
                            <!-- ...existing profile fields... -->
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <dt class="col-sm-3">Gender</dt>
                <dd class="col-sm-9">{{ ucfirst($user->gender) }}</dd>

                <dt class="col-sm-3">Birth Date</dt>
                <dd class="col-sm-9">{{ $user->birth_date }}</dd>

                <dt class="col-sm-3">Contact Number</dt>
                <dd class="col-sm-9">{{ $user->contact_number }}</dd>

                <dt class="col-sm-3">Emergency Contact</dt>
                <dd class="col-sm-9">{{ $user->emergency_contact_name }} ({{ $user->emergency_contact_number }})</dd>
                <hr>
                <h5>Change Password</h5>
                <form method="POST" action="{{ route('student.change-password') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </dl>
        </div>
    </div>
</div>

@endsection
