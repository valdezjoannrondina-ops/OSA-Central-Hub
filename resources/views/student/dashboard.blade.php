@extends('layouts.app')

@php
    $designation = auth()->user()->designation ?? optional(auth()->user()->staffProfile)->designation ?? null;
    $fullName = trim((auth()->user()->first_name ?? '') . ' ' . (auth()->user()->last_name ?? ''));
    $computedTitle = $designation ? ($designation . ' — ' . $fullName) : $fullName;
@endphp

@section('title', 'My Student Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar: Quick Actions -->
        <aside class="col-md-3 d-flex align-items-start">
            <div class="card mb-4 w-100" style="margin-top: 3.5rem;">
                <div class="card-header text-white" style="background-color: midnightblue; text-align: center; font-size: 1.5rem; padding-top: 0.7rem; padding-bottom: 0.7rem;">Quick Actions</div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>View My Appointments</h5>
                        <button type="button" class="btn w-100" style="background-color: midnightblue; border-color: midnightblue; color: white;" data-bs-toggle="modal" data-bs-target="#viewAppointmentsModal">View My Appointments</button>
                    </div>
                    <!-- Organization Registration Modal -->
                    <div class="modal fade" id="orgRegModal" tabindex="-1" aria-labelledby="orgRegModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orgRegModalLabel">Organization Registration Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <div class="row justify-content-center">
                                            <div class="col-md-8">
                                                <div class="card">
                                                    <div class="card-header">{{ __('Register Organization') }}</div>
                                                    <div class="card-body">
                                                        <form method="POST" action="/student/organization-registration-request">
                                                            @csrf
                                                            <!-- Organization Selection -->
                                                            <div class="row mb-3">
                                                                <label class="col-md-4 col-form-label text-md-end">Organization <span class="text-danger">*</span></label>
                                                                <div class="col-md-6">
                                                                    <select name="organization_id" id="organization_id" class="form-control" required>
                                                                        <option value="">Select Organization</option>
                                                                        @foreach($nonAcademicOrganizations as $org)
                                                                            <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                                                                {{ $org->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <small class="text-muted d-block mt-1">You are automatically a member of your department's organization.</small>
                                                                </div>
                                                            </div>
                                                            <!-- Details -->
                                                            <div class="row mb-3">
                                                                <label class="col-md-4 col-form-label text-md-end">Why do you want to join this organization?</label>
                                                                <div class="col-md-6">
                                                                    <textarea name="details" class="form-control" rows="3" required>{{ old('details') }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-0">
                                                                <div class="col-md-6 offset-md-4">
                                                                    <button type="submit" class="btn btn-warning">
                                                                        Submit Request
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- View My Appointments Modal -->
                    <div class="modal fade" id="viewAppointmentsModal" tabindex="-1" aria-labelledby="viewAppointmentsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewAppointmentsModalLabel">My Appointments</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    @if($appointments->isEmpty())
                                        <p class="text-muted">No active appointments.</p>
                                    @else
                                        @foreach($appointments as $appt)
                                        <div class="border-bottom pb-3 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="mb-1">{{ $appt->concern }}</h6>
                                                    <p class="text-sm text-grey mb-0">
                                                        📅 {{ $appt->appointment_date->format('M d, Y') }}
                                                        @if($appt->appointment_time)
                                                            | ⏰ {{ date('g:i A', strtotime($appt->appointment_time)) }}
                                                        @endif
                                                    </p>
                                                    <p class="text-sm mb-0">
                                                        👤 Staff: {{ $appt->assignedStaff ? $appt->assignedStaff->first_name . ' ' . $appt->assignedStaff->last_name : 'TBD' }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $appt->status === 'pending' ? 'warning text-dark' : ($appt->status === 'approved' ? 'success' : 'secondary') }}">
                                                        {{ ucfirst($appt->status) }}
                                                    </span>
                                                    @if($appt->status === 'pending')
                                                        <form action="{{ route('student.appointments.cancel', $appt->id) }}" method="POST" class="d-inline mt-1">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this appointment?')">Cancel</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ route('student.appointments.index') }}" class="btn btn-info">View All Appointments</a>
                                        <a href="{{ route('student.make-appointment') }}" class="btn btn-primary">Book Appointment</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h5>Organization Registration Request</h5>
                        <button type="button" class="btn w-100" style="background-color: midnightblue; border-color: midnightblue; color: white;" data-bs-toggle="modal" data-bs-target="#orgRegModal">Request Organization Registration</button>
                    </div>
                    <div class="mb-3">
                        <h5>Organizational Dashboard</h5>
                        @if(auth()->user()->hasAssistantAccess())
                            <button type="button" class="btn w-100" style="background-color: midnightblue; border-color: midnightblue; color: white;" data-bs-toggle="modal" data-bs-target="#assistantSwitchModal">Open</button>
                        @else
                            <button type="button" class="btn w-100" style="background-color: midnightblue; border-color: midnightblue; color: white; opacity: 0.6;" disabled>Unaccessible</button>
                            <small class="text-danger d-block mt-1">Student Leaders only.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <h5>My QR Code</h5>
                        <button type="button" class="btn w-100" style="background-color: midnightblue; border-color: midnightblue; color: white;" data-bs-toggle="modal" data-bs-target="#qrModal">View QR</button>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="col-md-9">
            
            <!-- Upcoming Events -->
            <div class="card mb-4 wow fadeInUp" data-wow-delay="200ms">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Upcoming Events</h5>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->isEmpty())
                        <p class="text-muted">No upcoming events.</p>
                    @else
                        <!-- Calendar View (Minimized) -->
                        <div class="mb-4" id="calendar-view-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Calendar View</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary calendar-nav-btn" 
                                            data-month="{{ $selectedMonth->copy()->subMonth()->format('Y-m') }}">
                                        <span>&laquo;</span> Previous
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled id="current-month-display">
                                        {{ $selectedMonth->format('F Y') }}
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary calendar-nav-btn" 
                                            data-month="{{ $selectedMonth->copy()->addMonth()->format('Y-m') }}">
                                        Next <span>&raquo;</span>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary calendar-nav-btn" 
                                            data-month="{{ now()->format('Y-m') }}">
                                        Today
                                    </button>
                                </div>
                            </div>
                            <div class="row" id="calendar-months-container">
                                @include('student.partials.calendar-view', ['selectedMonth' => $selectedMonth, 'eventsByDate' => $eventsByDate, 'year' => $year])
                            </div>
                            <small class="text-muted d-block mt-2">
                                <span class="badge bg-danger" style="font-size: 0.6rem;">●</span> = Event scheduled
                            </small>
                        </div>
                        
                        <!-- Events Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event Title</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingEvents as $event)
                                    <tr>
                                        <td><strong>{{ $event->name ?? $event->title ?? 'N/A' }}</strong></td>
                                        <td>
                                            @if($event->end_date && $event->event_date != $event->end_date)
                                                {{ $event->event_date->format('M d, Y') }} - {{ $event->end_date->format('M d, Y') }}
                                            @else
                                                {{ $event->event_date->format('M d, Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($event->start_time && $event->end_time)
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                            @elseif($event->start_time)
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                            @else
                                                TBD
                                            @endif
                                        </td>
                                        <td>{{ $event->location ?? 'TBD' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <h5>My Events</h5>
            <a href="{{ route('student.events.index') }}" class="btn btn-secondary mt-2">View Events</a>
            <div class="card mb-4 wow fadeInUp" data-wow-delay="400ms">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Participation History</h5>
                </div>
                <div class="card-body">
                    @if($participations->isEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($participations as $part)
                                    <tr>
                                        <td>{{ $part->event->title }}</td>
                                        <td>{{ $part->event->event_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($part->qr_scanned)
                                                <span class="badge bg-success">Attended</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Registered</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="qrModalLabel">My QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center" style="min-height: 300px;">
                    <div id="studentQrCodeSvg" style="width:80%; min-height:200px; text-align:center;"></div>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" id="qrModalOkBtn" data-bs-dismiss="modal">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assistant Switch Modal -->
    <div class="modal fade" id="assistantSwitchModal" tabindex="-1" aria-labelledby="assistantSwitchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('student.switch-to-assistant') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assistantSwitchModalLabel">Enter Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">To access the Organizational Dashboard, confirm your password.</p>
                    <input type="password" name="assistant_password" class="form-control" placeholder="Password" required />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Continue</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* Fix the page to prevent scrolling - page stays fixed, only calendar moves */
    html, body {
        height: 100%;
        overflow: hidden;
    }
    
    body {
        position: fixed;
        width: 100%;
    }
    
    .container-fluid {
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
    }
    
    .container-fluid > .row {
        min-height: 100vh;
    }
    
    /* Calendar sliding animation */
    #calendar-months-container {
        position: relative;
        overflow: hidden;
        min-height: 400px;
    }
    
    .calendar-slide-enter-right {
        animation: slideInFromRight 0.5s ease-out;
    }
    
    .calendar-slide-enter-left {
        animation: slideInFromLeft 0.5s ease-out;
    }
    
    .calendar-slide-exit-right {
        animation: slideOutToRight 0.5s ease-in;
    }
    
    .calendar-slide-exit-left {
        animation: slideOutToLeft 0.5s ease-in;
    }
    
    @keyframes slideOutToLeft {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(-100%);
            opacity: 0;
        }
    }
    
    @keyframes slideOutToRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes slideInFromRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideInFromLeft {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Minimalist dashboard overrides */
    .dashboard-header h1 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: .5rem;
    }
    .dashboard-header p {
        font-size: 1rem;
        color: var(--grey);
    }
    aside .card {
        box-shadow: none;
        border-radius: 8px;
        border: 1px solid #eee;
    }
    aside .card-header {
        font-size: 1.1rem;
        font-weight: 500;
        letter-spacing: .5px;
    }
    aside .btn, aside h6 {
        font-size: .95rem;
    }
    .card, .card-header, .card-body {
        background: #fff;
    }
    .card {
        border-radius: 10px;
        border: 1px solid #f0f0f0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
    .card-header {
        border-bottom: 1px solid #f0f0f0;
    }
    .table th, .table td {
        font-size: .95rem;
        padding: .5rem .75rem;
    }
    .btn, .form-control {
        border-radius: 6px;
    }
    .row.g-4 > * {
        padding-top: 0 !important;
    }
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    @media (max-width: 991px) {
        aside { margin-bottom: 2rem; }
    }
</style>
@endpush

@push('scripts')
<script>
// Calendar Navigation with AJAX - updates only the calendar without page reload
document.addEventListener('DOMContentLoaded', function() {
    const calendarNavButtons = document.querySelectorAll('.calendar-nav-btn');
    const calendarMonthsContainer = document.getElementById('calendar-months-container');
    const currentMonthDisplay = document.getElementById('current-month-display');
    
    if (calendarNavButtons.length > 0 && calendarMonthsContainer) {
        calendarNavButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const month = this.getAttribute('data-month');
                const isNext = this.textContent.includes('Next');
                const isPrevious = this.textContent.includes('Previous');
                
                // Disable buttons during loading
                calendarNavButtons.forEach(btn => btn.disabled = true);
                
                // Determine slide direction
                // Next button: current slides left, new slides in from right
                // Previous button: current slides right, new slides in from left
                const exitClass = isNext ? 'calendar-slide-exit-left' : 'calendar-slide-exit-right';
                const enterClass = isNext ? 'calendar-slide-enter-right' : 'calendar-slide-enter-left';
                
                // Remove any existing animation classes
                calendarMonthsContainer.classList.remove('calendar-slide-exit-left', 'calendar-slide-exit-right', 'calendar-slide-enter-left', 'calendar-slide-enter-right');
                
                // Add exit animation class to slide out current content
                calendarMonthsContainer.classList.add(exitClass);
                
                // After exit animation, fetch new content and slide in
                setTimeout(() => {
                    // Fetch calendar view via AJAX
                    fetch(`{{ route('student.dashboard.calendar') }}?month=${month}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Remove exit animation class
                        calendarMonthsContainer.classList.remove(exitClass);
                        
                        // Update calendar months container with new content
                        calendarMonthsContainer.innerHTML = html;
                        
                        // Add enter animation class to slide in new content
                        calendarMonthsContainer.classList.add(enterClass);
                        
                        // Update current month display
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        const firstMonthHeader = tempDiv.querySelector('.card-header strong');
                        if (firstMonthHeader) {
                            const monthText = firstMonthHeader.textContent.trim();
                            const monthMatch = monthText.match(/^(\w+)\s+(\d{4})/);
                            if (monthMatch) {
                                currentMonthDisplay.textContent = `${monthMatch[1]} ${monthMatch[2]}`;
                            }
                        }
                        
                        // Update button data-month attributes for next navigation
                        const selectedMonthDate = new Date(month + '-01');
                        const prevMonth = new Date(selectedMonthDate.getFullYear(), selectedMonthDate.getMonth() - 1, 1);
                        const nextMonth = new Date(selectedMonthDate.getFullYear(), selectedMonthDate.getMonth() + 1, 1);
                        
                        const formatMonth = (date) => {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            return `${year}-${month}`;
                        };
                        
                        calendarNavButtons.forEach(btn => {
                            if (btn.textContent.includes('Previous')) {
                                btn.setAttribute('data-month', formatMonth(prevMonth));
                            } else if (btn.textContent.includes('Next')) {
                                btn.setAttribute('data-month', formatMonth(nextMonth));
                            }
                        });
                        
                        // Remove enter animation class after animation completes
                        setTimeout(() => {
                            calendarMonthsContainer.classList.remove(enterClass);
                        }, 500);
                        
                        // Re-enable buttons
                        calendarNavButtons.forEach(btn => btn.disabled = false);
                    })
                    .catch(error => {
                        console.error('Error loading calendar:', error);
                        calendarMonthsContainer.classList.remove(exitClass);
                        calendarMonthsContainer.innerHTML = '<div class="col-12 text-center py-4 text-danger">Error loading calendar. Please refresh the page.</div>';
                        calendarNavButtons.forEach(btn => btn.disabled = false);
                    });
                }, 500); // Wait for exit animation to complete (500ms)
            });
        });
    }
});

// QR Code Modal Script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up QR Code Modal');
    
    var qrModal = document.getElementById('qrModal');
    var qrBtn = document.querySelector('[data-bs-target="#qrModal"]');
    
    console.log('QR Button found:', qrBtn);
    console.log('QR Modal found:', qrModal);
    
    // Function to load QR code
    function loadQRCode() {
        console.log('Loading QR code');
        var qrDiv = document.getElementById('studentQrCodeSvg');
        if (qrDiv) {
            qrDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading QR code...</span></div><p class="text-muted mt-2">Loading QR code...</p></div>';
            console.log('Fetching QR code from:', "{{ route('student.qr-code') }}");
            fetch("{{ route('student.qr-code') }}", {
                headers: {
                    'Accept': 'image/svg+xml, text/html, application/xhtml+xml, */*',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function(response) {
                    console.log('QR code fetch response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.text();
                })
                .then(function(svg) {
                    console.log('QR code SVG received, length:', svg.length);
                    qrDiv.innerHTML = svg;
                })
                .catch(function(error) {
                    console.error('Error loading QR code:', error);
                    qrDiv.innerHTML = '<div class="text-center py-4"><span class="text-danger">Failed to load QR code. Please try again.</span></div>';
                });
        } else {
            console.error('QR Div not found');
        }
    }
    
    if (qrBtn && qrModal) {
        // Store a flag to prevent double-loading
        var isLoading = false;
        
        qrBtn.addEventListener('click', function(e) {
            console.log('QR Button clicked!');
            if (isLoading) {
                console.log('Already loading, ignoring click');
                return;
            }
            isLoading = true;
            
            // Manually trigger the modal if Bootstrap didn't
            setTimeout(function() {
                if (!qrModal.classList.contains('show')) {
                    console.log('Modal not shown by Bootstrap, manually opening');
                    try {
                        var bsModal = new bootstrap.Modal(qrModal);
                        bsModal.show();
                        // Manually trigger QR code load since show.bs.modal won't fire
                        setTimeout(function() {
                            loadQRCode();
                            isLoading = false;
                        }, 50);
                    } catch(err) {
                        console.error('Error opening modal:', err);
                        isLoading = false;
                    }
                } else {
                    isLoading = false;
                }
            }, 100);
        });
    }
    
    if (qrModal) {
        console.log('QR Modal found, adding event listeners');
        qrModal.addEventListener('show.bs.modal', function() {
            console.log('QR Modal is being shown');
            loadQRCode();
        });
        qrModal.addEventListener('hidden.bs.modal', function() {
            var qrDiv = document.getElementById('studentQrCodeSvg');
            if (qrDiv) qrDiv.innerHTML = '';
        });
    } else {
        console.error('QR Modal not found!');
    }
});

// Modal Focus Script
document.addEventListener('shown.bs.modal', function (event) {
    if (event.target && event.target.id === 'qrModal') {
        const okBtn = event.target.querySelector('#qrModalOkBtn');
        if (okBtn) okBtn.focus();
    }
});

// Auto-open modal if hash is #orgRegModal
document.addEventListener('DOMContentLoaded', function () {
        // Fallback: open modal if hash is present
        if (window.location.hash === '#orgRegModal') {
            var modal = document.getElementById('orgRegModal');
            if (modal) {
                try {
                    var bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                } catch (e) {
                    modal.classList.add('show');
                    modal.style.display = 'block';
                    modal.setAttribute('aria-modal', 'true');
                    modal.removeAttribute('aria-hidden');
                }
            }
        }

        // Fallback: open modal on button click if Bootstrap fails
        var orgBtn = document.querySelector('[data-bs-target="#orgRegModal"]');
        var modal = document.getElementById('orgRegModal');
        if (orgBtn && modal) {
            orgBtn.addEventListener('click', function(e) {
                try {
                    var bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                } catch (err) {
                    modal.classList.add('show');
                    modal.style.display = 'block';
                    modal.setAttribute('aria-modal', 'true');
                    modal.removeAttribute('aria-hidden');
                }
            });
        }

        // Fallback for View My Appointments Modal
        var viewAppointmentsBtn = document.querySelector('[data-bs-target="#viewAppointmentsModal"]');
        var viewAppointmentsModal = document.getElementById('viewAppointmentsModal');
        if (viewAppointmentsBtn && viewAppointmentsModal) {
            viewAppointmentsBtn.addEventListener('click', function(e) {
                try {
                    var bsModal = new bootstrap.Modal(viewAppointmentsModal);
                    bsModal.show();
                } catch (err) {
                    viewAppointmentsModal.classList.add('show');
                    viewAppointmentsModal.style.display = 'block';
                    viewAppointmentsModal.setAttribute('aria-modal', 'true');
                    viewAppointmentsModal.removeAttribute('aria-hidden');
                }
            });
        }

    });
</script>
@endpush