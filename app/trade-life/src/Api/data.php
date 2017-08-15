<?php
//  AUTHOR: Tyler Quayle
//  DATE: 7/7/2017
//  FILE: trade-life/src/api/transaction.php
//  DESC: file to handle insertion of transactions into trade-life DB

namespace TradeLife\Api;
use PDO;
class Data extends APIAbstract
{


  public function userprofiles()
  {
    $num = $this->getCount("userprofile");
    if($num === 0)
      print "<h1>Userprofile Database Empty!</h1>";
    else
    {
      print "# of Users: $num";
      print "<table cellpadding='10' border=solid bordercolor=black>";
      print"<tr>
            <td>#</td> <td>USERNAME</td>
            <td>UPDATED</td> <td>Keyword 1</td>
            <td>Keyword 2</td> <td>Keyword 3</td>
            <td><b>Sector 1</b></td> <td>Company 1</td>
            <td>Company 2</td> <td>Company 3</td>
            <td><b>Sector 2</b></td> <td>Company 1</td>
            <td>Company 2</td> <td>Company 3</td>
            <td><b>Sector 3</b></td> <td>Company 1</td>
            <td>Company 2</td> <td>Company 3</td>
            <td>Algorithm</td>
            </tr>";
      $sql = 'SELECT * FROM userprofile';


      foreach ($this->conn->query($sql) as $row) {
        print "<tr> <td>";
        print $row['id'] .           "</td><td>";
        print $row['username'] .      "</td><td>";
        print $row['updated'] .       "</td><td>";
        print $row['key1'] .          "</td><td>";
        print $row['key2'] .          "</td><td>";
        print $row['key3'] .          "</td><td>";
        print $row['sector1'] .       "</td><td>";
        print $row['sec1_comp1'] .       "</td><td>";
        print $row['sec1_comp2'] .       "</td><td>";
        print $row['sec1_comp3'] .       "</td><td>";
        print $row['sector2'] .       "</td><td>";
        print $row['sec2_comp1'] .       "</td><td>";
        print $row['sec2_comp2'] .       "</td><td>";
        print $row['sec2_comp3'] .       "</td><td>";
        print $row['sector3'] .       "</td><td>";
        print $row['sec3_comp1'] .       "</td><td>";
        print $row['sec3_comp2'] .       "</td><td>";
        print $row['sec3_comp3'] .       "</td><td>";
        print $row['algo'] .       "</td></tr>";

      }
      print "</table>";
    }
  }

  /**
  *   Displays the whole database in html, for easier viewing
  */
  public function users()
  {
    $num = $this->getCount("users");
    if($num === 0)
      print "<h1>Users Database Empty</h1>";
    else
    {
      print "# of Users: $num";
      print "<table cellpadding='10' border=solid bordercolor=black>";
      print"<tr>
            <td>ID</td> <td>USERNAME</td>
            <td>PASSWORD</td> <td>EMAIL</td>
            <td>CREATED</td> <td>LAST LOGIN</td>
            </tr>";
      $sql = 'SELECT * FROM users';


      foreach ($this->conn->query($sql) as $row) {
        print "<tr> <td>";
        print $row['id'] .          "</td><td>";
        print $row['name'] .    "</td><td>";
        print $row['password'] .    "</td><td>";
        print $row['email'] .       "</td><td>";
        print $row['created_at'] .  "</td><td>";
        print $row['updated_at'] .  "</td></tr>";
      }
      print "</table>";
    }
  }

  /**
  *   Displays the whole database in html, for easier viewing
  */
  public function transactions()
  {
    $num = $this->getCount("transactions");
    if($num === 0)
      print "<h1>Transactions Database Empty!</h1>";
    else
    {
      print "# of Transactions: $num";
      print "<table cellpadding='10' border=solid bordercolor=black>";
      print"<tr>
            <td>ROW</td> <td>USER</td> <td>TIMESTAMP</td> <td>CONTAINER</td> <td>TRANSACTION_ID</td>
            <td>AMOUNT</td> <td>BASE_TYPE</td> <td>CATEGORY_TYPE</td> <td>CATEGORY_ID</td>
            <td>CATEGORY</td> <td>SIMPLE_DESC</td> <td>ORIGINAL_DESC</td> <td>TYPE</td>
            <td>SUB_TYPE</td> <td>TRANS_DATE</td>
            </tr>";
      $sql = 'SELECT *  FROM transactions';


      foreach ($this->conn->query($sql) as $row) {
          print "<tr> <td nowrap>";
          print $row['id'] .           "</td><td nowrap>";
          print $row['name'] .      "</td><td nowrap>";
          print $row['created_at'] .   "</td><td nowrap>";
          print $row['container'] .     "</td><td nowrap>";
          print $row['trans_id'] .      "</td><td nowrap>";
          print $row['amount'] .        "</td><td nowrap>";
          print $row['base_type'] .     "</td><td nowrap>";
          print $row['cat_type'] .      "</td><td nowrap>";
          print $row['cat_id'] .        "</td><td nowrap>";
          print $row['category'] .      "</td><td nowrap>";
          print $row['simple_desc'] .   "</td><td nowrap>";
          print $row['original_desc'] . "</td><td nowrap>";
          print $row['type'] .          "</td><td nowrap>";
          print $row['sub_type'] .      "</td><td nowrap>";
          print $row['trans_date'] .    "</td></tr>";
      }
      print "</table>";
    }
  }
}
 ?>
