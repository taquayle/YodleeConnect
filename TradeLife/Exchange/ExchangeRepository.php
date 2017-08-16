<?php
namespace Trader\Exchange;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Company;
/*
  THIS IS CURRENTLY SHELVED, RIGHT NOW THIS LOGIC IS DONE BY //ExScrape. BASICALLY
  BY DOING A //ExScarpe/Client->fullUpdate() WILL TAKE CARE OF THE LOGIC. THERE
  IS A COMMAND php aristan companies:update THAT WILL CALL AND UPDATE.
*/
class ExchangeRepository extends APIController
{

  public function temp(){
    $delim = "/[\/,\n,\:,\s]+/";
    $exchange = Company::select('*')->get();
    $keywords = array();
    $companies = array();

    foreach ($exchange as $row) // Check each row.
    {
      $current = preg_split($delim, trim($row['industry']));
      foreach ($current as $key) // Check each split word
      {
        $keywords[$key][] = $row['symbol'];
        if(! array_key_exists(strtoupper($row['symbol']), $companies))
          $companies[$row['symbol']] = ['Value' => 0, 'Keys' => array()];
        $companies[$row['symbol']]['Keys'][] = trim($key);
      }
      
      $current = preg_split($delim, trim($row['sector']));
      foreach ($current as $key) // Check each split word
      {
        $keywords[$key][] = $row['symbol'];
        if(! array_key_exists(strtoupper($row['symbol']), $companies))
          $companies[$row['symbol']] = ['Value' => 0, 'Keys' => array()];
        $companies[$row['symbol']]['Keys'][] = trim($key);
      }
    }
    $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
    file_put_contents($fileName, json_encode($companies));
    $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_KEYWORD.json";
    file_put_contents($fileName, json_encode($keywords));
    $temp = json_decode(file_get_contents($fileName));

    $fileName = env('COMPANY_KEYWORDS_REPO') . "PP_BY_COMPANY.json";
    file_put_contents($fileName, json_encode($companies, JSON_PRETTY_PRINT));
    $fileName = env('COMPANY_KEYWORDS_REPO') . "PP_BY_KEYWORD.json";
    file_put_contents($fileName, json_encode($keywords, JSON_PRETTY_PRINT));

    return [$temp, $keywords];
  }
}

?>
