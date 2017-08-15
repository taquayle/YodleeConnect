<?php
namespace Trader\Register;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Trader\Register\RegisterRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends APIController
{
  protected $registerRepository;
  function __construct(RegisterRepository $registerRepository)
  {
      $this->registerRepository = $registerRepository;
  }

  public function register()
  {
    $input = Input::json()->all();
    return $this->registerRepository->intiateInsert($input);
    
  }
}

?>
