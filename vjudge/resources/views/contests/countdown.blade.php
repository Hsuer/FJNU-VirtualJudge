@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Countdown of contest: {{ $contest-> title }}</div>
                <div class="panel-body">
                    <center>
                        <h3>
                        <div class="time-item">
                            <span id="day_show"></span>
                            <span id="hour_show"></span>
                            <span id="minute_show"></span>
                            <span id="second_show"></span>
                        </div>
                        </h3>
                        <br>
                        <span class="label label-primary">This contest will start at {{ $contest-> begin_time }}</span>
                        <br><br>
                        <a id="enter" data-loading-text="Loading..." class="btn btn-primary" href="{{ route('contest.show', ['id' => $contest->id]) }}" autocomplete="off">Enter</a>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    var $btn = $("#enter").button('loading');
    var now_unix = moment().unix();
    var begin_unix = moment("{{ $contest-> begin_time }}").unix();
    intDiff = parseInt(begin_unix - now_unix);
    function timer(intDiff){
        setTime();
        window.setInterval(function(){
            setTime();
        }, 1000);
    }
    function setTime() {
        var day=0,
            hour=0,
            minute=0,
            second=0;     
        if(intDiff > 0){
            day = Math.floor(intDiff / (60 * 60 * 24));
            hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
            minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
            second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
        }
        // if (hour <= 9) hour = '0' + hour;
        // if (minute <= 9) minute = '0' + minute;
        // if (second <= 9) second = '0' + second;
        $('#day_show').html(day + ' days&nbsp;&nbsp;');
        $('#hour_show').html(hour + ' hours&nbsp;&nbsp;');
        $('#minute_show').html(minute + ' minutes&nbsp;&nbsp;');
        $('#second_show').html(second + ' seconds');
        intDiff--;
        if(intDiff == 0) {
            $btn.button('reset');
        }
    }
    timer(intDiff);
});
</script>

@endsection
