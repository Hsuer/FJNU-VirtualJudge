@extends('layouts.app')

@section('content')
<div class="jumbotron index-bg">
    <!-- <div id="large-header" style="position: absolute; margin-top:-48px;">
        <canvas id="demo-canvas"></canvas>
    </div> -->
    <div class="container">
        <br>
        <h1><span class="element"></span></h1>
    </div>
</div>

<script src="{{ url('public') }}/js/TweenLite.min.js"></script>
<script src="{{ url('public') }}/js/EasePack.min.js"></script>
<script src="{{ url('public') }}/js/demo-1.js"></script>
<script>
    $(function(){
        $(".element").typed({
            strings: ["Welcome to", "FJNU VirtualJudge"],
            contentType: 'html',
            typeSpeed: 50,
            backSpeed: 50,
        });
    });
</script>

@endsection
