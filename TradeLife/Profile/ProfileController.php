<?php
namespace Trader\Profile;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Trader\Profile\ProfileRepository;
use Trader\Stocks\StockController;


class ProfileController extends APIController
{
  protected $profileRepository;
  protected $stockController;
  function __construct(ProfileRepository $profileRepository, StockController $stockController)
  {
      $this->profileRepository = $profileRepository;
      $this->stockController = $stockController;
  }

  public function put()
  {
    $input = Input::json()->all();
    return $this->profileRepository->generateProfile($input);
  }

  public function get()
  {
    $input = Input::json()->all();
    return $this->profileRepository->retrieveProfile($input);
  }

  public function post()
  {
    $input = Input::json()->all();
    return $this->profileRepository->updateUserKeywords($input);
  }
}

?>