<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Login
// DESC: Controller for login attempts

namespace TradeLife\Login;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use TradeLife\Login\LoginRepository;


class LoginController extends APIController
{
  protected $loginRepository;
  function __construct(LoginRepository $loginRepository)
  {
      $this->loginRepository = $loginRepository;
  }

  /**
  * Attempt to login to the server. logic is LoginRepository
  */
  public function login()
  {
    $input = Input::json()->all();
    return $this->loginRepository->intiateLogin($input);

  }

  public function logtout()
  {
    $input = Input::json()->all();
    return $this->logingRepository->intiateLogout($input);
  }

}

?>
