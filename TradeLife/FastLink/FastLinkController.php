<?php
// Author: Tyler Quayle
// Date: 8/2/2017
// FILE: TradeLife/FastLink
// DESC: Controller for fastLink attempts

namespace TradeLife\FastLink;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use TradeLife\FastLink\FastLinkRepository;


class FastLinkController extends APIController
{
  protected $fastLinkRepository;
  function __construct(FastLinkRepository $fastLinkRepository)
  {
      $this->fastLinkRepository = $fastLinkRepository;
  }

  /**
  * Attempt to fastLink to the server. logic is FastLinkRepository
  */
  public function fastLink()
  {
    $input = Input::json()->all();
    return $this->fastLinkRepository->intiateFastLink($input);

  }
}

?>
