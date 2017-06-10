@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-hover" id="contests-table" width="100%">
                        <thead>
                            <th width="5%">#</th>
                            <th width="30%">Title</th>
                            <th width="15%">Start Time</th>
                            <th width="10%">Duration</th>
                            <th width="8%">Status</th>
                            <th width="8%">Access</th>
                            <th width="10%">Author</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    var table = $('#contests-table').DataTable({
        serverSide: true,
        pageLength: 50,
        ordering: false,
        ajax: '{!! route('contest.list') !!}',
        columns: [
            { 
                data: 'id',
                class: 'text-center'
            },
            { 
                data: 'title',
                render: function ( data, type, row ) {
                    return '<a href="{{url('contest')}}/' + row['id'] + '">' + data + '</a>';
                }
            },
            { 
                data: 'begin_time',
                class: 'text-center',
                render: function ( data, type, row ) {
                    var begin_unix = moment(row['begin_time']).unix();
                    var end_unix = moment(row['end_time']).unix();
                    var now_unix = moment().unix();
                    if(now_unix < begin_unix) {
                        width = 0;
                    }
                    else if(now_unix >= end_unix) {
                        width = 100;
                    }
                    else {
                        var width = (now_unix - begin_unix) / (end_unix - begin_unix) * 100;
                        width = width.toFixed(2);
                    }
                    return data + '<br><div class="progress progress-xs"><div class="progress-bar progress-bar-striped active" role="progressbar" style="width: '+ width +'%;"></div></div>';
                }
            },
            { 
                data: 'end_time',
                class: 'text-center',
                render: function ( data, type, row ) {
                    var begin_unix = moment(row['begin_time']).unix();
                    var end_unix = moment(row['end_time']).unix();
                    var duration_unix = end_unix - begin_unix;
                    var hour = parseInt(duration_unix / 3600);
                    var minute = parseInt((duration_unix % 3600) / 60);
                    var second = parseInt((duration_unix % 3600) % 60);
                    return hour + ":" + (Array(2).join(0) + minute).slice(-2) + ":" + (Array(2).join(0) + second).slice(-2);
                    return data;
                }
            },
            { 
                data: 'id',
                class: 'text-center',
                render: function ( data, type, row ) {
                    var begin_timestamp = moment(row['begin_time']).unix();
                    var end_timestamp = moment(row['end_time']).unix();
                    var timestamp = moment().unix();
                    if(timestamp < begin_timestamp) {
                        return '<span class="success">Pending</span>';
                    }
                    else if(timestamp >= end_timestamp) {
                        return '<span color="gray">Ended</span>';
                    }
                    else {
                        return '<span class="danger">Running</span>';
                    }
                }
            },
            { 
                data: 'is_public',
                class: 'text-center',
                render: function ( data, type, row ) {
                    if(data === 1) {
                        return '<span color="gray"><i class="fa fa-group fa-fw"></i> Public</span>'
                    }
                    else {
                        return '<span color="gray"><i class="fa fa-lock fa-fw"></i> Private</span>'
                    }
                }
            },
            { 
                data: 'name',
                class: 'text-center',
                render: function ( data, type, row ) {
                    return '<a href="{{url('user')}}/' + row['user_id'] + '">' + data + '</a>';
                }
            },
        ],
    });
})
</script>

@endsection
