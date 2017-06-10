@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9 container">
            <div class="panel panel-default">

                <div class="panel-body">
                    <h2>{{ $problem-> title }}</h2>

                    <!-- <span class="label label-info">{{ $problem-> origin_oj }}-{{ $problem-> origin_id }}</span> -->
                    <span class="label label-primary">{{ $problem-> time }} ms</span>
                    <span class="label label-primary">{{ $problem-> memory }} kb</span>

                    @if ($problem-> father_id != null)
                        <span class="label label-danger">Rewrite</span>
                    @endif

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
                    
                    @if ($problem-> author != "")
                    <h3>Author</h3>
                    {{ $problem-> author }}
                    @endif
                    
                    @if ($problem-> source != "")
                    <h3>Source</h3>
                    {{ $problem-> source }}
                    @endif
                    
                    <br>
                    
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div id="problem-operation">
                @if ($is_ac == true)
                    <a class="btn btn-success" href="{{ route('problem.submit', ['id' => $problem->id]) }}" data-toggle="tooltip" data-placement="top" title="You have solved this problem."><i class="fa fa-check fa-fw" aria-hidden="true"></i> Submit</a>
                @else
                    <a class="btn btn-primary" href="{{ route('problem.submit', ['id' => $problem->id]) }}"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Submit</a>
                @endif

                <a class="btn btn-default disabled"><i class="fa fa-comments fa-fw" aria-hidden="true"></i> Discuss</a>

                @if ($problem-> father_id == null)
                    <a class="btn btn-default" href="{{ route('problem.rewrite', ['id' => $problem->id]) }}"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i> Rewrite</a>

                    <a id="send" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="{{ $problem-> updated_at }}"><i id="spin" class="fa fa-refresh fa-fw"></i> Update</a>
                @else
                    <a class="btn btn-default" href="{{ route('problem.show', ['id' => $problem->father_id]) }}" data-toggle="tooltip" data-placement="top" title="{{ $problem-> origin_oj }}-{{ $problem-> origin_id }}"><i class="fa fa-location-arrow fa-fw" aria-hidden="true"></i> Origin</a>

                    <a class="btn btn-default" href="{{ route('problem.rewrite', ['id' => $problem->id]) }}" data-toggle="tooltip" data-placement="top" title="{{ $problem-> updated_at }}"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i> Edit</a>
                @endif
            </div>

            <br>

            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-laptop"></i>&nbsp; Remote OJ</div>
                <div class="list-group">
                    <a class="list-group-item" style="font-size:12px;" target="_blank" href="{{ route('problem.origin', ['id' => $problem-> id]) }}"><i class="fa fa-location-arrow fa-fw"></i>&nbsp; {{ $problem-> origin_oj }}-{{ $problem-> origin_id }}</a>
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

            @if (count($branchset) != 0)
                <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-folder-open"></i>&nbsp; Branches</div>
                        <div class="list-group">
                            @foreach ($branchset as $key => $value)
                                <a class="list-group-item" href="{{ route('problem.show', ['id' => $value['id']]) }}" data-toggle="tooltip" data-placement="bottom" title="{{ $value['updated_at'] }}"><i class="fa fa-folder fa-fw" aria-hidden="true"></i> {{ $value['title'] }} - {{ $value['name'] }}</a>
                            @endforeach
                        </div>
                </div>
            @endif
        </div>

    </div>
</div>

<script type="text/javascript">
$(function() {
    $('[data-toggle="tooltip"]').tooltip();
    PNotify.prototype.options.styling = "bootstrap3";
    PNotify.prototype.options.styling = "fontawesome";

    function query(str) {
        var int = setInterval(function() {
            $.ajax({
                type: "GET",
                url: "{{ url('problem/query') }}/"+str,
                dataType: "json",
                success: function(result) {
                    console.log(result);
                    if(result.time != "{{ $problem-> updated_at }}") {
                        new PNotify({
                            type: 'success',
                            icon: false,
                            text: '<i class="fa fa-check"></i> Update successfully. Refresh in 3s.',
                            delay: 3000
                        });
                        setTimeout('window.location.reload();',3000);
                        clearInterval(int);
                    }
                }
            });
        },3000);
    }

    $("#send").click(function() {
        var txt = "{{ $problem-> origin_oj }}{{ $problem-> origin_id }}";
        $("#send").addClass("disabled");
        $("#spin").addClass("fa-spin");
        $.get("{{ url('problem/recrawl') }}/"+txt, function(result) {
            new PNotify({
                type: 'info',
                icon: false,
                text: '<i class="fa fa-info-circle"></i> ' + result.msg,
                delay: 2000
            });
            query(txt);
        });
    });
});
</script>
@endsection
