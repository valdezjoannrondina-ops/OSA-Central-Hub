@extends('layouts.app')

@php
    $designation = auth()->user()->designation ?? optional(auth()->user()->staffProfile)->designation ?? null;
    $fullName = trim((auth()->user()->first_name ?? '') . ' ' . (auth()->user()->last_name ?? ''));
    $computedTitle = $designation ? ($designation . ' — ' . $fullName) : $fullName;
@endphp
@section('title', $computedTitle)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main id="adminMain" class="col-12">
            

            <!-- Quick Actions -->
            <div class="mb-4 wow fadeInUp" data-wow-delay="100ms">
                <style>
                    .quick-actions-container {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 2rem;
                        justify-content: center;
                        padding: 4rem 7rem;
                        margin: 0 auto;
                        max-width: 100%;
                    }
                    .quick-actions-container > div:first-child,
                    .quick-actions-container > div:nth-child(2),
                    .quick-actions-container > div:nth-child(3),
                    .quick-actions-container > div:nth-child(4) {
                        flex: 0 1 calc((100% / 4) - (6rem / 4));
                        min-width: 200px;
                        padding: 0;
                    }
                    .quick-actions-container > div:nth-child(n+5) {
                        flex: 0 1 calc((100% / 3) - (4rem / 3));
                        min-width: 220px;
                        padding: 0;
                    }
                    .quick-actions-container .btn {
                        padding: 1.5rem 2rem;
                        font-size: 1.3rem;
                        min-height: 80px;
                        font-weight: 600;
                        line-height: 1.5;
                    }
                    .quick-actions-container .btn i {
                        font-size: 1.4rem;
                        margin-right: 0.5rem;
                    }
                    .quick-actions-container .btn,
                    .quick-actions-container .btn-primary,
                    .quick-actions-container .btn-secondary {
                        background-color: midnightblue !important;
                        border-color: midnightblue !important;
                        color: white !important;
                    }
                    .quick-actions-container .btn:hover,
                    .quick-actions-container .btn-primary:hover,
                    .quick-actions-container .btn-secondary:hover {
                        background-color: #1a237e !important;
                        border-color: #1a237e !important;
                        color: white !important;
                    }
                    .quick-actions-container .btn:focus,
                    .quick-actions-container .btn-primary:focus,
                    .quick-actions-container .btn-secondary:focus {
                        background-color: midnightblue !important;
                        border-color: midnightblue !important;
                        color: white !important;
                        box-shadow: 0 0 0 0.2rem rgba(25, 25, 112, 0.5);
                    }
                    .quick-actions-container .btn:active,
                    .quick-actions-container .btn-primary:active,
                    .quick-actions-container .btn-secondary:active {
                        background-color: #0d0d52 !important;
                        border-color: #0d0d52 !important;
                        color: white !important;
                    }
                    @media (max-width: 1400px) {
                        .quick-actions-container {
                            padding: 3.5rem 6rem;
                        }
                        .quick-actions-container > div:first-child,
                        .quick-actions-container > div:nth-child(2),
                        .quick-actions-container > div:nth-child(3),
                        .quick-actions-container > div:nth-child(4) {
                            flex: 0 1 calc((100% / 4) - (6rem / 4));
                        }
                        .quick-actions-container > div:nth-child(n+5) {
                            flex: 0 1 calc((100% / 3) - (4rem / 3));
                        }
                    }
                    @media (max-width: 992px) {
                        .quick-actions-container {
                            padding: 3rem 4rem;
                            gap: 2rem;
                        }
                        .quick-actions-container > div {
                            flex: 0 1 calc((100% / 3) - (4rem / 3)) !important;
                            margin-bottom: 0;
                        }
                        .quick-actions-container .btn {
                            padding: 1.2rem 1.8rem;
                            font-size: 1.2rem;
                            min-height: 70px;
                        }
                        .quick-actions-container .btn i {
                            font-size: 1.3rem;
                        }
                    }
                    @media (max-width: 768px) {
                        .quick-actions-container {
                            padding: 2.5rem 3rem;
                            gap: 2rem;
                        }
                        .quick-actions-container > div {
                            flex: 0 1 calc((100% / 2) - 1rem) !important;
                            margin-bottom: 0;
                        }
                        .quick-actions-container .btn {
                            padding: 1rem 1.5rem;
                            font-size: 1.1rem;
                            min-height: 65px;
                        }
                        .quick-actions-container .btn i {
                            font-size: 1.2rem;
                        }
                    }
                    @media (max-width: 576px) {
                        .quick-actions-container {
                            padding: 2rem 2.5rem;
                            gap: 2rem;
                        }
                        .quick-actions-container > div {
                            flex: 0 1 100% !important;
                            margin-bottom: 0;
                        }
                        .quick-actions-container .btn {
                            padding: 0.9rem 1.2rem;
                            font-size: 1rem;
                            min-height: 60px;
                            margin-bottom: 0;
                        }
                        .quick-actions-container .btn i {
                            font-size: 1.1rem;
                        }
                    }
                </style>
                <div class="quick-actions-container">
                    @php $isAdmin = auth()->user()?->role === 4; @endphp
                    @if($isAdmin)
                        <div>
                            <a href="{{ route('admin.appointments.index') }}" class="btn btn-primary w-100">
                                <i class="mai-calendar"></i> Appointments
                            </a>
                        </div>
                        
                        <!-- Events Dropdown -->
                        <div>
                            <div class="btn-group w-100">
                                <a href="{{ route('admin.events.index') }}" class="btn btn-primary">
                                    <i class="mai-calendar"></i> Events
                                </a>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.events.index') }}"><i class="mai-calendar"></i> Events</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.events.create') }}"><i class="mai-add"></i> Create Event</a>
                                    <a class="dropdown-item" href="{{ route('admin.participants.export') }}"><i class="mai-download"></i> Export Participation</a>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <a href="{{ route('admin.calendar') }}" class="btn btn-primary w-100">
                                <i class="mai-calendar"></i> Calendar
                            </a>
                        </div>
                        
                        <!-- Staff Dashboard Dropdown -->
                        <div>
                            <div class="btn-group w-100">
                                <a href="{{ route('admin.staff.dashboard') }}" class="btn btn-primary">
                                    <i class="mai-speedometer"></i> Staff Dashboard
                                </a>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle dropdown</span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.staff.dashboard') }}"><i class="mai-speedometer"></i> Staff Dashboards</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.show-staff') }}"><i class="mai-people"></i> Show Staff</a>
                                    <a class="dropdown-item" href="{{ route('admin.add-staff') }}"><i class="mai-add"></i> Add Staff</a>
                                    <a class="dropdown-item" href="{{ route('admin.assistants.index') }}"><i class="mai-people"></i> Show Assistant Staff</a>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <a href="{{ route('admin.show-students-list') }}" class="btn btn-secondary w-100">
                                <i class="mai-book"></i> Show Students
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary w-100">
                                <i class="mai-people"></i> Organizations
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('admin.organizational-structure') }}" class="btn btn-primary w-100">
                                <i class="bi bi-diagram-3"></i> Organizational Structure
                            </a>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('admin.staff.dashboard') }}" class="btn btn-primary w-100">
                                <i class="mai-speedometer"></i> Staff Dashboards
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('admin.organizational-structure') }}" class="btn btn-primary w-100">
                                <i class="bi bi-diagram-3"></i> Organizational Structure
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pending Events -->
            <style>
                .pending-events-card {
                    background-color: light;
                    color: black;
                    margin: 0 7rem;
                    padding-top: 2rem;
                    padding-bottom: 2rem;
                }
                .pending-events-card .card-body {
                    padding-top: 1.5rem;
                    padding-bottom: 1.5rem;
                }
                @media (max-width: 1400px) {
                    .pending-events-card {
                        margin: 0 6rem;
                    }
                }
                @media (max-width: 992px) {
                    .pending-events-card {
                        margin: 0 4rem;
                    }
                }
                @media (max-width: 768px) {
                    .pending-events-card {
                        margin: 0 3rem;
                    }
                }
                @media (max-width: 576px) {
                    .pending-events-card {
                        margin: 0 2.5rem;
                    }
                }
            </style>
            <div class="card mb-4 wow fadeInUp pending-events-card" data-wow-delay="200ms">
                <div class="card-header" style="background-color: midnightblue; color: white;">
                    <h5 class="mb-0">Pending Events for Approval</h5>
                </div>
                <div class="card-body" style="background-color: white; color: black;">
                    @if($pendingEvents->isEmpty())
                        <p class="text-white-50">No pending events.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle bg-white">
                                <thead>
                                    <tr align="center" style="background-color:midnightblue; color:white">
                                        <th>Event</th>
                                        <th>Creator</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center text-dark">
                                    @foreach($pendingEvents as $event)
                                    <tr>
                                        <td>{{ $event->name }}</td>
                                        <td>{{ $event->creator->first_name }} {{ $event->creator->last_name }}</td>
                                        <td>
                                            @if($event->event_date)
                                                {{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}
                                            @elseif($event->start_time)
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('Y-m-d') }}
                                            @else
                                                <span class="text-muted">No date</span>
                                            @endif
                                        </td>
                                        <td>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '' }}</td>
                                        <td>{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.events.approve', $event->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.events.decline', $event->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Decline</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

@endsection
