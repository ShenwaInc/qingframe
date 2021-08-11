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

Route::group(['namespace'=>'Auth', 'middleware'=>[\App\Http\Middleware\App::class]],function (){
    Route::any('/login', 'LoginController@index');
});

Route::group(['namespace'=>'Console', 'middleware'=>[\App\Http\Middleware\Installer::class, \App\Http\Middleware\App::class]],function (){
    Route::get('/', 'EntryController@index');
});

Route::group(['prefix' => 'console', 'namespace' => 'Console', 'middleware'=>['auth',\App\Http\Middleware\App::class,\App\Http\Middleware\ConsolePermission::class]], function () {

    Route::get('/', 'PlatformController@index');
    //Route::get('/login', 'LoginController@index');
    Route::get('/account/{uniacid?}', 'PlatformController@account');
    Route::get('/utils/{uniacid?}', 'PlatformController@utils');
    Route::get('/payment/{uniacid?}', 'PlatformController@payment');
    Route::get('/m/{modulename}', 'ModuleController@entry');

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
