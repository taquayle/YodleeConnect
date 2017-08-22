<?php
namespace TradeLife\Transactions;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use TradeLife\Transactions\TransactionRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TransactionController extends APIController
{
  protected $transactionRepository;
  function __construct(TransactionRepository $transactionRepository)
  {
      $this->transactionRepository = $transactionRepository;
  }

  public function put()
  {
    $input = Input::json()->all();
    return $this->transactionRepository->intiateTransaction($input);

  }

  public function get()
  {
    $input = Input::json()->all();
    return $this->transactionRepository->transactionHistory($input);
  }
}

?>
