<!doctype html>

<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #000000;
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
                text-align: left;
            }

            .title {
                font-size: 84px;
            }

            table, th, td {
                border: 1px solid black;
                span:900
            }

        </style>
    </head>
    <body>

            <div class="content">
              <?php
              use App\Company;
              function prettyPrint($stock, $keys){
                print "<table>";
                print "<tr><th colspan='2'>" . $stock['name'] . "</th></tr>";
                print "<tr> <td><b>Symbol</b></td><td>" . $stock['symbol']. "</td> </tr>";
                print "<tr> <td><b>Sector</b></td><td>" . $stock['sector']. "</td> </tr>";
                print "<tr> <td><b>Industry</b></td><td>" . $stock['industry']. "</td> </tr>";

                print "<tr> <td><b>Keywords:</b></td>";
                $first = true;
                foreach ($keys as $k => $value) {
                  if($first){
                    print "<td>" . $k . "</td></tr>";
                    $first = !$first;}
                  else{
                    print "<td></td><td>" . $k . "</td></tr>";
                  }
                }
                print "</table>";
              }

              function addNewKeys($keys, $symbol){
                if($keys === null)
                  return;
                $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
                $byCompany = json_decode(file_get_contents($fileName), true);
                $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_KEYWORD.json";
                $byKeyword = json_decode(file_get_contents($fileName), true);
                $keyArray = explode(",", $keys);

                foreach ($keyArray as $key) {
                  $byCompany[$symbol]['Keys'][strtoupper($key)] = 0;
                  $byKeyword[strtoupper($key)][] = $symbol;
                }

                $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
                file_put_contents($fileName, json_encode($byCompany));
                $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_KEYWORD.json";
                file_put_contents($fileName, json_encode($byKeyword));


                $fileName = env('COMPANY_KEYWORDS_REPO') . "PP_BY_COMPANY.json";
                file_put_contents($fileName, json_encode($byCompany, JSON_PRETTY_PRINT));
                $fileName = env('COMPANY_KEYWORDS_REPO') . "PP_BY_KEYWORD.json";
                file_put_contents($fileName, json_encode($byKeyword, JSON_PRETTY_PRINT));
              }

              $symbol = null;
              if(isset($_POST['submit'])) {
                $symbol = $_POST['symbol'];
                if($_POST['newKey'] != null)
                  addNewKeys($_POST['newKey'], $_POST['symbol']);

                $count = Company::select('*')->count();
                $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
                $byCompany = json_decode(file_get_contents($fileName), true);
                print "<h1>Number of Companies: " . $count . "</h1>";
                $current = Company::select('*')->where('symbol', '=', $symbol)->get()->first()->toArray();
                prettyPrint($current, $byCompany[$symbol]['Keys']);
              } else {
                $count = Company::select('*')->count();
                $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
                $byCompany = json_decode(file_get_contents($fileName), true);

                print "<h1>Number of Companies: " . $count . "</h1>";
                $current = Company::select('*')->where('id', '=', rand(0, $count))->get()->first()->toArray();
                $symbol = $current['symbol'];
                prettyPrint($current, $byCompany[$symbol]['Keys']);

              }

              ?>

            <form method="post" action="/company/edit">
              <input type="text" name="newKey">
              <input type="hidden" name="symbol" value="<?php echo $symbol?>" />
              <input type="submit" value="Submit Keys" name="submit"> <!-- assign a name for the button -->
            </form>
            <form method="get" action="/company/edit">
              <input type="submit" value="New Company" name="submit"> <!-- assign a name for the button -->
            </form>
            </div>
        </div>
    </body>
</html>
