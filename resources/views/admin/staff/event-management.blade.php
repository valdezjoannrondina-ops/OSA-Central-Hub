@extends('layouts.app')

@section('title', 'Moderator Event Management')

@section('content')
<div class="container-fluid">
  <main class="col-12">
    <h2 class="mb-3">Create Event (Student Org. Moderator)</h2>
    <div class="card mb-4">
      <div class="card-body">
        <form action="{{ route('admin.staff.dashboard.StudentOrgModerator.event.store') }}" method="POST">
          @csrf
          <div class="row g-2 mb-2">
            <div class="col-md-4">
              <input type="text" name="title" class="form-control" placeholder="Event Title*" required>
            </div>
            <div class="col-md-3">
              <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="col-md-3">
              <input type="text" name="location" class="form-control" placeholder="Location">
            </div>
            <div class="col-md-2">
              <select name="organization_id" class="form-control" required>
                <option value="">Select Organization*</option>
                @foreach($organizations as $org)
                  <option value="{{ $org->id }}">{{ $org->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-2">
            <textarea name="description" class="form-control" rows="3" placeholder="Event Description"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
      </div>
    </div>
    <!-- List of events created by moderator -->
    <div class="card">
      <div class="card-body">
        <h5>My Events</h5>
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Title</th>
              <th>Date</th>
              <th>Location</th>
              <th>Organization</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($events as $event)
            <tr>
              <td>{{ $event->title }}</td>
              <td>{{ $event->event_date }}</td>
              <td>{{ $event->location }}</td>
              <td>{{ $event->organization ? $event->organization->name : $event->organization_id }}</td>
              <td>{{ ucfirst($event->status) }}</td>
              <td>
                <!-- Edit/Delete actions can be added here -->
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
@endsection
