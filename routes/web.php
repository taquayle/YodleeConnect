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
Route::post('fastlink','\TradeLife\FastLink\FastLinkController@fastLink');
Route::post('register', '\TradeLife\Register\RegisterController@register');
Route::post('transaction/put', '\TradeLife\Transactions\TransactionController@put');
Route::post('transaction/get', '\TradeLife\Transactions\TransactionController@get');
Route::post('profile/retrieve', '\TradeLife\Profile\ProfileController@retrieve');
Route::post('profile/update', '\TradeLife\Profile\ProfileController@update');
Route::post('profile/add', '\TradeLife\Profile\ProfileController@add');
Route::post('stocks/get', '\TradeLife\Stocks\StockController@get');

Route::post('keywords/company', '\TradeLife\Keywords\KeywordsController@company');
Route::post('keywords/user', '\TradeLife\Keywords\KeywordsController@user');

Route::get('/', function () {
    return view('tradelifelanding');
});

Route::get('/company/edit', function () {
    return view('company');
});

Route::post('/company/edit', function () {
    return view('company');
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
