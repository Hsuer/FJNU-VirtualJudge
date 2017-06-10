<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ url('public') }}/css/extra.css" rel="stylesheet">
    <link href="{{ url('public') }}/css/app.css" rel="stylesheet">
    <link href="{{ url('public') }}/css/datatables.css" rel="stylesheet">
    <link href="{{ url('public') }}/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ url('public') }}/css/pnotify.css" rel="stylesheet">
    <link href="{{ url('public') }}/css/datetimepicker.min.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ url('public') }}/js/jquery.js"></script>
    <script src="{{ url('public') }}/js/moment.min.js"></script>
    <script src="{{ url('public') }}/js/datetimepicker.min.js"></script>
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('/problem') }}">Problem</a></li>
                        <li><a href="{{ url('/status') }}">Status</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-hover="dropdown" role="button" aria-expanded="false" style="cursor: default;">
                                Contest <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/contest') }}">Contests</a></li>
                                <li><a href="{{ url('/contest/create') }}">Add Contest</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ url('/ranklist') }}">Ranklist</a></li>
                        <!-- <li><a href="{{ url('/tools') }}">Tools</a></li> -->
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            <li><a href="{{ url('/register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-hover="dropdown" role="button" aria-expanded="false" style="cursor: default;">
                                    <span class="fa fa-user"></span> {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('/user') }}">Userinfo</a></li>
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ url('public') }}/js/app.js"></script>
    <script src="{{ url('public') }}/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="{{ url('public') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('public') }}/js/dataTables.bootstrap.min.js"></script>
    <script src="{{ url('public') }}/js/Chart.min.js"></script>
    <script src="{{ url('public') }}/js/pnotify.js"></script>
    <script src="{{ url('public') }}/js/typed.min.js"></script>
    <script type="text/javascript">
        $(function($) {
            $("#time").html(moment().format('YYYY-MM-DD HH:mm:ss'));
            setInterval( function () {
                $("#time").html(moment().format('YYYY-MM-DD HH:mm:ss'));
            }, 1000 );
        });
    </script>
</body>

<footer>
    <center>
        <p>
            Server Time: <span id="time">{{ date('Y-m-d H:i:s', time()) }}</span>
            <br>
            FJNU ACM-ICPC Group <a href="https://github.com/hsuer/FJNU-OnlineJudge" target="_blank">{{ config('app')['name'] }}</a> (Preview)
        </p>
    </center>
</footer>
</html>
