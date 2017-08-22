<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: .php
// DESC:

namespace ExScrape;

use ExScrape\Api\NASDAQ;
use ExScrape\Api\NYSE;
use ExScrape\Api\DATA;
use ExScrape\Api\IPO;
use ExScrape\Api\KEYWORDS;


class Client
{

  protected $connection;

  public function __construct()
  {
    return $this->connection = new Connection();
  }

  public function nasdaq()
  {
    return new NASDAQ($this->connection);
  }

  public function nyse()
  {
    return new NYSE($this->connection);
  }

  public function data()
  {
    return new DATA($this->connection);
  }

  public function ipo()
  {
    return new IPO($this->connection);
  }

  public function keywords()
  {
    return new KEYWORDS($this->connection);
  }

  public function fullUpdate()
  {
    $this->singleUpdate("NASDAQ", $this->nasdaq());
    //$this->singleUpdate("NYSE", $this->nyse());
    $this->singleUpdate("IPO DATES", $this->ipo());
    $this->singleUpdate("KEYWORDS", $this->keywords());
  }

  private function singleUpdate($stockexchange, $stockconn)
  {
    echo "$stockexchange UPDATING...........\n";
    $stockconn->update();
    echo "$stockexchange UPDATED...........\n";
  }
}
 ?>
