<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CloudService
{

    static $cloudapi = 'https://chat.gxit.org/app/index.php?i=4&c=entry&m=swa_supersale&do=api';
    static $cloudactive = 'https://chat.gxit.org/app/index.php?i=4&c=entry&m=swa_supersale&do=app&r=whotalkcloud.active&siteroot=';
    static $apilist = array('getcom'=>'cloud.vendor','rmcom'=>'cloud.vendor.remove','require'=>'cloud.install','structure'=>'cloud.structure','upgrade'=>'cloud.makepatch');
    static $vendors = array('aliyun'=>'阿里短信SDK','aop'=>'支付宝支付SDK','wxpayv3'=>'微信支付SDK','tim'=>'接口签名验证工具','getui'=>'APP推送SDK');
    static $identity = 'swa_framework_laravel';

    static function ComExists($component){
        return is_dir(self::com_path("{$component}/"));
    }

    static function LoadCom($component){
        if (!self::ComExists($component)) return error(-1,'未安装对应组件:'.self::$vendors[$component]);
        $mainclass = array('aliyun'=>'SmsDemo','aop'=>'AopClient','wxpayv3'=>'WxPayApi','tim'=>'TLSSigAPIv2','getui'=>'IGeTui');
        if (class_exists($mainclass[$component])) return true;
        $compath = self::com_path();
        switch ($component){
            case 'wxpayv3' :
                require_once "{$compath}wxpayv3/WxPay.Api.php";
                require_once "{$compath}wxpayv3/WxPay.Data.php";
                break;
            case 'aop' :
                require_once "{$compath}aop/AopClient.php";
                require_once "{$compath}aop/request/AlipayTradeQueryRequest.php";
                break;
            case 'aliyun' :
                require_once "{$compath}aliyun/src/dysmsapi.php";
                require_once "{$compath}aliyun/vendor/autoload.php";
                break;
            case 'tim' :
                include_once "{$compath}tim/TLSSigAPIv2.php";
                break;
            case 'getui' :
                require_once "{$compath}getui/autoload.php";
                break;
            default :
                break;
        }
        return true;
    }

    static function RequireCom(){
        $hasCom = self::ComExists('aliyun');
        if ($hasCom){
            return self::CloudUpdate('swa_whotalk_componet',self::com_path());
        }else{
            $requirecom = self::CloudRequire('swa_whotalk_componet',self::com_path());
            if (!is_error($requirecom)){
                //组件包下载标记
                DB::table('gxswa_cloud')->updateOrInsert(array(
                        'identity'=>'swa_whotalk_componet'
                    ),array(
                        'name'=>'Whotalk独立版组件包',
                        'modulename'=>'',
                        'type'=>2,
                        'logo'=>'https://shenwahuanan.oss-cn-shenzhen.aliyuncs.com/images/4/2021/08/Mpar00P5PjJPrxAW1FWCP3CPz87qjc.png',
                        'website'=>'https://www.whotalk.com.cn/',
                        'version'=>'1.0.4',
                        'releasedate'=>2021090401,
                        'rootpath'=>'',
                        'online'=>'',
                        'addtime'=>TIMESTAMP,
                        'dateline'=>TIMESTAMP
                    ));
            }
            return $requirecom;
        }
    }

    static function RequireModule($identity,$path='addons'){
        $requirename = $identity;
        if (!strexists($identity,'_')){
            $requirename = "laravel_module_{$identity}";
        }
        $targetpath = base_path("public/{$path}/{$identity}");
        $from = 'local';
        if (!is_dir($targetpath)){
            $result = self::CloudRequire($requirename,$targetpath);
            if(is_error($result)) return $result;
            $from = 'cloud';
        }
        //进入模块安装流程
        return ModuleService::install($identity,$path,$from);
    }

    static function com_path($path=""){
        global $_W;
        if (!isset($_W['com_path'])){
            $compath = substr(sha1($_W['config']['setting']['authkey']."-".$_W['config']['site']['id']),5,6);
            $_W['com_path'] = base_path("bootstrap/com{$compath}/");
        }
        return $_W['com_path'] . $path;
    }

    static function MoveDir($oldDir, $aimDir, $overWrite = false){
        $aimDir = str_replace('', '/', $aimDir);
        $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
        $oldDir = str_replace('', '/', $oldDir);
        $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
        if (!is_dir($oldDir)) {
            return false;
        }
        if (!file_exists($aimDir)) {
            FileService::mkdirs($aimDir);
        }
        @ $dirHandle = opendir($oldDir);
        if (!$dirHandle) {
            return false;
        }
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($oldDir . $file)) {
                FileService::file_move($oldDir . $file,$aimDir . $file);
            } else {
                self::MoveDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
        }
        closedir($dirHandle);
        return FileService::rmdirs($oldDir);
    }

    static function CloudRequire($identity,$targetpath,$patch=''){
        $data = array(
            'identity'=>$identity,
            'fp'=>self::$identity
        );
        $zipcontent = self::CloudApi('require',$data,true);
        if (is_error($zipcontent)) return $zipcontent;
        if (!$zipcontent) return error(-1,'安装包提取失败');
        if (!$patch){
            $patch = base_path("storage/patch/");
        }
        if (!is_dir($patch)){
            FileService::mkdirs($patch);
        }
        $filename = FileService::file_random_name($patch,'zip');
        if (false == file_put_contents($patch.$filename, $zipcontent)) {
            return error(-1,'安装包解压失败：权限不足');
        }

        $zip = new \ZipArchive();
        $openRes = $zip->open($patch.$filename);
        if ($openRes === TRUE) {
            $zip->extractTo($targetpath);
            $zip->close();
            //删除补丁包
            @unlink($patch.$filename);
        }else{
            @unlink($patch.$filename);
            return error(-1,'安装包解压失败，请重试');
        }

        //如果解压包内嵌则操作搬移
        if (is_dir($targetpath.$identity.'/')){
            if (is_dir($patch.$identity)){
                FileService::rmdirs($patch.$identity);
            }
            self::MoveDir($targetpath.$identity,$patch);
            self::CloudPatch($targetpath,$patch.$identity.'/',true);
            FileService::rmdirs($patch.$identity, true);
        }
        return true;
    }

    static function CloudUpdate($identity,$targetpath,$patch=''){
        $data = array(
            'identity'=>$identity,
            'fp'=>self::$identity
        );
        $ugradeinfo = self::CloudApi('structure',$data);
        if (is_error($ugradeinfo)) return $ugradeinfo;
        $structures = json_decode(base64_decode($ugradeinfo['structure']), true);
        $difference = self::CloudCompare($structures,$targetpath);
        if (empty($difference)) return true;
        $data = array(
            'identity'=>$identity,
            'fp'=>self::$identity,
            'releasedate'=>$ugradeinfo['releasedate'],
            'difference'=>base64_encode(json_encode($difference))
        );
        $zipcontent = self::CloudApi('upgrade',$data,true);
        if (is_error($zipcontent)) return $zipcontent;
        if (!$patch){
            $patch = base_path('storage/patch/');
        }
        if (!is_dir($patch)){
            FileService::mkdirs($patch);
        }
        $filename = FileService::file_random_name($patch,'zip');
        $fullname = $patch.$filename;
        if (false == file_put_contents($fullname, $zipcontent)) {
            return error(-1,'补丁下载失败：权限不足');
        }
        $patchpath = $patch.$identity.$ugradeinfo['releasedate'].'/';
        if (is_dir($patchpath)){
            FileService::rmdirs($patchpath);
        }
        $zip = new \ZipArchive();
        $openRes = $zip->open($fullname);
        if ($openRes === TRUE) {
            $zip->extractTo($patchpath);
            $zip->close();
            @unlink($fullname);
        }else{
            @unlink($fullname);
            return error(-1,'补丁解压失败，请重试');
        }
        //5、将补丁文件更新到本地
        self::CloudPatch($targetpath,$patchpath,true);
        FileService::rmdirs($patchpath);
        return true;
    }

    static function CloudCompare($structures=array(),$target='',$basedir=''){
        if (empty($structures) || !$target) return false;
        if (!is_dir($target)) return  $structures;
        $difference = array();
        foreach ($structures as $item){
            if (is_array($item)){
                $folder = $basedir.$item[0];
                $dirdiff = array();
                if (!is_dir($target.$folder)){
                    $dirdiff = $item;
                }else{
                    $structure = self::CloudCompare($item[1],$target,$folder.'/');
                    if (!empty($structure)){
                        $dirdiff = array($item[0],$structure);
                    }
                }
                if (!empty($dirdiff)){
                    $difference[] = $dirdiff;
                }
            }else{
                $fileinfo = explode('|',$item);
                $filepath = $basedir.$fileinfo[0];
                if (!file_exists($target.$filepath)){
                    $difference[] = $item;
                }else{
                    $md5 = md5_file($target.$filepath);
                    $hash = substr($md5,0,4).substr($md5,-4);
                    if($hash!=$fileinfo[1]){
                        $difference[] = $item;
                    }
                }
            }
        }
        return $difference;
    }

    static function CloudPatch($target,$source,$overwrite=false){
        if (!$target || !$source) return false;
        if (!is_dir($target)){
            if ($overwrite){
                FileService::mkdirs($target);
            }else{
                return false;
            }
        }
        if (!is_dir($source)) return  false;
        $handle = dir($source);
        if ($dh = opendir($source)){
            while ($entry = $handle->read()) {
                if ($entry!= "." && $entry!=".." && $entry!=".svn" && $entry!=".git"){
                    $new = $source.$entry;
                    if(is_dir($new)) {
                        if (!is_dir($target.$entry)){
                            FileService::mkdirs($target.$entry.'/');
                        }
                        self::CloudPatch($target.$entry.'/',$source.$entry.'/',$overwrite);
                    }else{
                        if(file_exists($target.$entry)){
                            if($overwrite){
                                @unlink($target.$entry);
                            }else{
                                if (md5_file($target.$entry)==md5_file($new)) continue;
                                @unlink($target.$entry);
                            }
                        }
                        @copy($new, $target.$entry);
                    }
                }
            }
            closedir($dh);
        }
        return true;
    }

    static function CloudApi($apiname,$data=array(),$return=false){
        global $_W;
        if (!$data['appsecret']) $data['appsecret'] = self::AppSecret();
        if (!isset($data['r'])){
            $data['r'] = self::$apilist[$apiname];
        }
        if (!isset($data['fp'])){
            $data['fp'] = self::$identity;
        }
        $data['t'] = TIMESTAMP;
        $data['siteroot'] = $_W['siteroot'];
        $data['siteid'] = $_W['config']['site']['id'];
        $data['sign'] = self::GetSignature($data['appsecret'],$data);
        $res = HttpService::ihttp_post(self::$cloudapi,$data);
        if (is_error($res)) return $res;
        $result = json_decode($res['content'],true);
        if(empty($result) && $return) return $res['content'];
        if (isset($result['message']) && isset($result['type'])){
            if ($result['type']!='success' && !is_array($result['message']) && !$result['redirect']){
                return error(-1,$result['message']);
            }
        }
        return $result;
    }

    static function CloudActive(){
        global $_W;
        $default = array('state'=>'已授权','siteid'=>0,'siteroot'=>$_W['siteroot'],'expiretime'=>0,'status'=>0);
        $cachekey = CacheService::system_key('Whotalk:Authorize:Active');
        $authorize = Cache::get($cachekey,$default);
        $res = self::CloudApi('',array('r'=>'whotalkcloud.active.state'));
        if (is_error($res)){
            $authorize['state'] = $res['message'];
            return $authorize;
        }
        if (!isset($res['site']) || !isset($res['authorize'])){
            $authorize['state'] = '授权状态查询失败';
            return $authorize;
        }

        $authorize['siteid'] = $res['site']['id'];
        if ($res['site']['status']==1 && $res['authorize']['status']==1){
            $authorize['expiretime'] = $res['authorize']['expiretime'];
            if ($res['authorize']['expiretime']>0 && $res['authorize']['expiretime']<TIMESTAMP){
                $authorize['status'] = 2;
                $authorize['state'] = '授权已到期';
            }else{
                $authorize['status'] = 1;
            }
        }

        Cache::put($cachekey, $authorize, 3600);

        return $authorize;
    }

    static function CloudSocket($domain='',$require=0){
        $domains = array("host"=>array());
        $domainfile = base_path("socket/composer.json");
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

    static function CloudEnv($search, $replace){
        if (empty($search) || empty($replace)) return false;
        $envfile = base_path(".env");
        $reader = fopen($envfile,'r');
        $envdata = fread($reader,filesize($envfile));
        fclose($reader);
        $envdata = str_replace($search, $replace,$envdata);
        $writer = fopen($envfile,'w');
        $complete = fwrite($writer,$envdata);
        fclose($writer);
        return $complete;
    }

    static function AppSecret(){
        global $_W;
        return sha1($_W['config']['setting']['authkey'].'-'.$_W['siteroot'].'-'.$_W['config']['site']['id']);
    }

    static function GetSignature($appsecret='',$data=array()){
        if (!$appsecret) return false;
        unset($data['sign'],$data['appsecret']);
        ksort($data);
        $string = base64_encode(http_build_query($data)).$appsecret;
        return md5($string);
    }

}
