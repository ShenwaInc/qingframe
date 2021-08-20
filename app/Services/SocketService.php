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
        //更新安装包文件
        $initshell = self::InitShell();
        if (is_error($initshell)) return $initshell;
        //初始化域名白名单
        return CloudService::CloudSocket();
    }

    static function InitShell(){
        if (file_exists(base_path('socket/install_socket.sh'))){
            $installfile = base_path("socket/install_socket.sh");
            $reader = fopen($installfile,'r');
            $socketdata = fread($reader,filesize($installfile));
            fclose($reader);
            if (strexists($socketdata,'{{GOPATH}}')){
                $basepath = str_replace('\\','/',base_path('socket'));
                $socketdata = str_replace('{{GOPATH}}',$basepath,$socketdata);
                $writer = fopen($installfile,'w');
                $complete = fwrite($writer,$socketdata);
                fclose($writer);
                if (!$complete) return error(-1,'SOCKET安装脚本写入失败');
            }
        }
        return true;
    }

}
