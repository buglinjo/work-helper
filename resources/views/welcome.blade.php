<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Buglinjo</title>

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

            .m-b-md {
                margin-bottom: 30px;
            }

            .salary {
                font-size: 48px;
            }

            .days-passed {
                font-size: 36px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                   <span class="today">After starting day: <strong>{{$today}}</strong></span><br/>
                    <span class="salary">After salary: <strong>{{$salary}}</strong></span><br/>
                    <span class="days-passed"><strong>{{$daysPassedAfterSalary}}</strong> days passed after salary.</span><br/>
                    <span class="days-passed">It's your <strong>{{$daysPassedAfterSalary+1}}th</strong> working day after salary.</span><br/>
                    <span class="days-passed"><strong>{{$daysLeftUntilSalary}}</strong> days left until salary.</span><br/>
                </div>
            </div>
        </div>
    </body>
</html>
