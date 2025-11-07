@extends('layouts.app')

@section('title', 'My Organizations')

@section('content')
<div class="container-fluid">
  
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
        @if($isAdmin)
          <a href="{{ route('admin.events.index') }}#create" class="list-group-item list-group-item-action">Create Event</a>
        @endif
      </div>
    </div>
    <main class="col-md-9 col-lg-10">
      <h2 class="mb-3">My Organizations</h2>
      
      @if(!isset($organizationsWithStats) || $organizationsWithStats->isEmpty())
        <div class="alert alert-info">
          <p>You are not assigned to any organizations yet.</p>
        </div>
      @else
        <div class="row">
          @foreach($organizationsWithStats as $orgData)
            @php
              $organization = $orgData['organization'];
              $totalMembers = $orgData['total_members'];
              $maleCount = $orgData['male_count'];
              $femaleCount = $orgData['female_count'];
              $otherCount = $orgData['other_count'];
              $yearLevelCounts = $orgData['year_level_counts'];
            @endphp
            <div class="col-md-6 mb-4">
              <div class="card">
                <div class="card-header" style="background-color: midnightblue; color: white;">
                  <h5 class="mb-0">
                    <a href="{{ route('admin.organizations.profile', $organization->id) }}" style="color: white; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                      {{ $organization->name }}
                    </a>
                  </h5>
                  @if($organization->department)
                    <small>{{ $organization->department->name }} - Academic Organization</small>
                  @else
                    <small>Non-Academic Organization</small>
                  @endif
                </div>
                <div class="card-body">
                  <!-- Organizational Profile Section -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3" style="color: midnightblue; font-weight: bold;">ORGANIZATIONAL PROFILE</h6>
                    <div class="row g-2 mb-2">
                      <div class="col-5">
                        <strong>Acronym:</strong>
                      </div>
                      <div class="col-7">
                        {{ $organization->acronym ?? 'N/A' }}
                      </div>
                    </div>
                    <div class="row g-2 mb-2">
                      <div class="col-5">
                        <strong>Mailing Address:</strong>
                      </div>
                      <div class="col-7">
                        {{ $organization->mailing_address ?? 'N/A' }}
                      </div>
                    </div>
                    <div class="row g-2 mb-2">
                      <div class="col-5">
                        <strong>Org. Email Address:</strong>
                      </div>
                      <div class="col-7">
                        {{ $organization->official_email ?? 'N/A' }}
                      </div>
                    </div>
                    <div class="row g-2 mb-3">
                      <div class="col-5">
                        <strong>Date Established:</strong>
                      </div>
                      <div class="col-7">
                        {{ $organization->date_established ? \Carbon\Carbon::parse($organization->date_established)->format('F d, Y') : 'N/A' }}
                      </div>
                    </div>
                  </div>

                  <!-- Membership Distribution Table -->
                  <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3" style="color: midnightblue; font-weight: bold;">MEMBERSHIP DISTRIBUTION</h6>
                    <div class="table-responsive">
                      <table class="table table-bordered table-sm" style="font-size: 0.875rem;">
                        <thead style="background-color: #f0f0f0;">
                          <tr class="text-center">
                            <th rowspan="2" style="vertical-align: middle; width: 80px;">YEAR LEVEL</th>
                            <th colspan="3" style="border-bottom: 1px solid #ddd;">GENDER</th>
                            <th rowspan="2" style="vertical-align: middle;">TOTAL</th>
                          </tr>
                          <tr style="border-top: none;">
                            <th>Male</th>
                            <th>Female</th>
                            <th>Other</th>
                          </tr>
                        </thead>
                        <tbody>
                          @for($year = 1; $year <= 5; $year++)
                            <tr class="text-center">
                              <td><strong>{{ $year }}{{ $year == 1 ? 'st' : ($year == 2 ? 'nd' : ($year == 3 ? 'rd' : 'th')) }}</strong></td>
                              <td>{{ $yearLevelCounts[$year]['male'] ?? 0 }}</td>
                              <td>{{ $yearLevelCounts[$year]['female'] ?? 0 }}</td>
                              <td>{{ $yearLevelCounts[$year]['other'] ?? 0 }}</td>
                              <td><strong>{{ $yearLevelCounts[$year]['total'] ?? 0 }}</strong></td>
                            </tr>
                          @endfor
                          <tr class="text-center" style="background-color: #f0f0f0; font-weight: bold;">
                            <td><strong>TOTAL</strong></td>
                            <td>{{ $maleCount }}</td>
                            <td>{{ $femaleCount }}</td>
                            <td>{{ $otherCount }}</td>
                            <td><strong>{{ $totalMembers }}</strong></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="d-flex flex-column gap-2 border-top pt-3">
                    <a href="{{ route('staff.organizations.assistants', $organization->id) }}" class="btn btn-primary">
                      <i class="bi bi-people"></i> My Assistant Staff
                    </a>
                    <a href="{{ route('staff.assistants.create', ['organization_id' => $organization->id]) }}" class="btn btn-success">
                      <i class="bi bi-person-plus"></i> Add Assistant
                    </a>
                    <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.create-event', ['organization_id' => $organization->id]) }}" class="btn btn-warning">
                      <i class="bi bi-calendar-event"></i> Create Event
                    </a>
                    <a href="{{ route('staff.organization-files.index', $organization->id) }}" class="btn btn-info">
                      <i class="bi bi-folder"></i> Organization Files
                    </a>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                      <i class="bi bi-calendar-check"></i> All Events
                    </a>
                    <a href="{{ route('admin.participants.export') }}" class="btn btn-secondary">
                      <i class="bi bi-person-lines-fill"></i> Participants History
                    </a>
                  </div>
                  
                  <!-- QR Code Scanner Section -->
                  <div class="mt-4 border-top pt-3">
                    <h6 class="border-bottom pb-2 mb-3" style="color: midnightblue; font-weight: bold;">QR CODE SCANNER - EVENT PARTICIPATION</h6>
                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <label for="qrEventSelect{{ $organization->id }}">Select Event:</label>
                        <select id="qrEventSelect{{ $organization->id }}" class="form-control" required>
                          <option value="">-- Select an Event --</option>
                          @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title ?? $event->name }} ({{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }})</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-12 mb-3">
                        <label for="qrScannerInput{{ $organization->id }}" class="form-label">QR Code Data:</label>
                        <div class="input-group">
                          <input type="text" id="qrScannerInput{{ $organization->id }}" class="form-control" placeholder="Scan QR code or paste QR data here" autocomplete="off">
                          <button type="button" id="startCameraBtn{{ $organization->id }}" class="btn btn-primary">
                            <i class="bi bi-camera"></i> Start Camera
                          </button>
                          <button type="button" id="stopCameraBtn{{ $organization->id }}" class="btn btn-secondary" style="display: none;">
                            <i class="bi bi-camera-video-off"></i> Stop Camera
                          </button>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Camera Preview -->
                    <div id="cameraPreview{{ $organization->id }}" class="mb-3" style="display: none;">
                      <video id="qrVideo{{ $organization->id }}" width="100%" height="300" style="border: 2px solid midnightblue; border-radius: 4px;"></video>
                      <canvas id="qrCanvas{{ $organization->id }}" style="display: none;"></canvas>
                    </div>
                    
                    <!-- Scan Results -->
                    <div id="scanResults{{ $organization->id }}" class="mt-3"></div>
                    
                    <!-- Recent Scans -->
                    <div class="mt-4">
                      <h6>Recent Scans</h6>
                      <div id="recentScans{{ $organization->id }}" class="list-group" style="max-height: 300px; overflow-y: auto;">
                        <p class="text-muted">No scans yet. Start scanning to see results here.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </main>
  </div>
</div>

@push('scripts')
<script>
  // QR Code Scanner functionality for each organization
  @foreach($organizationsWithStats as $orgData)
    @php
      $organization = $orgData['organization'];
    @endphp
    (function() {
      const orgId = {{ $organization->id }};
      let qrStream{{ $organization->id }} = null;
      let qrScanInterval{{ $organization->id }} = null;
      const recentScans{{ $organization->id }} = [];
      
      const qrScannerInput = document.getElementById('qrScannerInput' + orgId);
      const qrEventSelect = document.getElementById('qrEventSelect' + orgId);
      const startCameraBtn = document.getElementById('startCameraBtn' + orgId);
      const stopCameraBtn = document.getElementById('stopCameraBtn' + orgId);
      const cameraPreview = document.getElementById('cameraPreview' + orgId);
      const qrVideo = document.getElementById('qrVideo' + orgId);
      const qrCanvas = document.getElementById('qrCanvas' + orgId);
      const scanResults = document.getElementById('scanResults' + orgId);
      const recentScansDiv = document.getElementById('recentScans' + orgId);
      
      if (!qrScannerInput || !qrEventSelect) return;
      
      // Manual QR code input
      qrScannerInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          processQRCode(qrScannerInput.value);
        }
      });
      
      // Start camera
      if (startCameraBtn) {
        startCameraBtn.addEventListener('click', function() {
          startQRScanner();
        });
      }
      
      // Stop camera
      if (stopCameraBtn) {
        stopCameraBtn.addEventListener('click', function() {
          stopQRScanner();
        });
      }
      
      function startQRScanner() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
          .then(function(stream) {
            qrStream{{ $organization->id }} = stream;
            qrVideo.srcObject = stream;
            cameraPreview.style.display = 'block';
            if (startCameraBtn) startCameraBtn.style.display = 'none';
            if (stopCameraBtn) stopCameraBtn.style.display = 'inline-block';
            
            // Start scanning with jsQR library
            qrScanInterval{{ $organization->id }} = setInterval(function() {
              if (qrVideo.readyState === qrVideo.HAVE_ENOUGH_DATA) {
                qrCanvas.width = qrVideo.videoWidth;
                qrCanvas.height = qrVideo.videoHeight;
                const ctx = qrCanvas.getContext('2d');
                ctx.drawImage(qrVideo, 0, 0, qrCanvas.width, qrCanvas.height);
                const imageData = ctx.getImageData(0, 0, qrCanvas.width, qrCanvas.height);
                
                if (typeof jsQR !== 'undefined') {
                  const code = jsQR(imageData.data, imageData.width, imageData.height);
                  if (code) {
                    processQRCode(code.data);
                    qrScannerInput.value = code.data;
                  }
                }
              }
            }, 500);
          })
          .catch(function(err) {
            alert('Camera access denied. Please allow camera access or use manual input.');
            console.error('Camera error:', err);
          });
      }
      
      function stopQRScanner() {
        if (qrStream{{ $organization->id }}) {
          qrStream{{ $organization->id }}.getTracks().forEach(track => track.stop());
          qrStream{{ $organization->id }} = null;
        }
        if (qrScanInterval{{ $organization->id }}) {
          clearInterval(qrScanInterval{{ $organization->id }});
          qrScanInterval{{ $organization->id }} = null;
        }
        cameraPreview.style.display = 'none';
        if (startCameraBtn) startCameraBtn.style.display = 'inline-block';
        if (stopCameraBtn) stopCameraBtn.style.display = 'none';
        qrVideo.srcObject = null;
      }
      
      function processQRCode(qrData) {
        if (!qrData || qrData.trim() === '') {
          showMessage('Please enter or scan a QR code.', 'warning');
          return;
        }
        
        const eventId = qrEventSelect.value;
        if (!eventId) {
          showMessage('Please select an event first.', 'warning');
          return;
        }
        
        scanResults.innerHTML = '<div class="alert alert-info"><i class="bi bi-hourglass-split"></i> Processing QR code...</div>';
        
        fetch('{{ route("admin.qr.scan") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            qr_data: qrData,
            event_id: eventId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showMessage(data.message, 'success');
            addRecentScan(data.student, data.event, new Date().toLocaleString());
            qrScannerInput.value = '';
            qrScannerInput.focus();
          } else {
            showMessage(data.message, 'danger');
          }
        })
        .catch(error => {
          console.error('QR scan error:', error);
          showMessage('An error occurred while processing the QR code.', 'danger');
        });
      }
      
      function showMessage(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : 'alert-danger';
        scanResults.innerHTML = `<div class="alert ${alertClass} alert-dismissible fade show">
          ${message}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>`;
        
        setTimeout(function() {
          const alert = scanResults.querySelector('.alert');
          if (alert) {
            alert.remove();
          }
        }, 5000);
      }
      
      function addRecentScan(student, event, timestamp) {
        recentScans{{ $organization->id }}.unshift({ student, event, timestamp });
        if (recentScans{{ $organization->id }}.length > 10) {
          recentScans{{ $organization->id }}.pop();
        }
        
        if (recentScans{{ $organization->id }}.length === 0) {
          recentScansDiv.innerHTML = '<p class="text-muted">No scans yet. Start scanning to see results here.</p>';
          return;
        }
        
        let html = '';
        recentScans{{ $organization->id }}.forEach(function(scan) {
          html += `<div class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong>${scan.student.name}</strong> (ID: ${scan.student.student_id})<br>
                <small class="text-muted">Event: ${scan.event.title}</small><br>
                <small class="text-muted">Scanned: ${scan.timestamp}</small>
              </div>
              <span class="badge bg-success"><i class="bi bi-check-circle"></i> Recorded</span>
            </div>
          </div>`;
        });
        recentScansDiv.innerHTML = html;
      }
    })();
  @endforeach
</script>

<!-- Include jsQR library for QR code scanning -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
@endpush
@endsection
