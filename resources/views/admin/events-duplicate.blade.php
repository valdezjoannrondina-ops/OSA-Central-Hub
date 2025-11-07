@extends('layouts.app')

@section('title', 'Duplicate Event Detected')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('admin.partials.sidebar')
        <main class="col-md-10 py-4">
            <div class="admin-back-btn-wrap mb-3">
                <a href="{{ route('admin.events.create') }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Create Event</a>
            </div>
            
            <div class="alert alert-warning">
                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Duplicate Event Detected!</h4>
                <p class="mb-0">The system detected <strong>{{ $duplicates->count() }}</strong> existing event(s) with the same name and overlapping dates.</p>
            </div>

            <div class="row">
                <!-- New Event Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> New Event (To Be Created)</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td><strong>{{ $newEvent['name'] }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $newEvent['description'] }}</td>
                                </tr>
                                <tr>
                                    <th>Start Time:</th>
                                    <td>{{ \Carbon\Carbon::parse($newEvent['start_datetime'])->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>End Time:</th>
                                    <td>{{ \Carbon\Carbon::parse($newEvent['end_datetime'])->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $newEvent['location'] ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Existing Events Card -->
                <div class="col-md-6 mb-4">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-calendar-x"></i> Existing Duplicate Event(s)</h5>
                        </div>
                        <div class="card-body">
                            @foreach($duplicates as $duplicate)
                                <div class="mb-3 p-3 border rounded">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <th width="40%">Name:</th>
                                            <td><strong>{{ $duplicate->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Description:</th>
                                            <td>{{ $duplicate->description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Start Time:</th>
                                            <td>{{ \Carbon\Carbon::parse($duplicate->start_time)->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Time:</th>
                                            <td>{{ \Carbon\Carbon::parse($duplicate->end_time)->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Location:</th>
                                            <td>{{ $duplicate->location ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td><span class="badge bg-{{ $duplicate->status === 'approved' ? 'success' : 'warning' }}">{{ ucfirst($duplicate->status) }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Created:</th>
                                            <td>{{ $duplicate->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Form -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-question-circle"></i> What would you like to do?</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.events.resolve-duplicate') }}" method="POST" id="duplicateForm">
                        @csrf
                        
                        <input type="hidden" name="existing_event_ids" id="existing_event_ids" value="{{ $duplicates->pluck('id')->toJson() }}">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-success">
                                    <div class="card-body text-center">
                                        <input type="radio" name="action" id="keep_new" value="keep_new" class="form-check-input" checked>
                                        <label for="keep_new" class="form-check-label w-100 cursor-pointer">
                                            <div class="mt-2">
                                                <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Keep New Event</h6>
                                                <p class="text-muted small mb-0">Delete existing duplicate(s) and create the new event</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-warning">
                                    <div class="card-body text-center">
                                        <input type="radio" name="action" id="keep_existing" value="keep_existing" class="form-check-input">
                                        <label for="keep_existing" class="form-check-label w-100 cursor-pointer">
                                            <div class="mt-2">
                                                <i class="bi bi-x-circle-fill text-warning" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Keep Existing Event(s)</h6>
                                                <p class="text-muted small mb-0">Cancel creation and keep the existing event(s)</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-info">
                                    <div class="card-body text-center">
                                        <input type="radio" name="action" id="keep_both" value="keep_both" class="form-check-input">
                                        <label for="keep_both" class="form-check-label w-100 cursor-pointer">
                                            <div class="mt-2">
                                                <i class="bi bi-plus-circle-fill text-info" style="font-size: 2rem;"></i>
                                                <h6 class="mt-2">Keep Both</h6>
                                                <p class="text-muted small mb-0">Create the new event and keep all existing events</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check2"></i> Confirm Action
                            </button>
                            <a href="{{ route('admin.events.create') }}" class="btn btn-secondary btn-lg px-5 ms-2">
                                <i class="bi bi-x"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .form-check-input {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 1.25rem;
        height: 1.25rem;
    }
    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: box-shadow 0.2s;
    }
    input[type="radio"]:checked + label .card {
        border-width: 3px !important;
        background-color: #f8f9fa;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make the entire card clickable
    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
        const card = radio.closest('.card');
        card.addEventListener('click', function(e) {
            if (e.target !== radio) {
                radio.checked = true;
            }
        });
    });
});
</script>
@endsection

