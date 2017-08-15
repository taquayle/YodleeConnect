<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (! ini_get('date.timezone')) {
        date_default_timezone_set('America/New_York');
    }

    require_once __DIR__ . '/../../vendor/autoload.php';

    /*
    * Connects to TradeLife and Yodlee servers, before
    * grabbing all Yodlee information and putting in tradelife database
    */
    function populate($conn, $yodleeApi, $user, $pass)
    {
      if($conn->user()->login($user, $pass))
      {
        $response = $yodleeApi->user()->login($user, $pass);
        print "<br>$user - Login: ";
        var_dump ($response);
        $response = $yodleeApi->transactions()->getPreviousYears(10);
        $reCount = count($response);
        $conn->transaction()->insert($response);
        print "<table cellpadding='10' border=solid bordercolor=black><tr>";
        for ($i=0; $i < 8; $i++) {
          oneCell($response[rand(0, $reCount)]);
        }
        print '</tr></table>';

        return true;
      }
      return false;
    }

    /*
    * Displays 1 cell for each yodelee response;
    */
    function oneCell($response)
    {
        print '<td valign="top"><pre>';
        print_r ($response);
        print '</pre></td>';
    }

    $conn = new \TradeLife\Client();

    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();

    $yodleeApi = new \YodleeApi\Client(getenv('YODLEEAPI_URL'));
    $response = $yodleeApi->cobrand()->login(getenv('YODLEEAPI_COBRAND_LOGIN'), getenv('YODLEEAPI_COBRAND_PASSWORD'));
    print 'Cobrand Login: ';
    var_dump ($response);



    if(!populate($conn, $yodleeApi, getenv('YODLEEAPI_USER_LOGIN'), getenv('YODLEEAPI_USER_PASSWORD'))){
      print "<h3> ERROR: Test users not found, inserting them";
      include 'db_test_users.php';
    }

    populate($conn, $yodleeApi, "sbMemtaquayle1", "sbMemtaquayle1#123");
    populate($conn, $yodleeApi, "sbMemtaquayle2", "sbMemtaquayle2#123");
    populate($conn, $yodleeApi, "sbMemtaquayle3", "sbMemtaquayle3#123");
    populate($conn, $yodleeApi, "sbMemtaquayle4", "sbMemtaquayle4#123");
    populate($conn, $yodleeApi, "sbMemtaquayle5", "sbMemtaquayle5#123");

    $conn->data()->transactions();
 ?>
