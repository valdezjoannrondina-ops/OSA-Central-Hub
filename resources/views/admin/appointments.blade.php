
@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
  <div class="container-fluid">
      <div class="row">
        @include('admin.partials.sidebar')
    <main class="col-md-10">
      <div class="admin-back-btn-wrap">
        @if(request()->has('return_to'))
          <a href="{{ urldecode(request('return_to')) }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Dashboard</a>
        @else
          <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Dashboard</a>
        @endif
      </div>
      <style>
        .section-header { display:block; width:100%; box-sizing:border-box; background:#fff; color: midnightblue; padding:.5rem 1rem; border:none; border-bottom:1px solid midnightblue; border-radius:0; }
      </style>
      <h2 class="mb-3"><span class="section-header">Appointments</h2>
      @if(isset($isAdmin) && $isAdmin)
      <form method="GET" class="row g-2 align-items-end mb-3">
        @if(request()->has('return_to'))
          <input type="hidden" name="return_to" value="{{ request('return_to') }}">
        @endif
        <div class="col-auto">
          <label for="assigned_staff_id" class="form-label">Assigned Staff</label>
          <select name="assigned_staff_id" id="assigned_staff_id" class="form-select">
            <option value="">All</option>
            <option value="unassigned" {{ (isset($filterAssigned) && $filterAssigned==='unassigned') ? 'selected' : '' }}>Unassigned</option>
            @foreach($staffList as $s)
              <option value="{{ $s->id }}" {{ (isset($filterAssigned) && (string)$filterAssigned === (string)$s->id) ? 'selected' : '' }}>
                {{ $s->first_name }} {{ $s->last_name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Filter</button>
          @if(request()->has('return_to'))
            <a href="{{ route('admin.appointments.index', ['return_to' => request('return_to')]) }}" class="btn btn-outline-secondary">Reset</a>
          @else
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-secondary">Reset</a>
          @endif
        </div>
      </form>
      @elseif((isset($isStaff) && $isStaff && !$isAdmin))
      <p class="text-muted mb-3">Showing appointments assigned to you ({{ isset($userDesignation) && $userDesignation ? $userDesignation : 'Staff' }}).</p>
      @endif

      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead>
            <tr class="text-center" style="background-color:midnightblue; color:white">
              <th>Full Name</th>
              <th>Email</th>
              <th>Appointment Date</th>
              <th>Schedule Time</th>
              <th>Category / Concern</th>
              <th>Assigned Staff</th>
              <th>Actions Taken</th>
              <th>Status</th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($appointments as $appointment)
              <tr>
                <td>{{ $appointment->full_name }}</td>
                <td>{{ $appointment->email }}</td>
                <td>{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                <td>{{ $appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : '-' }}</td>
                <td>
                  @php
                    $isGuidanceCounselor = $appointment->concern && (
                        stripos($appointment->concern, 'Guidance') !== false && 
                        stripos($appointment->concern, 'Counsellor') !== false
                    );
                  @endphp
                  @if($isGuidanceCounselor && $appointment->category)
                    @php
                      $categoryColors = [
                        'Red' => '#dc3545',
                        'Blue' => '#0d6efd',
                        'Yellow' => '#ffc107'
                      ];
                      $bgColor = $categoryColors[$appointment->category] ?? '#6c757d';
                    @endphp
                    <span style="background-color: {{ $bgColor }}; color: white; padding: 4px 12px; border-radius: 4px; font-weight: bold;">
                      {{ $appointment->category }}
                    </span>
                  @else
                    {{ $appointment->concern }}
                  @endif
                </td>
                <td>
                  @if(!empty($appointment->concern))
                    {{ $appointment->concern }}
                  @elseif($appointment->assignedStaff)
                    {{ $appointment->assignedStaff->first_name }} {{ $appointment->assignedStaff->last_name }}
                  @else
                    <span class="text-muted">Unassigned</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($appointment->status === 'pending')
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $appointment->id }}">
                        <i class="bi bi-check-circle"></i> Approve
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#declineModal{{ $appointment->id }}">
                        <i class="bi bi-x-circle"></i> Decline
                      </button>
                      <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#rescheduleModal{{ $appointment->id }}">
                        <i class="bi bi-calendar-event"></i> Reschedule
                      </button>
                    </div>
                  @else
                    @if($appointment->action_taken === 'approve')
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> Approved
                      </span>
                    @elseif($appointment->action_taken === 'decline')
                      <span class="badge bg-danger">
                        <i class="bi bi-x-circle"></i> Declined
                        @if($appointment->action_reason)
                          <br><small class="d-block mt-1" style="font-size: 0.75em; font-weight: normal;">{{ \Illuminate\Support\Str::limit($appointment->action_reason, 50) }}</small>
                        @endif
                      </span>
                    @elseif($appointment->action_taken === 'reschedule')
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-calendar-event"></i> Rescheduled
                        @if($appointment->rescheduled_date)
                          <br><small class="d-block mt-1" style="font-size: 0.75em; font-weight: normal;">
                            {{ \Carbon\Carbon::parse($appointment->rescheduled_date)->format('M d, Y') }}
                            @if($appointment->rescheduled_time)
                              {{ date('g:i A', strtotime($appointment->rescheduled_time)) }}
                            @endif
                          </small>
                        @endif
                      </span>
                    @else
                      <span class="badge bg-secondary">Pending</span>
                    @endif
                  @endif
                </td>
                <td>{{ ucfirst($appointment->status) }}</td>
                <td>{{ optional($appointment->created_at)->format('M d, Y g:i A') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-5">
                  <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>No data available</strong>
                    <p class="mb-0 mt-2">@if(isset($isStaff) && $isStaff && !$isAdmin)You have no assigned appointments at this time.@else No appointments found.@endif</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        {{ $appointments->links() }}
      </div>

      <!-- Approve Modal -->
      @foreach($appointments as $appointment)
        @if($appointment->status === 'pending')
          <!-- Approve Modal -->
          <div class="modal fade" id="approveModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $appointment->id }}" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ route('admin.appointments.approve', $appointment->id) }}" method="POST">
                  @csrf
                  @if(request()->has('return_to'))
                    <input type="hidden" name="return_to" value="{{ request('return_to') }}">
                  @endif
                  <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel{{ $appointment->id }}">Approve Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want to approve this appointment?</p>
                    <p><strong>Name:</strong> {{ $appointment->full_name }}</p>
                    <p><strong>Date:</strong> {{ optional($appointment->appointment_date)->format('M d, Y') }}</p>
                    <p><strong>Time:</strong> {{ $appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : '-' }}</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Decline Modal -->
          <div class="modal fade" id="declineModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="declineModalLabel{{ $appointment->id }}" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ route('admin.appointments.decline', $appointment->id) }}" method="POST">
                  @csrf
                  @if(request()->has('return_to'))
                    <input type="hidden" name="return_to" value="{{ request('return_to') }}">
                  @endif
                  <div class="modal-header">
                    <h5 class="modal-title" id="declineModalLabel{{ $appointment->id }}">Decline Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p><strong>Name:</strong> {{ $appointment->full_name }}</p>
                    <p><strong>Date:</strong> {{ optional($appointment->appointment_date)->format('M d, Y') }}</p>
                    <p><strong>Time:</strong> {{ $appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : '-' }}</p>
                    <div class="mb-3 mt-3">
                      <label for="declineReason{{ $appointment->id }}" class="form-label">
                        Reason for declining <span class="text-danger">*</span>
                      </label>
                      <textarea class="form-control" id="declineReason{{ $appointment->id }}" name="reason" rows="3" required placeholder="Please provide a short description why this appointment is declined..."></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Reschedule Modal -->
          <div class="modal fade" id="rescheduleModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="rescheduleModalLabel{{ $appointment->id }}" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="{{ route('admin.appointments.reschedule', $appointment->id) }}" method="POST">
                  @csrf
                  @method('PUT')
                  @if(request()->has('return_to'))
                    <input type="hidden" name="return_to" value="{{ request('return_to') }}">
                  @endif
                  <div class="modal-header">
                    <h5 class="modal-title" id="rescheduleModalLabel{{ $appointment->id }}">Reschedule Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p><strong>Original Appointment:</strong></p>
                    <p><strong>Name:</strong> {{ $appointment->full_name }}</p>
                    <p><strong>Date:</strong> {{ optional($appointment->appointment_date)->format('M d, Y') }}</p>
                    <p><strong>Time:</strong> {{ $appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : '-' }}</p>
                    
                    <hr>
                    
                    <div class="row g-3 mt-2">
                      <div class="col-md-6">
                        <label for="rescheduleDate{{ $appointment->id }}" class="form-label">
                          New Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="rescheduleDate{{ $appointment->id }}" name="appointment_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                      </div>
                      <div class="col-md-6">
                        <label for="rescheduleTime{{ $appointment->id }}" class="form-label">
                          New Time <span class="text-danger">*</span>
                        </label>
                        <input type="time" class="form-control" id="rescheduleTime{{ $appointment->id }}" name="appointment_time" required>
                      </div>
                      <div class="col-12">
                        <label for="rescheduleReason{{ $appointment->id }}" class="form-label">
                          Reason for rescheduling (optional)
                        </label>
                        <textarea class="form-control" id="rescheduleReason{{ $appointment->id }}" name="reschedule_reason" rows="3" placeholder="Please provide a short description why this appointment is rescheduled (optional)..."></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reschedule</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif
      @endforeach

      <style>
      /* Ensure modals are above navbar and accessible */
      .modal {
          z-index: 10000 !important;
          position: fixed !important;
          top: 0 !important;
          left: 0 !important;
          width: 100% !important;
          height: 100% !important;
      }
      .modal-backdrop {
          z-index: 9999 !important;
          background-color: rgba(0, 0, 0, 0.5) !important;
          position: fixed !important;
          top: 0 !important;
          left: 0 !important;
          width: 100vw !important;
          height: 100vh !important;
          pointer-events: auto !important;
      }
      .modal-backdrop ~ .modal {
          pointer-events: none !important;
      }
      .modal-backdrop ~ .modal .modal-dialog {
          pointer-events: auto !important;
      }
      .modal-backdrop ~ .modal .modal-content {
          pointer-events: auto !important;
      }
      .modal-dialog {
          pointer-events: auto !important;
          z-index: 10001 !important;
          margin: 30px auto !important;
          position: relative !important;
          max-width: 500px !important;
          pointer-events: auto !important;
      }
      .modal-content {
          pointer-events: auto !important;
          position: relative !important;
          z-index: 10002 !important;
          background-color: #fff !important;
          pointer-events: auto !important;
      }
      .modal.show * {
          pointer-events: auto !important;
      }
      .modal.show .modal-dialog * {
          pointer-events: auto !important;
      }
      .modal.show .modal-content * {
          pointer-events: auto !important;
      }
      .modal.show {
          display: block !important;
          overflow-x: hidden !important;
          overflow-y: auto !important;
          opacity: 1 !important;
          visibility: visible !important;
      }
      .modal.show .modal-dialog {
          -webkit-transform: translate(0, 0) !important;
          transform: translate(0, 0) !important;
          opacity: 1 !important;
          visibility: visible !important;
      }
      .modal.show .modal-content {
          opacity: 1 !important;
          visibility: visible !important;
      }
      body.modal-open {
          overflow: hidden !important;
          padding-right: 0 !important;
      }
      /* Force modal visibility when forced */
      .modal.force-show {
          display: block !important;
          opacity: 1 !important;
          visibility: visible !important;
          z-index: 10000 !important;
      }
      .modal.force-show .modal-dialog {
          opacity: 1 !important;
          visibility: visible !important;
          transform: translate(0, 0) !important;
      }
      .modal.force-show .modal-content {
          opacity: 1 !important;
          visibility: visible !important;
      }
      </style>
      
      @push('scripts')
      <script>
      // Wait for jQuery and Bootstrap to load, then ensure modals work
      (function() {
          function initModals() {
              // Check if jQuery is available
              if (typeof jQuery === 'undefined') {
                  console.log('jQuery not loaded yet, retrying...');
                  setTimeout(initModals, 100);
                  return;
              }
              
          var $ = jQuery;
          
          // Check if Bootstrap modal is available
          if (typeof $.fn.modal === 'undefined') {
                  console.log('Bootstrap modal not loaded yet, retrying...');
                  setTimeout(initModals, 100);
                  return;
          }
          
          console.log('jQuery version:', $.fn.jquery);
          console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
          
          // Count buttons
          var modalButtons = $('[data-toggle="modal"]');
          console.log('Found', modalButtons.length, 'modal trigger buttons');
          
          // Test each button and modal
          modalButtons.each(function() {
              var $btn = $(this);
              var target = $btn.attr('data-target');
              console.log('Button:', $btn[0], 'Target:', target);
              
              if (target) {
                  var $modal = $(target);
                  if ($modal.length > 0) {
                      console.log('Modal exists:', target);
                  } else {
                      console.error('Modal NOT found:', target);
                  }
              }
          });
          
          // Add our own handler that will definitely work
          $(document).on('click', '[data-toggle="modal"]', function(e) {
              var $btn = $(this);
              var target = $btn.attr('data-target');
              
              console.log('Button clicked! Target:', target);
              
              if (!target) {
                  target = $btn.data('target');
              }
              
              if (!target) {
                  console.error('No target specified');
                  return;
              }
              
              var $modal = $(target);
              
              if ($modal.length === 0) {
                  console.error('Modal element not found:', target);
                  alert('Modal not found: ' + target);
                  return;
              }
              
              console.log('Opening modal:', target);
              
              // Clean up
              $('.modal.show').not($modal).removeClass('show').css('display', 'none');
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              
              // Force show the modal with explicit steps
              try {
                  // Remove fade class temporarily to show immediately, then add it back
                  var hadFade = $modal.hasClass('fade');
                  $modal.removeClass('fade');
                  
                  // Show modal
                  $modal.modal({
                      backdrop: true,
                      keyboard: true,
                      show: true
                  });
                  
                  // Re-add fade class after a moment
                  if (hadFade) {
                      setTimeout(function() {
                          $modal.addClass('fade');
                      }, 300);
                  }
                  
                  console.log('Modal.show() called successfully');
                  
                  // Ensure modal is visible immediately
                  setTimeout(function() {
                      if (!$modal.hasClass('show')) {
                          console.log('Modal not showing, forcing display...');
                          $modal.addClass('show force-show');
                          $modal.attr('style', 'display: block !important; opacity: 1 !important; visibility: visible !important; z-index: 10000 !important; padding-right: 0 !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; overflow-x: hidden !important; overflow-y: auto !important;');
                          $modal.attr('aria-hidden', 'false');
                          $modal.removeAttr('aria-hidden');
                          
                          // Also ensure dialog is visible and positioned correctly
                          var $dialog = $modal.find('.modal-dialog');
                          if ($dialog.length > 0) {
                              $dialog.attr('style', 'opacity: 1 !important; visibility: visible !important; transform: translate(0, 0) !important; z-index: 10001 !important; margin: 30px auto !important; position: relative !important; display: block !important; pointer-events: auto !important;');
                              console.log('Dialog style applied');
                          } else {
                              console.error('Dialog not found in modal!');
                          }
                          
                          // Ensure content is visible and interactive
                          var $content = $modal.find('.modal-content');
                          if ($content.length > 0) {
                              $content.attr('style', 'opacity: 1 !important; visibility: visible !important; z-index: 10002 !important; background-color: #fff !important; display: block !important; pointer-events: auto !important;');
                              console.log('Content style applied');
                              
                              // Ensure all form elements are interactive
                              $content.find('input, textarea, select, button').css('pointer-events', 'auto');
                          } else {
                              console.error('Content not found in modal!');
                          }
                          
                          // Make sure modal structure allows clicks through empty space but not through content
                          $modal.css('pointer-events', 'none');
                          
                          // Remove old backdrops first
                          $('.modal-backdrop').remove();
                          
                          // Create backdrop with correct z-index
                          var $backdrop = $('<div class="modal-backdrop fade show"></div>').css({
                              'z-index': '9999',
                              'position': 'fixed',
                              'top': '0',
                              'left': '0',
                              'width': '100vw',
                              'height': '100vh',
                              'background-color': 'rgba(0, 0, 0, 0.5)',
                              'pointer-events': 'auto'
                          });
                          $('body').append($backdrop);
                          
                          // Enable pointer events on dialog and content so they're clickable
                          $dialog.css('pointer-events', 'auto');
                          $content.css('pointer-events', 'auto');
                          
                          // Ensure all interactive elements are clickable
                          $content.find('input, textarea, select, button, a').css('pointer-events', 'auto');
                          
                          // Make backdrop clickable to close modal
                          $backdrop.off('click.modal-close');
                          $backdrop.on('click.modal-close', function(e) {
                              if ($(e.target).is('.modal-backdrop')) {
                                  $modal.modal('hide');
                                  $backdrop.remove();
                                  $('body').removeClass('modal-open');
                              }
                          });
                          $('body').addClass('modal-open');
                          
                          console.log('Modal forced to show');
                          console.log('Modal display:', $modal.css('display'));
                          console.log('Modal visibility:', $modal.css('visibility'));
                          console.log('Modal z-index:', $modal.css('z-index'));
                          console.log('Modal position:', $modal.css('position'));
                          console.log('Modal top:', $modal.css('top'));
                          console.log('Modal left:', $modal.css('left'));
                          console.log('Modal width:', $modal.css('width'));
                          console.log('Modal height:', $modal.css('height'));
                          
                          // Check dialog
                          var $dialog = $modal.find('.modal-dialog');
                          console.log('Dialog display:', $dialog.css('display'));
                          console.log('Dialog visibility:', $dialog.css('visibility'));
                          console.log('Dialog opacity:', $dialog.css('opacity'));
                          
                          // Check content
                          var $content = $modal.find('.modal-content');
                          console.log('Content display:', $content.css('display'));
                          console.log('Content visibility:', $content.css('visibility'));
                          console.log('Content background:', $content.css('background-color'));
                      } else {
                          // Modal has 'show' class but might still not be visible
                          console.log('Modal has show class, checking visibility...');
                          var modalDisplay = $modal.css('display');
                          if (modalDisplay === 'none' || modalDisplay === '') {
                              console.log('Modal display is none, forcing...');
                              $modal.addClass('force-show');
                              $modal.attr('style', 'display: block !important; opacity: 1 !important; visibility: visible !important; z-index: 10000 !important;');
                              
                              var $dialog = $modal.find('.modal-dialog');
                              $dialog.attr('style', 'opacity: 1 !important; visibility: visible !important; transform: translate(0, 0) !important;');
                              
                              var $content = $modal.find('.modal-content');
                              $content.attr('style', 'opacity: 1 !important; visibility: visible !important;');
                          }
                      }
                  }, 50);
                  
              } catch (err) {
                  console.error('Error calling modal.show():', err);
                  // Fallback manual show
                  $modal.addClass('show');
                  $modal.css({
                      'display': 'block',
                      'padding-right': '0'
                  });
                  $modal.attr('aria-hidden', 'false');
                  $modal.removeAttr('aria-hidden');
                  $('body').addClass('modal-open');
                  if ($('.modal-backdrop').length === 0) {
                      $('<div class="modal-backdrop fade show"></div>').appendTo('body');
                  }
                  console.log('Used fallback to show modal');
              }
              
              // Focus on first input after modal is shown
              $modal.one('shown.bs.modal', function() {
                  console.log('Modal shown event fired');
                  var $input = $(this).find('textarea, input:not([type="hidden"]), select').first();
                  if ($input.length > 0) {
                      setTimeout(function() {
                          $input.focus();
                      }, 300);
                  }
              });
              
              // Also try focusing after a delay in case event doesn't fire
              setTimeout(function() {
                  if ($modal.hasClass('show')) {
                      var $input = $modal.find('textarea, input:not([type="hidden"]), select').first();
                      if ($input.length > 0) {
                          $input.focus();
                      }
                  }
              }, 500);
          });
          
          console.log('Modal handlers attached successfully');
          }
          
          // Start when DOM is ready
          if (document.readyState === 'loading') {
              document.addEventListener('DOMContentLoaded', initModals);
          } else {
              initModals();
          }
          
          // Also try when jQuery is ready (in case jQuery loads after DOM)
          if (typeof jQuery !== 'undefined') {
              jQuery(document).ready(initModals);
          }
      })();
      </script>
      @endpush
        </main>
      </div>
    </div>
@endsection