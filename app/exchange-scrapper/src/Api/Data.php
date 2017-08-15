<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: exchange-scrapper/src/api/Data.php
// DESC: Used to print out various company related pieces of information
namespace ExScrape\Api;

class DATA extends APIAbstract{

    public function companyCount()
    {
      $stmt = $this->conn->prepare("SELECT * FROM companies");
      $stmt->execute();
      return $stmt->rowCount();
    }

    public function test()
    {
      print "<br>DATA.php: Hello World</br>";
    }

    public function display()
    {
      print "<h1><br>Company Count: ";
      print $this->companyCount();
      print "</h1><br>";
      $this->fullDataBase();
    }

    /**
    * Print the full company database in HTML format used for debugging
    */
    public function fullDataBase()
    {
      print "<table cellpadding='10' border=solid bordercolor=black>";
      print"<tr>
            <td>ROW</td>      <td>EXCHANGE</td>
            <td>NAME</td>     <td>IPO</td>
            <td>SYMBOL</td>   <td>PRICE</td>
            <td>CAP</td>      <td>UPDATED</td>
            <td>SECTOR</td>   <td>INDUSTRY</td>
            </tr>";
      $sql = "SELECT *  FROM companies ORDER BY ipodate DESC, cap DESC";

      foreach ($this->conn->query($sql) as $row) {
          print "<tr> <td nowrap>";
          print $row['id'] .           "</td><td nowrap>";
          print $row['stockexchange'] . "</td><td nowrap>";
          print $row['name'] .          "</td><td nowrap>";
          print $row['ipodate'] .       "</td><td nowrap>";
          print $row['symbol'] .        "</td><td nowrap>";
          print $row['price'] .         "</td><td nowrap>";
          print $row['cap'] .           "</td><td nowrap>";
          print $row['created_at'] .   "</td><td nowrap>";
          print $row['sector'] .        "</td><td nowrap>";
          print $row['industry'] .      "</td></tr>";
      }
      print "</table>";
    }

    public function displayKeywords()
    {

      $fileName = __DIR__."/data/keywords.json";
      $json = json_decode(file_get_contents($fileName));
      print "<pre>";
      print_r($json);
      print "</pre>";
    }
}
 ?>
