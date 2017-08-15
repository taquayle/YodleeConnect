<?php
  require_once __DIR__ . '/../../vendor/autoload.php';

  $conn = new \TradeLife\Client();

  $conn->user()->login('sbMemtaquayle1', 'sbMemtaquayle1#123');

  $conn->profile()->create();

  //$conn->user()->login('sbMemtaquayle2', 'sbMemtaquayle2#123');

  //$conn->profile()->create();

?>
