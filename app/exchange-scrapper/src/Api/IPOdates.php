<?php
// AUTH: Tyler Quayle
// DATE: 7/13/2017
// FILE: exchange-scrapper/src/api/IPOdates.php
// DESC: Updates the basic IPO dates given from NASDAQ.com with specific dates
//        from ipomonitor.com
namespace ExScrape\Api;
use PHPExcel_IOFactory;
//require_once "Classes/PHPExcel/IOFactory.php";
class IPO extends APIAbstract{


  /**
  * Public call to s
  */
  public function test(){
    echo "IPOdates.php: Hello <br>";
  }

  /**
  * Updates the current company database to reflect the ipo dates obtained from
  * ipomonitor.com
  */
  public function update()
  {
    $ipodates = $this->fetchList();
    foreach ($ipodates as $key => $ipo) {
      $this->updateIPO($ipo[0], $ipo[1]);
    }
  }


  /**
  * Get a XLS file from ipomonitor.com to get more accurate IPO dates
  *
  *
  * @param int, optional
  * @return 2d Array
  */
  private function fetchList($end = 7500)
  {
    $end +=5; //Offset for the start of XLS
    $file = 'ipo_data.xls';
    $url = "http://www.ipomonitor.com/pages/ipo-filings.html?start=0&max=$end&export=excel";

    $fileName = $this->dataFile($file, $url);
    $excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);
    //if we dont need any formatting on the data
    $excelReader->setReadDataOnly();
    //the default behavior is to load all sheets
    $excelReader->setLoadAllSheets();

    $excelObj = $excelReader->load($fileName);
    $excelLoaded = $excelObj->getActiveSheet()->rangeToArray("E5:G$end");
    $results = array();

    foreach ($excelLoaded as $key => $row) {
      if($row[0] != NULL)
      {
        $results[] = [$row[0], $row[2]];
      }
    }

    return $results;
  }
}

?>
