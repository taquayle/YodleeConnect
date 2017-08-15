<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: exchange-scrapper/src/api/keywords.php
// DESC:
namespace ExScrape\Api;

class KEYWORDS extends APIAbstract{

    /**
    * Calls the parse function and creates a new JSON file with the results
    *
    */
    public function update()
    {
      $fileName = __DIR__ . "/data/keywords.json";
      file_put_contents($fileName, json_encode($this->parse(), JSON_FORCE_OBJECT));
    }

    /**
    *
    *
    */
    public function retrieve()
    {
      $fileName = __DIR__ . "/data/keywords.json";
      return file_get_contents($fileName);
    }

    /**
    *
    *
    */
    public function parse()
    {
      $delim = "/[\/,\n,\:,\s]+/";
      $db = $this->getDatabase();
      $bad_kw = $this->buildIgnoredKeywords();
      $sec = $this->sectors();

      foreach ($db as $row) // Check each row.
      {
        // Split the current cell string by given delmiters. $current is an
        // array
        $current = preg_split($delim, trim($row['industry']));
        foreach ($current as $key) // Check each split word
        {
          if($this->checkedForIgnoredKeywords($bad_kw, trim($key))){
            if($this->checkKeyword($sec, $key, $row['sector'])){ // New Keyword
                $this->addKey($sec, $row['sector'], $key);
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
    public function getDatabase()
    {
      return $this->conn->query("SELECT * FROM companies");
    }

    /**
    *
    *
    */
    public function sectors()
    {
      $db = $this->getDatabase();
      $sectors = array();

      foreach ($db as $row) // Check each row.
        if($this->checkSector($sectors, $row['sector'])) // New Keyword
          $sectors[] = [ 'Sector' => $row['sector'], 'Associated'=> array(), 'Weight' => 0];

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
      foreach ($sectors as $k => $key){
        if(strcmp($key['Sector'], $cmp) === 0){
          return false; // Contained previous/bad keyword
        }
      }
      return true;
    }

    /**
    *
    *
    */
    private function buildIgnoredKeywords()
    {
      $contents = file_get_contents(__DIR__ . '/data/ignore_company_keywords.txt');
      $arr = preg_split('/\s+/', $contents);
      $bad_kw = array();
      foreach($arr as $value){
        $bad_kw[] = $value;
      }
      return $bad_kw;
    }

    /**
    *
    *
    */
    private function checkedForIgnoredKeywords($arr, $word)
    {
      foreach ($arr as $value) {
        if(strcmp($value, $word) === 0)
          return false;
      }
      return true;
    }

    /**
    *
    *
    */
    private function checkKeyword($arr, $cmp, $sector)
    {
      foreach ($arr as $key => $value){
        if(strcmp($value['Sector'], $sector) === 0){
          foreach ($value['Associated'] as $soc) {
            if(strcmp($soc, $cmp) === 0){
              return false; // Contained previous/bad keyword
            }
          }
        }
      }
      return true;
    }

    /**
    *
    *
    */
    private function addKey(&$arr, $sector, $word)
    {
      foreach ($arr as $key => &$value) {
        if(strcmp($value['Sector'], $sector) === 0){
          $value['Associated'][] = $word;
        }
      }
    }

    public function test()
    {
      print "<br>Keywords.php: Hello World</br>";
    }
}
 ?>
