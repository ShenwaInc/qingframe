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
            $settings = Cache::get($cachekey, array());
        }
        if (empty($settings)) {
            $_settings = Setting::get()->keyBy('key');
            if (!empty($_settings)) {
                foreach ($_settings as $k => &$v) {
                    $settings[$k] = $v['value'] ? unserialize($v['value']) : array();
                }
            }
            Cache::put($cachekey, $settings, 86400*7);
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
