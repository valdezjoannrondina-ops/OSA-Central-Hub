<aside class="col-md-3 col-lg-2 mb-4">
    @php
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user->role ?? null;
        $dashboardLabel = 'Dashboard';
        switch ($role) {
            case 4:
                $dashboardUrl = route('admin.dashboard');
                $dashboardActive = request()->routeIs('admin.dashboard');
                $dashboardLabel = 'Admin Dashboard';
                break;
            case 2:
                $dashboardUrl = route('staff.dashboard');
                $dashboardActive = request()->routeIs('staff.dashboard');
                $designation = $user->designation ?? optional($user->staffProfile)->designation;
                $dashboardLabel = $designation ? ($designation.' Dashboard') : 'Staff Dashboard';
                break;
            case 3:
                $dashboardUrl = route('assistant.dashboard');
                $dashboardActive = request()->routeIs('assistant.dashboard');
                $dashboardLabel = 'Assistant Dashboard';
                break;
            default:
                $dashboardUrl = route('student.dashboard');
                $dashboardActive = request()->routeIs('student.dashboard');
                $dashboardLabel = 'Student Dashboard';
        }

        // Role-aware URLs for Appointments and Events
        if ($role === 4) {
            $appointmentsUrl = route('admin.appointments.index');
            $appointmentsActive = request()->routeIs('admin.appointments.*');
            $eventsUrl = route('admin.events.index');
            $eventsActive = request()->routeIs('admin.events.*');
        } else {
            // Default to staff routes
            $appointmentsUrl = route('staff.appointments.index');
            $appointmentsActive = request()->routeIs('staff.appointments.*');
            $eventsUrl = route('staff.events.index');
            $eventsActive = request()->routeIs('staff.events.*');
        }
    @endphp
    <div class="list-group">
        <a href="{{ $dashboardUrl }}" class="list-group-item list-group-item-action {{ $dashboardActive ? 'active' : '' }}">
            {{ $dashboardLabel }}
        </a>
        <a href="{{ $appointmentsUrl }}" class="list-group-item list-group-item-action {{ $appointmentsActive ? 'active' : '' }}">
            My Appointments
        </a>
        <a href="{{ $eventsUrl }}" class="list-group-item list-group-item-action {{ $eventsActive ? 'active' : '' }}">
            My Events
        </a>
        @if($role === 2)
            <a href="{{ route('staff.files.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('staff.files.*') ? 'active' : '' }}">
                My Files
            </a>
            <a href="{{ route('staff.assistants.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('staff.assistants.*') ? 'active' : '' }}">
                My Assistant Staff
            </a>
            <a href="{{ route('staff.assistants.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('staff.assistants.create') ? 'active' : '' }}">
                Add Assistant
            </a>
        @endif
        @if($role === 4)
            <a href="{{ route('admin.participants.export') }}" class="list-group-item list-group-item-action">
                Export Participants
            </a>
        @endif
    </div>
</aside>
