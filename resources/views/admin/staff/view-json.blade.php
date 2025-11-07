@extends('layouts.app')

@section('title', 'View File: ' . $filename)

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
      <a href="{{ url()->previous() }}" class="btn btn-secondary">&larr; Back</a>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <h2>Viewing: {{ $filename }}</h2>
      @if(is_array($data))
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                @foreach(array_keys($data[0] ?? []) as $col)
                  <th>{{ $col }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($data as $row)
                <tr>
                  @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <pre>{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
      @endif
    </div>
  </div>
</div>
@endsection
