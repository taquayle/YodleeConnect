<?php
namespace TradeLife\Stocks;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use TradeLife\Stocks\StockRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StockController extends APIController
{
  protected $stockRepository;
  function __construct(StockRepository $stockRepository)
  {
      $this->stockRepository = $stockRepository;
  }

  public function get()
  {
    $input = Input::json()->all();
    return $this->stockRepository->gatherData($input);

  }
}

?>
