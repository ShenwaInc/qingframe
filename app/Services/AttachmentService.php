<?php


namespace App\Services;


class AttachmentService
{
    public static $AliossClient = null;
    public static $Cosv5 = null;

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
            if ($_W['setting']['remote']['type'] == 0) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 1) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 2) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 3) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
            }
        }
        return $attach_url;
    }

    public static function initOss(){
        if (!self::$AliossClient){
            include_once app_path('Utils/alioss/src/autoload.php');
            self::$AliossClient = true;
        }
        return true;
    }

    public static function initCos(){
        if (!self::$Cosv5){
            include_once app_path('Utils/cosv5/index.php');
            self::$Cosv5 = true;
        }
        return true;
    }

    static function alioss_buctkets($key, $secret) {
        self::initOss();
        $url = 'http://oss-cn-beijing.aliyuncs.com';
        try {
            $ossClient = new \OSS\OssClient($key, $secret, $url);
        } catch(\OSS\Core\OssException $e) {
            return error(1, $e->getMessage());
        }
        try{
            $bucketlistinfo = $ossClient->listBuckets();
        } catch(OSS\OSS_Exception $e) {
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
