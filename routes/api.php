<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', 'Auth\LoginApiController@login');
Route::get('/logout', 'Auth\LoginApiController@logout');
Route::post('/logout', 'Auth\LoginApiController@logout');

$auth = Route::middleware('auth:api');

$urlAccount = "/account";
$auth->get($urlAccount . '/loginStatus', 'AccountController@loginStatus');
$auth->get($urlAccount . '/loginAccount', 'AccountController@loadLoginAccount');

$urlAsset = "/asset";
$auth->get($urlAsset . '/cio/unprocessedOut', 'AssetController@findUnprocessedCashOut');
$auth->post($urlAsset . '/cio/withdraw', 'AssetController@withdraw');
