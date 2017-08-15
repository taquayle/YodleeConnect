<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Register/RegisterRepository
// DESC: Attempt to insert user into user DB

namespace Trader\Register;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use YodleeApi;
use App\User;
use PDOException;

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
    // save them lower, to avoid a world of issues
    $user->name = strtolower($input['userName']);
    $user->email = strtolower($input['userEMail']);
    $user->password = $input['userPass'];
    try{
      $saveSuccess = $user->save();
    }
    catch(PDOException $e) // Something went wrong, most likely unique()
    {
      $errorCode = $e->errorInfo[0];
      $fullError = $e->errorInfo[2];
      $message = "Failure to Register";
      if($errorCode == 23505){
        // Error 23505 means a unique constraint was violated, check which of
        // 2 culprits tripped it. Email or UserName
        // IF BOTH TAKEN, USER IS PROBABLY ALREADY REGISTERED
        if(User::where('name', '=',  strtolower($input['userName']))->exists() &&
          User::where('email', '=',  strtolower($input['userEMail']))->exists()){
          $message = 'You are already registered.';}
        // CHECK IF IT WAS THE USERNAME THAT WAS TAKEN
        else if(User::where('name', '=',  strtolower($input['userName']))->exists()){
          $message = 'Username already taken';}
        // E-Mail was already taken.
        else {
          $message = 'E-mail already taken';
        }
      }
      return response()->json(['error' => true,
          'message' => $message,
          'error_code' => 'unique_db_constraint'], 401);
    }
    // This is a generic response, if it wasn't PDO exception
    if(!$saveSuccess)
      return response()->json(['error' => true,
          'message' => "Failed to create User",
          'error_code' => 'yodlee_sandbox_mode'], 401);
    return response()->json(['error' => false,
        'message' => "Successfully Registered",], 200);
  }

  /**
  * Cannot use this until we are out of sandbox mode
  *
  * @param $input, array of strings
  * @return \Illuminate\Http\JsonResponse
  */
  private function intiateYodleeInsert($input)
  {
    return response()->json(['errors' => true,
        'messages' => "Cannot insert to yodlee during sandbox mode",
        'error_code' => 'yodlee_sandbox_mode'], 401);
  }
}

?>
