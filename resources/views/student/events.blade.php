
@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar: Quick Actions -->
        <aside class="col-md-3 d-flex align-items-start">
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
                    <div class="mb-3">
                        <h5>Organizational Dashboard</h5>
                        <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#assistantSwitchModal">Open</button>
                    </div>
                    <div class="mb-3">
                        <h5>My QR Code</h5>
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#qrModal">View QR</button>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="col-md-9">
            <div class="dashboard-header text-center mb-4">
                <h1>Events</h1>
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary mt-3">&larr; Return to Dashboard</a>
            </div>
            <div class="page-section">
                <!-- Place all events main content here -->
                @yield('events-content')
            </div>
        </main>
    </div>
</div>
