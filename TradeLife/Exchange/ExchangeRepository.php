<?php
namespace Trader\Exchange;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Company;

class ExchangeRepository extends APIController
{

  public function createKeywords(){
    $exchange = Company::select('*')->get();
    $keywords = array();
    $companies = array();

    foreach ($exchange as $currentStock) // Check each row.
    {
      $this->parseColumn($currentStock, $keywords, $companies, 'industry');
      $this->parseColumn($currentStock, $keywords, $companies, 'sector');
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

  private function parseColumn($cur, &$keywords, &$companies, $column, $delim = "/[\/,\n,\:,\s]+/"){
    // Split the sector column by Delim
    $columnSplit = preg_split($delim, trim($cur[$column]));
    foreach ($columnSplit as $key) // Check each split word
    {
      // Add to (or create) the keyword and add the company symbol
      $keywords[$key][] = $cur['symbol'];
      // Check if the company exists in the list, if it doesn't, create
      if(! array_key_exists(strtoupper($cur['symbol']), $companies))
        $companies[$cur['symbol']] = ['Symbol' => $cur['symbol'],'Value' => 0,
                                    'Keys' => array(), 'Cap' => $cur['cap']];
      // Add the keyword to the company list
      $companies[$cur['symbol']]['Keys'][$key] = 0;
    }
  }
}

?>
