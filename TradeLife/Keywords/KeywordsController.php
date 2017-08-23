<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/Keywords
// DESC: Controller for keywords attempts

namespace TradeLife\Keywords;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use TradeLife\Keywords\KeywordsRepository;


class KeywordsController extends APIController
{
  protected $keywordsRepository;
  function __construct(KeywordsRepository $keywordsRepository)
  {
      $this->keywordsRepository = $keywordsRepository;
  }

  /**
  * Attempt to keywords to the server. logic is KeywordsRepository
  */
  public function company()
  {
    $input = Input::json()->all();
    return $this->keywordsRepository->updateCompanyKeywords($input);

  }

  public function user()
  {
    echo ('hello');
  }
}

?>
