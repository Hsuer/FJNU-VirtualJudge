<div class="list-group">
    <a id="home" href="{{ route('user.show', ['id' => $user->id]) }}" class="list-group-item"><i class="fa fa-home fa-fw"></i>&nbsp; Home</a>
    <a id="data" href="{{ route('user.show.data', ['id' => $user->id]) }}" class="list-group-item"><i class="fa fa-book fa-fw"></i>&nbsp; Data</a>
    <a id="submission" href="#" class="list-group-item disabled"><i class="fa fa-upload fa-fw"></i>&nbsp; Submission</a>
</div>