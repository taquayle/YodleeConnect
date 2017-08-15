<?php

  namespace TradeLife\Api;
  use PDO;

  class UserStocks extends APIAbstract
  {

    public function generate()
    {
      if($this->loggedIn())
      {
        $fileName =   $fileName = env('USER_PROFILE_REPO') . $this->session->getUser() . ".json";
        if(! file_exists($fileName) )
          echo "No user profile created. TradeLife/Api/Profile->create() first";
        else
        {
          $profile = json_decode(file_get_contents($fileName));
          $weight_used = 'Hits';
          $target_sectors =
          $profile->Target_Sectors = ["By" => $weight_used, "Sectors" => $this->weightedChoice(get_object_vars($profile->Desc_Keywords), $weight_used)] ;

          $profile->Target_Companies = NULL; //Clear before use
          $profile->Target_Companies['Default'] = $this->companyByWeight($profile->Target_Sectors['Sectors']);
          $profile->Target_Companies['Cap'] = $this->companyByWeight($profile->Target_Sectors['Sectors']);
          $profile->Target_Companies['Disruptive'] = $this->disruptiveCompanyByWeight($profile->Target_Sectors['Sectors']);
          file_put_contents($fileName, json_encode($profile, JSON_FORCE_OBJECT));
        }
      }
    }
    /**
    * Given the generated userKeyWordArray, check it against the exchange keys.
    * If there is a match, give the sector containing the keyword more weight.
    * The $weight is a string so this function may be reused for both 'Value'
    * and 'Hits'
    *
    * @param Array
    * @param String
    * @return Array of Strings
    */
    private function weightedChoice($userKeyWordArray, $weight)
    {
      $temp = new \ExScrape\Client();
      // Decode the JSON file containing the generated keywords
      $secArr = json_decode($temp->keywords()->retrieve());

      // Check each keyword in the user keyword array $userKeyWordArray
      foreach ($userKeyWordArray as $k => $userKeyword) {
        // Iterate through each Sector from company keywords. Each obj has
        //  [Sector] containgin sectorr name and [Associated] array that
        //  contains all of the keywords found with that
        foreach ($secArr as $key => &$sector) {
          // Iterate through the [Associated] array. containing the keywords
          foreach ($sector->Associated as $key => $sectorKeyword) {
            // If the user keyword is in the sector keyword, increase the
            //  [Weight] of the [Sector] associated with the [Associated]
            //  array
            if(stristr($sectorKeyword, $userKeyword->Name)){
              $sector->Weight += $userKeyword->$weight;
            }
          }
        }
      }
      // Convert obj-of-obj into array-of-obj so we can sort.
      $result = get_object_vars($secArr);
      usort($result, array($this, 'cmpWeight'));

      return [$result[0]->Sector, $result[1]->Sector, $result[2]->Sector];
    }

    /**
    *
    *
    */
    private function directCompanyHits($arr)
    {
      $companies = array();
      foreach ($arr as $k =>$candidate) {
        $test = str_replace("'", "", $candidate['Name']);
        $sql = "SELECT * FROM companies WHERE name LIKE '%$test%'";
        if ( ! $results = $this->conn->query($sql) )
          die(var_export($this->conn->errorinfo(), TRUE));
        else {
          if($results->rowCount() == 1){
              foreach ($results as $value) {
                $companies[] = $value['symbol'];
              }
            }
        }
      }
      return $companies;
    }


    private function companyByWeight($sectors)
    {
      $companies = array();
      foreach ($sectors as $sec) {
        $sql = "SELECT * FROM companies WHERE sector = '$sec'
                ORDER BY cap DESC LIMIT 3 ";
        $results = $this->conn->query($sql);
        $companies[$sec] = $results->fetchAll(PDO::FETCH_COLUMN, 3);
      }
      return $companies;
    }

    private function disruptiveCompanyByWeight($sectors)
    {
      $companies = array();
      foreach ($sectors as $sec) {
        $sql = "SELECT * FROM companies WHERE sector = '$sec'
                ORDER BY ipodate DESC, cap DESC LIMIT 3 ";
        $results = $this->conn->query($sql);
        $companies[$sec] = $results->fetchAll(PDO::FETCH_COLUMN, 3);
      }
      return $companies;
    }


    /**
    * Used in conjunction with usort to sort weighted-sectors in descending
    * order by weight.
    *   @return int
    */
    private function cmpWeight($a, $b)
    {
        if ($a->Weight == $b->Weight) {
            return 0;
        }
        return ($a->Weight > $b->Weight) ? -1 : 1;
    }

  }

?>
