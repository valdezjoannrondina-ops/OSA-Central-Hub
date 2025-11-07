@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('admin.partials.sidebar')
        <main class="col-md-10 py-4">
        <h1 class="h4 mb-4">Event History</h1>
        @include('admin.partials.filters._events_filter', [
          'action' => route('admin.events.history'),
          'resetRoute' => route('admin.events.history'),
          'departments' => $departments ?? [],
          'courses' => $courses ?? [],
        ])
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->event_date->format('M d, Y') }}</td>
                    <td>{{ $event->location }}</td>
                    <td>{{ ucfirst($event->status) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $events->links() }}
    </div>
    </main>
  </div>
</div>
@endsection
