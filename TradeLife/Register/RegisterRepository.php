<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Register/RegisterRepository
// DESC: Attempt to insert user into user DB

namespace TradeLife\Register;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use YodleeApi;
use App\User;
use PDOException;
use \Cache;

class RegisterRepository extends APIController
{
  public function __construct()
  {
    return response()->json("Register Repo");
  }

  /**
  * Attempt to insert into the user database. Check for Unique constraints
  *
  * @param JSON decoded array from APP
  * @return JSON respone to app
  */
  public function intiateInsert($input)
  {
    // User the App/User model
    $user = new User;
    $message = 'Successfully Registered';
    $error_status = false;
    $error_code = '';
    if(User::select('*')->where('name', '=', strtolower($input['userName']))->exists()){
      return response()->json(['error' => true,
            'message' => "Username Already Taken",
            'error_code' => 'unique_constraint_violation'], 200);}

    else if(User::select('*')->where('email', '=', strtolower($input['userEMail']))->exists()){
      return response()->json(['error' => true,
            'message' => "Email Already Taken",
            'error_code' => 'unique_constraint_violation'], 200);}

    else{
      /************************************************************************/
      // ONCE OUT OF SANDBOX MODE USE THIS
      /************************************************************************/
      // if(!$this->intiateYodleeInsert($input)){
      //   return response()->json(['error' => true,
      //         'message' => "Failed to Register with Yodlee",
      //         'error_code' => 'yodlee_sandbox_mode'], 200);
      // }
      /************************************************************************/

      /************************************************************************/
      // AND DELETE THIS
      $still_in_sandbox_mode = true;
      if(!$still_in_sandbox_mode){
        $foo = 'bar';
      }
      /************************************************************************/
      else{
        $user->name = strtolower($input['userName']);
        $user->email = strtolower($input['userEMail']);
        $user->password = $input['userPass'];
        try{
          $saveSuccess = $user->save();
        }
        catch(PDOException $e) // Something went wrong, most likely unique()
        {
          return response()->json(['error' => true,
                'message' => "Failed to Register",
                'error_code' => 'unique_constraint_violation'], 200);
        }
        return response()->json(['error' => false,
            'message' => "Successfully Registered",], 200);
      }
    }
  }

  /**
  * Cannot use this until we are out of sandbox mode
  *
  * @param $input, array of strings
  * @return \Illuminate\Http\JsonResponse
  */
  private function intiateYodleeInsert($input)
  {
    $yodleeApi = new \YodleeApi\Client(env('YODLEEAPI_URL'));
    $response = true;
    if(Cache::has('cobrand'))
      $yodleeApi->setCobrand(Cache::get('cobrand'));
    else{
      $response = $yodleeApi->cobrand()->login(env('YODLEEAPI_COBRAND_LOGIN'), env('YODLEEAPI_COBRAND_PASSWORD'));
      Cache::put('cobrand', $yodleeApi->session()->getCobrandSessionToken(), 30);
    }

    $response = $yodleeApi->user()->register($input['userName'],
                                            $input['userPass'],
                                            $input['userEMail']);
    if(!$response)
      return false;
    return true;
  }
}

?>
