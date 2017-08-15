<?php
/******************************************************************************/
//  profile.php
/******************************************************************************/
private function companyByWeight($sectors)
{
  $companies = array();
  foreach ($sectors as $sec) {
    $sql = "SELECT * FROM companies WHERE sector = '$sec' ORDER BY cap DESC LIMIT 3 ";
    $results = $this->conn->query($sql);
    $companies[] = $results->fetchColumn(3);
  }

  print_r($companies);
  return $companies;
}

private function disruptiveCompanyByWeight($sectors)
{
  foreach ($sectors as $sec) {
    $sql = "SELECT * FROM companies WHERE sector = '$sec'
            ORDER BY ipodate DESC, cap DESC LIMIT 3 ";
    $results = $this->conn->query($sql);
    return $results->fetchColumn(3);
  }
}

public function displayKeywords($kw, $tableName)
{
  print "<table cellpadding='10' border=solid bordercolor=black>";
  print "<caption><h2>$tableName</h2></caption>";
  print "<tr>
         <td>#</td><td>KEYWORD</td> <td>TOTAL AMOUNT</td> <td>HITS</td>
         <td>% of Spending</td>
         </tr>";

  foreach ($kw as $n => $row) {
      print "<tr> <td nowrap>";
      print $n .    "</td><td nowrap>";
      print $row['Name'] .           "</td><td nowrap>";
      print $row['Value'] .      "</td><td nowrap>";
      print $row['Hits'] .        "</td><td nowrap>";
      print $row['Percent'] .     "</td>";
      print "</td></tr>";
  }
  print "</table>";
}

protected function printCompany($arr, $title)
{
  print "<h3>$title</h3>";
  print "<table cellpadding='10' border=solid bordercolor=black>";
  print"<tr>
        <td>ROW</td>      <td>EXCHANGE</td>
        <td>NAME</td>     <td>IPO</td>
        <td>SYMBOL</td>   <td>PRICE</td>
        <td>CAP</td>      <td>UPDATED</td>
        <td>SECTOR</td>   <td>INDUSTRY</td>
        </tr>";

  foreach ($arr as $k => $row) {
    print "<tr> <td nowrap>";
    print $row['row'] .           "</td><td nowrap>";
    print $row['stockexchange'] . "</td><td nowrap>";
    print $row['name'] .          "</td><td nowrap>";
    print $row['ipodate'] .       "</td><td nowrap>";
    print $row['symbol'] .        "</td><td nowrap>";
    print $row['price'] .         "</td><td nowrap>";
    print $row['cap'] .           "</td><td nowrap>";
    print $row['updated'] .   "</td><td nowrap>";
    print $row['sector'] .        "</td><td nowrap>";
    print $row['industry'] .      "</td></tr>";
  }
  print "</table>";
}

/******************************************************************************/
//  .php
/******************************************************************************/

/******************************************************************************/
//  .php
/******************************************************************************/

/******************************************************************************/
//  .php
/******************************************************************************/

/******************************************************************************/
//  .php
/******************************************************************************/

/******************************************************************************/
//  .php
/******************************************************************************/

/******************************************************************************/
//  .php
/******************************************************************************/

/******************************************************************************/
//  .php
/******************************************************************************/

 ?>
