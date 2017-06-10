@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <form id="form" class="form-horizontal" role="form">
                        {{ csrf_field() }}
                    
                        <div class="row">
                            <div class="col-lg-6">

                                <div id="errorMsg_left"></div>

                                <div class="input-group">
                                    <span class="input-group-addon">Contest Title</span>
                                    <input id="title" type="text" class="form-control" name="title" placeholder="Title" autofocus>
                                </div>              

                                <br>

                                <div class="input-group date form_datetime_begin">
                                    <span class="input-group-addon">Begin Time</span>
                                    <input id="begin_time" size="16" type="text" name="begin_time" class="form-control" value="{{ date('Y-m-d', time()) }} 09:00:00" autofocus>
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                </div>
                                <span class="help-block-xs">At least after 3 minutes.</span>
                                
                                <br>

                                <div class="input-group">
                                  <span class="input-group-addon">Duration</span>
                                  <input id="duration" size="16" type="text" name="duration" class="form-control" value="5:00:00" autofocus>
                                  <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="fa fa-chevron-down"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a id="duration-day">1 day</a></li>
                                            <li><a id="duration-3days">3 days</a></li>
                                            <li><a id="duration-week">1 week</a></li>
                                            <li><a id="duration-mouth">1 mouth</a></li>
                                        </ul>
                                    </div><!-- /btn-group -->
                                </div>
                                <span class="help-block-xs">Duration should be between 30 minutes and 30 days</span>
                                
                                <br>

                                <div class="input-group date form_datetime_end">
                                    <span class="input-group-addon">End Time</span>
                                    <input  id="end_time" size="16" type="text" name="end_time" class="form-control" value="{{ date('Y-m-d', time()) }} 14:00:00" autofocus> 
                                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                </div>
                                <span class="help-block-xs">Must be later than start time</span>

                                <br>

                                <div class="input-group">
                                    <span class="input-group-addon">Password</span>
                                    <input type="text" class="form-control" name="password" placeholder="Leave it blank if not needed">
                                </div>

                                <br>
                                    
                                <textarea class="form-control" rows="5" name="description" placeholder="put contest description here~"></textarea>

                                <br>

                                <button id="submit" class="btn btn-primary" type="button" data-loading-text="Loading..."><i class="fa fa-upload"></i> Submit</button>&nbsp;
                                <button class="btn btn-danger btn-submit-bottom" type="button" onclick="document.forms[0].reset();location.reload();"><i class="fa fa-trash"></i> Reset</button>
                            </div>
                            
                            <div class="col-lg-6">
                                <div id="errorMsg_right"></div>
                                <table id="addTable" class="table table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:30px"><a id="addBtn" style="cursor:pointer"><span class="fa fa-plus" aria-hidden="true"></span></a></th>
                                            <th style="width:100px">OJ</th>
                                            <th style="width:70px">PID</th>
                                            <th style="width:150px">Title</th>
                                            <th style="text-align:left">Origin Title</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr class="tr_problem">
                                            <td style="width:30px">
                                                <a class="deleteRow" style="cursor:pointer"><span class="fa fa-minus" aria-hidden="true"></span></a>
                                            </td>
                                            <td>
                                                <select name="OJs" class="form-control input-sm">
                                                    @foreach ($OJs as $key => $value)
                                                        <option value="{{ $key }}">{{ $key }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" class="vids" name="vids[]" value="">
                                            </td>
                                            <td><input type="text" name="probNums" value="" style="width:70px" class="form-control input-sm"></td>
                                            <td><input class="protitles form-control input-sm" name="protitles[]"></td>
                                            <td style="text-align:left"><span class="label label-info">Waiting input</span></td>
                                        </tr>

                                        <tr id="addRow" class="tr_problem" style="display:none"></tr>
                                    </tbody>
                                </table>
                            </div>  <!-- end of col-lg-6 -->
                        </div>  <!-- end of row -->
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('#submit').click(function() {
        var $btn = $(this).button('loading');
        var dup = 0, err = 0, $trs = $("#addTable tr.tr_problem:visible");
        for (i = 0; i < $trs.length; i=i+1){
            for (j = 0; j < i; j=j+1){
                if ($("[name=OJs]", $trs.eq(i)).val() == $("[name=OJs]", $trs.eq(j)).val() && $("[name=probNums]", $trs.eq(i)).val() == $("[name=probNums]", $trs.eq(j)).val()){
                    dup = 1;
                    break;
                }
            }
            tmp = $trs.eq(i).children().eq(-1).html().charAt(1);
            if (tmp != 'a' && tmp != 'A'){
                err = 1;
                break;
            }
        }
        if (dup == 1){
            $("#errorMsg_right").addClass('alert alert-warning');
            $("#errorMsg_right").html('<i class="fa fa-warning"></i>&nbsp; Duplcate problems are not allowed!');
            $btn.button('reset');
            return;
        }
        if (err == 1){
            $("#errorMsg_right").addClass('alert alert-warning');
            $("#errorMsg_right").html('<i class="fa fa-warning"></i>&nbsp; There are invalid problems!');
            $btn.button('reset');
            return;
        }
        // $("tr:not(:visible)").remove();
        $("#errorMsg_right").addClass('hidden');
        $.ajax({
            type: 'post',
            dataType: 'JSON',
            url: '{{ route('contest.store') }}',
            data: $("form").serialize(),
            success: function(data){
                $btn.button('reset');
                self.location="{{ url('contest') }}"+'/'+data['id'];
            },
            error: function(data) {
                $btn.button('reset');
                for(var key in data.responseJSON) {
                    $("#errorMsg_left").addClass('alert alert-warning');
                    $("#errorMsg_left").html('<i class="fa fa-warning"></i>&nbsp; ' + data.responseJSON[key][0]);
                }
            }
        });
    });

    $('#duration').change(function() {
        var val = $(this).val();
        var pattern = /^(\d+)\:([0-5]\d)\:([0-5]\d)$/;
        if(val == "" || !pattern.test(val)) {
            $(this).val("05:00:00");
        }
        var ret = val.match(pattern);
        if(ret == null) {
            ret = new Array(0, 5, 0, 0);
        }
        var duration_unix = ret[1] * 3600 + ret[2] * 60 + ret[3] * 1;
        var begin_unix = moment($("#begin_time").val()).unix();
        var end_unix = begin_unix + duration_unix;
        var end_time = moment.unix(end_unix).format("YYYY-MM-DD HH:mm:ss");
        $("#end_time").val(end_time);
    });

    $('#duration-3days').click(function() {
        $('#duration').val("72:00:00").change();
    });

    $('#duration-day').click(function() {
        $('#duration').val("24:00:00").change();
    });

    $('#duration-week').click(function() {
        $('#duration').val("168:00:00").change();
    });

    $('#duration-mouth').click(function() {
        $('#duration').val("720:00:00").change();
    });


});

$('.form_datetime_begin').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:00',
    weekStart: 1,
    todayHighlight: 1,
    startView: 2,
    todayBtn: true,
}).on('changeDate', function(ev){
    $('#duration').change();
});

$('.form_datetime_end').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:00',
    weekStart: 1,
    todayHighlight: 1,
    startView: 2,
    todayBtn: true,
}).on('changeDate', function(ev){
    var begin_unix = moment($("#begin_time").val()).unix();
    var end_unix = moment($("#end_time").val()).unix();
    var duration_unix = end_unix - begin_unix;
    var hour = parseInt(duration_unix / 3600);
    var minute = parseInt((duration_unix % 3600) / 60);
    var second = parseInt((duration_unix % 3600) % 60);
    var duration = hour + ":" + (Array(2).join(0) + minute).slice(-2) + ":" + (Array(2).join(0) + second).slice(-2);
    $('#duration').val(duration);
});
</script>
<script src="{{ url('public') }}/js/addproblem.js"></script>

@endsection
