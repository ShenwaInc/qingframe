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
            'rootpath'=>'swasocket/',
            'version'=>'1.0.4',
            'online'=>'',
            'releasedate'=>2021091201,
            'addtime'=>TIMESTAMP,
            'dateline'=>TIMESTAMP
        ));
        //更新安装包文件
        $initshell = self::InitShell();
        if (is_error($initshell)) return $initshell;
        //初始化域名白名单
        return self::SocketAuthorize();
    }

    static function Upgrade(){
        if(is_dir(base_path('socket'))){
            FileService::rmdirs(base_path('socket'));
        }
    }

    static function InitShell(){
        if (file_exists(base_path('swasocket/install_socket.sh'))){
            $installfile = base_path("swasocket/install_socket.sh");
            $reader = fopen($installfile,'r');
            $socketdata = fread($reader,filesize($installfile));
            fclose($reader);
            if (strexists($socketdata,'{{GOPATH}}')){
                $basepath = str_replace('\\','/',base_path('swasocket'));
                $socketdata = str_replace('{{GOPATH}}',$basepath,$socketdata);
                $writer = fopen($installfile,'w');
                $complete = fwrite($writer,$socketdata);
                fclose($writer);
                if (!$complete) return error(-1,'SOCKET安装脚本写入失败');
            }
        }
        return true;
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
