@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="container">
    <h2 class="mb-4">Appointments Assigned to Me</h2>
    @if($appointments->isEmpty())
        <p class="text-muted">No appointments assigned.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Appointment Date</th>
                        <th>Schedule Time</th>
                        <th>Assigned Staff</th>
                    </tr>
                </thead>
                <tbody>
                                        @foreach($appointments as $appt)
                                        <tr>
                                                <td>{{ $appt->full_name }}</td>
                                                <td>{{ $appt->email }}</td>
                                                <td>{{ $appt->appointment_date->format('M d, Y') }}</td>
                                                <td>{{ date('g:i A', strtotime($appt->appointment_time)) }}</td>
                                                <td>
                                                        @if($appt->assignedStaff)
                                                                {{ $appt->assignedStaff->first_name }} {{ $appt->assignedStaff->last_name }}
                                                        @else
                                                                <span class="text-muted">Unassigned</span>
                                                        @endif
                                                </td>
                                                <td>{{ $appt->message }}</td>
                                                <td>
                                                        @if(auth()->user()->is_staff)
                                                                <form action="{{ route('appointments.approve', $appt->id) }}" method="POST" style="display:inline-block;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                                </form>
                                                                <form action="{{ route('appointments.decline', $appt->id) }}" method="POST" style="display:inline-block;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                                                                </form>
                                                                <!-- Reschedule Button triggers modal -->
                                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $appt->id }}">Reschedule</button>

                                                                <!-- Modal for rescheduling -->
                                                                <div class="modal fade" id="rescheduleModal{{ $appt->id }}" tabindex="-1" aria-labelledby="rescheduleModalLabel{{ $appt->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <form action="{{ route('appointments.reschedule', $appt->id) }}" method="POST">
                                                                                @csrf
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="rescheduleModalLabel{{ $appt->id }}">Reschedule Appointment</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="mb-3">
                                                                                        <label for="appointment_date{{ $appt->id }}" class="form-label">New Date</label>
                                                                                        <input type="date" class="form-control" name="appointment_date" id="appointment_date{{ $appt->id }}" required>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="appointment_time{{ $appt->id }}" class="form-label">New Time</label>
                                                                                        <select class="form-select" name="appointment_time" id="appointment_time{{ $appt->id }}" required>
                                                                                            @for($hour = 8; $hour <= 15; $hour++)
                                                                                                @foreach([0, 30] as $minute)
                                                                                                    <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}">{{ date('g:i A', strtotime(sprintf('%02d:%02d', $hour, $minute))) }}</option>
                                                                                                @endforeach
                                                                                            @endfor
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                                    <button type="submit" class="btn btn-warning">Reschedule</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        @endif
                                                </td>
                                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
