<?php
namespace Trader\Exchange;
use Trader\Exchange\ExchangeRepository;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
/*
  THIS IS CURRENTLY SHELVED, RIGHT NOW THIS LOGIC IS DONE BY //ExScrape. BASICALLY
  BY DOING A //ExScarpe/Client->fullUpdate() WILL TAKE CARE OF THE LOGIC. THERE
  IS A COMMAND php aristan companies:update THAT WILL CALL AND UPDATE.
*/
class ExchangeController extends APIController
{
  protected $exchangeRepository;

  function __construct(ExchangeRepository $exchangeRepository)
  {
      $this->exchangeRepository = $exchangeRepository;
  }

  public function generate()
  {
    $input = Input::json()->all();
    return $this->exchangeRepository->temp();
  }

}

?>
