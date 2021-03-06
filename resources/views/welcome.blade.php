<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Buglinjo</title>
        <link rel="manifest" href="/manifest.json">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .first {
                font-size: 48px;
            }

            .second {
                font-size: 36px;
            }

            .isLunchBreak {
                display: none;
            }
        </style>
    </head>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-1287387058265593",
            enable_page_level_ads: true
        });
    </script>
    <body id="body">
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        <a href="{{ route('home') }}">{{ Auth::user()->name }}</a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    @endif
                </div>
            @endif
            <div class="content">
                <div class="title m-b-md">
                    @if (Auth::check())
                        <span class="today">Hi <strong id="name">{{$name}}</strong>,</span><br/>
                        <span class="first isLunchBreak">Yey! It's lunch time ^_^</span><br class="isLunchBreak"/>
                        <span class="first">You have worked <strong id="today">{{$today}}</strong> of your full day</span><br/>
                        <span class="first">and <strong id="salary">{{$salary}}</strong> of your salary cycle.</span><br/>
                        <span class="second"><strong id="daysPassedAfterSalary">{{$daysPassedAfterSalary}}</strong> days passed after salary.</span><br/>
                        <span class="second">It's your <strong id="isDayNum">{{$isDayNum}}</strong> day after salary.</span><br/>
                        <span class="second"><strong id="daysLeftUntilSalary">{{$daysLeftUntilSalary}}</strong> workdays left until salary.</span><br/>
                        <input type="hidden" value="{{csrf_token()}}" id="_token">
                    @endif
                </div>
            </div>
        </div>
        <script src="/js/app.js"></script>
        @if (Auth::check())
            <script src="/js/main.js"></script>
        @endif
    </body>
</html>
