@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row">
    @include('admin.partials.sidebar')
    <main class="col-md-10 py-4">
        <div class="admin-back-btn-wrap mb-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="py-3">
            <h1 class="h4 mb-4">Organizations Management</h1>
            <p class="text-muted small mb-3">Manage organizations and their official email addresses. Official emails are used to send notifications about events (approval, decline, missing requirements).</p>

            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Organization Name</th>
                            <th>Department</th>
                            <th>Official Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($organizations as $organization)
                        <tr>
                            <td><strong>{{ $organization->name }}</strong></td>
                            <td>{{ $organization->department->name ?? 'N/A' }}</td>
                            <td>
                                @if($organization->official_email)
                                    <span class="text-success">{{ $organization->official_email }}</span>
                                @else
                                    <span class="text-danger">Not Set</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.organizations.profile', $organization->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-person-circle me-1"></i>View Profile
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#emailModal{{ $organization->id }}">
                                        <i class="bi bi-envelope me-1"></i>{{ $organization->official_email ? 'Update Email' : 'Add Email' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No organizations found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
  </div>
</div>

<!-- Email Modal for each organization -->
@foreach ($organizations as $organization)
<div class="modal fade" id="emailModal{{ $organization->id }}" tabindex="-1" aria-labelledby="emailModalLabel{{ $organization->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="emailModalLabel{{ $organization->id }}">Set Official Email - {{ $organization->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.organizations.update-email', $organization->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> This email will be used to send notifications about:
                        <ul class="mb-0 mt-2">
                            <li>Event approvals</li>
                            <li>Event declines (with reason)</li>
                            <li>Missing requirements notifications</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label for="official_email{{ $organization->id }}" class="form-label">Official Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="official_email{{ $organization->id }}" name="official_email" value="{{ old('official_email', $organization->official_email) }}" placeholder="organization@ustp.edu.ph" required>
                        <small class="form-text text-muted">Enter the official email address for this organization.</small>
                        @error('official_email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

