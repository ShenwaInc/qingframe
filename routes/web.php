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

Route::group(['prefix' => 'auth','namespace'=>'Auth', 'middleware'=>[\App\Http\Middleware\App::class]],function (){
    Route::post('/login', 'AuthController@Login');
    Route::post('/logout', 'AuthController@Logout');
});

Route::group(['namespace'=>'Console', 'middleware'=>[\App\Http\Middleware\Installer::class, \App\Http\Middleware\App::class]],function (){
    Route::get('/', 'EntryController@index');
});

Route::group(['prefix' => 'console', 'namespace' => 'Console', 'middleware'=>['auth', \App\Http\Middleware\App::class,\App\Http\Middleware\ConsolePermission::class]], function () {

    Route::get('/', 'PlatformController@index');
    Route::get('/util/{op?}', 'UtilController@index');
    Route::post('/util/{op?}', 'UtilController@save');
    Route::get('/active/{op?}', 'SettingController@active');
    Route::get('/setting/{op?}', 'SettingController@index');
    Route::post('/setting', 'SettingController@save');
    Route::get('/account/{uniacid}', 'PlatformController@checkout')->where('uniacid','[0-9]+');
    Route::get('/account/{action}', 'PlatformController@account')->where('action','[a-z]+');
    Route::get('/m/{modulename}', 'ModuleController@entry');
    Route::get('/m/{modulename}/{$do}', 'ModuleController@doweb');

});

Route::group(['prefix'=>'installer', 'middleware'=>[\App\Http\Middleware\Installed::class]],function (){
    Route::get('/', 'installController@index');
    Route::post('/agreement', 'installController@agreement');
    Route::get('/database', 'installController@database');
    Route::post('/database', 'installController@dbDetect');
    Route::get('/socket', 'installController@socket');
    Route::post('/socket', 'installController@wsDetect');
    Route::get('/render', 'installController@render');
    Route::post('/render', 'installController@install');
    Route::get('/complete', 'installController@complete');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
