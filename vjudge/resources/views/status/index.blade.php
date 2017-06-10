@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-hover text-center" id="status-table" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center" width="15%">
                                    <input id="user-selector" class="form-control input-sm full-width" type="text" placeholder="Username">
                                </th>
                                <th class="text-center" width="8%">
                                    <select id="oj-selector" class="form-control input-sm full-width">
                                        <option value="">OJ</option>
                                        @foreach ($OJs as $key => $value)
                                            <option value="{{ $key  }}">{{ $key  }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center" width="10%">
                                    <input id="id-selector" class="form-control input-sm full-width" type="text" placeholder="PID">
                                </th>
                                <th class="text-center" width="18%">
                                    <select id="result-selector" class="form-control input-sm full-width">
                                        <option value="">All Result</option>
                                        <option value="Accepted">Accepted</option>
                                        <option value="Wrong">Wrong Answer</option>
                                        <option value="Presentation">Presentation Error</option>
                                        <option value="Compil">Compile Error</option>
                                        <option value="Runtime">Runtime Error</option>
                                        <option value="Time">Time Limit Exceeded</option>
                                        <option value="Memory">Memory Limit Exceeded</option>
                                        <option value="Output">Output Limit Exceeded</option>
                                        <option value="Judge">Judge Error</option>
                                    </select>
                                </th>
                                <!-- <th class="text-center" width="8%">
                                    <select id="lang-selector" class="form-control input-sm full-width">
                                        <option value="">Lang</option>
                                    </select>
                                </th> -->
                                <th class="text-center" width="8%">Language</th>
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


<script>
$(function() {
    @if (!Auth::guest()) 
    user_id = {{ Auth::user()->id }};
    @else 
    user_id = null;
    @endif
    language_str = '{!! $language !!}';
    language = jQuery.parseJSON(language_str);
    PNotify.prototype.options.styling = "bootstrap3";
    PNotify.prototype.options.styling = "fontawesome";

    var table = $('#status-table').DataTable({
        processing: true,
        stateSave: true,
        serverSide: true,
        pageLength: 50,
        dom: '<"row"<"#toolbar.col-lg-6"><"col-lg-6"p>>rt<"row"<"col-lg-6"l><"col-lg-6"p>>',
        ordering: false,
        ajax: '{!! route('status.list') !!}',
        rowId: 'id',
        columns: [
            { 
                data: 'id',
            },
            { 
                data: 'name',
                render: function ( data, type, row ) {
                    return '<a href="{{url('user')}}/' + row['user_id'] + '">' + data + '</a>';
                }
            },
            { 
                data: 'origin_oj',
                render: function ( data, type, row ) {
                    return data;
                }
            },
            { 
                data: 'origin_id',
                render: function ( data, type, row ) {
                    return '<a href="{{url('problem')}}/' + row['problem_id'] + '">' + data + '</a>';
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
                render: function ( data, type, row ) {
                    return language[row['origin_oj']][data];
                }
            },
            { 
                data: 'time',
                render: function ( data, type, row ) {
                    return data + ' ms';
                }
            },
            { 
                data: 'memory',
                render: function ( data, type, row ) {
                    return data + ' kb';
                }
            },
            { 
                data: 'length',
                render: function ( data, type, row ) {
                    if(row['is_public'] === 1) {
                        return '<a href="{{url('status')}}/' + row['id'] + '">' + data + ' b' + '</a>';
                    }
                    else if(row['user_id'] === user_id){
                        return '<a href="{{url('status')}}/' + row['id'] + '">' + data + ' b' + '</a>';
                    }
                    else {
                        return data + ' b';
                    }
                }
            },
            { 
                data: 'created_at',
                render: function ( data, type, row ) {
                    return data;
                }
            },
        ],
        initComplete: function () {
            var api = this.api();
            $("#user-selector").val(api.column( 1 ).search());
            $("#oj-selector").val(api.column( 2 ).search());
            $("#id-selector").val(api.column( 3 ).search());
            $("#result-selector").val(api.column( 4 ).search());
            $("#lang-selector").val(api.column( 5 ).search());

            $("#user-selector").keydown(function (e) {
                var column = api.column( 1 );
                var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
                if (keyCode == 13) {
                    e.preventDefault();
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    column
                        .search( 'd+' , true, false )
                        .draw();
                }
            });
            $("#oj-selector").on( 'change', function () {
                var column = api.column( 2 );
                var val = $(this).children('option:selected').val();
                column
                    .search( val, true, false )
                    .draw();
            });
            $("#id-selector").keydown(function (e) {
                var column = api.column( 3 );
                var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
                if (keyCode == 13) {
                    e.preventDefault();
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    column
                        .search( val ? '^'+val+'$' : '', true, false )
                        .draw();
                }
            });
            $("#result-selector").on( 'change', function () {
                var column = api.column( 4 );
                var val = $(this).children('option:selected').val();
                column
                    .search( val, true, false )
                    .draw();
            });
        }
    });
    
    $("#toolbar").html('<button id="reset" class="btn btn-default"><i class="fa fa-paper-plane"></i> Reset Filter</button>');
    $("#reset").click(function() {
        table.state.clear();
        window.location.reload();
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
                $('#'+id).children().eq(4).html(brush(data.result));
                $('#'+id).children().eq(6).html(data.time + ' ms');
                $('#'+id).children().eq(7).html(data.memory + ' kb');
                if(checkStatus(data.result)) {
                    clearInterval(int);
                }
            }
        });
    },2000);
}

function rejudge(id) {
    $('#'+id).children().eq(4).html(brush("Rejudging"));
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
