<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::post('/api/login', 'Auth\AuthController@login');
Route::get('/api/logout', 'Auth\AuthController@logout');
Route::post('/api/logout', 'Auth\AuthController@logout');

$urlAccount = "/api/account";
Route::get($urlAccount . '/loginStatus', 'AccountController@loginStatus');
Route::get($urlAccount . '/loginAccount', 'AccountController@loadLoginAccount');

$urlAsset = "/api/asset";
Route::get($urlAsset . '/cio/unprocessedOut', 'AssetController@findUnprocessedCashOut');
Route::post($urlAsset . '/cio/withdraw', 'AssetController@withdraw');
