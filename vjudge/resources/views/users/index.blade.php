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
                <div class="panel-body">
                <h2><i class="fa fa-address-card-o"></i>&nbsp; Information of {{ $user-> name }}</h2>
                <table class="table table-bordered table-hover">
                    <tbody>
                    <tr><td width="25%"><b>Email</td><td>{{ $user-> email }}</td></tr>
                    <tr><td><b>Name</td><td>{{ $user-> name }}</td></tr>
                    <tr><td><b>Nick</td><td>{{ $user-> nick }}</td></tr>
                    <tr><td><b>School</td><td>{{ $user-> school }}</td></tr>
                    <tr><td><b>StudentID</td><td>{{ $user-> student_id }}</td></tr>
                    <tr><td><b>Description</td><td>{{ $user-> description }}</td></tr>
                    <tr><td><b>Register Time</td><td>{{ $user-> created_at }}</td></tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#home").addClass('active');
</script>

@endsection