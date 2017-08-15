<?php
//  AUTHOR: Tyler Quayle
//  DATE: 7/7/2017
//  FILE: trade-life/src/api/APIAbstract.php
//  DESC: Abstract parent class that handles generic functions for children
namespace TradeLife\Api;
use PDO;

use TradeLife\Connection;
use TradeLife\Session;
use ExScrape;

//require_once __DIR__ . '/../vendor/autoload.php';
abstract class APIAbstract
{
    protected $conn;
    protected $session;
    protected $temp;

    public function __construct(Connection $con, Session $session)
    {
        $this->conn = $con->getConnection();
        $this->session = $session;
        $this->temp = array();

    }

    protected function getCount($dbName)
    {
      $stmt = $this->conn->prepare("SELECT * FROM $dbName");
      $stmt->execute();
      return $stmt->rowCount();
    }

    protected function loggedIn()
    {
      if($this->session->getUser() != NULL)
        return true;
      echo "ERROR: No user logged in";
      return false;
    }

    protected function printArray($arr)
    {
      print "<pre>";
      print_r($arr);
      print "</pre>";
    }
}


?>
