@extends('layouts.app')

@section('title', 'View All Events')

@section('content')
<div class="container-fluid">
  <main class="col-12">
    <div class="card mb-3">
      <div class="card-body">
        <h2 class="mb-3">All Events (Student Org. Moderator)</h2>
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Name</th>
              <th>Date Started</th>
              <th>Date Ended</th>
              <th>Time Started</th>
              <th>Time Ended</th>
              <th>Location</th>
              <th>Organization</th>
              <th>Status</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($events as $event)
            <tr>
              <td>{{ $event->name }}</td>
              <td>{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : ($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '') }}</td>
              <td>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('M d, Y') : ($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '') }}</td>
              <td>{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('h:i A') : '' }}</td>
              <td>{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('h:i A') : '' }}</td>
              <td>{{ $event->location }}</td>
              <td>{{ $event->organization ? $event->organization->name : $event->organization_id }}</td>
              <td>{{ ucfirst($event->status) }}</td>
              <td>{{ $event->description }}</td>
              <td>
                <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.event.show', $event->id) }}" class="btn btn-info btn-sm">Details</a>
                <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.event.edit', $event->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('admin.staff.dashboard.StudentOrgModerator.event.delete', $event->id) }}" method="POST" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
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
