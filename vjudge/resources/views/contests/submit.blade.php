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
                                <li role="presentation" class="active"><a href="{{ route('contest.submit', ['id' => $contest->id, 'pid' => chr(65 + $i)]) }}">{{ chr(65 + $i) }}</a></li>
                            @else
                                <li role="presentation"><a href="{{ route('contest.submit', ['id' => $contest->id, 'pid' => chr(65 + $i)]) }}">{{ chr(65 + $i) }}</a></li>
                            @endif
                        @endfor
                    </ul>

                    <h2>{{ $problem-> title }}</h2>

                    <span class="label label-info">{{ $problem-> time }} ms</span>
                    <span class="label label-info">{{ $problem-> memory }} kb</span>

                    @if ($problem->special_judge == 1)
                        <span class="label label-danger">Special Judge</span>
                    @endif
                    
                    <br><br>
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('status/store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('language') ? ' has-error' : '' }}">
                            <label for="language" class="col-md-12">Language</label>

                            <div class="col-md-12">
                                <select name="language" class="form-control">
                                    @foreach ($language as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('language'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('language') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                            <label for="code" class="col-md-12">Code</label>

                            <div class="col-md-12">
                                <textarea id="code" type="text" class="form-control" name="code" rows="10" placeholder="Input solution code here~" required autofocus></textarea>

                                @if ($errors->has('code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <input class="hidden" name="contest_id" value="{{ $contest->id }}"></input>
                        <input class="hidden" name="problem_id" value="{{ $problem->id }}"></input>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Submit</button>
                    </form>
                
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <a class="btn btn-danger btn-block" href="{{ route('contest.problem', ['id' => $contest->id, 'pid' => chr(65 + $pid)]) }}"><i class="fa fa-arrow-circle-left fa-fw" aria-hidden="true"></i> Go back to Problem</a>
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
    $("#submit").addClass('active');
</script>

@endsection
