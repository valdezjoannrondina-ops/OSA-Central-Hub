{{-- Success Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-check-circle-fill me-2"></i>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Duplicate/Info Messages --}}
@if(session('duplicate_message'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Duplicate Detected!</strong>
        <p class="mb-2">{{ session('duplicate_message') }}</p>
        @if(session('duplicate_student_id'))
            <a href="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student.show', session('duplicate_student_id')) }}" class="btn btn-sm btn-primary mt-2">
                <i class="bi bi-eye me-1"></i> View Details
            </a>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Error Messages --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Validation Error!</strong>
        <p class="mb-2">Please fix the following errors:</p>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Info Messages --}}
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-info-circle-fill me-2"></i>Info!</strong> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

