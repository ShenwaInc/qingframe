<?php

use App\Http\Middleware\AppRuntime;

Route::group(['prefix' => 'app','namespace' => 'App', 'middleware'=>['app']],function (){
    Route::match(['get', 'post'],'/m/{modulename}/{do?}', 'ModuleController@entry');
    Route::match(['get', 'post'],'/util/{option}', 'UtilController@Main');
    Route::get('auth', 'AuthController@index');
});

Route::group(['prefix'=>'server', 'middleware'=>['app']],function (){
    Route::any('/{server}/{segment1?}/{segment2?}', 'HttpController@ServerApp')->where('server','[a-z]+')->middleware(AppRuntime::class);
    Route::any('/run/{server}/{segment1?}', 'HttpController@ServerRun')->where('server','[a-z]+');
});
