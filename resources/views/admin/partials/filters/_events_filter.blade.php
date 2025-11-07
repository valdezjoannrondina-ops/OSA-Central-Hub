<form method="GET" action="{{ $action ?? url()->current() }}" class="row g-2 align-items-end mb-3">
  <div class="col-auto">
    <label for="title" class="form-label">Event Title</label>
    <input type="text" name="title" id="title" value="{{ request('title') }}" class="form-control" placeholder="Title">
  </div>
  <div class="col-auto">
    <label for="date" class="form-label">Date</label>
    <input type="date" name="date" id="date" value="{{ request('date') }}" class="form-control">
  </div>
  <div class="col-auto">
    <label for="department_id" class="form-label">Department</label>
    <select name="department_id" id="department_id" class="form-select">
      <option value="">All</option>
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
      <option value="">All</option>
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
      <option value="">All</option>
      @for ($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}" @selected(request('year_level') == $i)>Year {{ $i }}</option>
      @endfor
    </select>
  </div>
  <div class="col-auto">
    <label for="status" class="form-label">Status</label>
    <select name="status" id="status" class="form-select">
      <option value="">All</option>
      <option value="pending" @selected(request('status') == 'pending')>Pending</option>
      <option value="approved" @selected(request('status') == 'approved')>Approved</option>
      <option value="declined" @selected(request('status') == 'declined')>Declined</option>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary">Filter</button>
    @if (!empty($resetRoute ?? null))
      <a href="{{ $resetRoute }}" class="btn btn-outline-secondary">Reset</a>
    @else
      <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
    @endif
  </div>
</form>
