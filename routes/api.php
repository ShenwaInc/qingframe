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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group(['prefix' => 'm/{uniacid}', 'namespace' => 'Api'], function () {

    Route::get('/{modulename}', 'ModuleController@Main');

});

Route::group(['prefix' => 'auth/{uniacid}', 'namespace' => 'Auth'], function () {
    //账户相关路由
    Route::get('/', 'MainController@Main');
    Route::post('/login', 'MainController@Login');
    Route::post('/register', 'MainController@Register');
    Route::post('/quiklogin', 'MainController@QuikLogin');
    Route::post('/smscode', 'MainController@SmsCode');
    Route::post('/repwd', 'MainController@ResetPassword');

});

Route::group(['prefix' => 'member/{uniacid}', 'namespace' => 'Auth'], function () {
    //会员相关路由
    Route::get('/{uid?}', 'MainController@Main');
    Route::post('/login', 'MainController@Login');
    Route::post('/register', 'MainController@Register');
    Route::post('/quiklogin', 'MainController@QuikLogin');
    Route::post('/smscode', 'MainController@SmsCode');
    Route::post('/repwd', 'MainController@ResetPassword');

});
