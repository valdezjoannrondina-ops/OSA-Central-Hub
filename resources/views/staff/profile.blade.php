@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Staff Profile</h2>
    @if($staff)
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <div class="card-body">
            <div class="text-center mb-3">
                @if($staff->image)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($staff->image) }}" alt="{{ $staff->first_name }} {{ $staff->last_name }}" class="rounded-circle" width="120" height="120">
                @else
                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" width="120" height="120" style="width: 120px; height: 120px;">
                    <span class="text-white fs-1">{{ substr($staff->first_name ?? 'N', 0, 1) }}</span>
                </div>
                @endif
            </div>
            <h4 class="card-title text-center">{{ $staff->first_name ?? '' }} {{ $staff->middle_name ?? '' }} {{ $staff->last_name ?? '' }}</h4>
            <p class="card-text text-center">{{ $staff->designation ?? 'N/A' }}</p>
            <p class="card-text"><strong>Email:</strong> {{ $staff->email ?? 'N/A' }}</p>
            <p class="card-text"><strong>Contact:</strong> {{ $staff->contact_number ?? 'N/A' }}</p>
            <p class="card-text"><strong>Department:</strong> {{ $staff->department ? $staff->department->name : 'N/A' }}</p>
            <p class="card-text">
                <strong>Organizations:</strong>
                @php
                    // Collect all organizations assigned to this staff
                    $allOrganizations = collect();
                    
                    // Add single organization if exists
                    if ($staff->organization) {
                        $allOrganizations->push($staff->organization);
                    }
                    
                    // Add many-to-many organizations if exists
                    if (method_exists($staff, 'organizations') && $staff->organizations) {
                        foreach ($staff->organizations as $org) {
                            // Avoid duplicates
                            if (!$allOrganizations->contains('id', $org->id)) {
                                $allOrganizations->push($org);
                            }
                        }
                    }
                @endphp
                @if($allOrganizations->isEmpty())
                    N/A
                @else
                    <ul class="list-unstyled mb-0">
                        @foreach($allOrganizations as $org)
                            <li>• {{ $org->name }}</li>
                        @endforeach
                    </ul>
                @endif
            </p>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <p class="mb-0">No profile information available.</p>
    </div>
    @endif
</div>
@endsection
