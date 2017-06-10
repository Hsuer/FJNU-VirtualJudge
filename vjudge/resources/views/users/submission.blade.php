@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            @if (!Auth::guest())
                @if ($user-> id === Auth::user()->id)
                    @include('users.sidebar_self')
                @else
                    @include('users.sidebar_view')
                @endif
            @else
                @include('users.sidebar_view')
            @endif
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <table class="table table-bordered table-hover" id="status-table" width="100%">
                    <thead>
                        <th>ID</th>
                        <th>OJ</th>
                        <th>ID</th>
                        <th>Result</th>
                        <th>Language</th>
                        <th>Time</th>
                        <th>Memory</th>
                        <th>Length</th>
                        <th>Submit Time</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#submission").addClass('active');
</script>

@endsection