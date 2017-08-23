<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Keywords/KeywordsRepository
// DESC: Controller for login attempts

namespace TradeLife\Keywords;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use YodleeApi;
use App\User;
use \Cache;


class KeywordsRepository extends APIController
{
  public function __construct()
  {
    return response()->json("hello");
  }

  public function updateCompanyKeywords($input)
  {
    $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_COMPANY.json";
    $byCompanies = json_decode(file_get_contents($fileName), true);

    $fileName = env('COMPANY_KEYWORDS_REPO') . "BY_KEYWORD.json";
    $byKeywords = json_decode(file_get_contents($fileName), true);


  }

}

?>
