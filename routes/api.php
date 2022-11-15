<?php

use App\Http\Middleware\AppRuntime;
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

Route::group(['prefix'=>'server', 'middleware'=>['app']],function (){
    Route::any('/run/{server}/{segment1?}', 'HttpController@ServerRun')->where('server','[a-z]+');
    Route::any('/{server}/{segment1?}/{segment2?}', 'HttpController@ServerApi')->where('server','[a-z]+')->middleware(AppRuntime::class);
});

Route::group(['prefix' => 'm/', 'namespace' => 'App','middleware'=>['app','runtime']], function () {
    //模块接口路由
    Route::match(['get', 'post'],'/{modulename}', 'ModuleController@Api');
});

Route::group(['prefix'=>'payment', 'namespace' => 'App', 'middleware'=>['app']],function (){
    //支付接口路由
    Route::any('/{payment}', 'PaymentController@notify')->where('payment','[a-z]+');
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
