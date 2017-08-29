<html lang="{{ config('app.locale') }}">
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <title>TradeLife edit keywords</title>
      <script>
      function myFunction(x, _this) {
        x.style.backgroundColor = _this.checked ? '#00FF00' : '#E8E8E8';
      }
      </script>
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
        .checked {
          background-color: #ff0000;
          font-size: 20;
        }​
        textarea{
          display:block;
        }

        input[type="submit"]{
          /* change these properties to whatever you want */
          background-color: #555;
          color: #fff;
          border-radius: 10px;
          height: 70;
          width:500;
        }
        .smallButton{
          /* change these properties to whatever you want */
          background-color: #555;
          color: #fff;
          border-radius: 10px;
        }
        label { display: inline-block; width: 100%; }
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
            height:99%;
        }

        .title {
            font-size: 84px;
        }

        table, th, td {
            border: 1px solid black;
            margin-left:auto;
            margin-right:auto;
        }
        .leftCol{
          float: left;
          width: 33%;
          background: #E8E8E8;
          height: 100%;
          border: 1px solid black;
          align-items: center;
        }
        .rightCol{
          float: left;
          width: 33%;
          background: #E8E8E8;
          border: 1px solid black;
          height: 100%;
          overflow-y: scroll;
          align-items: center;
        }
        .centerCol{
          float: left;
          width: 33%;
          background: #E8E8E8;
          height: 100%;
          border: 1px solid black;
          align-items: center;
        }
        .formBox{
          flex: 1;
        }
        .inline {
          display: inline;
        }

        .link-button {
          background: none;
          border: none;
          color: blue;
          text-decoration: underline;
          cursor: pointer;
          font-size: 1em;
          font-family: serif;
        }
        .link-button:focus {
          outline: none;
        }
        .link-button:active {
          color:red;
        }
      </style>
    </head>
  <body>

    <div class="content">
    <?php
    use App\Company;

    function printStocks($db){
      print "<table width=99%>";
      foreach ($db as $company) {
        print "<tr>";
        print "<td>";
        print "<form method='post' class='inline' action='/keywords/edit'>";
        print  "<input type='hidden' name='keys' value='' />";
        print  "<input type='hidden' name='symbol' value='".$company['symbol']."' />";
        print  "<button type='submit' name='submit' class='link-button'> ".$company['name']." </button>";
        print "</form>";


        print "</td>";
        print "<td>" .$company['symbol'] . "</td>";
        print "<td>" .$company['sector'] . "</td>";
        print "</tr>";
      }
    }

    function googleSearch($name){
      $nameArray = explode(' ', $name);
      $search = "<a target='_blank' href='http://www.google.com/search?q=";

      foreach ($nameArray as $value) {
        $search = $search . $value . '+';
      }
      rtrim($search,"+");
      $search = $search . "'>". $name ."</a>";
      return $search;
    }

    function prettyPrint($stock, $keys){
      if(empty($stock)){
        print "<h2> NO SUCH STOCK EXISTS WITH SYMBOL: " . $stock . "</h2>";
      }
      print "<table width=99%>";
      print "<tr><th colspan='2'><h2>" . googleSearch($stock['name']) . "</h2></th></tr>";
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

    function updateKeys($keys, $symbol){
      if($keys === null)
        return;
      $file = env('COMPANY_KEYWORDS_REPO') . "BY_";

      $byCompany = json_decode(file_get_contents(($file . 'COMPANY.JSON')), true);
      $byKeyword = json_decode(file_get_contents(($file . 'KEYWORD.JSON')), true);
      $keyArray = explode(",", $keys);
      foreach ($keyArray as $key) {
        if($key != ''){
        $byCompany[$symbol]['Keys'][strtoupper($key)] = 0;
        $byKeyword[strtoupper($key)][] = $symbol;}
      }

      if(isset($_POST['del'])){
        foreach($_POST['del'] as $key){
          unset($byCompany[$symbol]['Keys'][strtoupper($key)]);
          unset($byKeyword[strtoupper($key)][$symbol]);
        }
      }

      if(isset($_POST['add'])){
        foreach($_POST['add'] as $key){
          $byCompany[$symbol]['Keys'][strtoupper($key)] = 0;
          $byKeyword[strtoupper($key)][] = $symbol;
        }
      }



      file_put_contents(($file . 'COMPANY.JSON'), json_encode($byCompany));
      file_put_contents(($file . 'KEYWORD.JSON'), json_encode($byKeyword));

      file_put_contents(($file . 'COMPANY_PP.JSON'), json_encode($byCompany, JSON_PRETTY_PRINT));
      file_put_contents(($file . 'KEYWORD_PP.JSON'), json_encode($byKeyword, JSON_PRETTY_PRINT));
    }

    function keyWordCheckBox($keywords, $arrName ,$columns){
      if(empty($keywords)){
        print "<H3> No Keywords </H3>";
        return;}
      $cols = $columns;
      print "<table width=99% padding=2px>";
      foreach ($keywords as $key => $value) {
        if($cols == $columns){
          print "<tr>";}
        print "<td>";
        print "<div id='".$arrName.$key."'>";
        print "<label><input type='checkbox' name='". $arrName .
              "[]' value=" . $key . " onChange='myFunction(".$arrName.$key.", this)'/>" . $key . "</label></td>";
        $cols--;
        if($cols == 0){
          $cols = $columns;
          print "</tr>";
        }

      }
      print "</table>";
    }

    function printPost(){
      print "<pre>";
      print_r($_POST);
      print "</pre>";
    }
    $symbol = null;
    $current = null;
    $goodIndex = false;
    $count = Company::select('*')->count();
    if(isset($_POST['submit'])) {
      $symbol = strtoupper($_POST['symbol']);

      if(Company::select('*')->where('symbol', '=', $symbol)->exists()){
        if(isset($_POST['keys'])){
          updateKeys($_POST['keys'], $_POST['symbol']);
        }
        $goodIndex = true;
        $current = Company::select('*')->where('symbol', '=', $symbol)->get()->first()->toArray();
      }
    }

    else {
      $current = Company::select('*')->where('id', '=', rand(0, $count))->get()->first()->toArray();
      $symbol = $current['symbol'];
    }


      $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
      $byCompany = json_decode(file_get_contents($fileName), true);

      $fileName = env('USER_KEYWORDS_REPO') . "USER.json";
      $userKeywords = json_decode(file_get_contents($fileName), true);
    ?>

    <div class="leftCol">
      <h1>Edit</h1>
      <form method="post" action="/keywords/edit">
        <input type="submit" value="search" name="submit" class='smallButton'>
        <input type="text" name='symbol'>
      </form>
      <form method="get" action="/keywords/edit">
        <input type="submit" value="Random Company" name="submit">
      </form>
      <div width=100px>
        <div >
          <form method='post' action='/keywords/edit'>
            <input type='hidden' name='symbol' value='<?php echo $symbol?>' />

            <div style='float:left'>
              <textarea class='text' cols='40' rows ='4' name='keys'></textarea>
            </div>
            <div style='float:left'>

            </div>

            <div style='clear:both'>
              <h3> ADD Keywords </h3>
              <?php keyWordCheckBox($userKeywords, 'add', 5) ?>
              <h3> Delete Keywords </h3>
              <?php
                if($goodIndex)
                  keyWordCheckBox($byCompany[$symbol]['Keys'], 'del', 5);
                else
                  print "<h2>Bad Index</h2>";
              ?>
            </div>
            <input type='submit' value='SUBMIT' name='submit'>
          </form>
        </div>

      </div>


      <?php printPost()?>
    </div>

    <div class="centerCol">
      <h1>Current Selection</h1>
      <?php
      if($goodIndex)
        prettyPrint($current, $byCompany[$symbol]['Keys']);
      else
        print "<h2>No such company ". $symbol ."</h2>";
      ?>


    </div>

    <div class='rightCol'>
        <h1>Number of Companies: <?php echo $count?></h1>
        <?php printStocks(Company::select('*')->get())?>
    </div>


  </div>

  </body>
</html>
