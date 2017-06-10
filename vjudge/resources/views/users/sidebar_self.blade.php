<div class="list-group">
    <a id="home" href="{{ url('/user') }}" class="list-group-item">
        <i class="fa fa-home fa-fw"></i>&nbsp; Home
    </a>
    <a id="data" href="{{ url('/user/data') }}" class="list-group-item">
        <i class="fa fa-book fa-fw"></i>&nbsp; Data
    </a>
    <a id="modify" href="{{ url('/user/modify') }}" class="list-group-item">
        <i class="fa fa-pencil fa-fw"></i>&nbsp; Modify
    </a>
    <a id="password" href="{{ url('/user/password') }}" class="list-group-item">
        <i class="fa fa-key fa-fw"></i>&nbsp; Password
    </a>
    <a id="submission" href="#" class="list-group-item disabled">
        <i class="fa fa-upload fa-fw"></i>&nbsp; Submission
    </a>
</div>