<?php

Route::group(['prefix' => 'app','namespace' => 'App'],function (){
    Route::match(['get', 'post'],'/{uniacid}/m/{modulename}/{do?}', 'ModuleController@entry')->where('uniacid','[0-9]+');
    Route::get('auth', 'AuthController@index');
});
