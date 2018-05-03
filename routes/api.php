<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::get('/symbols', 'SymbolController@index');

Route::get('/symbol/fetch/{symbol}', 'OhlcValueController@create');

Route::get('/symbol/current/{symbol}', 'OhlcValueController@current');

Route::get('/symbol/recent2/{symbol}', 'OhlcValueController@recent2');


Route::get('/symbol/graph-data/{symbol}', 'OhlcValueController@getOhlcDataForGraph');


