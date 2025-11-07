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
      <div class="table-responsive">
        <table class="table table-bordered table-sm">
          <tbody>
            @foreach($rows as $i => $row)
              <tr>
                @foreach($row as $cell)
                  <td>{{ $cell }}</td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
