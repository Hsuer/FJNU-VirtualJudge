@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            @include('contests.navbar')
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>{{ $contest->title }}</h3>
                    <span><i class="fa fa-clock-o fa-fw"></i> {{ $contest->begin_time }}</span>
                    <span class="pull-right"><i class="fa fa-clock-o fa-fw"></i> {{ $contest->end_time }}</span>
                    <br>
                    <div class="progress">
                        <div id="progressbar" class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="min-width: 4em; width: 2%;">
                            <span></span>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover" id="contest-problems-table" width="100%">
                        <thead>
                            <th>ID</th>
                            <th>Title</th>
                            <th>AC</th>
                            <th>Submit</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            @if (!Auth::guest())
                @if ($contest-> user_id === Auth::user()->id)
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <a class="btn btn-primary btn-block" href="{{ $contest->id }}/edit"><i class="fa fa-edit fa-fw" aria-hidden="true"></i> Edit Contest</a>
                        </div>
                    </div>
                @endif
            @endif
            
            @include('contests.sidebar')
        </div>
    </div>
</div>

<script>
$(function() {
    $("#home").addClass("active");
    var begin_timestamp = moment("{{ $contest-> begin_time }}").unix();
    var end_timestamp = moment("{{ $contest-> end_time }}").unix();
    var timestamp = moment().unix();
    
    setProgreeBar(timestamp, begin_timestamp, end_timestamp);

    setInterval( function () {
        var timestamp = moment().unix();
        setProgreeBar(timestamp, begin_timestamp, end_timestamp);
    }, 5000 );

    function setProgreeBar(now, begin, end) {
        if(now < begin) {
            $("#progressbar").css("width", "0%");
            $("#progressbar span").html("0%");
        }
        else if(now >= begin && now < end) {
            $("#progressbar").addClass("progress-bar-striped active");
            var width = (now - begin) / (end - begin) * 100;
            width = width.toFixed(2);
            if (width >= 90) {
                $("#progressbar").addClass("progress-bar-danger");
            }
            $("#progressbar").css("width", width + "%");
            $("#progressbar span").html(width + "%");
        }
        else {
            $("#progressbar").css("width", "100%");
            $("#progressbar span").html("100%");
        }
    }

    table = $('#contest-problems-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        dom: '',
        order: [[ 0, 'asc' ]],
        ajax: '{!! route("contest.problem.list", ['id' => $contest->id]) !!}',
        fnDrawCallback: function(){
            this.api().column(0).nodes().each(function(cell, i) {
                cell.innerHTML =  String.fromCharCode(65 + i);
            });
            this.api().column(1).nodes().each(function(cell, i) {
                $(cell).children().attr("href", 
                    "{{ route('contest.problem', ['id' => $contest->id]) }}/"+String.fromCharCode(65 + i)
                );
            });
        },
        columns: [
            { 
                data: 'problem_id',
                orderable: false,
                render: function ( data, type, row ) {
                    return data;
                }
            },
            { 
                data: 'title',
                orderable: false,
                render: function ( data, type, row ) {
                    return '<a>'+data+'</a>';
                }
            },
            { 
                data: 'ac_num',
                orderable: false,
            },
            { 
                data: 'submit_num',
                orderable: false,
            },
        ],
    });
});
</script>
@endsection
