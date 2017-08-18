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

      /*CONTINUE FROM HERE*/
      $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
      $byCompany = json_decode(file_get_contents($fileName));
      $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_KEYWORD.json";
      $byKeyword = json_decode(file_get_contents($fileName));

      if($profile->Desc_Keywords != null){
        $targetSectors = $this->weightedChoice($secArr, get_object_vars($profile->Desc_Keywords), $weight_used);}
      if($profile->User_Keywords != null){
        $targetSectors = $this->weightedChoice($secArr, get_object_vars($profile->User_Keywords), $weight_used);}

      $profile->Target_Sectors = ["By" => $weight_used, "Sectors" => $targetSectors] ;

      $profile->Target_Companies = NULL; //Clear before use
      $profile->Target_Companies['Disruptive'] = $this->disruptiveCompanyByWeight($profile->Target_Sectors['Sectors']);
      $profile->Target_Companies['Cap'] = $this->companyByWeight($profile->Target_Sectors['Sectors']);

      $profile->Tailored_Companies = $this->tailoredCompanies($this->user, $profile);
      $fileName = env('USER_PROFILE_REPO') . $user . ".json";
      file_put_contents($fileName, json_encode($profile, JSON_FORCE_OBJECT));

      return true;

    }

    /**
    *  THIS IS FOR THE NEW VERSION OF THE MATCHING ALGO. NOT USED AT THIS TIME
    *
    *
    */
    public function tailoredCompanies($user, $profile){
      $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
      $byCompanies = json_decode(file_get_contents($fileName), true);

      $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_KEYWORD.json";
      $byKeywords = json_decode(file_get_contents($fileName), true);

      if($profile->User_Keywords != NULL){
        $userKeywords = get_object_vars($profile->User_Keywords);
        $this->tailoredLoop($byKeywords, $byCompanies, $userKeywords);
      }
      if($profile->Desc_Keywords != NULL){
        $userKeywords = get_object_vars($profile->Desc_Keywords);
        $this->tailoredLoop($byKeywords, $byCompanies, $userKeywords);
      }
      if($profile->Cate_Keywords != NULL){
        $userKeywords = get_object_vars($profile->Cate_Keywords);
        $this->tailoredLoop($byKeywords, $byCompanies, $userKeywords);
      }

      $topCompanies = array();
      foreach ($byCompanies as $c => $company) {
        if($company['Value'] > 0){
          $topCompanies[] = $company;}
      }
      usort($topCompanies, array($this, 'cmpWeightVersion2'));

      $returnValues = array();
      for($i = 0; $i < env('NUMBER_OF_TAILORED_STOCKS'); $i++){
        $temp = Company::select('*')->where('symbol', '=', $topCompanies[$i]['Symbol'])->get()->first()->toArray();
        $temp['keys'] = $topCompanies[$i]['Keys'];
        $returnValues[] = $temp;
      }
      return $returnValues;
    }

    private function tailoredLoop($byKeyword, &$byCompany, $keywordsToCheck){
      foreach ($keywordsToCheck as $k => $key){
        if(array_key_exists($k, $byKeyword)){
          foreach ($byKeyword[$k] as $relatedCompanies){
            if(array_key_exists($relatedCompanies, $byCompany)){
                $byCompany[$relatedCompanies]['Value'] += $key->Value;
                $byCompany[$relatedCompanies]['Keys'][$k] += $key->Value;
            }
          }
        }
      }
    }

    /**
    * Used in conjunction with usort to sort weighted-sectors in descending
    * order by weight.
    *   @return int
    */
    private function cmpWeightVersion2($a, $b)
    {
        if ($a['Value'] == $b['Value']) {
            return ($a['Cap'] > $b['Cap']) ? -1 : 1;
        }
        return ($a['Value'] > $b['Value']) ? -1 : 1;
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
