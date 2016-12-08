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
    <body id="body">
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    <span class="today">Hi <strong id="name">{{$name}}</strong>,</span><br/>
                    <span class="first isLunchBreak">Yey! It's lunch time ^_^</span><br class="isLunchBreak"/>
                    <span class="first">You have worked <strong id="today">{{$today}}</strong> of your full day</span><br/>
                    <span class="first">and <strong id="salary">{{$salary}}</strong> of your salary cycle.</span><br/>
                    <span class="second"><strong id="daysPassedAfterSalary">{{$daysPassedAfterSalary}}</strong> days passed after salary.</span><br/>
                    <span class="second">It's your <strong id="isDayNum">{{$isDayNum}}</strong> day after salary.</span><br/>
                    <span class="second"><strong id="daysLeftUntilSalary">{{$daysLeftUntilSalary}}</strong> workdays left until salary.</span><br/>
                    <input type="hidden" value="{{csrf_token()}}" id="_token">
                </div>
            </div>
        </div>
        <script src="js/jquery.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
