<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CacheService
{

    static function key_all() {
        global $_W;
        $caches_all = array(
            'common_params' => array(
                'uniacid' => $_W['uniacid'],
                'uid' => $_W['uid'],
            ),

            'caches' => array(
                'module_info' => array(
                    'key' => 'module_info:%module_name',
                    'group' => 'module',
                ),

                'module_main_info' => array(
                    'key' => 'module_main_info:%module_name',
                    'group' => 'module',
                ),

                'module_setting' => array(
                    'key' => 'module_setting:%module_name:%uniacid',
                    'group' => 'module',
                ),

                'last_account' => array(
                    'key' => 'last_account:%switch:%uid',
                    'group' => '',
                ),

                'last_account_type' => array(
                    'key' => 'last_account_type',
                    'group' => '',
                ),

                'user_modules' => array(
                    'key' => 'user_modules:%uid',
                    'group' => '',
                ),

                'user_accounts' => array(
                    'key' => 'user_accounts:%type:%uid',
                    'group' => '',
                ),

                'unimodules' => array(
                    'key' => 'unimodules:%uniacid',
                    'group' => '',
                ),

                'unimodules_binding' => array(
                    'key' => 'unimodules_binding:%uniacid',
                    'group' => '',
                ),

                'expired_modules' => array(
                    'key' => 'expired_modules',
                    'group' => '',
                ),

                'uni_groups' => array(
                    'key' => 'uni_groups:%groupids',
                    'group' => '',
                ),

                'permission' => array(
                    'key' => 'permission:%uniacid:%uid',
                    'group' => '',
                ),

                'memberinfo' => array(
                    'key' => 'memberinfo:%uid',
                    'group' => '',
                ),

                'statistics' => array(
                    'key' => 'statistics:%uniacid',
                    'group' => '',
                ),

                'uniacid_visit' => array(
                    'key' => 'uniacid_visit:%uniacid:%today',
                    'group' => '',
                ),

                'material_reply' => array(
                    'key' => 'material_reply:%attach_id',
                    'group' => '',
                ),

                'keyword' => array(
                    'key' => 'keyword:%content:%uniacid',
                    'group' => '',
                ),

                'back_days' => array(
                    'key' => 'back_days',
                    'group' => '',
                ),

                'miniapp_version' => array(
                    'key' => 'miniapp_version:%version_id',
                    'group' => '',
                ),

                'site_store_buy' => array(
                    'key' => 'site_store_buy:%type:%uniacid',
                    'group' => '',
                ),

                'proxy_wechatpay_account' => array(
                    'key' => 'proxy_wechatpay_account',
                    'group' => '',
                ),

                'recycle_module' => array(
                    'key' => 'recycle_module',
                    'group' => '',
                ),
                'random' => array(
                    'key' => 'random',
                    'group' => '',
                ),
                'sync_fans_pindex' => array(
                    'key' => 'sync_fans_pindex:%uniacid',
                    'group' => '',
                ),

                'uniaccount' => array(
                    'key' => 'uniaccount:%uniacid',
                    'group' => 'uniaccount',
                ),

                'unisetting' => array(
                    'key' => 'unisetting:%uniacid',
                    'group' => 'uniaccount',
                ),

                'defaultgroupid' => array(
                    'key' => 'defaultgroupid:%uniacid',
                    'group' => 'uniaccount',
                ),

                'uniaccount_type' => array(
                    'key' => 'uniaccount_type:%account_type',
                    'group' => '',
                ),


                'accesstoken' => array(
                    'key' => 'accesstoken:%uniacid',
                    'group' => 'accesstoken',
                ),

                'jsticket' => array(
                    'key' => 'jsticket:%uniacid',
                    'group' => 'accesstoken',
                ),

                'cardticket' => array(
                    'key' => 'cardticket:%uniacid',
                    'group' => 'accesstoken',
                ),


                'accesstoken_key' => array(
                    'key' => 'accesstoken_key:%key',
                    'group' => '',
                ),

                'account_oauth_refreshtoken' => array(
                    'key' => 'account_oauth_refreshtoken:%acid',
                    'group' => '',
                ),

                'account_auth_refreshtoken' => array(
                    'key' => 'account_auth_refreshtoken:%uniacid',
                    'group' => '',
                ),

                'account_tags' => array(
                    'key' => 'account_tags:%uniacid',
                    'group' => '',
                ),

                'unicount' => array(
                    'key' => 'unicount:%uniacid',
                    'group' => '',
                ),

                'checkupgrade' => array(
                    'key' => 'checkupgrade',
                    'group' => '',
                ),

                'cloud_transtoken' => array(
                    'key' => 'cloud_transtoken',
                    'group' => '',
                ),
                'cloud_w7_request_token' => array(
                    'key' => 'cloud_w7_request_token',
                    'group' => '',
                ),

                'upgrade' => array(
                    'key' => 'upgrade',
                    'group' => '',
                ),

                'account_ticket' => array(
                    'key' => 'account_ticket',
                    'group' => '',
                ),

                'oauthaccesstoken' => array(
                    'key' => 'oauthaccesstoken:%acid',
                    'group' => '',
                ),

                'account_component_assesstoken' => array(
                    'key' => 'account_component_assesstoken',
                    'group' => '',
                ),
                'cloud_api' => array(
                    'key' => 'cloud_api:%method',
                    'group' => '',
                ),
                'cloud_ad_uniaccount' => array(
                    'key' => 'cloud_ad_uniaccount:%uniacid',
                    'group' => '',
                ),

                'cloud_ad_uniaccount_list' => array(
                    'key' => 'cloud_ad_uniaccount_list',
                    'group' => '',
                ),

                'cloud_flow_master' => array(
                    'key' => 'cloud_flow_master',
                    'group' => '',
                ),

                'cloud_ad_tags' => array(
                    'key' => 'cloud_ad_tags',
                    'group' => '',
                ),

                'cloud_ad_type_list' => array(
                    'key' => 'cloud_ad_type_list',
                    'group' => '',
                ),

                'cloud_ad_app_list' => array(
                    'key' => 'cloud_ad_app_list:%uniacid',
                    'group' => '',
                ),

                'cloud_ad_app_support_list' => array(
                    'key' => 'cloud_ad_app_support_list',
                    'group' => '',
                ),

                'cloud_ad_site_finance' => array(
                    'key' => 'cloud_ad_site_finance',
                    'group' => '',
                ),

                'cloud_ad_store_notice' => array(
                    'key' => 'cloud_ad_store_notice',
                    'group' => '',
                ),
                'cloud_file_permission_pass' => array(
                    'key' => 'cloud_file_permission_pass',
                    'group' => '',
                ),

                'couponsync' => array(
                    'key' => 'couponsync:%uniacid',
                    'group' => '',
                ),

                'storesync' => array(
                    'key' => 'storesync:%uniacid',
                    'group' => '',
                ),

                'cloud_auth_transfer' => array(
                    'key' => 'cloud_auth_transfer',
                    'group' => '',
                ),

                'modulesetting' => array(
                    'key' => 'modulesetting:%module:%acid',
                    'group' => '',
                ),

                'scan_config' => array(
                    'key' => 'scan_config',
                    'group' => 'scan_file',
                ),

                'scan_file' => array(
                    'key' => 'scan_file',
                    'group' => 'scan_file',
                ),

                'scan_badfile' => array(
                    'key' => 'scan_badfile',
                    'group' => 'scan_file',
                ),

                'bomtree' => array(
                    'key' => 'bomtree',
                    'group' => '',
                ),

                'setting' => array(
                    'key' => 'setting',
                    'group' => '',
                ),

                'stat_todaylock' => array(
                    'key' => 'stat_todaylock:%uniacid',
                    'group' => '',
                ),

                'account_preauthcode' => array(
                    'key' => 'account_preauthcode',
                    'group' => '',
                ),

                'account_auth_accesstoken' => array(
                    'key' => 'account_auth_accesstoken:%key',
                    'group' => '',
                ),

                'usersfields' => array(
                    'key' => 'usersfields',
                    'group' => '',
                ),

                'userbasefields' => array(
                    'key' => 'userbasefields',
                    'group' => '',
                ),

                'system_frame' => array(
                    'key' => 'system_frame:%uniacid',
                    'group' => '',
                ),

                'module_receive_enable' => array(
                    'key' => 'module_receive_enable',
                    'group' => '',
                ),

                'module_entry_call' => array(
                    'key' => 'module_entry_call:%module_name',
                    'group' => '',
                ),

                'system_check' => array(
                    'key' => 'system_check',
                    'group' => '',
                ),

                'delete_visit_ip' => array(
                    'key' => 'delete_visit_ip:%date',
                    'group' => '',
                ),

                'account_web_view_domain' => array(
                    'key' => 'account_web_view_domain:%uniacid',
                    'group' => '',
                ),
            ),
            'groups' => array(
                'uniaccount' => array(
                    'relations' => array('uniaccount', 'unisetting', 'defaultgroupid'),
                ),

                'accesstoken' => array(
                    'relations' => array('accesstoken', 'jsticket', 'cardticket'),
                ),

                'scan_file' => array(
                    'relations' => array('scan_file', 'scan_config', 'scan_badfile'),
                ),

                'module' => array(
                    'relations' => array('module_info', 'module_setting', 'module_main_info'),
                ),
            ),
        );

        return $caches_all;
    }

    static function system_key($cache_key) {
        $cache_key_all = self::key_all();

        $params = array();
        $args = func_get_args();
        if (empty($args[1])) {
            $args[1] = '';
        }
        if (!is_array($args[1])) {
            $cache_key = $cache_key_all['caches'][$cache_key]['key'];
            preg_match_all('/\%([a-zA-Z\_\-0-9]+)/', $cache_key, $matches);
            for ($i = 0; $i < func_num_args() - 1; ++$i) {
                $cache_key = str_replace($matches[0][$i], $args[$i + 1], $cache_key);
            }

            return ':' . $cache_key;
        } else {
            $params = $args[1];
        }

        if (empty($params)) {
            $res = preg_match_all('/([a-zA-Z\_\-0-9]+):/', $cache_key, $matches);
            if ($res) {
                $key = count($matches[1]) > 0 ? $matches[1][0] : $matches[1];
            } else {
                $key = $cache_key;
            }
            if (empty($cache_key_all['caches'][$key])) {
                return error(1, 'Cache' . $key . ' does not exist!');
            } else {
                $cache_info_key = $cache_key_all['caches'][$key]['key'];
                preg_match_all('/\%([a-zA-Z\_\-0-9]+)/', $cache_info_key, $key_params);
                preg_match_all('/\:([a-zA-Z\_\-0-9]+)/', $cache_key, $val_params);

                if (count($key_params[1]) != count($val_params[1])) {
                    foreach ($key_params[1] as $key => $val) {
                        if (in_array($val, array_keys($cache_key_all['common_params']))) {
                            $cache_info_key = str_replace('%' . $val, $cache_key_all['common_params'][$val], $cache_info_key);
                            unset($key_params[1][$key]);
                        }
                    }

                    if (count($key_params[1]) == count($val_params[1])) {
                        $arr = array_combine($key_params[1], $val_params[1]);
                        foreach ($arr as $key => $val) {
                            if (preg_match('/\%' . $key . '/', $cache_info_key)) {
                                $cache_info_key = str_replace('%' . $key, $val, $cache_info_key);
                            }
                        }
                    }

                    if (strexists($cache_info_key, '%')) {
                        return error(1, 'Missing or incorrect cache parameter!');
                    } else {
                        return ':' . $cache_info_key;
                    }
                } else {
                    return ':' . $cache_key;
                }
            }
        }

        $cache_info = $cache_key_all['caches'][$cache_key];
        $cache_common_params = $cache_key_all['common_params'];

        if (empty($cache_info)) {
            return error(2, 'Cache ' . $cache_key . ' does not exist!');
        } else {
            $cache_key = $cache_info['key'];
        }

        foreach ($cache_common_params as $param_name => $param_val) {
            preg_match_all('/\%([a-zA-Z\_\-0-9]+)/', $cache_key, $matches);
            if (in_array($param_name, $matches[1]) && !in_array($param_name, array_keys($params))) {
                $params[$param_name] = $cache_common_params[$param_name];
            }
        }

        if (is_array($params) && !empty($params)) {
            foreach ($params as $key => $param) {
                $cache_key = str_replace('%' . $key, $param, $cache_key);
            }

            if (strexists($cache_key, '%')) {
                return error(1, 'Missing or incorrect cache parameter!');
            }
        }

        $cache_key = ':' . $cache_key;
        if (strlen($cache_key) > 100) {
            trigger_error('Cache name is over the maximum length');
        }

        return $cache_key;
    }

    static function build_module($module_name,$uniacid){
        $cachekey = self::system_key('module_setting', array('module_name' => $module_name, 'uniacid' => $uniacid));
        Cache::forget($cachekey);
    }

    static function build_module_subscribe(){
        global $_W;
        $modules = Module::where('subscribes','!=','')->select(['name', 'subscribes'])->get()->toArray();
        if (empty($modules)) {
            return array();
        }
        $subscribe = array();
        foreach ($modules as $module) {
            $module['subscribes'] = unserialize($module['subscribes']);
            if (!empty($module['subscribes'])) {
                foreach ($module['subscribes'] as $event) {
                    if ($event == 'text') {
                        continue;
                    }
                    $subscribe[$event][] = $module['name'];
                }
            }
        }

        $module_ban = $_W['setting']['module_receive_ban'];
        foreach ($subscribe as $event => $module_group) {
            if (!empty($module_group)) {
                foreach ($module_group as $index => $module) {
                    if (!empty($module_ban[$module])) {
                        unset($subscribe[$event][$index]);
                    }
                }
            }
        }
        Cache::put(self::system_key('module_receive_enable'),$subscribe,7*86400);
        return $subscribe;
    }

    static function build_member($uid){
        $uid = intval($uid);
        Cache::forget(self::system_key('memberinfo', array('uid' => $uid)));
        return true;
    }

    static function flush(){
        global $_W;
        //清空系统缓存
        Cache::flush();
        //更新模板缓存
        FileService::rmdirs(storage_path('framework/tpls/'), true);
        //更新路由缓存
        Artisan::call('route:clear');
        //更新视图缓存
        Artisan::call('view:clear');
        //更新配置缓存
        Artisan::call('config:clear');
        //重建系统配置
        SettingService::Load();
        if ($_W['uniacid']){
            SettingService::uni_load('',$_W['uniacid']);
        }
        return true;
    }

}
