@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-default">
                <!-- <div class="panel-heading">Problem {{ $problem->id }}</div> -->

                <div class="panel-body">
                    <h2>{{ $problem-> title }}</h2>

                    <span class="label label-info">{{ $problem-> origin_oj }}-{{ $problem-> origin_id }}</span>
                    <span class="label label-info">{{ $problem-> time }} ms</span>
                    <span class="label label-info">{{ $problem-> memory }} kb</span>

                    @if ($problem-> father_id != null)
                        <span class="label label-danger">Rewrite</span>
                    @endif

                    @if ($problem->special_judge == 1)
                        <span class="label label-danger">Special Judge</span>
                    @endif
                    
                    <br><br>
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('status/store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('language') ? ' has-error' : '' }}">
                            <label for="language" class="col-md-12">Language</label>

                            <div class="col-md-12">
                                <select name="language" class="form-control">
                                    @foreach ($language as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('language'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('language') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="code" class="col-md-12">Share Code?</label>
                            <div class="col-md-12">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-primary">
                                        <input type="radio" name="share" value="1" autocomplete="off">Yes
                                    </label>
                                    <label class="btn btn-primary active">
                                        <input type="radio" name="share" value="0" autocomplete="off" checked>No
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                            <label for="code" class="col-md-12">Code</label>

                            <div class="col-md-12">
                                <textarea id="code" type="text" class="form-control" name="code" rows="10" placeholder="Input solution code here~" required autofocus></textarea>

                                @if ($errors->has('code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <input class="hidden" name="problem_id" value="{{ $problem->id }}"></input>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-upload fa-fw" aria-hidden="true"></i> Submit</button>
                    </form>
                
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <a class="btn btn-danger btn-block" href="{{ url('/problem/').'/'.$problem->id }}"><i class="fa fa-arrow-circle-left fa-fw" aria-hidden="true"></i> Go back</a>
                    <a class="btn btn-default btn-block"><i class="fa fa-comments fa-fw" aria-hidden="true"></i> Discuss</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
