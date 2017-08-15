<?php

  require_once __DIR__ . '/../../vendor/autoload.php';
  $conn = new \TradeLife\Client();

  $conn->data()->users();
?>
