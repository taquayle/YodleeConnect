<?php
  require_once __DIR__ . '/../../vendor/autoload.php';


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
    include 'db_user_show.php';

    print "<br>ATTEMPTING LOGIN: ";
    print $conn->user()->login("sbMemtaquayle1", "sbMemtaquayle1#123");
    //$conn->user()->validUserName("sbMemtaquayle1");



?>
