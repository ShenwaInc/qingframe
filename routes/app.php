<?php

Route::group(['namespace' => 'App','middleware' => [\App\Http\Middleware\ModulePermission::class]],function (){
    Route::get('{uniacid}/{module}', 'ModuleController@index');
});
