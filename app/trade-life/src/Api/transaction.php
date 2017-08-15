<?php
//  AUTHOR: Tyler Quayle
//  DATE: 7/7/2017
//  FILE: trade-life/src/api/transaction.php
//  DESC: file to handle insertion of transactions into trade-life DB

namespace TradeLife\Api;
use PDO;
class Transaction extends APIAbstract
{

  /**
  * Given an array of JSON strings, insert them one-by-one
  *
  *   @param Array of JSON strings
  */
  public function insert($arr)
  {
    foreach ($arr as $trans) {
      $this->insertSingle($trans);
    }
  }

  /**
  *   Attempts to insert a single JSON array into the database
  *
  *   @param JSON string
  *   @return string, on fail;
  */
  public function insertSingle($arr)
  {
    $action = json_decode(json_encode($arr));
    $user = $this->session->getUser();

    if($this->check($user, $action->{'id'}))
    {
      $stmt = $this->conn->prepare("INSERT INTO
        transactions (username, container, trans_id, amount, base_type,
                      cat_type, cat_id, category, simple_desc, original_desc,
                      type, sub_type, trans_date)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bindParam(1, $user);
      $stmt->bindParam(2, $action->{'CONTAINER'});
      $stmt->bindParam(3, $action->{'id'});
      $stmt->bindParam(4, $action->{'amount'}->{'amount'});
      $stmt->bindParam(5, $action->{'baseType'});
      $stmt->bindParam(6, $action->{'categoryType'});
      $stmt->bindParam(7, $action->{'categoryId'});
      $cate =  strtoupper($action->{'category'});
      $stmt->bindParam(8, $cate);

      // LOAN, INVESTMENT, INSURANCE have different JSON strings then normal
      //  transactions. Must handle them.
      if($action->{'CONTAINER'} == 'loan' ||
          $action->{'CONTAINER'} == 'investment' ||
          $action->{'CONTAINER'} == 'insurance')
      {
        $cate =  strtoupper($action->{'category'});
        $stmt->bindParam(9, $cate);
        $stmt->bindParam(10, $cate);
        $stmt->bindParam(11, $action->{'category'});
        $stmt->bindParam(12, $action->{'category'});
      }

      else
      {
        $simp = strtoupper($action->{'description'}->{'simple'});
        $simp = str_replace("'", "", $simp);
        $stmt->bindParam(9, $simp);
        $orig = strtoupper($action->{'description'}->{'original'});
        $orig = str_replace("'", "", $orig);
        $stmt->bindParam(10, $orig);
        $stmt->bindParam(11, $action->{'type'});
        $stmt->bindParam(12, $action->{'subType'});
      }
      $stmt->bindParam(13, $action->{'date'});

      try{
          $stmt->execute();
      }
      catch(PDOException $e)
      {
          echo "ERROR INSERT to database";
      }
    }
  }


  /**
  *   Check to see if the current transaction is in the database already using
  *   username and transaction ID
  *   @param string
  *   @param int
  *   @return boolean
  */
  private function check($username, $transid)
  {
      $stmt = $this->conn->prepare("SELECT username FROM transactions WHERE
                              (username = :username AND trans_id = :transid)");
      $stmt->bindParam(':username', $username);
      $stmt->bindParam(':transid', $transid);
      $stmt->execute();

      return !($stmt->rowCount() > 0);
  }
}
 ?>
