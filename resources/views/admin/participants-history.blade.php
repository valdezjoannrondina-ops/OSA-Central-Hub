@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('admin.partials.sidebar')
        <main class="col-md-10 py-4">
        <h1 class="h4 mb-4">Participant History</h1>
        @include('admin.partials.filters._participants_filter', [
          'action' => route('admin.participants.history'),
          'resetRoute' => route('admin.participants.history'),
          'exportRoute' => route('admin.participants.export'),
          'events' => $events ?? [],
          'departments' => $departments ?? [],
          'courses' => $courses ?? [],
          'users' => $users ?? [],
        ])
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Participant</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($participants as $participant)
                <tr>
                    <td>{{ $participant->event->title ?? '-' }}</td>
                    <td>{{ $participant->user->first_name ?? '-' }} {{ $participant->user->last_name ?? '' }}</td>
                    <td>{{ optional($participant->event->event_date)->format('M d, Y') ?? '-' }}</td>
                    <td>{{ ucfirst($participant->status) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No participants found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $participants->links() }}
    </div>
    </main>
  </div>
</div>
@endsection
