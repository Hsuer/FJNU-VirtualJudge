@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @include('contests.navbar')
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-bordered table-hover text-center" id="standing-table" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="4%" >#</th>
                                <th id="username" class="text-center" width="10%">Username</th>
                                <th id="nickname" class="text-center" width="10%">Nickname</th>
                                <th class="text-center" width="5%">Solve</th>
                                <th class="text-center" width="5%">Penalty</th>
                                @foreach ($contest_problem_id as $id)
                                    <th class="text-center" width="{{ 65 / count($contest_problem_id) }}%"><a href="{{ route('contest.problem', ['id' => $contest->id, 'pid' => $id]) }}">{{ $id }}</a><br>{{ $first_blood[$id]['ac'] }} / {{ $first_blood[$id]['submit'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <!-- {{ $i = 1 }} -->
                            @foreach ($users_list as $key => $row)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td><a href="{{ route('user.show', ['id' => $row['id']]) }}" target="_blank">{{ $row['name'] }}</a></td>
                                    <td><a href="{{ route('user.show', ['id' => $row['id']]) }}" target="_blank">{{ $row['nick'] }}</a></td>
                                    <td>{{ $row['ac_num'] }}</td>
                                    <td>{{ $row['penalty'] }}</td>
                                    @foreach ($contest_problem_id as $id)
                                        @if ($row[$id]['ac'] == 1)
                                            @if ($row[$id]['fb'] == 1)
                                                <td class="first_blood"><span class="fb-cell-time">{{ $row[$id]['ac_time'] }}<br>({{ $row[$id]['wa'] }})</span></td>
                                            @else
                                                <td class="accept">{{ $row[$id]['ac_time'] }}<br>({{ $row[$id]['wa'] }})</td>
                                            @endif
                                        @elseif($row[$id]['wa'] == 0)
                                            <td></td>
                                        @else
                                            <td><span class="failed">--:--:--<br>({{ $row[$id]['wa'] }})</span></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $("#standing").addClass("active");
});
</script>
@endsection
