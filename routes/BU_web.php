<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('login','\TradeLife\Login\LoginController@login');
Route::post('logout','\TradeLife\Login\LoginController@logout');
Route::post('register', '\TradeLife\Register\RegisterController@register');
Route::post('transaction', '\TradeLife\Transactions\TransactionController@transaction');
Route::post('profile/update', '\TradeLife\Profile\ProfileController@update');
Route::post('profile/retrieve', '\TradeLife\Profile\ProfileController@retrieve');
Route::post('profile/add', '\TradeLife\Profile\ProfileController@add');
Route::post('stocks/get', '\TradeLife\Stocks\StockController@get');
Route::post('exchange/generate', '\TradeLife\Exchange\ExchangeController@generate');
Route::post('fastlink','\TradeLife\FastLink\FastLinkController@fastlink');

Route::get('/', function () {
    return view('keywords');
    //return Redirect::to('testsuite.php');
});
Route::post('/keywords/edit', function () {
    return view('keywords');
    //return Redirect::to('testsuite.php');
});
Route::get('/keywords/edit', function () {
    return view('keywords');
    //return Redirect::to('testsuite.php');
});
Route::match(['GET', 'POST', 'PATCH', 'PUT', 'DELETE'],'{any}', ['as'=>'any',function($any)
        { // Any api url that does not exist
            $message = [
                "error" => true,
                "message" => "API not found",
                "error_code" => "api_not_found"
            ];
            return Response::json($message,404);
        }])->where('any','.*');
