@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @include('contests.navbar')
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-hover text-center" id="contest-status-table" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Username</th>
                                <th class="text-center">ID</th>
                                <th class="text-center">Result</th>
                                <th class="text-center">Language</th>
                                <th class="text-center">Time</th>
                                <th class="text-center">Memory</th>
                                <th class="text-center">Length</th>
                                <th class="text-center">Submit Time</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $("#status").addClass('active');
    @if (!Auth::guest()) 
    user_id = {{ Auth::user()->id }};
    @else 
    user_id = null;
    @endif
    language_str = '{!! $language !!}';
    language = jQuery.parseJSON(language_str);
    contest_problem_id_str = '{!! $contest_problem_id !!}';
    contest_problem_id = jQuery.parseJSON(contest_problem_id_str);
    console.log(contest_problem_id);
    PNotify.prototype.options.styling = "bootstrap3";
    PNotify.prototype.options.styling = "fontawesome";

    var table = $('#contest-status-table').DataTable({
        stateSave: true,
        serverSide: true,
        pageLength: 50,
        ordering: false,
        ajax: '{!! route('contest.status.list', ['id' => $contest->id]) !!}',
        rowId: 'id',
        columns: [
            { 
                data: 'id'
            },
            { 
                data: 'name',
                render: function ( data, type, row ) {
                    return '<a href="{{url('user')}}/' + row['user_id'] + '">' + data + '</a>';
                }
            },
            {
                data: 'problem_id',
                render: function( data, type, row) {
                    if(!contest_problem_id.hasOwnProperty(data)) {
                        return '<div class="danger" data-toggle="tooltip" data-placement="top" title="Problem is removed from this contest"><i class="fa fa-warning"></i></div>';
                    }
                    return '<a href="{{url('contest')}}/'+row['contest_id']+'/problem/'+contest_problem_id[data]+'">' + contest_problem_id[data] + '</a>';
                }
            },
            { 
                data: 'result',
                render: function ( data, type, row ) {
                    if(checkStatus(data) == 0) {
                        var unix = moment().unix();
                        var updated_unix = moment(row['updated_at']).unix();
                        if(unix - updated_unix > 1800) {
                            return '<div style="color: grey;">Judge Error&nbsp; <button onclick="rejudge('+row['id']+')" class="btn btn-default btn-xs"><i class="fa fa-refresh"></i> Rejudge</button></div>';
                        }
                        getStatus(row['id']);
                    }
                    return brush(data);
                }
            },
            { 
                data: 'language',
                class: 'text-center',
                render: function ( data, type, row ) {
                    return language[row['origin_oj']][data];
                }
            },
            { 
                data: 'time',
                class: 'text-center',
                render: function ( data, type, row ) {
                    return data + ' ms';
                }
            },
            { 
                data: 'memory',
                class: 'text-center',
                render: function ( data, type, row ) {
                    return data + ' kb';
                }
            },
            { 
                data: 'length',
                class: 'text-center',
                render: function ( data, type, row ) {
                    if(row['user_id'] === user_id){
                        return '<a href="{{url('status')}}/' + row['id'] + '">' + data + ' b' + '</a>';
                    }
                    else {
                        return data + ' b';
                    }
                }
            },
            { 
                data: 'created_at',
                class: 'text-center'
            },
        ],
        initComplete: function () {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
});

function checkStatus(str) {
    if(str.indexOf('Accepted')>=0) return 1;
    if(str.indexOf('Wrong Answer')>=0)  return 2;
    if(str.indexOf('Error')>=0) return 2;
    if(str.indexOf('Exceed')>=0) return 2;
    return 0; 
}

function brush(str) {
    var status = checkStatus(str);
    if(status == 0) {
        return '<div style="color: grey;"><i class="fa fa-refresh fa-spin fa-fw"></i> <span class="hidden-xs">' + str + '</span></div>';
    }
    else if(status == 1) {
        return '<div class="success"><i class="fa fa-check fa-fw visible-xs"></i><span class="hidden-xs">' + str + '</span></div>';
    }
    else if(status == 2) {
        return '<div class="danger"><i class="fa fa-exclamation-triangle fa-fw visible-xs"></i><span class="hidden-xs">' + str + '</span></div>';
    }
}

function getStatus(id) {
    var int = setInterval(function() {
        $.ajax({
            type: "GET",
            url: "{{ url('status/get') }}/"+id,
            dataType: "json",
            success: function(data) {
                $('#'+id).children().eq(3).html(brush(data.result));
                $('#'+id).children().eq(5).html(data.time + ' ms');
                $('#'+id).children().eq(6).html(data.memory + ' kb');
                if(checkStatus(data.result)) {
                    clearInterval(int);
                }
            }
        });
    },2000);
}

function rejudge(id) {
    $('#'+id).children().eq(3).html(brush("Rejudging"));
    $.get('{{ url("/status/rejudge") }}/' + id, function(data){
        getStatus(id);
        new PNotify({
            type: 'success',
            icon: false,
            text: '<i class="fa fa-check"></i> Send to rejudge successfully.',
            delay: 3000
        });
    });
}
</script>

@endsection
