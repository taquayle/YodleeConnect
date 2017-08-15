<!doctype html>

<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->


        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
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

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
          <div class="content">

            <?php
                $conn = new \TradeLife\Client();
                print "<h3>ATTEMPTING TO INSERT USERS......</h3>";
                //$conn->user()->test();
                $conn->user()->insert("sbMemtaquayle1", "example2@gmail.com","sbMemtaquayle1#123");
                print '<br>';
                $conn->user()->insert("sbMemtaquayle2", "something@gmail.com","sbMemtaquayle2#123");
                print '<br>';
                $conn->user()->insert("sbMemtaquayle3", "emailaccount@gmail.com","sbMemtaquayle3#123");
                print '<br>';
                $conn->user()->insert("sbMemtaquayle4", "chris@gmail.com","sbMemtaquayle4#123");
                print '<br>';
                $conn->user()->insert("sbMemtaquayle5", "george@gmail.com","sbMemtaquayle5#123");
                print '<br>';
                $conn->user()->insert("bogus", "notARealAccount@gmail.com","foo");
                print "<h3>INSERTION FINISHED </h3>";
                $conn->data()->users();

                print "<br>ATTEMPTING LOGIN: ";
                print $conn->user()->login("sbMemtaquayle1", "sbMemtaquayle1#123");
                //$conn->user()->validUserName("sbMemtaquayle1");



            ?>
          </div>
        </div>
    </body>
</html>
