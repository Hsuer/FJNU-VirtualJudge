@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <ul class="list-group">
                @if ($status-> result == "Accepted")
                    <li class="list-group-item list-group-item-success"><i class="fa fa-check fa-fw"></i> {{ $status-> result }}</li>
                @else
                    <li class="list-group-item list-group-item-danger"><i class="fa fa-warning fa-fw"></i> {{ $status-> result }}</li>
                @endif
                @if ($status-> contest_id == null)
                    <a class="list-group-item" href="{{ route('problem.show', ['id' => $status->problem_id]) }}">
                        <i class="fa fa-location-arrow fa-fw"></i> 
                        {{ $status-> origin_oj }}-{{ $status-> origin_id }}
                    </a>
                @endif
                <li class="list-group-item"><i class="fa fa-clock-o fa-fw"></i> {{ $status-> time }} ms</li>
                <li class="list-group-item"><i class="fa fa-microchip fa-fw"></i> {{ $status-> memory }} kb</li>
                <li class="list-group-item"><i class="fa fa-code fa-fw"></i> {{ $status-> length }} b</li>
            </ul>
        </div>

        <div class="col-md-9">
            @if ($status-> ceinfo != null)
                <pre class="bg-danger">{{ $status-> ceinfo }}</pre>
            @endif
            <pre>{{ $status-> code }}</pre>
        </div>
    </div>
</div>

@endsection
