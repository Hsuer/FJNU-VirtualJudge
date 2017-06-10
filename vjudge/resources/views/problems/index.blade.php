@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-hover" id="problems-table" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th width="10%">
                                    <select id="oj-selector" class="form-control input-sm full-width">
                                        <option value="">All OJ</option>
                                        @foreach ($OJs as $key => $value)
                                            <option value="{{ $key }}">{{ $key }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th width="10%">
                                    <input id="id-selector" class="form-control input-sm full-width" type="text" placeholder="PID" onClick="event.cancelBubble = true">
                                </th>
                                <th width="30%"> 
                                    <input id="title-selector" class="form-control input-sm full-width" type="text" placeholder="Title">
                                </th>
                                <th width="50%">
                                <input id="source-selector" class="form-control input-sm full-width" type="text" placeholder="Source">
                                </th>
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
    PNotify.prototype.options.styling = "bootstrap3";
    PNotify.prototype.options.styling = "fontawesome";
    var flag = 0;
    table = $('#problems-table').DataTable({
        // processing: true,
        stateSave: true,
        serverSide: true,
        pageLength: 50,
        dom: '<"row"<"#toolbar.col-lg-6"><"col-lg-6"p>>rt<"row"<"col-lg-6"l><"col-lg-6"p>>',
        order: [[ 0, 'desc' ]],
        ajax: '{!! route('problem.list') !!}',
        columns: [
            { 
                data: 'id',
                visible: false,
                searchable: false,
            },
            { 
                data: 'origin_oj',
                orderable: false,
            },
            { 
                data: 'origin_id',
            },
            { 
                data: 'title',
                orderable: false,
                render: function ( data, type, row ) {
                    return '<a href="{{url('problem')}}/'+row['id']+'">'+data+'</a> <a href="{{ url('problem') }}/'+row['id']+'/submit" style="color: gray;"><i class="fa fa-upload pull-right"></i></a>';
                }
            },
            { 
                data: 'source',
                orderable: false,
                render: function ( data, type, row ) {
                    return '<a onClick="setSource(\''+data+'\')" style="cursor: pointer;">'+data+'</a>';
                }
            },
        ],
        initComplete: function () {
            var api = this.api();
            $("#oj-selector").val(api.column( 1 ).search());
            $("#id-selector").val(api.column( 2 ).search());
            $("#title-selector").val(api.column( 3 ).search());
            $("#source-selector").val(api.column( 4 ).search());

            $("#oj-selector").on( 'change', function () {
                var column = api.column( 1 );
                var val = $(this).children('option:selected').val();
                column
                    .search( val, true, false )
                    .draw();
            });
            $("#id-selector").keydown(function (e) {
                e.stopPropagation();
                e.cancelBubble=true; 
                var column = api.column( 2 );
                var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode;
                if (keyCode == 13) {
                    e.preventDefault();
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    column
                        .search( val, true, false )
                        .draw();
                }
            });
            $("#title-selector").keydown(function (e) {
                e.stopPropagation();
                e.cancelBubble=true; 
                var column = api.column( 3 );
                var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode; 
                if (keyCode == 13) {
                    e.preventDefault();
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    column
                        .search( val, true, false )
                        .draw();
                }
            });
            $("#source-selector").keydown(function (e) {
                e.stopPropagation();
                e.cancelBubble=true; 
                var column = api.column( 4 );
                var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode; 
                if (keyCode == 13) {
                    e.preventDefault();
                    var val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    column
                        .search( val, true, false )
                        .draw();
                }
            });
        }
    });

    $("#toolbar").html('<div class="input-group input-group"><span class="input-group-addon" id="sizing-addon3">Add Problem</span><input id="pid_input" class="form-control" placeholder="hdu1000"  aria-describedby="sizing-addon3"></div>&nbsp;&nbsp;&nbsp; <button id="reset" class="btn btn-default"><i class="fa fa-paper-plane"></i> Reset Filter</button>');

    $("#reset").click(function() {
        table.state.clear();
        window.location.reload();
    });

    $("#pid_input").keydown(function (e) {
        var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : e.charCode; 
        if (keyCode == 13 && !flag){ 
            flag = 1;
            txt = $("#pid_input").val();
            $.get("{{ url('problem/crawl') }}/"+txt, function(result) {
                if(result.status == 1) {
                    new PNotify({
                        type: 'info',
                        icon: false,
                        text: '<i class="fa fa-info-circle"></i> ' + result.msg,
                        delay: 1000
                    });
                    query(txt);
                }
                else {
                    new PNotify({
                        type: 'warning',
                        icon: false,
                        text: '<i class="fa fa-warning"></i> ' + result.msg,
                        delay: 2000
                    });
                }
            });
        }
        if(keyCode != 13) {
            flag = 0;
        }
    });

    function query(str) {
        var int = setInterval(function() {
            $.ajax({
                type: "GET",
                url: "{{ url('problem/query') }}/"+str,
                dataType: "json",
                success: function(result) {
                    if(result.status == 1) {
                        new PNotify({
                            type: 'success',
                            icon: false,
                            text: '<i class="fa fa-check"></i> '+result.oj +'-'+ result.id + ' is added to list.',
                            delay: 3000
                        });
                        table.ajax.reload( null, false );
                        clearInterval(int);
                    }
                }
            });
        },1000);
    }
});

function setSource(source) {
    var column = table.column( 4 );
    var val = $.fn.dataTable.util.escapeRegex(
        source
    );
    $("#source-selector").val(val);
    column
        .search( val, true, false )
        .draw();
}
</script>

@endsection