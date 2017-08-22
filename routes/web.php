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
Route::post('transaction/put', '\TradeLife\Transactions\TransactionController@put');
Route::post('transaction/get', '\TradeLife\Transactions\TransactionController@get');
Route::post('profile/get', '\TradeLife\Profile\ProfileController@get');
Route::post('profile/put', '\TradeLife\Profile\ProfileController@put');
Route::post('profile/post', '\TradeLife\Profile\ProfileController@post');
Route::post('stocks/get', '\TradeLife\Stocks\StockController@get');
Route::post('exchange/generate', '\TradeLife\Exchange\ExchangeController@generate');



Route::get('/', function () {
    return view('testsuite');
    //return Redirect::to('testsuite.php');
});

Route::get('/builduser', function () {
    return view('createuserprofile');
    //return Redirect::to('testsuite.php');
});

Route::get('/showdatabases', function () {
    return view('showdatabases');
    //return Redirect::to('testsuite.php');
});


Route::get('/database/users/default',function(){
    return view('default_users');
});

Route::get('login',function(){
    return view('logintest');
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
