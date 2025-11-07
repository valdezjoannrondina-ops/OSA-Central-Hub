
@extends('layouts.app')

@section('title', 'Event Details')

@section('content')
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 mb-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="mb-3">Navigation</h5>
          <ul class="nav flex-column">
            <li class="nav-item mb-2">
              <a class="nav-link btn btn-outline-primary w-100" href="{{ route('admin.staff.dashboard.StudentOrgModerator.view-events') }}">Back to Event List</a>
            </li>
            <li class="nav-item mb-2">
              <a class="nav-link btn btn-outline-success w-100" href="#">Create New Event</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Main Content -->
    <div class="col-md-9 col-lg-10">
      <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0"><i class="bi bi-calendar-event"></i> Event Details</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-7">
              <table class="table table-bordered table-hover mb-3">
                <tbody>
                  <tr><th><i class="bi bi-type"></i> Name</th><td>{{ $event->name }}</td></tr>
                  <tr><th><i class="bi bi-calendar"></i> Date Started</th><td>{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : ($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '') }}</td></tr>
                  <tr><th><i class="bi bi-calendar-check"></i> Date Ended</th><td>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('M d, Y') : ($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '') }}</td></tr>
                  <tr><th><i class="bi bi-clock"></i> Time Started</th><td>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('h:i A') : '' }}</td></tr>
                  <tr><th><i class="bi bi-clock-history"></i> Time Ended</th><td>{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('h:i A') : '' }}</td></tr>
                  <tr><th><i class="bi bi-geo-alt"></i> Location</th><td>{{ $event->location }}</td></tr>
                  <tr><th><i class="bi bi-people"></i> Organization</th><td>{{ $event->organization ? $event->organization->name : $event->organization_id }}</td></tr>
                  <tr><th><i class="bi bi-info-circle"></i> Status</th><td><span class="badge bg-info text-dark">{{ ucfirst($event->status) }}</span></td></tr>
                  <tr><th><i class="bi bi-card-text"></i> Description</th><td>{{ $event->description }}</td></tr>
                </tbody>
              </table>
              <div class="d-flex gap-2 mb-3">
                <a href="#" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                <a href="#" class="btn btn-danger"><i class="bi bi-trash"></i> Delete</a>
              </div>
            </div>
            <div class="col-md-5 text-center">
              <h5 class="mb-3">QR Code for Attendance</h5>
              @if($event->qr_code_path)
                <img src="{{ asset($event->qr_code_path) }}" alt="QR Code" style="width:180px; height:180px;" class="mb-3 border border-2 rounded">
              @else
                <span class="text-danger">QR code not available for this event.</span>
              @endif
              <button type="button" class="btn btn-primary mt-3" onclick="openCameraScanner({{ $event->id }})"><i class="bi bi-camera"></i> Open Camera to Scan</button>
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
</script>
@endsection
