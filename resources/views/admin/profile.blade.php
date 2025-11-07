@extends('layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Admin Profile</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $admin->first_name }} {{ $admin->last_name }}</p>
            <p><strong>Email:</strong> {{ $admin->email }}</p>
            <p><strong>Role:</strong> Admin</p>
            <!-- Add more admin details as needed -->
            <hr>
            <h5>Change Password</h5>
            <form method="POST" action="{{ route('admin.change-password') }}">
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
