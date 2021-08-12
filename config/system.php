<?php

return [
    'version'=>env('APP_VERSION','3.0.47'),
    'release'=>env('APP_RELEASE','2021081902'),
    'setting'=>array(
        'charset'=>'utf-8',
        'cache'=>'mysql',
        'timezone'=>'Asia/Shanghai',
        'memory_limit'=>env('INI_MEMORY','256M'),
        'filemode'=>'0644',
        'authkey'=>env('APP_AUTHKEY',''),
        'founder'=>1,
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
    'upload'=>array(
        'image'=>array('extentions'=>['gif', 'jpg', 'jpeg', 'png'],'limit'=>5000),
        'attachdir'=>'attachment',
        'audio'=>array('extentions'=>array('mp3'),'limit'=>5000)
    )
];
