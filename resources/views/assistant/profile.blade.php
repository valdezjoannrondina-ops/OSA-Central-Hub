@extends('layouts.app')

@section('title', 'Assistant Profile')

@section('content')
<div class="container">
  <h3 class="mb-3">Assistant Profile</h3>
  <div class="card">
    <div class="card-body">
      <p><strong>Name:</strong> {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
      <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
      <p><strong>Organization:</strong> {{ optional(auth()->user()->organization)->name ?? '—' }}</p>
      <p><strong>Position:</strong> {{ auth()->user()->position ?? '—' }}</p>
      <hr>
      <h5>Change Password</h5>
      <form method="POST" action="{{ route('assistant.change-password') }}">
        @csrf
        <div class="mb-3">
          <label for="current_password" class="form-label">Current Password</label>
          <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="new_password" class="form-label">New Password</label>
          <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
          <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
      </form>
    </div>
  </div>
</div>
@endsection
