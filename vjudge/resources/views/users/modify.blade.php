@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-3">
            @include('users.sidebar_self')
        </div>
        <div class="col-md-9">
            @if ($errors->has('modify_success'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i>&nbsp; <b>{{ $errors->first('modify_success') }}</b>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-body">
                <br>
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/user/modify_profile') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="email" class="col-md-4 control-label">E-mail</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $user-> email }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">Name</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $user-> name }}</p>
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('nick') ? ' has-error' : '' }}">
                        <label for="nick" class="col-md-4 control-label">Nick</label>

                        <div class="col-md-6">
                            <input id="nick" type="text" class="form-control" name="nick" value="{{ $user-> nick }}" required>

                            @if ($errors->has('nick'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nick') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('school') ? ' has-error' : '' }}">
                        <label for="school" class="col-md-4 control-label">School</label>

                        <div class="col-md-6">
                            <input id="school" type="text" class="form-control" name="school" value="{{ $user-> school }}">

                            @if ($errors->has('school'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('school') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('student_id') ? ' has-error' : '' }}">
                        <label for="student_id" class="col-md-4 control-label">StudentID</label>

                        <div class="col-md-6">
                            <input id="student_id" type="text" class="form-control" name="student_id" value="{{ $user-> student_id }}" required>

                            @if ($errors->has('student_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('student_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-md-4 control-label">Description</label>

                        <div class="col-md-6">
                            <input id="description" type="text" class="form-control" name="description" value="{{ $user-> description }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload"></i> Submit
                            </button>
                        </div>
                    </div>
                </form>
                <br>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#modify").addClass('active');
</script>

@endsection