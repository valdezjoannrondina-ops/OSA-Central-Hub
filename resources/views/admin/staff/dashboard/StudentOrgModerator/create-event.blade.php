@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<div class="container-fluid">
  <div class="row">
    @include('admin.partials.sidebar')
    <main class="col-md-10 py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Create Event</h2>
        <a href="{{ route('admin.staff.dashboard.StudentOrgModerator') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
      </div>

      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Event Information</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.staff.dashboard.StudentOrgModerator.event.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="title" class="form-label">Event Name <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
              </div>
              <div class="col-md-6">
                <label for="organization_id" class="form-label">Organization <span class="text-danger">*</span></label>
                <select name="organization_id" id="organization_id" class="form-control" required>
                  <option value="">Select Organization</option>
                  @foreach($userOrganizations as $org)
                    <option value="{{ $org->id }}" {{ old('organization_id', $selectedOrganizationId ?? null) == $org->id ? 'selected' : '' }}>
                      {{ $org->name }}
                      @if($org->department)
                        - {{ $org->department->name }}
                      @endif
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
              </div>
              <div class="col-md-3">
                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required>
              </div>
              <div class="col-md-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', '00:00') }}">
              </div>
              <div class="col-md-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', '23:59') }}">
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" placeholder="Event Location">
              </div>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" id="description" class="form-control" rows="3" placeholder="Event Description">{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Create Event</button>
              <a href="{{ route('admin.staff.dashboard.StudentOrgModerator') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
@endsection

