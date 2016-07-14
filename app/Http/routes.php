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
use Illuminate\Http\Request;

use App\Http\Requests;

Route::get('/', function () {
    return "no direct access!";
});

Route::group(['prefix' => 'set'], function() {
    Route::post('configuration', "configuration@set");
});


