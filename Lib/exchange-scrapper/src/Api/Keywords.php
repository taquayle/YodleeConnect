<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: exchange-scrapper/src/api/keywords.php
// DESC:
namespace ExScrape\Api;
use App\Company;
class KEYWORDS extends APIAbstract{

  /**
  * Calls the parse function and creates a new JSON file with the results
  *
  */
  public function update()
  {
    $exchange = Company::select('*')->get();
    $keywords = array();
    $companies = array();

    $by_sec = $this->bySector($exchange);
    $this->parseColumn($exchange, $keywords, $companies, 'industry');
    $this->parseColumn($exchange, $keywords, $companies, 'sector');

    $fileName = env('SCRAPPER_KEYWORDS_REPO') . "BY_SECTOR.json";
    file_put_contents($fileName, json_encode($by_sec, JSON_FORCE_OBJECT));

    $fileName = env('SCRAPPER_KEYWORDS_REPO') . "PP_BY_SECTOR.json";
    file_put_contents($fileName, json_encode($by_sec, JSON_PRETTY_PRINT));

    $fileName = env('SCRAPPER_KEYWORDS_REPO') . "BY_COMPANY.json";
    file_put_contents($fileName, json_encode($companies));
    $fileName = env('SCRAPPER_KEYWORDS_REPO') . "BY_KEYWORD.json";
    file_put_contents($fileName, json_encode($keywords));


    $fileName = env('SCRAPPER_KEYWORDS_REPO') . "PP_BY_COMPANY.json";
    file_put_contents($fileName, json_encode($companies, JSON_PRETTY_PRINT));
    $fileName = env('SCRAPPER_KEYWORDS_REPO') . "PP_BY_KEYWORD.json";
    file_put_contents($fileName, json_encode($keywords, JSON_PRETTY_PRINT));
  }

  private function parseColumn($db, &$keywords, &$companies, $column, $delim = "/[\/,\n,\:,\s]+/"){
    // Split the sector column by Delim
    foreach ($db as $cur) {
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
  /**
  *
  *
  */
  public function bySector($db)
  {
    $delim = "/[\/,\n,\:,\s]+/";
    $bad_kw = $this->buildIgnoredKeywords();
    $sec = $this->sectors();

    foreach ($db as $row) // Check each row.
    {
      // Split the current cell string by given delmiters. $current is an
      // array
      if(strcmp($row['sector'], "N/A") !== 0)
      {
        $current = preg_split($delim, trim($row['industry']));
        foreach ($current as $key) // Check each split word
        {
          if(!array_key_exists(strtoupper($key), $bad_kw)){
            if(!array_key_exists(strtoupper($key), $sec[$row['sector']]['Associated'])){
                $sec[$row['sector']]['Associated'][$key] = $key;

            }
          }
        }
      }
    }
    return $sec;
  }

  /**
  *
  *
  */
  public function sectors()
  {
    $db = Company::select('*')->get();
    $sectors = array();

    foreach ($db as $row) // Check each row.
      if($this->checkSector($sectors, $row['sector'])) // New Keyword
        $sectors[$row['sector']] = [ 'Sector' => $row['sector'], 'Associated'=> array(), 'Weight' => 0];

    return $sectors;
  }

  /**
  *
  *
  */
  private function checkSector($sectors, $cmp)
  {
    if(strcmp($cmp, "N/A") === 0)
      return false;
    if(array_key_exists(strtoupper($cmp), $sectors))
      return false;

    return true;
  }

  /**
  *
  *
  */
  private function buildIgnoredKeywords()
  {
    $contents = file_get_contents(env('SCRAPPER_KEYWORDS_REPO') . 'ignore_company_keywords.txt');
    $arr = preg_split('/\s+/', $contents);
    $bad_kw = array();
    foreach($arr as $value){
      $bad_kw[$value] = $value;
    }
    return $bad_kw;
  }

}
 ?>
