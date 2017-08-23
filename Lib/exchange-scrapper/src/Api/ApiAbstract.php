<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: exchange-scrapper/src/api/ApiAbstract.php
// DESC: Abstract parent class that is used for generic functions for all
//        children
namespace ExScrape\Api;

use ExScrape\Connection;

abstract class APIAbstract
{
  protected $conn;
  public function __construct(Connection $con)
  {
      $this->conn = $con->getConnection();
  }

  /**
  * Insert into the company database, cleaning up certain inputs along the way
  * such as weeding out ' and n/a, while upper-casing all words for easy
  * matching. Any n/a found on the IPO date is set to Jan 1, 1970, and for any
  * other YEAR, set to Jan 1, YEAR.
  *
  * @param string
  * @param string
  * @param string
  * @param float
  * @param float
  * @param date
  * @param string
  * @param string
  */
  protected function insert($exchange, $symbol, $name, $price,
                            $cap, $ipo, $sector, $industry)
    {
    $stmt = $this->conn->prepare("INSERT INTO companies (stockexchange, name,
                                  symbol, price, cap, ipodate, sector, industry)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $exchange);
    $name = str_replace("&#39;", '', $name); //Remove ', causes errors in SQL
    $name = strtoupper($name);
    $stmt->bindParam(2, $name);
    $stmt->bindParam(3, trim($symbol));
    $stmt->bindParam(4, $price);
    $cap = round(($cap / 1000000), 2);
    $stmt->bindParam(5, $cap);
    if(strcmp($ipo, "n/a") === 0)
      $ipo = "19710101";
    else
      $ipo = strval($ipo) . "0101";
    $stmt->bindParam(6, $ipo);
    $sector = strtoupper($sector);
    $stmt->bindParam(7, $sector);
    $industry = strtoupper($industry);
    $stmt->bindParam(8, $industry);
    try{
        $stmt->execute() or die(print_r($stmt->errorInfo(), true));}
    catch(PDOException $e)
    {
        echo "ERROR INSERT to database";
    }
  }

  /**
  * Update the market cap and price for each company based on the NASDAQ.com end
  * of day CSV report.
  *
  * @param string
  * @param float
  * @param float
  */
  protected function updateValue($symbol, $price, $cap)
  {
    $stmt = $this->conn->prepare("UPDATE companies
                                  SET price = :price, cap = :cap,
                                      updated_at = CURRENT_TIMESTAMP
                                  WHERE symbol = :symbol");
    $cap = round(($cap / 1000000), 2);
    $stmt->bindParam('symbol', $symbol);
    $stmt->bindParam('cap', $cap);
    $stmt->bindParam('price', $price);
    try{
        $stmt->execute() or die(print_r($stmt->errorInfo(), true));}
    catch(PDOException $e)
    {
        echo "ERROR INSERT to database";
    }
  }

  /**
  * Update the IPO column of the database using the values found from IPOmonitor
  *
  * @param string
  * @param date
  */
  protected function updateIPO($symbol, $ipo)
  {
    $stmt = $this->conn->prepare("UPDATE companies SET ipodate = :ipo WHERE symbol = :symbol");
    $stmt->bindParam('symbol', $symbol);
    $stmt->bindParam('ipo', $ipo);
    try{
        $stmt->execute() or die(print_r($stmt->errorInfo(), true));}
    catch(PDOException $e)
    {
        echo "ERROR INSERT to database";
    }
  }

  /**
  * Given 1 row from the excel document, grab the required variables and get
  * ready to insert them into the database. If quickCheck() returns true, use
  * updateValue() instead as the company already exists in the DB
  *
  * @param array, CSV
  * @param string
  */
  protected function format($company, $exchange)
  {
    if($this->quickCheck($company[0]))
      if( strcmp($company[2], 'n/a') !== 0) // Create New Company
        $this->insert($exchange, $company[0], $company[1], $company[2],
        $company[3], $company[5], $company[6], $company[7]);
    else
      if( strcmp($company[2], 'n/a') !== 0)
        $this->updateValue($company[0], $company[2], $company[3]);
  }

  /**
  * Check if the given company $symbol is already in the datavase, if it is,
  * return false so as not to double-insert a company
  *
  * @param string
  */
  protected function quickCheck($symbol)
  {
    $stmt = $this->conn->prepare("SELECT symbol FROM companies WHERE (symbol = :symbol)");
    $stmt->bindParam(':symbol', $symbol);
    $stmt->execute();

    return !($stmt->rowCount() > 0);
  }

  /**
  * Check if $file exsists, if it does not, create an empty file before
  * downloading a copy from the given $url. Also re-download if the file is over
  * 24 hours old. Else use the local copy
  *
  * @param string
  * @param string
  */
  protected function dataFile($file, $url)
  {
    $filePath = __DIR__."/data/$file";
    if(!file_exists($filePath)){
      echo "\tERROR: $file does not exsist, downloading\n";
      file_put_contents($filePath, file_get_contents($url));}

    else if (time()-filemtime($filePath) > 24 * 3600){
      if ($fp = curl_init($url)){
        echo "\tWARNING: $file Contains old data, re-downloading\n";
        file_put_contents($filePath, file_get_contents($url));
      }
      else {
        echo "\tERROR: $url<br>Does no exsist or is not responding\n";
      }
    }
    else {
      echo "\t$file is up to date, using local copy\n";}

    return $filePath;
  }
}


?>
