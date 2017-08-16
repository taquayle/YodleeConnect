<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Profile/ProfileRepository
// DESC: Functions that to create, get, and update user profile. Profile can be
//    found at env('USER_PROFILE_REPO')

namespace Trader\Profile;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Trader\Profile\UserKeys;
use Trader\Stocks\UserStocks;
use YodleeApi;
use App\User;
use PDOException;

class ProfileRepository extends APIController
{
  protected $userKeys;
  protected $userStocks;
  public function __construct(UserKeys $userKeys, UserStocks $userStocks)
  {
    $this->userKeys = $userKeys;
    $this->userStocks = $userStocks;
  }

  /**
  * Generate the user profile, this will destroy old profile and create a new
  * one if it exsists, but will save the entered user_keywords. Else it will
  * create a new one. This is the PUT of the profile REST API
  *
  * @param $input - JSON from TradeLife containng userName
  * @return JSON to App & user_profile updated and saved
  */
  public function generateProfile($input)
  {
    // $message = $this->userStocks->versionTwo($input['userName']);
    // return response()->json(['error' => true,
    //     'messages' => $message,
    //     'error_code' => 0], 200);

    $message = "Successfully Generated Profile";
    $error = false;
    $errorCode = 0;
    // First, generate user keys from transactions, this will fail if:
    //    1. User has not linked any bank accounts to create transactions
    if(!$this->userKeys->generate($input['userName'])){
      $message = " No Transaction Keywords";
      $error = true;
      $errorCode = 1;
    }

    // Second, generate stocks from the user_keywords. This will fail if:
    //    1.  Company database does not exist or is corrupted
    if(!$this->userStocks->generate($input['userName'])){
      $message = "Company Database Failure";
      $error = true;
      $errorCode = 2;
    }


    return response()->json(['error' => $error,
        'messages' => $message,
        'error_code' => $errorCode], 200);
  }

  /**
  * The GET of the user profile, attempt to read in user_profile.json file and
  * return to TradeLifeApp
  *
  * @param $input - JSON from TradeLifeApp
  * @return JSON - response containing user_profile
  */
  public function retrieveProfile($input)
  {
    $fileName = env('USER_PROFILE_REPO') . $input['userName'] . ".json";
    if(file_exists($fileName)){
      return response()->json(['error' => false,
          'message' => "Successfully retrieved profile",
          'profile' => json_decode(file_get_contents($fileName))], 200);}

    file_put_contents($fileName, json_encode($this->userKeys->emptyProfile($input['userName']), JSON_FORCE_OBJECT));
    return response()->json(['error' => true,
        'message' => "Created Basic Profile",
        'profile' => json_decode(file_get_contents($fileName))], 200);
  }

  /**
  * Insert into the user profile the keywords from the App entered by the user
  *
  * @param JSON - From TradeLifeApp
  * @return JSON - repsone to TradeLifeApp with profile.
  */
  public function updateUserKeywords($input)
  {
    if($input['keyWords'] == null)
      return response()->json(['error' => true,
          'message' => "no keywords given"], 200);
    // Check if the user profile exists
    $fileName = env('USER_PROFILE_REPO') . $input['userName'] . ".json";
    if(!file_exists($fileName))
      return response()->json(['error' => true,
          'message' => "Profile does not exist"], 200);

    // Get the profile from JSON file and return a PHP array
    $profile = json_decode(file_get_contents($fileName), true);
    // The value of the 'heaviest' keyword by value found in File

    $maxValue = 1;
    $maxHits = 1;
    foreach ($profile['Desc_Keywords'] as $key => $value) {
      if($value['Value'] > $maxValue){
        $maxValue = $value['Value'];
        $maxHits = $value['Hits'];
      }
    }
    $maxValue = $maxValue * env('USER_KEY_WEIGHT');
    //$maxValue = $profile['Desc_Keywords'][0]['Value'] * env('USER_KEY_WEIGHT');

    // The value of the 'heaviest' keyword by hits found in file
    //$maxHits = $profile['Desc_Keywords'][0]['Hits'] * env('USER_KEY_WEIGHT');


    // Insert first keyword if nothing has been inserted yet

    // If user_keywords hasn't been used yet. insert first given keyword
    //  THIS MAY NOT NEED TO BE HERE. WILL LOOK BACK AT THIS.
    if($profile['User_Keywords'] == null)
      $profile['User_Keywords'][strtoupper($input['keyWords'][0])] =
          [ 'Name' => strtoupper($input['keyWords'][0]), 'Value'=> $maxValue,
            'Hits' => $maxHits, 'Percent' => 0.0];

    // Check if each given keyword is new or not.
    foreach($input['keyWords'] as $word)
    {
      // Since php arrays have a key-value pairing. check if word exists
      if (!array_key_exists(strtoupper($word),$profile['User_Keywords'])) {
        $profile['User_Keywords'][strtoupper($word)] =
                [ 'Name' => strtoupper($word), 'Value'=> $maxValue,
                  'Hits' => $maxHits, 'Percent' => 0.0];
      }
    }

    // Update all the user_keywords in profile for the newest weight given in
    //  env('USER_KEY_WEIGHT') file.
    foreach($profile['User_Keywords'] as &$words)
    {
      $words['Value'] = $maxValue;
      $words['Hits'] = $maxHits;
    }

    // Save updated profile
    file_put_contents($fileName, json_encode($profile, JSON_FORCE_OBJECT));

    return response()->json(['error' => false,
        'message' => "Successfully updated User_Keywords",
        'keywords' => $profile['User_Keywords']], 200);

  }

}

?>
