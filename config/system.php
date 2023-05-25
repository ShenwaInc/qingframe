<?php

return [
    'name'=>env('APP_NAME'),
    'identity'=>env('APP_IDENTITY', 'swa_framework_laravel'),
    'defaultmodule'=>env('APP_MODULE','whotalk'),
    'setting'=>array(
        'charset'=>'utf-8',
        'cache'=>'mysql',
        'timezone'=>env('APP_TIMEZONE','Asia/Shanghai'),
        'memory_limit'=>env('INI_MEMORY','256M'),
        'filemode'=>'0644',
        'authkey'=>env('APP_AUTHKEY',''),
        'founder'=>env('APP_FOUNDER',1),
        'development'=>0,
        'referrer'=>0,
        'memcache'=>array(
            'server'=>'',
            'port'=>11211,
            'pconnect'=>1,
            'timeout'=>30,
            'session'=>1
        ),
        'proxy'=>['host'=>'','auth'=>'']
    ),
    'site'=>array(
        'id'=>env('APP_SITEID',0),
        'key'=>env('APP_AUTHKEY',''),
        'name'=>env('APP_NAME', 'qingwork')
    ),
    'upload'=>array(
        'image'=>array('extentions'=>['gif', 'jpg', 'jpeg', 'png'],'limit'=>5000),
        'attachdir'=>'storage',
        'media'=>array('extentions'=>array('mp3','mp4','mov','avi','rm','rmvb','m3u8','amr','acc','3gp','vod'),'limit'=>5000)
    )
];
