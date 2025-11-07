@extends('layouts.app')

@section('title', 'Events on ' . \Carbon\Carbon::parse($day)->format('M d, Y'))

@section('content')
<div class="container">
    <h2 class="mb-4">Events on {{ \Carbon\Carbon::parse($day)->format('F d, Y') }}</h2>

    @if($events->isEmpty())
        <div class="alert alert-info">No approved events on this day.</div>
    @else
        <div class="list-group mb-4">
            @foreach($events as $ev)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $ev->name }}</h5>
                        <small>
                            @if($ev->start_time && $ev->end_time)
                                {{ $ev->start_time }} - {{ $ev->end_time }}
                            @elseif($ev->start_time)
                                {{ $ev->start_time }}
                            @endif
                        </small>
                    </div>
                    @if($ev->location)
                        <div class="small text-muted mb-1">📍 {{ $ev->location }}</div>
                    @endif
                    @if($ev->description)
                        <p class="mb-1" style="white-space: pre-line;">{{ $ev->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ url('/').'?month='.(\Carbon\Carbon::parse($day)->format('Y-m')) }}#upcoming-events" class="btn btn-outline-primary">Back to Calendar</a>
</div>
@endsection


