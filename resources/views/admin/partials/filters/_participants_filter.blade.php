<form method="GET" action="{{ $action ?? url()->current() }}" class="row g-2 align-items-end mb-3">
  <div class="col-auto">
    <label for="date" class="form-label">Date</label>
    <input type="date" name="date" id="date" value="{{ request('date') }}" class="form-control" />
  </div>
  <div class="col-auto">
    <label for="event_id" class="form-label">Event</label>
    <select name="event_id" id="event_id" class="form-select">
      <option value="">All Events</option>
      @isset($events)
        @foreach ($events as $event)
          <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>{{ $event->title }}</option>
        @endforeach
      @endisset
    </select>
  </div>
  <div class="col-auto">
    <label for="department_id" class="form-label">Department</label>
    <select name="department_id" id="department_id" class="form-select">
      <option value="">All Departments</option>
      @isset($departments)
        @foreach ($departments as $dept)
          <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
        @endforeach
      @endisset
    </select>
  </div>
  <div class="col-auto">
    <label for="course_id" class="form-label">Course</label>
    <select name="course_id" id="course_id" class="form-select">
      <option value="">All Courses</option>
      @isset($courses)
        @foreach ($courses as $course)
          <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->name }}</option>
        @endforeach
      @endisset
    </select>
  </div>
  <div class="col-auto">
    <label for="year_level" class="form-label">Year</label>
    <select name="year_level" id="year_level" class="form-select">
      <option value="">All Years</option>
      @for ($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}" @selected(request('year_level') == $i)>Year {{ $i }}</option>
      @endfor
    </select>
  </div>
  <div class="col-auto">
    <label for="user_id" class="form-label">Participant</label>
    <select name="user_id" id="user_id" class="form-select">
      <option value="">All Participants</option>
      @isset($users)
        @foreach ($users as $user)
          <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->first_name }} {{ $user->last_name }}</option>
        @endforeach
      @endisset
    </select>
  </div>
  <div class="col-auto">
    <label for="status" class="form-label">Status</label>
    <select name="status" id="status" class="form-select">
      <option value="">All Statuses</option>
      <option value="present" @selected(request('status') == 'present')>Present</option>
      <option value="absent" @selected(request('status') == 'absent')>Absent</option>
    </select>
  </div>
  <div class="col-auto d-flex gap-2 align-items-end">
    <button type="submit" class="btn btn-primary">Filter</button>
    @if (!empty($resetRoute ?? null))
      <a href="{{ $resetRoute }}" class="btn btn-outline-secondary">Reset</a>
    @else
      <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
    @endif
    @if (!empty($exportRoute ?? null))
      <a href="{{ $exportRoute }}?{{ http_build_query(request()->all()) }}" class="btn btn-success">Export CSV</a>
    @endif
  </div>
</form>
