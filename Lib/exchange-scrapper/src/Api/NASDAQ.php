<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: .php
// DESC:
namespace ExScrape\Api;

class NASDAQ extends APIAbstract{

    public function update()
    {
      $companies = $this->fetchList();
      foreach ($companies as $company) {
        $this->format($company, "NASDAQ");
      }
    }


    private function fetchList()
    {
      $file = "nasdaq_data.csv";
      $url = 'http://www.nasdaq.com/screening/companies-by-industry.aspx?exchange=NASDAQ&render=download';
      $data = file_get_contents($this->dataFile($file, $url));
      $rows = explode("\n",$data);
      $s = array();
      foreach($rows as $row) {
        $s[] = str_getcsv($row);
      }
        array_pop($s);    // Last row was null, pop off
        array_shift($s);  // First row was headers, shift off
        return $s;
    }

    /**
    * Public call to see if the files is being called correctly.
    */
    public function test()
    {
      print "<br>NASDAQ.php: Hello World</br>";
    }
}
 ?>
