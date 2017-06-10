@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Rewrite</div>

                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('problem.restore', ['id' => $problem-> id]) }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label">Title</label>
                            <div class="col-md-9">
                                <input id="title" type="text" class="form-control" name="title" value="{{ $problem-> title }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="col-md-2 control-label">Description</label>
                            <div class="col-md-9">
                                <textarea id="description" type="text" class="form-control" name="description">{{ $problem-> description }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="input" class="col-md-2 control-label">Input</label>
                            <div class="col-md-9">
                                <textarea id="input" type="text" class="form-control" name="input">{{ $problem-> input }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label">Output</label>
                            <div class="col-md-9">
                                <textarea id="output" type="text" class="form-control" name="output">{{ $problem-> output }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title" class="col-md-2 control-label">Hint</label>
                            <div class="col-md-9">
                                <textarea id="hint" type="text" class="form-control" name="hint">{{ $problem-> hint }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-upload"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="/css/styles/simditor.css" />
<script type="text/javascript" src="/js/scripts/module.js"></script>
<script type="text/javascript" src="/js/scripts/hotkeys.js"></script>
<script type="text/javascript" src="/js/scripts/uploader.js"></script>
<script type="text/javascript" src="/js/scripts/simditor.js"></script>

<script type="text/javascript">
var description = new Simditor({
    textarea: $('#description')
});
var input = new Simditor({
    textarea: $('#input')
});
var output = new Simditor({
    textarea: $('#output')
});
var hint = new Simditor({
    textarea: $('#hint')
});
</script>

@endsection