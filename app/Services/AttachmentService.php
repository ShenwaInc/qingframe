<?php


namespace App\Services;


use Illuminate\Support\Facades\Log;
use JohnLui\AliyunOSS;
use Qcloud\Cos\Client;

class AttachmentService
{

    static function InitOss($setting=array()){
        global $_W;
        if (empty($setting)) $setting = $_W['setting']['remote']['alioss'];
        if (!isset($_W['services']['alioss'])){
            if (empty($setting) || !$setting['key'] || !$setting['secret'] || !$setting['bucket']){
                return error(-1,'阿里云存储配置不正确');
            }
            $internal = $setting['internal'] ? true : false;
            $city = self::alioss_city($setting['bucket'],$setting['city']);

            try {
                $ossClient = AliyunOSS::boot(
                    $city,
                    '经典网络',
                    $internal,
                    $setting['key'],
                    $setting['secret']
                );
                $_W['services']['alioss'] = $ossClient;
            }catch (\Exception $exception){
                Log::error("remote{$_W['uniacid']}alioss_error",error(-1,$exception->getMessage()));
                return error(-1,'OSS初始化失败');
            }
        }
        return $_W['services']['alioss'];
    }

    static function InitCos($remote=array()){
        global $_W;
        if (empty($remote)) $remote = $_W['setting']['remote']['cos'];
        if (!isset($_W['services']['cosv5'])){
            if (empty($remote) || empty($remote['appid']) || empty($remote['secretid']) || empty($remote['secretkey']) || empty($remote['local'])){
                return error(-1,'腾讯云存储配置未完善');
            }
            try {
                $cosClient = new Client(array('region' => $remote['local'],
                    'credentials'=> array(
                        'appId' => $remote['appid'],
                        'secretId'    => $remote['secretid'],
                        'secretKey' => $remote['secretkey'])));
                $_W['services']['cosv5'] = $cosClient;
            }catch (\Exception $exception){
                return error(-1,$exception->getMessage());
            }
        }
        return $_W['services']['cosv5'];
    }

    static function cos_upload($key,$setting=array(),$test=false){
        global $_W;
        if ($_W['setting']['remote']['type']!=4 && !$test) return error(-1,'未开启腾讯云存储');
        $filePath = ATTACHMENT_ROOT . $key;
        if (!file_exists($filePath)) return error(-1,'该文件不存在');
        if (empty($setting)) $setting = $_W['setting']['remote']['alioss'];
        if (empty($setting['bucket'])) return error(-1,'Bucket名称不正确');
        $cosClient = self::InitCos($setting);
        if (is_error($cosClient)) return $cosClient;
        try {
            $result = $cosClient->putObject(array(
                'Bucket' => $setting['bucket'],
                'Key' =>  $key,
                'Body' => fopen($filePath, 'rb'),
                'ServerSideEncryption' => 'AES256'));
        }catch (\Exception $exception){
            return error(-1,$exception->getMessage());
        }
        return $result;
    }

    static function alioss_city($bucket,$default=''){
        if (!empty($default)) return $default;
        $citys = array('hangzhou'=>'杭州','shanghai'=>'上海','qingdao'=>'青岛','beijing'=>'北京','zhangjiakou'=>'张家口','shenzhen'=>'深圳','hongkong'=>'香港','west-1'=>'硅谷','us-east-1'=>'弗吉尼亚','southeast-1'=>'新加坡','southeast-2'=>'悉尼','northeast-1'=>'日本','central-1'=>'法兰克福','me-east-1'=>'迪拜');
        foreach ($citys as $key=>$city){
            if (strexists($bucket,$key)){
                return $city;
            }
        }
        return '';
    }

    public static function SetAttachUrl() {
        global $_W;
        if(empty($_W['setting']['remote_complete_info'])){
            $_W['setting']['remote_complete_info'] = $_W['setting']['remote'];
        }
        if (!empty($_W['uniacid'])) {
            $uni_remote_setting = SettingService::uni_load('remote');
            if (!empty($uni_remote_setting['remote']['type'])) {
                $_W['setting']['remote'] = $uni_remote_setting['remote'];
            }
        }
        $attach_url = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
        if (!empty($_W['setting']['remote']['type'])) {
            if ($_W['setting']['remote']['type'] == 1) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 2) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 3) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 4) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
            }
        }
        return $attach_url;
    }

    static function alioss_upload($ossKey, $filePath, $setting=array(), $test=false){
        global $_W;
        if ($_W['setting']['remote']['type']!=2 && !$test) return error(-1,'未开启阿里云存储');
        if (!file_exists($filePath)) return error(-1,'该文件不存在');
        if (empty($setting)) $setting = $_W['setting']['remote']['alioss'];
        $bucket = explode('/',$setting['bucket'])[1];
        if (empty($bucket)) return error(-1,'Bucket名称不正确');
        $ossClient = self::InitOss($setting);
        if (is_error($ossClient)) return $ossClient;
        $ossClient->setBucket($bucket);
        return $ossClient->uploadFile($ossKey, $filePath);
    }

    static function alioss_buctkets($key, $secret) {
        $loadoss = CloudService::LoadCom('alioss');
        if (is_error($loadoss)) return $loadoss;
        $url = 'http://oss-cn-beijing.aliyuncs.com';
        try {
            $ossClient = new \OSS\OssClient($key, $secret, $url);
        } catch(\OSS\Core\OssException $e) {
            return error(1, $e->getMessage());
        }
        try{
            $bucketlistinfo = $ossClient->listBuckets();
        } catch(\OSS\OSS_Exception $e) {
            return error(1, $e->getMessage());
        }
        $bucketlistinfo = $bucketlistinfo->getBucketList();
        $bucketlist = array();
        foreach ($bucketlistinfo as &$bucket) {
            $bucketlist[$bucket->getName()] = array('name' => $bucket->getName(), 'location' => $bucket->getLocation());
        }
        return $bucketlist;
    }

}
