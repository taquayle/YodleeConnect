<?php

  namespace Trader\Stocks;
  use PDO;
  use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
  use App\Http\Controllers\APIController;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Hash;
  use Illuminate\Support\Facades\Auth;

  use App\Company;

  class UserStocks extends APIController
  {
    private $user;
    public function generate($user)
    {
      // Checks if amazon exists, this has 2 uses, will check if db exsists and
      //  contains valid data. If you update company DB while NASDAQ servers are
      //  down, weird data gets brought in (it downloads the 404 page).
      if(!(Company::where('symbol', '=', 'AMZN')->exists()))
      {
        return false;
      }

      $this->user = $user;
      $fileName = env('USER_PROFILE_REPO') . $user . ".json";

      $profile = json_decode(file_get_contents($fileName));
      $weight_used = 'Value';

      $temp = new \ExScrape\Client();
      // Decode the JSON file containing the generated keywords
      $secArr = json_decode($temp->keywords()->retrieve());

      if($profile->User_Keywords == null && $profile->Desc_Keywords == null)
        return false;
      $targetSectors = null;
      if($profile->Desc_Keywords != null)
        $targetSectors = $this->weightedChoice($secArr, get_object_vars($profile->Desc_Keywords), $weight_used);
      if($profile->User_Keywords != null)
        $targetSectors = $this->weightedChoice($secArr, get_object_vars($profile->User_Keywords), $weight_used);

      $profile->Target_Sectors = ["By" => $weight_used, "Sectors" => $targetSectors] ;

      $profile->Target_Companies = NULL; //Clear before use
      //$profile->Target_Companies['Default'] = $this->companyByWeight($profile->Target_Sectors['Sectors']);
      $profile->Target_Companies['Disruptive'] = $this->disruptiveCompanyByWeight($profile->Target_Sectors['Sectors']);
      $profile->Target_Companies['Cap'] = $this->companyByWeight($profile->Target_Sectors['Sectors']);

      file_put_contents($fileName, json_encode($profile, JSON_FORCE_OBJECT));

      return true;

    }
    /**
    * Given the generated userKeyWordArray, check it against the exchange keys.
    * If there is a match, give the sector containing the keyword more weight.
    * The $weight is a string so this function may be reused for both 'Value'
    * and 'Hits'
    *
    * @param Array
    * @param Array
    * @param String
    * @return Array of Strings
    */
    private function weightedChoice(&$secArr, $userKeyWordArray, $weight)
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
            // Since php arrays have a key-value pairing. check if word exists
            // if (!array_key_exists(strtoupper($sectorKeyword),$profile['User_Keywords'])) {
            //   $profile['User_Keywords'][strtoupper($word)] =
            //           [ 'Name' => strtoupper($word), 'Value'=> $maxValue,
            //             'Hits' => $maxHits, 'Percent' => 0.0];
            // }
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
        $comp= Company::where('sector', '=', $sec)
          ->orderBy('cap', 'desc')
          ->take(env('NUMBER_OF_STOCKS_TO_USE'))
          ->get()
          ->toArray();
        $companies[$sec] = $comp;
      }
      return $companies;
    }

    private function disruptiveCompanyByWeight($sectors)
    {
      $companies = array();
      foreach ($sectors as $sec) {
        $comp= Company::where('sector', '=', $sec)
          ->orderBy('ipodate', 'desc')
          ->orderBy('cap', 'desc')
          ->take(env('NUMBER_OF_STOCKS_TO_USE'))
          ->get()
          ->toArray();
        $companies[$sec] = $comp;
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
