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

use App\Http\Middleware\ConsolePermission;
use App\Http\Middleware\ModulePermission;
use Illuminate\Http\Request;

Route::group(['prefix' => 'auth','namespace'=>'Auth', 'middleware'=>['app']],function (){
    Route::post('/login', 'AuthController@Login');
    Route::post('/logout', 'AuthController@Logout');
});

Route::group(['namespace'=>'Auth', 'middleware'=>['app']],function (){
    Route::get('/login/{uniacid}', 'AuthController@Entry')->where('uniacid','[0-9]+');
});

Route::group(['prefix' => 'app','namespace' => 'App', 'middleware'=>['app', 'runtime']],function (){
    Route::match(['get', 'post'],'/m/{modulename}/{do?}', 'ModuleController@entry');
    Route::get('auth', 'AuthController@index');
});

Route::group(['prefix' => 'wem','namespace' => 'App', 'middleware'=>['app','runtime']],function (){
    Route::match(['get', 'post'],'/{modulename}/{do?}', 'ModuleController@entry');
    Route::post('/subscribe/{action}', 'ModuleController@subscribe');
});

Route::group(['namespace'=>'Console', 'middleware'=>['installer', 'app']],function (){
    Route::get('/', 'EntryController@index');
});

Route::group(['prefix' => 'console', 'namespace' => 'Console', 'middleware'=>['auth', 'app', ConsolePermission::class]], function () {
    Route::get('/', 'PlatformController@index');
    Route::get('/util/{op?}', 'UtilController@index');
    Route::post('/util/{op?}', 'UtilController@save');
    Route::get('/active', 'SettingController@active');
    Route::get('/setting/{op?}', 'SettingController@index');
    Route::post('/setting', 'SettingController@save');
    Route::get('/account/{uniacid}', 'PlatformController@checkout')->where('uniacid','[0-9]+');
    Route::match(['get', 'post'],'/account/{action}', 'AccountController@index')->where('action','[a-z]+');
    Route::match(['get', 'post'],'/user/{op?}', 'UserController@index');
    Route::match(['get', 'post'],'/m/{modulename}/{do?}', 'ModuleController@entry')->middleware(ModulePermission::class);
    Route::get('/server', 'ServerController@index');
    Route::get('/server/account', 'ServerController@checkout');
    Route::get('/server/apis/{server}', 'ServerController@Apis');
    Route::get('/server/methods/{server}', 'ServerController@Methods');
});

Route::group(['prefix'=>'server', 'namespace' =>'Console', 'middleware'=>['auth', 'app', ConsolePermission::class]],function (){
    Route::any('/{server}/{segment1?}/{segment2?}', 'ServerController@HttpRequest');
});

Route::group(['prefix'=>'payment', 'namespace' => 'App', 'middleware'=>['app']],function (){
    Route::any('/{payment}', 'PaymentController@notify')->where('payment','[a-z]+');
    Route::match(['get', 'post', 'option'], '/return/{payment}', 'PaymentController@response');
});

Route::group(['prefix'=>'installer', 'middleware'=>['app']],function (){
    Route::get('/', 'InstallController@index');
    Route::post('/agreement', 'InstallController@agreement');
    Route::get('/database', 'InstallController@database');
    Route::post('/database', 'InstallController@dbDetect');
    Route::get('/render', 'InstallController@render');
    Route::post('/render', 'InstallController@install');
    Route::get('/complete', 'InstallController@complete');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin/{modulename}', function (Request $request, $modulename){
    $user = $request->user();
    if (empty($user)){
        $referer = url('login') ."?referer=console/m/{$modulename}";
        return response()->redirectTo($referer);
    }
    return response()->redirectTo("console/m/{$modulename}");
});
