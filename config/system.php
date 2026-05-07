<?php

return [
    'name'=>env('APP_NAME'),
    'url'=>env('APP_URL'),
    'identity'=>env('APP_IDENTITY', 'swa_framework_laravel'),
    'version'=>env("APP_VERSION",'1.0.0'),
    'versionCode'=>env('APP_RELEASE',2026050701),
    'defaultModule'=>env('APP_MODULE',''),
    'debugMode'=>(bool)env('APP_DEBUG', false),
    'setting'=>array(
        'charset'=>'utf-8',
        'cache'=>'mysql',
        'timezone'=>env('APP_TIMEZONE','Asia/Shanghai'),
        'memory_limit'=>env('INI_MEMORY','256M'),
        'filemode'=>'0644',
        'authkey'=>env('APP_AUTHKEY',''),
        'founder'=>env('APP_FOUNDER',1),
        'development'=>env('APP_DEVELOPMENT', 0),
        'referrer'=>0,
        'memcache'=>array(
            'server'=>'',
            'port'=>11211,
            'pconnect'=>1,
            'timeout'=>30,
            'session'=>1
        ),
        'proxy'=>['host'=>'','auth'=>''],
        'force_https'=>env('APP_FORCE_HTTPS', 0)
    ),
    'site'=>array(
        'id'=>env('APP_SITEID',0),
        'key'=>env('APP_AUTHKEY',''),
        'name'=>env('APP_NAME', 'qingwork'),
        'uniacid'=>env('APP_UNIACID', 0)
    ),
    'upload'=>array(
        'image'=>array('extentions'=>['gif', 'jpg', 'jpeg', 'png'],'limit'=>5000),
        'attachdir'=>'storage',
        'media'=>array('extentions'=>array('mp3','mp4','mov','avi','rm','rmvb','m3u8','amr','acc','3gp','vod'),'limit'=>5000)
    ),
    'cdn'=>[
        'header_column'=>env('CDN_REAL_IP_HEADER', 'x-forwarded-for')
    ]
];
