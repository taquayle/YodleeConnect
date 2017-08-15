<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (! ini_get('date.timezone')) {
    date_default_timezone_set('America/New_York');
}
  require_once __DIR__ . '/../../vendor/autoload.php';

  $conn = new \ExScrape\Client();
  $conn->fullUpdate();
  $conn->data()->display();
 ?>
