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

Route::group(['prefix' => 'auth/{uniacid}', 'namespace' => 'Auth'], function () {
    //账户相关路由
    Route::get('/', 'MainController@Main');
    Route::post('/login', 'MainController@Login');
    Route::post('/register', 'MainController@Register');
    Route::post('/quiklogin', 'MainController@QuikLogin');
    Route::post('/smscode', 'MainController@SmsCode');
    Route::post('/repwd', 'MainController@ResetPassword');

});

Route::group(['prefix' => 'm/', 'namespace' => 'App','middleware'=>[\App\Http\Middleware\App::class,\App\Http\Middleware\AppRuntime::class]], function () {
    //模块接口路由
    Route::match(['get', 'post'],'/{modulename}', 'ModuleController@Api');
});

Route::group(['prefix' => 'member/', 'namespace' => 'Auth'], function () {
    //会员相关路由
    Route::get('/{uid?}', 'MainController@Main')->where('uid','[0-9]+');
    Route::post('/login', 'MainController@Login');
    Route::post('/register', 'MainController@Register');
    Route::post('/quiklogin', 'MainController@QuikLogin');
    Route::post('/smscode', 'MainController@SmsCode');
    Route::post('/repwd', 'MainController@ResetPassword');

});
