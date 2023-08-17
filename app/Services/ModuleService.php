<?php


namespace App\Services;

use App\Models\Account;
use App\Models\Module;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ModuleService
{

    static function getManifest($identity,$path='addons'){
        $manifestFile = base_path("public/$path/$identity/manifest.json");
        if(!file_exists($manifestFile)) return error(-1,'Unable to parse module installation package');
        $JSON = file_get_contents($manifestFile);
        $result = json_decode($JSON, true);
        if (empty($result) || !isset($result['application'])) return error(-1,'invalid application package');
        $result['installed'] = false;
        if (DB::table('modules')->where('name', $identity)->exists()){
            $result['installed'] = true;
        }
        return $result;
    }

    static function install($identity,$path='addons',$from='cloud'){
        $startTime = time();
        $ManiFest = self::getManifest($identity, $path);
        if (is_error($ManiFest)) return $ManiFest;
        if ($ManiFest['installed']) return true;
        $application = $ManiFest['application'];
        MSService::TerminalSend(['mode'=>'info', 'message'=>"即将安装应用模块【{$application['name']}^{$application['version']}】"]);
        //执行安装脚本
        if (!empty($ManiFest['install'])){
            try {
                MSService::TerminalSend(['mode'=>'info', 'message'=>"正在运行应用安装脚本..."]);
                script_run($ManiFest['install'], public_path("{$path}/{$identity}/"));
            } catch (\Exception $exception){
                return error(-1,__('installFailed', ['reason'=>DEVELOPMENT?$exception->getMessage():__('installFailedRender')]));
            }
        }
        //写入模块数据表
        $subscribes = $ManiFest['subscribes'] ?: array();
        $handles = $ManiFest['handles'] ?: array();
        $module = self::ModuleData($application,$subscribes,$handles);
        if (!empty($ManiFest['permissions'])){
            $module['permissions'] = serialize($ManiFest['permissions']);
        }
        $module['from'] = $from;
        if (!DB::table('modules')->insert($module)){
            return error(-1,'Unable to parse module installation package');
        }
        if (!empty($ManiFest['servers'])){
            try {
                $MSS = new MSService();
                $MSS->checkRequire($ManiFest['servers']);
            }catch (\Exception $exception){
                return error(-1,__('installFailed', ['reason'=>$exception->getMessage()]));
            }
        }
        $stopTime = time();
        $timeOut = $from=='cloud' ? '' : __('takesTime', ['time'=>$stopTime-$startTime]);
        MSService::TerminalSend(['mode'=>'success', 'message'=>__('installSuccessfully').$timeOut], true);
        //写入组件表
        if ($from=='cloud'){
            $comdata = array(
                'name'=>$module['title'],
                'modulename'=>$identity,
                'type'=>1,
                'logo'=>$module['logo'],
                'website'=>$module['url'],
                'version'=>$application['version'],
                'releasedate'=>$application['releasedate'],
                'updatetime'=>TIMESTAMP,
                'addtime'=>TIMESTAMP,
                'dateline'=>TIMESTAMP
            );
            DB::table('gxswa_cloud')->updateOrInsert(array(
                'identity'=>self::SysPrefix($identity),
                'rootpath'=>"public/$path/$identity/"
            ),$comdata);
        }
        return true;
    }

    static function installCheck($identity){
        $ManiFest = self::getManifest($identity);
        if (is_error($ManiFest)) return $ManiFest;
        if (!$ManiFest['installed']) return error(-3,__('applicationNotInstall'));
        return $ManiFest;
    }

    /**
     * 获取模块列表（已安装）
     * @param int $recycle 0正常1停用2删除-1所有
     * @param int $issystem 0普通应用1系统应用
     * @return array 模块列表
    */
    static function moduleList($recycle=0, $issystem=0){
        $modules = DB::table('modules')->select(['mid','name','title','version','logo','from','status','permissions'])->where('issystem', intval($issystem))->get()->keyBy('name')->toArray();
        if (empty($modules)) return [];
        if ($recycle==-1) return $modules;
        $recycles = DB::table('modules_recycle')->select('type')->get()->keyBy('name')->toArray();
        foreach ($modules as $key=>$module){
            if ($recycle>0){
                if ($recycle!=intval($recycles[$module['name']])){
                    unset($modules[$key]);
                }
            }elseif (isset($recycles[$key])){
                unset($modules[$key]);
            }
        }
        return $modules;
    }

    static function localExists($identity){
        return file_exists(base_path("public/addons/$identity/manifest.json"));
    }

    static function upgrade($identity,$from=''){
        $startTime = time();
        $ManiFest = self::installCheck($identity);
        if (is_error($ManiFest)) return $ManiFest;
        $application = $ManiFest['application'];
        MSService::TerminalSend(['mode'=>'info', 'message'=>"即将升级应用模块【{$application['name']}^{$application['version']}】"]);
        $component = self::SysComponent($application['identifie']);
        if (!empty($component)){
            //已经是最新版本
            if ($component['releasedate']>=$application['releasedate']){
                return true;
            }
        }
        //执行升级脚本
        if (!empty($ManiFest['upgrade'])){
            try {
                MSService::TerminalSend(['mode'=>'info', 'message'=>"正在运行应用升级脚本..."]);
                script_run($ManiFest['upgrade'], public_path("addons/$identity/"));
            } catch (\Exception $exception){
                return error(-1,__('installFailed', ['reason'=>$exception->getMessage()]));
            }
        }
        //更新模块数据表
        $subscribes = $ManiFest['subscribes'] ?: array();
        $handles = $ManiFest['handles'] ?: array();
        $moduledata = self::ModuleData($application,$subscribes,$handles);
        $moduledata['permissions'] = empty($ManiFest['permissions']) ? "" : serialize($ManiFest['permissions']);
        DB::table('modules')->where('name',$application['identifie'])->update($moduledata);
        //安装模块依赖服务
        if (!empty($ManiFest['servers'])){
            try {
                $MSS = new MSService();
                $MSS->checkRequire($ManiFest['servers']);
            }catch (\Exception $exception){
                return error(-1,__('installServerFailed', ['reason'=>$exception->getMessage()]));
            }
        }
        //更新模块数据表
        if (!empty($component) || $from=='cloud'){
            $cloudinfo = empty($component['online']) ? array() : unserialize($component['online']);
            $cloudinfo['isnew'] = false;
            $cloudinfo['releasedate'] = $application['releasedate'];
            $cloudinfo['version'] = $application['version'];
            $cloudIdentity = self::SysPrefix($identity);
            $comInfo = array(
                'name'=>$application['name'],
                'logo'=>$application['logo'],
                'website'=>$application['url'],
                'online'=>serialize($cloudinfo),
                'version'=>$application['version'],
                'updatetime'=>TIMESTAMP,
                'releasedate'=>$application['releasedate'],
                'dateline'=>TIMESTAMP
            );
            if (empty($component)){
                $comInfo['modulename'] = $identity;
                $comInfo['type'] = 1;
                $comInfo['rootpath'] = "public/addons/$identity/";
                $comInfo['addtime'] = TIMESTAMP;
                DB::table('gxswa_cloud')->insert($comInfo);
            }else{
                DB::table('gxswa_cloud')->where('identity', $cloudIdentity)->update($comInfo);
            }
        }
        $stopTime = time();
        MSService::TerminalSend(['mode'=>'success', 'message'=>"模块升级完成！耗时".($stopTime-$startTime)."秒"]);
        return true;
    }

    static function uninstall($identity){
        $ManiFest = self::installCheck($identity);
        if (is_error($ManiFest)) return $ManiFest;
        $component = self::SysComponent($ManiFest['application']['identifie']);
        //执行卸载脚本
        if (!empty($ManiFest['uninstall'])){
            try {
                MSService::TerminalSend(['mode'=>'info', 'message'=>"正在运行应用卸载脚本..."]);
                script_run($ManiFest['uninstall'], public_path("addons/$identity/"));
            } catch (\Exception $exception){
                return error(-1,__('uninstallFailed', array('reason'=>DEVELOPMENT?$exception->getMessage():__('installFailedRender'))));
            }
        }
        //更新模块数据表
        DB::table('modules')->where('name',$ManiFest['application']['identifie'])->delete();
        if (!empty($component)){
            DB::table('gxswa_cloud')->where('id',$component['id'])->delete();
            if (!DEVELOPMENT){
                //删除安装包
                FileService::rmdirs(base_path($component['rootpath']));
            }
        }
        CacheService::flush();
        return true;
    }

    static function fetch($name, $enabled = true) {
        global $_W;
        $cachekey = CacheService::system_key('module_info', array('module_name' => $name));
        $module = Cache::get($cachekey,array());
        if (empty($module)) {
            $module_info = Module::where('name',$name)->first();
            if (empty($module_info)) {
                return array();
            }
            $module_info['isdisplay'] = 1;
            $module_info['logo'] = tomedia($module_info['logo']);
            $module_info['preview'] = tomedia(IA_ROOT . '/addons/' . $module_info['name'] . '/preview.jpg', '', true);
            if (file_exists(IA_ROOT . '/addons/' . $module_info['name'] . '/preview-custom.jpg')) {
                $module_info['preview'] = tomedia(IA_ROOT . '/addons/' . $module_info['name'] . '/preview-custom.jpg', '', true);
            }
            $module_receive_ban = (array)SettingService::Load('module_receive_ban');
            if (is_array($module_receive_ban['module_receive_ban']) && in_array($name, $module_receive_ban['module_receive_ban'])) {
                $module_info['is_receive_ban'] = true;
            }
            $module_ban = (array)SettingService::Load('module_ban');
            if (is_array($module_ban['module_ban']) && in_array($name, $module_ban['module_ban'])) {
                $module_info['is_ban'] = true;
            }
            $module_upgrade = (array)SettingService::Load('module_upgrade');
            if (is_array($module_upgrade['module_upgrade']) && in_array($name, array_keys($module_upgrade['module_upgrade']))) {
                $module_info['is_upgrade'] = true;
            }

            $module_info['recycle_info'] = array();
            $recycle_info = DB::table('modules_recycle')->where('name',$name)->first();
            if (!empty($recycle_info)) {
                $is_delete = true;
                $account_support = array(
                    'account_support' => array(
                        'type' => 'account',
                        'type_name' => '公众号',
                        'support' => 2,
                        'not_support' => 1,
                        'store_type' => 1,
                    )
                );
                foreach ($account_support as $support => $value) {
                    if (!empty($recycle_info[2][$support])) {
                        $module_info['recycle_info'][$support] = 2; 				} else {
                        $module_info['recycle_info'][$support] = empty($recycle_info[1][$support]) ? 0 : 1;
                    }
                    if ($module_info[$support] == $value['support'] && empty($module_info['recycle_info'][$support])) {
                        $is_delete = false;
                    }
                }
                $module_info['is_delete'] = $is_delete; 		}

            $module = $module_info;
            Cache::put($cachekey, $module_info);
        }

        if (!empty($enabled)) {
            if (!empty($module['is_delete'])) {
                return array();
            }
        }

        if (!empty($module) && !empty($_W['uniacid'])) {
            $setting_cachekey = CacheService::system_key('module_setting', array('module_name' => $name, 'uniacid' => $_W['uniacid']));
            $setting = Cache::get($setting_cachekey,array());
            if (!isset($setting['settings'])) {
                $setting = DB::table('uni_account_modules')->where(array('module'=>$name,'uniacid'=>$_W['uniacid']))->first();
                $setting = empty($setting) ? array('module' => $name) : $setting;
                Cache::put($setting_cachekey, $setting, 86400*7);
            }
            $module['config'] = unserialize($setting['settings']);
            $module['enabled'] = $module['issystem'] || !isset($setting['enabled']) ? 1 : $setting['enabled'];
            $module['displayorder'] = $setting['displayorder'];
            $module['shortcut'] = $setting['shortcut'];
            $module['module_shortcut'] = $setting['module_shortcut'];
        }
        return $module;
    }

    static function ModuleData($application,$subscribes=array(),$handles=array()){
        return array(
            'name'=>$application['identifie'],
            'application_type'=>$application['module_type']??1,
            'type'=>$application['type'],
            'title'=>$application['name'],
            'version'=>$application['version'],
            'ability'=>$application['ability'],
            'description'=>$application['description'],
            'author'=>$application['author'],
            'url'=>$application['url'],
            'logo'=>$application['logo'],
            'subscribes'=>serialize($subscribes),
            'handles'=>serialize($handles),
            'isrulefields'=>0,
            'issystem'=>0,
            'title_initial'=>'W',
            'wxapp_support'=>1,
            'welcome_support'=>1,
            'oauth_type'=>1,
            'webapp_support'=>1,
            'phoneapp_support'=>1,
            'account_support'=>2,
            'xzapp_support'=>1,
            'aliapp_support'=>1,
            'baiduapp_support'=>1,
            'toutiaoapp_support'=>1
        );
    }

    static function SupportType(){
        $module_support_type = array(
            'account_support' => array(
                'type' => 'account',
                'type_name' => '公众号',
                'support' => 2,
                'not_support' => 1,
                'store_type' => 1,
            )
        );
        return $module_support_type;
    }

    static function UniModules($uniacidOrAccount, $apptype=null){
        $account_info = is_numeric($uniacidOrAccount) ? Account::getByUniacid($uniacidOrAccount) : $uniacidOrAccount;
        $uni_account_type = AccountService::GetType(1);
        $owner_uid = DB::table('uni_account_users')->where(array('uniacid' => $account_info['uniacid'], 'role' => array('owner', 'vice_founder')))->select(array('uid', 'role'))->get()->keyBy('role')->toArray();
        $owner_uid = !empty($owner_uid['owner']) ? $owner_uid['owner']['uid'] : (!empty($owner_uid['vice_founder']) ? $owner_uid['vice_founder']['uid'] : 0);

        $cachekey = CacheService::system_key('unimodules',array('uniacid'=>$account_info['uniacid']));
        $modules = Cache::get($cachekey,array());
        if (empty($modules)){
            $enabled_modules = self::NonRecycleModules();
            if (!empty($owner_uid) && !UserService::isFounder($owner_uid, true)) {
                $group_modules = AccountService::GroupModules($account_info['uniacid']);

                $user_modules = UserService::GetModules($owner_uid);
                if (!empty($user_modules)) {
                    $group_modules = array_unique(array_merge($group_modules, array_keys($user_modules)));
                    $group_modules = array_intersect(array_keys($enabled_modules), $group_modules);
                }
            } else {
                $group_modules = array_keys($enabled_modules);
            }
            Cache::put($cachekey, $group_modules, 7*86400);
            $modules = $group_modules;
        }
        if (empty($apptype)){
            $modules = array_merge($modules, self::SysModules());
        }

        $module_list = array();
        if (!empty($modules)) {
            foreach ($modules as $name) {
                if (empty($name)) {
                    continue;
                }
                $module_info = self::fetch($name);
                if ($module_info[$uni_account_type['module_support_name']] != $uni_account_type['module_support_value']) {
                    continue;
                }
                if (!empty($module_info['recycle_info'])) {
                    if ($module_info['recycle_info']['account'] > 0 && $module_info['account'] == 2) {
                        $module_info['account'] = 1;
                    }
                }
                if ($module_info['account_support'] != 2 && in_array($account_info['type'], array(1, 3))) {
                    continue;
                }
                if (!empty($module_info)) {
                    $module_list[$name] = $module_info;
                }
            }
        }
        if (empty($apptype)){
            $module_list['core'] = array('title' => '系统事件处理模块', 'name' => 'core', 'issystem' => 1, 'enabled' => 1, 'isdisplay' => 0);
        }
        return $module_list;
    }

    static function NonRecycleModules(){
        $modules = Module::where('issystem' , 0)->orderBy('mid', 'DESC')->get()->keyBy('name')->toArray();
        if (empty($modules)) {
            return array();
        }
        foreach ($modules as &$module) {
            $module_info = self::fetch($module['name']);
            if (empty($module_info)) {
                unset($module);
            }
            if (!empty($module_info['recycle_info'])) {
                if ($module_info['recycle_info']['account_support'] > 0 && $module_info['account_support'] == 2) {
                    $module['account_support'] = 1;
                }
            }
        }
        return $modules;
    }

    static function SysModules(){
        return array('basic', 'news', 'music', 'service', 'userapi', 'recharge', 'images', 'video', 'voice', 'wxcard', 'custom', 'chats', 'paycenter', 'keyword', 'special', 'welcome', 'default', 'apply', 'reply', 'core', 'store', 'wxapp');
    }

    static function SysPrefix($identity=""){
        return env('APP_MODULE_PRE', 'laravel_module_') . $identity;
    }

    static function SysComponent($identity){
        return DB::table('gxswa_cloud')->where('identity',self::SysPrefix($identity))->first();
    }

}
