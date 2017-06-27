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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/activity','IndexController@showLottery');
Route::post('/activity-do','IndexController@doLottery');
Route::get('/login','IndexController@login');
Route::get('/call-friend','IndexController@callFriend');
Route::get('/charge','IndexController@charge');
Route::get('/center','IndexController@center');
Route::get('/group','IndexController@group');