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
    $file = env('SCRAPPER_KEYWORDS_REPO') . "BY_";
    if(file_exists(($file.'COMPANY.JSON')))
      $companies = json_decode(file_get_contents($file.'COMPANY.JSON'), true);
    if(file_exists(($file.'KEYWORD.JSON')))
      $keywords = json_decode(file_get_contents($file.'KEYWORD.JSON'), true);

    $by_sec = $this->bySector($exchange);
    $this->parseColumn($exchange, $keywords, $companies, 'industry');
    $this->parseColumn($exchange, $keywords, $companies, 'sector');


    file_put_contents(($file.'SECTOR.JSON'), json_encode($by_sec, JSON_FORCE_OBJECT));
    file_put_contents(($file.'COMPANY.JSON'), json_encode($companies));
    file_put_contents(($file.'KEYWORD.JSON'), json_encode($keywords));

    file_put_contents(($file.'SECTOR_PP.JSON'), json_encode($by_sec, JSON_PRETTY_PRINT));
    file_put_contents(($file.'COMPANY_PP.JSON'), json_encode($companies, JSON_PRETTY_PRINT));
    file_put_contents(($file.'KEYWORD_PP.JSON'), json_encode($keywords, JSON_PRETTY_PRINT));
  }

  private function parseColumn($db, &$keywords, &$companies, $column, $delim = "/[\(,\)\/,\n,\:,\s]+/"){
    // Split the sector column by Delim
    $bad_kw = $this->buildIgnoredKeywords();
    foreach ($db as $cur) {
      if(strcmp($cur[$column], "N/A") !== 0){
        $columnSplit = preg_split($delim, trim($cur[$column]));
        foreach ($columnSplit as $key) // Check each split word
        {
          if(!array_key_exists(strtoupper($key), $bad_kw)){
            // Add to (or create) the keyword and add the company symbol
            $keywords[$key][$cur['symbol']] = $cur['symbol'];
            // Check if the company exists in the list, if it doesn't, create
            if(! array_key_exists(strtoupper($cur['symbol']), $companies))
              $companies[$cur['symbol']] = ['Symbol' => $cur['symbol'],'Value' => 0,
                                          'Keys' => array(), 'Cap' => $cur['cap']];
            // Add the keyword to the company list
            $companies[$cur['symbol']]['Keys'][$key] = 0;
          }
        }
      }
      else{
        $companies[$cur['symbol']] = ['Symbol' => $cur['symbol'],'Value' => 0,
                                    'Keys' => array(), 'Cap' => $cur['cap']];
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
