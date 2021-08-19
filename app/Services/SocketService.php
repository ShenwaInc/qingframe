<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SocketService
{

    static function Initializer(){
        //插入服务组件列表
        DB::table('gxswa_cloud')->updateOrInsert(array(
            'identity'=>'laravel_whotalk_socket'
        ),array(
            'name'=>'自建SOCKET源码包',
            'modulename'=>'',
            'type'=>3,
            'logo'=>'https://shenwahuanan.oss-cn-shenzhen.aliyuncs.com/images/4/2021/08/gdSI484HDp4pXxHshPxXpShdi3XHP8.png',
            'website'=>'https://www.whotalk.com.cn/',
            'rootpath'=>'socket/',
            'version'=>'1.0.2',
            'online'=>'',
            'releasedate'=>2021081905,
            'addtime'=>TIMESTAMP,
            'dateline'=>TIMESTAMP
        ));
        //初始化域名白名单
        return CloudService::CloudSocket();
    }

}
