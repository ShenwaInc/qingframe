<?php

Route::group(['prefix' => 'app/{uniacid}','namespace' => 'App'],function (){
    Route::get('auth', 'AuthController@index');
});
