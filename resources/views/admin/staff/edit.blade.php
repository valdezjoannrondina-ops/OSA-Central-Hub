@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="container-fluid">
  <main class="col-12">
    <div class="card mb-4">
      <div class="card-body">
        <h2 class="mb-3">Edit Event</h2>
        <form action="{{ route('admin.staff.dashboard.StudentOrgModerator.event.update', $event->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row g-2 mb-2">
            <div class="col-md-3">
              <input type="text" name="title" class="form-control" value="{{ $event->name }}" placeholder="Event Title*" required>
            </div>
            <div class="col-md-2">
              <input type="date" name="event_date" class="form-control" value="{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '' }}" required>
            </div>
            <div class="col-md-1">
              <input type="time" name="start_time" class="form-control" value="{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '' }}" required>
            </div>
            <div class="col-md-2">
              <input type="date" name="end_date" class="form-control" value="{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('Y-m-d') : '' }}" required>
            </div>
            <div class="col-md-1">
              <input type="time" name="end_time" class="form-control" value="{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '' }}" required>
            </div>
            <div class="col-md-1">
              <input type="text" name="location" class="form-control" value="{{ $event->location }}" placeholder="Location">
            </div>
            <div class="col-md-2">
              <select name="organization_id" class="form-control" required>
                <option value="">Select Organization*</option>
                @foreach($organizations as $org)
                  <option value="{{ $org->id }}" {{ $event->organization_id == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-2">
            <textarea name="description" class="form-control" rows="3" placeholder="Event Description">{{ $event->description }}</textarea>
          </div>
          <button type="submit" class="btn btn-primary">Update Event</button>
        </form>
      </div>
    </div>
  </main>
</div>
@endsection
