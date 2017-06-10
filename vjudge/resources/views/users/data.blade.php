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
                    <div><canvas id="myChart"></canvas></div>
                </div>
            </div>

            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                  <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="text-decoration: none;">
                      <i class="fa fa-ellipsis-v"></i>&nbsp; Solved
                    </a>
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                  <div class="panel-body">
                      @foreach ($solved_list as $key => $value)
                          <h3><i class="fa fa-location-arrow"></i>&nbsp; {{ $key }}</h3>
                          @foreach ($value as $key => $value)
                              <a href="{{ route('problem.show', ['id' => $value['id']]) }}">{{ $value['origin_id'] }}</a>
                              &nbsp;
                          @endforeach
                      @endforeach
                  </div>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingTwo">
                  <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="text-decoration: none;">
                      <i class="fa fa-ellipsis-v"></i>&nbsp; Unsolved
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                  <div class="panel-body">
                      @foreach ($unsolved_list as $key => $value)
                          <h3><i class="fa fa-location-arrow"></i>&nbsp; {{ $key }}</h3>
                          @foreach ($value as $key => $value)
                              <a href="{{ route('problem.show', ['id' => $value['id']]) }}">{{ $value['origin_id'] }}</a>
                          @endforeach
                      @endforeach
                  </div>
                </div>
              </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $("#data").addClass('active');

    var data = {
        labels: ["AC", "WA", "CE", "PE", "RE", "TLE", "MLE", "OLE", "OTHER"],
        datasets: [
            {
                label: "{{ $user-> name }}'s Dataset",
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)',
                ],
                borderWidth: 1,
                data: [
                    {{ $user-> ac }},
                    {{ $user-> wa }},
                    {{ $user-> ce }},
                    {{ $user-> pe }},
                    {{ $user-> re }},
                    {{ $user-> tle }},
                    {{ $user-> mle }},
                    {{ $user-> ole }},
                    {{ $user-> other }}
                ]
            }
        ]
    };

    var ctx = $("#myChart").get(0).getContext("2d");

    var myBarChart = new Chart(ctx, {
        type: "bar",
        data: data,
        options: {
            scales: {
                xAxes: [{
                    stacked: true
                }],
                yAxes: [{
                    stacked: true
                }]
            },
            title: {
                display: true,
                text: 'Data Chart'
            },
        }
    });
});
</script>

@endsection
