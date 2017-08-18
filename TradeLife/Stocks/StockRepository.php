<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Stocks/StocksRepository
// DESC: Attempt to download stock data from quandl, to use these functions you
//    will need to have successfully created a profile and find stocks to show
//    if the stock data can't be found, set stock_data to null so TradeLifeApp
//    will know to display 'n/a'

namespace Trader\Stocks;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PDOException;

class StockRepository extends APIController
{
  public function __construct()
  {
    return response()->json("Stock Repo");
  }

  /**
  * Update the user profile to have the current stock data for the last X days
  *
  * @param $input - JSON from TradeLife app
  * @return updated profile
  */
  public function gatherData($input)
  {
    /* Check if the user profile exists */
    $fileName = env('USER_PROFILE_REPO') . $input['userName'] . ".json";
    if(!file_exists($fileName))
      return response()->json(['error' => true,
        'message' => "No such profile found"], 200);

    // Get the user profile, and decode it into php array
    $profile = json_decode(file_get_contents($fileName), true);

    // Loop through the companies gathered in UserStocks.php
    foreach ($profile['Target_Companies'] as $order => &$sector) {
      foreach($sector as $sectorName => &$topCompanies){
        foreach($topCompanies as $index => &$company){
          // Get the data for the last X days from Quandl.com
          $company['stock_data'] = $this->getData($company['symbol']);
        }
      }
    }
    foreach ($profile['Tailored_Companies'] as &$company) {
      $company['stock_data'] = $this->getData($company['symbol']);
    }

    //
    $profile['Invest_Date'] = date('F jS, Y', strtotime('-'.env('DEFAULT_AMOUNT_OF_DAYS').' days'));
    file_put_contents($fileName, json_encode($profile, JSON_FORCE_OBJECT));
    $fileNamePP = env('USER_PROFILE_REPO') . 'PP_'.$input['userName'] . ".json";
    file_put_contents($fileNamePP, json_encode($profile, JSON_PRETTY_PRINT));
    return response()->json(['error' => false,
        'messages' => "Successfully gathered stock data",
        'profile' => json_decode(file_get_contents($fileName))], 200);
  }

  /**
  * Get the data from file, first call downloadData(), which checks if a local
  * copy of the
  *
  * @param $symbol of the stock to get data from
  * @return array of closing prices of $stock
  */
  private function getData($symbol){
    $file = $symbol . ".csv";


    $checkForData = $this->downloadData($file, $symbol);
    if($checkForData === FALSE)
      return null;

    // $rows = explode("\n",$data);
    $results = array();

    ini_set('auto_detect_line_endings',TRUE);
    $handle = fopen($checkForData,'r');
    $i = env('DEFAULT_AMOUNT_OF_DAYS');
    while ( ($data = fgetcsv($handle) ) !== FALSE ) {
      $results[] = $data[4];
      //$results[$data[4]] = ["Date" => $data[0], "Close" => $data[4]];
    }
    ini_set('auto_detect_line_endings',FALSE);
    array_shift($results);  // First row was headers, shift off
    $results = array_reverse($results);
    return $results;
  }

  /**
  * Checks if the Local data exsists and is up to date (less than 24hrs old).
  * If the file does not exist, download from quandl, if it's old, re-download.
  * Else the file is good and up to date. Return file location
  *
  * @param $file - Name  of file to be saved. $symbol.csv
  * @param $symbol - Symbol of stock to downloadData
  * @return $filepath - location of file containing data
  */
  private function downloadData($file, $symbol){
    $api_key = env('QUANDL_USER_API_KEY');
    $end_date = date('Y-m-d');
    $start_date = date('Y-m-d', strtotime('-'.env('DEFAULT_AMOUNT_OF_DAYS').' days'));
    $url = "http://www.quandl.com/api/v3/datasets/WIKI/$symbol.csv?start_date=$start_date&end_date=$end_date&api_key=$api_key";

    $filePath = env('STOCK_DATA_REPO')."$file";
    if(!file_exists($filePath)){  // Check if file exists
      $fromURL = @file_get_contents($url);
        if($fromURL === FALSE) //If no file was able to be downloaded, return
          return false;
      file_put_contents($filePath, $fromURL); }// File downloaded successfully

    else if (time()-filemtime($filePath) > 24 * 3600){  //Check for old (24hr)
      if ($fp = curl_init($url)){
        file_put_contents($filePath, file_get_contents($url));
      }
    }

    return $filePath;
  }
}

?>
