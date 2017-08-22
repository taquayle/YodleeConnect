<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/FastLink/FastLinkRepository
// DESC: Controller for login attempts

namespace TradeLife\FastLink;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use YodleeApi;
use App\User;
use \Cache;


class FastLinkRepository extends APIController
{
  public function __construct()
  {
    return response()->json("hello");
  }

  public function intiateFastLink($input)
  {
    $yodleeApi = new \YodleeApi\Client(env('YODLEEAPI_URL'));
    $response = true;

    if(!Cache::has('cobrand')){
      $response = $yodleeApi->cobrand()->login(env('YODLEEAPI_COBRAND_LOGIN'), env('YODLEEAPI_COBRAND_PASSWORD'));
      Cache::put('cobrand', $yodleeApi->session()->getCobrandSessionToken(), 30);
    }
    $yodleeApi->setTokens(Cache::get('cobrand'), $input['yodleeToken']);

    $fastlinkTokens = $yodleeApi->user()->accessTokensIputs();

    if($fastlinkTokens != false){
      return response()->json(array('error' => false,
                  'messages' => "Token",
                  'fastlinktokens' => $fastlinkTokens,
                  'url' => env('FASTLINK_URL')), 200);
    }
    else{
      return response()->json(array('error' => true,
                  'messages' => "Fast link token failure"), 200);
    }

  }
}
?>
