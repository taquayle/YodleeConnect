<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: NYSE.php
// DESC:
namespace ExScrape\Api;

class NYSE extends APIAbstract{

    public function update()
    {
      $companies = $this->fetchList();
      foreach ($companies as $company) {
        $this->format($company, "NYSE");
      }
    }

    private function fetchList()
    {
      $file = "nyse_data.csv";
      $url = 'http://www.nasdaq.com/screening/companies-by-industry.aspx?exchange=NYSE&render=download';
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
      print "<br>NYSE.php: Hello World</br>";
    }

}
 ?>
