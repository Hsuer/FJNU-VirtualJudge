<div class="panel panel-default">
    <div class="panel-body" style="padding: 5px!important;">
		<ul id="contest-navbar" class="nav nav-pills">
		    <li id="home" role="presentation">
		    	<a href="{{ route('contest.show', ['id' => $contest->id]) }}">
		    	<i class="fa fa-home fa-fw"></i> Overview</a>
		    </li>
		    <li id="problem" role="presentation">
		    	<a href="{{ route('contest.problem', ['id' => $contest->id, 'pid' => 'A']) }}">
		    	<i class="fa fa-book fa-fw"></i> Problem</a>
		    </li>
		    <li id="submit" role="presentation">
		    	<a href="{{ route('contest.submit', ['id' => $contest->id, 'pid' => 'A']) }}">
		    	<i class="fa fa-upload fa-fw"></i> Submit</a>
		    </li>
		    <li id="status" role="presentation">
		    	<a href="{{ route('contest.status', ['id' => $contest->id]) }}">
		    	<i class="fa fa-heartbeat fa-fw"></i> Status</a>
		    </li>
		    <li id="standing" role="presentation">
		    	<a href="{{ route('contest.standing', ['id' => $contest->id]) }}">
		    	<i class="fa fa-flag fa-fw"></i> Standing</a>
		    </li>
		    <li id="clarification" role="presentation" class="disabled"><a href="#"><i class="fa fa-commenting fa-fw"></i> Clarification</a></li>
		</ul>
	</div>
</div>