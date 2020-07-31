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
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('details', 'API\UserController@details');
    Route::post('create-contact', 'API\UserController@createcontact');
    Route::post('update-contact', 'API\UserController@updatecontact');
    Route::post('contact', 'API\UserController@contact');
    Route::post('user-contact', 'API\UserController@usercontact');
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });