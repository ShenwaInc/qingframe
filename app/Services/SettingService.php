<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService{

    static function Load($key = '', $nocache=false) {
        global $_W;
        $cachekey = 'larapp:setting';
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

    static function uni_load(){

    }

}
