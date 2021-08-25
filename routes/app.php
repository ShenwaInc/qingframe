<?php

Route::group(['prefix' => 'app','namespace' => 'App', 'middleware'=>[\App\Http\Middleware\App::class]],function (){
    Route::match(['get', 'post'],'/m/{modulename}/{do?}', 'ModuleController@entry');
    Route::get('auth', 'AuthController@index');
});
