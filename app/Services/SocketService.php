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
            'name'=>'独立SOCKET服务',
            'modulename'=>'',
            'type'=>3,
            'logo'=>'https://shenwahuanan.oss-cn-shenzhen.aliyuncs.com/images/4/2021/08/gdSI484HDp4pXxHshPxXpShdi3XHP8.png',
            'website'=>url('console/setting/socket'),
            'rootpath'=>'swasocket/',
            'version'=>'1.0.5',
            'online'=>'',
            'releasedate'=>2021101028,
            'addtime'=>TIMESTAMP,
            'dateline'=>TIMESTAMP
        ));
        //初始化域名白名单
        return self::SocketAuthorize();
    }

    static function Upgrade(){
        if(is_dir(base_path('socket'))){
            FileService::rmdirs(base_path('socket'));
        }
        DB::table('gxswa_cloud')->where('identity','=','laravel_whotalk_socket')->update(array('name'=>'独立SOCKET服务','website'=>url('console/setting/socket')));
    }

    static function SocketAuthorize($domain='',$require=0){
        $domains = array("host"=>array());
        $domainfile = base_path("swasocket/composer.json");
        if (file_exists($domainfile)){
            $reader = fopen($domainfile,'r');
            $domaintext = fread($reader,filesize($domainfile));
            fclose($reader);
            if (!empty($domaintext)){
                $domains = @json_decode($domaintext, true);
            }
        }
        if (empty($domain)){
            if ($require==1) return $domains['host'];
            $domain = $_SERVER['HTTP_HOST'];
        }
        if ($require==2){
            $domains['host'] = array();
        }
        if (!in_array($domain,$domains['host'])){
            $domains['host'][] = $domain;
            $writer = fopen($domainfile,'w');
            $complete = fwrite($writer,json_encode($domains));
            fclose($writer);
            return $complete;
        }
        return true;
    }

}
