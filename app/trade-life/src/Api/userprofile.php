<?php

  namespace TradeLife\Api;
  use PDO;


  class UserProfile extends APIAbstract
  {

    public function create()
    {
      if($this->loggedIn())
      {
        $profile = array();
        $profile = ["UserName" => NULL, "Cate_Keywords" => NULL ,
                    "Desc_Keywords" => NULL, "Target_Sectors" => NULL,
                    "Target_Companies" => NULL];
        $profile['UserName'] = $this->session->getUser();
        $profile['Cate_Keywords'] = $this->buildCategory();
        $profile['Desc_Keywords'] = $this->buildDescription();
        
        $fileName = env('USER_PROFILE_REPO') . $profile['UserName'] . ".json";

        file_put_contents($fileName, json_encode($profile, JSON_FORCE_OBJECT));
      }
    }

    public function displayKeywords($kw, $tableName)
    {
      print "<table cellpadding='10' border=solid bordercolor=black>";
      print "<caption><h2>$tableName</h2></caption>";
      print "<tr>
             <td>#</td><td>KEYWORD</td> <td>TOTAL SPENT</td> <td>HITS</td>
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


    /**
    * Used in conjunction with usort to sort list in descending order by money
    * spent; If tied, more hits go first
    *   @return int
    */
    private static function cmpVal($a, $b)
    {
        if ($a['Value'] == $b['Value']) {
            return ($a['Hits'] > $b['Hits']) ? -1 : 1;
        }
        return ($a['Value'] > $b['Value']) ? -1 : 1;
    }

    /**
    * Used in conjunction with usort to sort list in descending order by the
    * amount of hits; If tied, higher money spent goes first
    *   @return int
    */
    private static function cmpHit($a, $b)
    {
        if ($a['Hits'] == $b['Hits']) {
            return ($a['Value'] > $b['Value']) ? -1 : 1;
        }
        return ($a['Hits'] > $b['Hits']) ? -1 : 1;
    }

    /**
    * Calculate the percent of total spending that each keyword uses. This is
    * not wholly accurate as 3 columns of keywords with no duplicates makes this
    * somewhat useless
    *
    * @param Multidimensional Array, pass by reference
    */
    private function percents(&$arr)
    {
      $total = $this->totalSpending();
      foreach ($arr as $key => &$row) {
        $row['Percent'] = round((($row['Value'] / $total) * 100), 2);
      }
    }

    /**
    * Run through transaction history and get a running total of expenses
    *
    * @return double
    */
    private function totalSpending()
    {
      $trans = $this->getTransactions(); // Requery/reset to top of list
      $runningTotal = 0.0;
      foreach ($trans as $row) {
        $runningTotal += $row['amount'];
      }
      return $runningTotal;
    }

    /**
    * Parses the given user transaction table in order to get keywords from the
    * given data.
    *
    * @param Multidimensional Array, pass by Reference
    * @param Multidimensional Array
    * @param String
    * @param String, Optional
    * @return Multidimensional Array
    */
    private function parse(&$kw, $bad_kw, $column, $delimiter = "/[\/,\n]+/")
    {

       // Requery/reset to top of list
        $trans = $this->getTransactions();
        foreach ($trans as $row) // Check each row.
        {
          // Split the current cell string by given delmiters. $current is an
          // array
          $current = preg_split($delimiter, trim($row[$column]));
          foreach ($current as $key) // Check each split word
          {
            // Check if current candidate keyword matches the list of blocked
            // keyWords
            if($this->checkKeyword($bad_kw, $key))
            {
              if($this->checkKeyword($kw, $key)){ // New Keyword
                $kw[] = [ 'Name' => $key, 'Value'=> doubleval($row['amount']),
                          'Hits' => 1, 'Percent' => 0.0];}
              else { // Keyword already in list. update values
                $this->addValue($kw, $key, $row['amount']);}
            }
          }
        }
        return $kw;
    }

    /**
    * If update the values of an already entered keyword
    *
    * @param Multidimensional arrays
    * @param string
    * @param int
    */
    private function addValue(&$arr, $name, $val)
    {
      foreach ($arr as $k => &$key) {
        if(strcmp($key['Name'], $name) == 0){
          $key['Value'] += round(($val),2);
          $key['Hits'] += 1;
        }
      }
    }

    /**
    * Checks current canadidate keyword against the given array, first Checks
    * for equal strings, if no equal string is found, explodes the value in
    * array and recompares, this is done to catch candidates that contain
    * certain blocked arrays.
    *
    * @param Multidimensional Array
    * @param String
    * @return Boolean
    */
    private function checkKeyword($arr, $word)
    {
      foreach ($arr as $k => $key){
        if(strcmp($key['Name'], $word) === 0){
          return false; // Contained previous/bad keyword
        }
        else{
          $temp = explode(" ", $key['Name']);
          foreach($temp as $candidate){
            if(!empty($candidate) && stristr($word, $candidate)){
              return false; // Contained bad/previous keyword
            }
          }
        }
      }
      return true; // Clean
    }

    /**
    * Build an array of ignored keywords that will not be allowed into the user
    * keyword space. Returning a Multidimensional array that allows it to be
    * used in other functions.
    *
    * @return Multidimensional Array of Strings
    */
    private function buildIgnoreKeywords()
    {
      $contents = file_get_contents(__DIR__ . '/ignore_user_keywords.txt');
      $arr = preg_split('/\s+/', $contents);
      $bad_kw = array();
      foreach($arr as $value){
        $bad_kw[] = ['Name' => $value, 'Value'=> 0];}
      return $bad_kw;
    }

    /**
    * Get all the transactions associated with a specific user.
    *
    * @return Multidimensional Array of Strings
    */
    private function getTransactions()
    {
      $user = $this->session->getUser();
      $sql = "SELECT * FROM transactions WHERE
        (username = '$user' AND cat_type = 'EXPENSE')";
      return $this->conn->query($sql);
    }

    /**
    * Parses all 3 columns (category, original desc, simple desc) and returns an
    * array containing all keywords.
    *
    * @return Multidimensional array of Strings
    */
    public function buildKeywords()
    {
      $categoryKW = $this->buildCategory();
      $descriptionKW = $this->buildDescription();
      $combinedKW = array_unique(array_merge_recursive($categoryKW, $descriptionKW), SORT_REGULAR);
      usort($combinedKW, array($this, 'cmpVal'));
      return $combinedKW;
    }

    /**
    * Parses only the category column of the transaction database to build
    * keywords.
    *
    * @return Multidimensional array of Strings
    */
    public function buildCategory()
    {
      $bad_kw = $this->buildIgnoreKeywords();
      $kw_cat = array();

      $this->parse($kw_cat, $bad_kw, 'category');
      usort($kw_cat, array($this, 'cmpVal'));
      $this->percents($kw_cat);
      return $kw_cat;
    }

    /**
    * Parses both of the description columns in the transaction database to
    * build the keyword array.
    *
    * @return Multidimensional array of Strings
    */
    public function buildDescription()
    {
      $bad_kw = $this->buildIgnoreKeywords();
      $kw_simp = array();
      $kw_orig = array();

      $this->parse($kw_simp, $bad_kw, 'simple_desc');
      $this->parse($kw_orig, $bad_kw, 'original_desc', "/[\/,\n,\s]+/");

      $kw_desc = array_unique(array_merge($kw_orig, $kw_simp), SORT_REGULAR);
      usort($kw_desc, array($this, 'cmpVal'));
      $this->percents($kw_desc);

      return $kw_desc;
    }

    /**
    * Used to test if file was accessible.
    */
    public function test()
    {
      echo "PROFILE.php: Hello";
    }
  }
?>
