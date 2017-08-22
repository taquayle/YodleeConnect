<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Login/LoginRepository
// DESC: Controller for login attempts

namespace TradeLife\Login;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use YodleeApi;
use App\User;
use \Cache;


class LoginRepository extends APIController
{
  public function __construct()
  {
    return response()->json("hello");
  }

  public function intiateLogin($input)
  {
    $credentials = ['name' => strtolower($input['userName']), 'password' => $input['userPassword']];
    // attempt to verify the credentials and create a token for the user
    //  TOKEN NOT YET CREATED FOR TRADELIFE-USER RELATION 8/2/2017
    if (!Auth::attempt($credentials)) {
      $credentials = ['email' => strtolower($input['userName']), 'password' => $input['userPassword']];
      if (!Auth::attempt($credentials)) {
        return response()->json(['error' => true,
             'messages' => "Authentication Failed",
             'error_code' => 'invalid_credentials'], 401);
     }
    }
    $user = User::where('name', '=', strtolower($input['userName']))->first();
    $user->touch();

    
    $yodleeResponse = $this->intiateYodleeLogin($input);
    if(!$yodleeResponse)
      return response()->json(array('error' => true,
                  'messages' => "Yodlee Cobrand Failure"), 200);
    else {
      return response()->json(array('error' => false,
                  'messages' => "Successful Login",
                  'yodleeToken' => $yodleeResponse['UserToken']), 200);
    }
  }

  /**
  * Use a modified progknife YodleeApi\User login attempt a Yodlee Server login
  *
  * @param $input, array of strings
  * @return \Illuminate\Http\JsonResponse
  */
  private function intiateYodleeLogin($input)
  {
    $yodleeApi = new \YodleeApi\Client(env('YODLEEAPI_URL'));
    $response = true;
    if(Cache::has('cobrand'))
      $yodleeApi->setCobrand(Cache::get('cobrand'));
    else{
      $response = $yodleeApi->cobrand()->login(env('YODLEEAPI_COBRAND_LOGIN'), env('YODLEEAPI_COBRAND_PASSWORD'));
      Cache::put('cobrand', $yodleeApi->session()->getCobrandSessionToken(), 30);
    }

    if ($response){
      $response = $yodleeApi->user()->login($input['userName'], $input['userPassword']);
      return ["Cobrand" => $yodleeApi->session()->getCobrandSessionToken(),
              "UserToken" => $yodleeApi->session()->getUserSessionToken()];
    }
    else
      return false;
  }

  /**
  * Has not been implemented yet. Dont know if it even does. Set everything to
  * NULL on the app. Tokens should expire.
  *
  */
  public function intiateLogout($input)
  {
    return true;
  }
}

?>
