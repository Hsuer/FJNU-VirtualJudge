<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-info fa-fw"></i> Information</div>
    <div class="list-group">
    	@if (strip_tags(trim($contest->description)) != "")
        	<a class="list-group-item"><i class="fa fa-bullhorn fa-fw"></i>&nbsp; {{ $contest->description }}</a>
        @else
        	<a class="list-group-item"><i class="fa fa-bullhorn fa-fw"></i>&nbsp; No Description</a>
        @endif
        <a class="list-group-item"><i class="fa fa-hourglass-2 fa-fw"></i>&nbsp; {{ $contest->end_time }}</a>
    </div>
</div>
