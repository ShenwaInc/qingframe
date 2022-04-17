<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingService{

    static function Load($key = '', $nocache=false) {
        global $_W;
        $cachekey = CacheService::system_key('setting');
        if($nocache){
            Cache::forget($cachekey);
            $settings = array();
        }else{
            //从缓存中读取
            $settings = Cache::get($cachekey, array());
        }
        if (empty($settings)) {
            //如果找不到缓存则从数据库中读取
            $_settings = Setting::get()->keyBy('key');
            if (!empty($_settings)) {
                foreach ($_settings as $k => $v) {
                    $settings[$k] = $v['value'] ? unserialize($v['value']) : array();
                }
            }
            if (empty($key)){
                //写入缓存
                Cache::put($cachekey, $settings, 86400*7);
            }
            unset($_settings);
        }
        $_W['setting'] = array_merge($settings, (array)$_W['setting']);
        if (!empty($key)) {
            return array($key => $settings[$key]);
        } else {
            return $settings;
        }
    }

    static function uni_load($name = '', $uniacid = 0){
        global $_W;
        $uniacid = empty($uniacid) ? $_W['uniacid'] : $uniacid;
        $cachekey = CacheService::system_key('unisetting', array('uniacid' => $uniacid));
        $unisetting = Cache::get($cachekey,array());
        if (empty($unisetting) || ($name == 'remote' && empty($unisetting['remote']))) {
            $unisetting = Setting::getUni($uniacid);
            if (!empty($unisetting)) {
                $serialize = array('site_info', 'stat', 'oauth', 'passport', 'notify',
                    'creditnames', 'default_message', 'creditbehaviors', 'payment',
                    'recharge', 'tplnotice', 'mcplugin', 'statistics', 'bind_domain', 'remote');
                foreach ($unisetting as $key => &$row) {
                    if (in_array($key, $serialize)) {
                        $row = !empty($row) ? (array)unserialize($row) : array();
                    }
                }
            } else {
                $unisetting = array();
            }
            Cache::put($cachekey, $unisetting,86400*7);
        }
        if (empty($unisetting)) {
            return array();
        }
        if (empty($name)) {
            return $unisetting;
        }
        if (!is_array($name)) {
            $name = array($name);
        }
        return array_elements($name, $unisetting);
    }

    static function uni_save($uniacid,$key,$value){
        $complete = DB::table('uni_settings')->updateOrInsert(['uniacid'=>$uniacid],[$key=>$value]);
        if ($complete){
            $cachekey = CacheService::system_key('unisetting', array('uniacid' => $uniacid));
            Cache::forget($cachekey);
        }
        return $complete;
    }

    static function check_php_ext($extension) {
        return extension_loaded($extension);
    }

    static function Save($data = '', $key = ''){
        if (empty($data) && empty($key)) {
            return FALSE;
        }
        if (is_array($data) && empty($key)) {
            $record = array();
            $keys = array_keys($data);
            foreach ($data as $key => $value) {
                $record[] = ['key'=>$key,'value'=>serialize($value)];
            }
            if (!empty($record)) {
                DB::table('core_settings')->whereIn('key',$keys)->delete();
                $return = DB::table('core_settings')->insert($record);
            }
        } else {
            $return = DB::table('core_settings')->updateOrInsert(
                array('key'=>$key),
                array('value'=>serialize($data))
            );
        }
        $cachekey = CacheService::system_key('setting');
        Cache::forget($cachekey);
        return $return;
    }
}
