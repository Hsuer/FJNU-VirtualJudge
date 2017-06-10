@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            @include('contests.navbar')
            <div class="panel panel-default">
                <div class="panel-body">
                    <ul class="nav nav-pills">
                        @for ($i = 0; $i < $total_problems; $i++)
                            @if ($pid == $i)
                                <li role="presentation" class="active"><a href="{{ route('contest.problem', ['id' => $contest->id, 'pid' => chr(65 + $i)]) }}">{{ chr(65 + $i) }}</a></li>
                            @else
                                <li role="presentation"><a href="{{ route('contest.problem', ['id' => $contest->id, 'pid' => chr(65 + $i)]) }}">{{ chr(65 + $i) }}</a></li>
                            @endif
                        @endfor
                    </ul>

                    <h2>{{ $problem-> title }}</h2>

                    <span class="label label-info">{{ $problem-> time }} ms</span>
                    <span class="label label-info">{{ $problem-> memory }} kb</span>

                    @if ($problem->special_judge == 1)
                        <span class="label label-danger">Special Judge</span>
                    @endif
                    
                    <h3>Description</h3>
                    <div id="problem-block">
                        {!! $problem-> description !!}
                    </div>

                    @if (strip_tags($problem-> input) != "")
                    <h3>Input</h3>
                    <div id="problem-block">
                        {!! $problem-> input !!}
                    </div>
                    @endif

                    @if ($problem-> output != "")
                    <h3>Output</h3>
                    <div id="problem-block">
                        {!! $problem-> output !!}
                    </div>
                    @endif
                    
                    @if ($problem-> sample_input != "")
                    <h3>Sample Input</h3>
                    <pre>{{ $problem-> sample_input }}</pre>
                    @endif
                    
                    @if ($problem-> sample_output != "")
                    <h3>Sample Output</h3>
                    <pre>{{ $problem-> sample_output }}</pre>
                    @endif

                    @if ($problem-> hint != "")
                    <h3>Hint</h3>
                    <div id="problem-block">
                        {!! $problem-> hint !!}
                    </div>
                    @endif
                    
                    <br>
                    
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    @if ($is_ac == true)
                    <a class="btn btn-success btn-block" href="{{ route('contest.submit', ['id' => $contest->id, 'pid' => chr(65 + $pid)]) }}" data-toggle="tooltip" data-placement="top" title="You have solved this problem."><i class="fa fa-check fa-fw" aria-hidden="true"></i> Submit</a>
                    @else
                    <a class="btn btn-primary btn-block" href="{{ route('contest.submit', ['id' => $contest->id, 'pid' => chr(65 + $pid)]) }}"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Submit</a>
                    @endif
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-laptop"></i>&nbsp; Remote OJ</div>
                <div class="list-group">
                    @if ($oj_info['system'] === 'Windows')
                        <a class="list-group-item" style="font-size:12px;"><i class="fa fa-windows fa-fw"></i>&nbsp; {{ $oj_info['system'] }}</a>
                        <a class="list-group-item" style="font-size: 12px;"><i class="fa fa-percent fa-fw"></i>&nbsp; I64d</a>
                    @else
                        <a class="list-group-item" style="font-size:12px;"><i class="fa fa-linux fa-fw"></i>&nbsp; {{ $oj_info['system'] }}</a>
                        <a class="list-group-item" style="font-size: 12px;"><i class="fa fa-percent fa-fw"></i>&nbsp; lld</a>
                    @endif
                    
                </div>
            </div>

            @if (count($status) != 0)
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-heartbeat"></i>&nbsp; Recent</div>
                    <div class="list-group">
                    @foreach ($status as $key => $value)
                        @if ($key < 4)
                            @if ($value['result'] === 'Accepted')
                                <a class="list-group-item list-group-item-success" href="{{ route('status.show', ['id' => $value['id']]) }}" data-toggle="tooltip" data-placement="top" title="{{ $value['created_at'] }}" style="font-size:12px;"><i class="fa fa-check fa-fw" aria-hidden="true"></i> {{ $value['result'] }} - {{ substr($value['created_at'], 0, -9) }}</a>
                            @else
                                <a class="list-group-item list-group-item-danger" href="{{ route('status.show', ['id' => $value['id']]) }}" data-toggle="tooltip" data-placement="top" title="{{ $value['created_at'] }}" style="font-size:12px;"><i class="fa fa-warning fa-fw" aria-hidden="true"></i> {{ $value['result'] }} - {{ substr($value['created_at'], 0, -9) }}</a>
                            @endif
                        @else
                            <a class="list-group-item text-center" href="{{ url('status') }}"style="font-size:12px;"><i class="fa fa-ellipsis-h fa-fw" aria-hidden="true"></i> More</a>
                        @endif
                    @endforeach
                    </div>
                </div>
            @endif

            @include('contests.sidebar')
        </div>

    </div>
</div>

<script type="text/javascript">
$(function() {
    $("#problem").addClass("active");
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

@endsection
