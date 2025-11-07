@extends('layouts.app')

@section('title', $organization->name . ' - My Assistant Staff')

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
      <a href="{{ route('staff.organizations.index') }}" class="btn btn-secondary">&larr; Back to Organizations</a>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 col-lg-2">
      <div class="list-group mb-3">
        <div class="list-group-item active" style="background-color: midnightblue; border-color: midnightblue;">Quick Actions</div>
        <a href="{{ route('admin.appointments.index') }}" class="list-group-item list-group-item-action">Assigned Appointments</a>
        @php
          $isStaff = (auth()->user()->role ?? 0) == 2;
          $isAdmin = (auth()->user()->role ?? 0) == 4;
        @endphp
        @if($isStaff)
          <a href="{{ route('staff.organizations.index') }}" class="list-group-item list-group-item-action">My Organization</a>
        @endif
        <a href="{{ route('admin.events.index') }}" class="list-group-item list-group-item-action">All Events</a>
        @if($isAdmin)
          <a href="{{ route('admin.events.index') }}#create" class="list-group-item list-group-item-action">Create Event</a>
        @endif
        <a href="{{ route('admin.participants.export') }}" class="list-group-item list-group-item-action">Participants History</a>
      </div>
    </div>
    <main class="col-md-9 col-lg-10">
      <h2 class="mb-3">{{ $organization->name }} - My Assistant Staff</h2>
      
      <div class="mb-3">
        <a href="{{ route('staff.assistants.create', ['organization_id' => $organization->id]) }}" class="btn btn-success">
          <i class="bi bi-person-plus"></i> Add Assistant
        </a>
      </div>
      
      @if($assistants->isEmpty())
        <div class="alert alert-info">
          <p>No assistants assigned to this organization yet.</p>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($assistants as $assistant)
                <tr>
                  <td>{{ $assistant->user_id }}</td>
                  <td>{{ $assistant->first_name }} {{ $assistant->middle_name ?? '' }} {{ $assistant->last_name }}</td>
                  <td>{{ $assistant->email }}</td>
                  <td>
                    @if($assistant->suspended)
                      <span class="badge bg-danger">Suspended</span>
                    @else
                      <span class="badge bg-success">Active</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('staff.assistants.edit', $assistant->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    @if($assistant->suspended)
                      <form action="{{ route('staff.assistants.resume', $assistant->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-success">Resume</button>
                      </form>
                    @else
                      <form action="{{ route('staff.assistants.suspend', $assistant->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-warning">Suspend</button>
                      </form>
                    @endif
                    <form action="{{ route('staff.assistants.destroy', $assistant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assistant?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </main>
  </div>
</div>
@endsection

