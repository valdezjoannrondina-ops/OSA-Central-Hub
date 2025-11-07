

@extends('layouts.app')

@section('title', 'Moderator Event Management')

@section('content')
<div class="container-fluid">
  <main class="col-12">
    <div class="card mb-3">

      <div class="container-fluid">
        <div class="row">
          <!-- Sidebar -->
          <div class="col-md-3 col-lg-2">
            <div class="card mb-4">
              <div class="card-body">
                <h5 class="mb-3">Features</h5>
                <ul class="nav flex-column">
                  <li class="nav-item mb-2">
                    <a class="nav-link btn btn-outline-primary w-100" href="{{ route('admin.staff.dashboard.StudentOrgModerator.create-event') }}"><span class="sidebar-icon">📅</span> Create Event</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link btn btn-outline-info w-100" href="{{ route('admin.staff.dashboard.StudentOrgModerator.view-events') }}"><span class="sidebar-icon">�</span> Event Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
            <main>
              <h2 class="mb-3">Event Management</h2>
              <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <select class="form-select" style="max-width: 180px;">
                  <option>All Events</option>
                  <!-- Add more event types as needed -->
                </select>
                <input type="date" class="form-control" placeholder="Start Date" style="max-width: 160px;">
                <input type="date" class="form-control" placeholder="End Date" style="max-width: 160px;">
              </div>
              <div class="event-cards" style="display: flex; gap: 2rem; flex-wrap: wrap;">
                @foreach($events as $event)
                  <div class="event-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 1.5rem; min-width: 320px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                      <span style="font-weight: bold; background: #f3f3f3; border-radius: 6px; padding: 0.25rem 0.75rem;">{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</span>
                      <span style="font-size: 0.95em; color: {{ $event->status == 'Approved' ? '#3bb07f' : ($event->status == 'Pending' ? '#f7b731' : '#eb3b5a') }}; background: #f3f3f3; border-radius: 6px; padding: 0.25rem 0.75rem;">{{ ucfirst($event->status) }}</span>
                    </div>
                    <h3 style="margin-top: 1rem; color: #5f2dab;">{{ $event->name }}</h3>
                    <div style="margin: 0.5rem 0; color: #555;"><span>📍</span> {{ $event->location }}</div>
                    <div style="margin: 0.5rem 0; color: #555;"><span>🕒</span> {{ $event->start_time }} - {{ $event->end_time }}</div>
                    <div style="margin: 0.5rem 0; color: #555;"><span>👥</span> Expected Attendance: {{ $event->expected_attendance }}</div>
                    <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                      <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.event.edit', $event->id) }}" class="btn btn-light">Edit</a>
                      <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.event.qrcode', $event->id) }}" class="btn btn-light">QR Code</a>
                      <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.event.show', $event->id) }}" class="btn btn-primary">Details</a>
                    </div>
                  </div>
                @if($events->isEmpty())
                  <div>No events found.</div>
                @endif
              </div>
                  <div class="modal fade" id="eventDetailsModal{{ $event->id }}" tabindex="-1" aria-labelledby="eventDetailsLabel{{ $event->id }}" aria-hidden="true">
                  </main>
                  <main>
                    <div class="dashboard-header">
                    <h2>Upcoming Events</h2>
                    <div class="event-cards" style="display: flex; gap: 2rem; flex-wrap: wrap;">
                      @foreach($events->where('event_date', '>=', now()) as $event)
                        <div class="event-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 1.5rem; min-width: 320px;">
                          <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: bold; background: #f3f3f3; border-radius: 6px; padding: 0.25rem 0.75rem;">{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</span>
                            <span style="font-size: 0.95em; color: {{ $event->status == 'Approved' ? '#3bb07f' : ($event->status == 'Pending' ? '#f7b731' : '#eb3b5a') }}; background: #f3f3f3; border-radius: 6px; padding: 0.25rem 0.75rem;">{{ ucfirst($event->status) }}</span>
                          </div>
                          <h3 style="margin-top: 1rem; color: #5f2dab;">{{ $event->name }}</h3>
                          <div style="margin: 0.5rem 0; color: #555;"><span>📍</span> {{ $event->location }}</div>
                          <div style="margin: 0.5rem 0; color: #555;"><span>🕒</span> {{ $event->start_time }} - {{ $event->end_time }}</div>
                          <div style="margin: 0.5rem 0; color: #555;"><span>👥</span> Expected Attendance: {{ $event->expected_attendance }}</div>
                        </div>
                      @endforeach
                      @if($events->where('event_date', '>=', now())->isEmpty())
                        <div>No upcoming events.</div>
                      @endif
                    </div>
                    <hr>
                    <h2>Academic Calendar</h2>
                    <div class="academic-calendar">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Event</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($academicCalendar ?? [] as $item)
                            <tr>
                              <td>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</td>
                              <td>{{ $item['name'] }}</td>
                            </tr>
                          @endforeach
                          @if(empty($academicCalendar))
                            <tr><td colspan="2">No academic calendar items available.</td></tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </main>
                      </div>
                    </div>
                  </div>
                  <!-- Delete Event Modal -->
                  <div class="modal fade" id="deleteEventModal{{ $event->id }}" tabindex="-1" aria-labelledby="deleteEventLabel{{ $event->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="deleteEventLabel{{ $event->id }}">Delete Event</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                          <form action="{{ route('admin.staff.dashboard.StudentOrgModerator.event.delete', $event->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <div class="modal-body">
                            <p>Are you sure you want to delete the event <strong>{{ $event->name }}</strong>?</p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="eventDetailsLabel{{ $event->id }}">Event Details</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-md-7">
                              <table class="table table-bordered table-sm mb-3">
                                <tbody>
                                  <tr><th>Name</th><td>{{ $event->name }}</td></tr>
                                  <tr><th>Date Started</th><td>{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : ($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '') }}</td></tr>
                                  <tr><th>Date Ended</th><td>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('M d, Y') : ($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '') }}</td></tr>
                                  <tr><th>Time Started</th><td>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('h:i A') : '' }}</td></tr>
                                  <tr><th>Time Ended</th><td>{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('h:i A') : '' }}</td></tr>
                                  <tr><th>Location</th><td>{{ $event->location }}</td></tr>
                                  <tr><th>Organization</th><td>{{ $event->organization ? $event->organization->name : $event->organization_id }}</td></tr>
                                  <tr><th>Status</th><td>{{ ucfirst($event->status) }}</td></tr>
                                  <tr><th>Description</th><td>{{ $event->description }}</td></tr>
                                </tbody>
                              </table>
                              <div class="text-center mb-3">
                                <h6>QR Code for Attendance:</h6>
                                @if($event->qr_code_path)
                                  <img src="{{ asset($event->qr_code_path) }}" alt="QR Code" style="width:180px; height:180px;" class="mb-3">
                                @else
                                  <span class="text-danger">QR code not available for this event.</span>
                                @endif
                              </div>
                              <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editEventModal{{ $event->id }}">Edit</button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEventModal{{ $event->id }}">Delete</button>
                              </div>
                            </div>
                            <div class="col-md-5 text-center">
                              <button type="button" class="btn btn-primary" onclick="openCameraScanner({{ $event->id }})">Open Camera to Scan</button>
                              <div id="cameraScanner{{ $event->id }}" style="display:none; margin-top:15px;">
                                <video id="video{{ $event->id }}" width="250" height="200" autoplay></video>
                                <canvas id="canvas{{ $event->id }}" style="display:none;"></canvas>
                                <p id="scanResult{{ $event->id }}"></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
                <script>
  // Automatically open the edit modal if there are validation errors for an event
  @if ($errors->any() && old('edit_event_id'))
    document.addEventListener('DOMContentLoaded', function() {
      var modalId = 'editEventModal' + {{ old('edit_event_id') }};
      var modal = document.getElementById(modalId);
      if (modal) {
        var bsModal = new bootstrap.Modal(modal);
        bsModal.show();
      }
    });
  @endif
                          <input type="hidden" name="edit_event_id" value="{{ $event->id }}">
                  function openCameraScanner(eventId) {
                    const scannerDiv = document.getElementById('cameraScanner' + eventId);
                    scannerDiv.style.display = 'block';
                    const video = document.getElementById('video' + eventId);
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                      navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
                        video.srcObject = stream;
                        video.play();
                      });
                    }
                  }
                  // Diagnostic: Check if modals exist and can be opened
                  document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('button[data-bs-toggle="modal"]').forEach(function(btn) {
                      btn.addEventListener('click', function(e) {
                        var targetId = btn.getAttribute('data-bs-target');
                        var modal = document.querySelector(targetId);
                        if (!modal) {
                          alert('Modal with selector ' + targetId + ' not found. Please check event ID and modal markup.');
                        } else {
                          // Try to show modal via Bootstrap JS
                          try {
                            var bsModal = new bootstrap.Modal(modal);
                            bsModal.show();
                          } catch (err) {
                            alert('Bootstrap modal error: ' + err);
                          }
                        }
                      });
                    });
                  });
                </script>
              </div>
            </main>
          </div>
        </div>
      </div>
<script>
  function openCameraScanner(eventId) {
    const scannerDiv = document.getElementById('cameraScanner' + eventId);
    scannerDiv.style.display = 'block';
    const video = document.getElementById('video' + eventId);
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
        video.srcObject = stream;
        video.play();
      });
    }
  }

  // Diagnostic: Check if modals exist and can be opened
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('button[data-bs-toggle="modal"]').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        var targetId = btn.getAttribute('data-bs-target');
        var modal = document.querySelector(targetId);
        if (!modal) {
          alert('Modal with selector ' + targetId + ' not found. Please check event ID and modal markup.');
        } else {
          // Try to show modal via Bootstrap JS
          try {
            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
          } catch (err) {
            alert('Bootstrap modal error: ' + err);
          }
        }
      });
    });
  });
</script>
@endsection
