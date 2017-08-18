<?php
namespace Trader\Transactions;
use Illuminate\Support\Facades\Response;  //Laravel Response class. Use response()->json()
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use YodleeApi;
use App\Transaction;
use PDOException;
use \Cache;


class TransactionRepository extends APIController
{
  public function __construct()
  {
    return response()->json("hello");
  }

  public function intiateTransaction($input)
  {

    $yodleeApi =  new \YodleeApi\Client(env('YODLEEAPI_URL'));
    if(Cache::has('cobrand'))
      $yodleeApi->setCobrand(Cache::get('cobrand'));
    else{
      $yodleeApi->cobrand()->login(env('YODLEEAPI_COBRAND_LOGIN'), env('YODLEEAPI_COBRAND_PASSWORD'));
      Cache::put('cobrand', $yodleeApi->session()->getCobrandSessionToken(), 30);
    }

    $yodleeApi->setTokens(Cache::get('cobrand'), $input['yodleeToken']);
    $response = $yodleeApi->transactions()->getPreviousYears(10);
    $transCount = 0;
    foreach ($response as $transaction) {
      if($this->insertTransaction($input['userName'], $transaction))
        $transCount++;
    }
    return response()->json(['error' => false,
        'messages' => "Inserted: $transCount"], 200);
  }

  protected function insertTransaction($user, $action)
  {
    $tran = new Transaction;
    $tran->name = $user;
    $tran->container = $action->{'CONTAINER'};
    $tran->trans_id = $action->{'id'};
    $tran->amount = $action->{'amount'}->{'amount'};
    $tran->base_type = $action->{'baseType'};
    $tran->cat_type = $action->{'categoryType'};
    $tran->cat_id = $action->{'categoryId'};
    $tran->category = strtoupper($action->{'category'});
    if($action->{'CONTAINER'} == 'loan' ||
        $action->{'CONTAINER'} == 'investment' ||
        $action->{'CONTAINER'} == 'insurance')
    {
      $tran->simple_desc = strtoupper($action->{'category'});
      $tran->original_desc = strtoupper($action->{'category'});
      $tran->type = $action->{'category'};
      $tran->sub_type = $action->{'category'};
    }
    else
    {
      $tran->simple_desc = str_replace("'", "", strtoupper($action->{'description'}->{'simple'}));
      $tran->original_desc = str_replace("'", "", strtoupper($action->{'description'}->{'original'}));
      $tran->type = $action->{'type'};
      $tran->sub_type = $action->{'subType'};
    }
    $tran->trans_date = $action->{'date'};
    try{
      $saveSuccess = $tran->save();
    }
    catch(PDOException $e)
    {
      return false;
    }
    return true;
  }
}

?>
