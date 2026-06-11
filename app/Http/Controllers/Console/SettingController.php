<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\SystemLogs;
use App\Services\CacheService;
use App\Services\CloudService;
use App\Services\ModuleService;
use App\Services\MSService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{

    public function active(){
        global $_W;
        if(!$_W['isfounder']){
            return $this->message('暂无权限');
        }
        $activeState = CloudService::CloudActive();
        if (checksubmit()){
            $siteInfo = \request()->input('site');
            if (empty($siteInfo['mobile']) || !preg_match('/^(\+)?(86)?0?1\d{10}$/', $siteInfo['mobile'])){
                return $this->message("请输入正确的手机号");
            }
            if (empty($siteInfo['verify_code'])){
                return $this->message("请输入手机短信验证码");
            }
            $userId = intval($siteInfo['uid']);
            if (!$userId && empty($siteInfo['password'])){
                return $this->message("请设置一个云端密码");
            }
            if (empty($siteInfo['name'])) $siteInfo['name'] = $activeState['name'];
            $siteInfo['siteid'] = $activeState['siteid'];
            $siteInfo['r'] = "cloud.active.save";
            $res = CloudService::CloudApi("", $siteInfo);
            if (is_error($res)){
                return $this->message($res['message']);
            }
            $redirect = "";
            if ($res['type']=="success"){
                if ($_W['config']['site']['id']==0) {
                    //首次激活
                    $_W['config']['site']['id'] = $activeState['siteid'];
                    if (!CloudService::CloudEnv('APP_SITEID=0', "APP_SITEID={$activeState['siteid']}")) {
                        return $this->message('文件写入失败，请检查根目录权限');
                    }
                    //自动安装默认微服务
                    Artisan::call('update:service');
                    //自动安装默认应用
                    $defaultModule = config('system.defaultModule', '');
                    if (!empty($defaultModule) && file_exists(public_path("addons/$defaultModule/manifest.json"))) {
                        ModuleService::install($defaultModule);
                    }
                    CacheService::flush();
                }else{
                    if($_W['config']['site']['id'] != $activeState['siteid']){
                        //重置云服务站点ID
                        if (!CloudService::CloudEnv('APP_SITEID=0', "APP_SITEID={$activeState['siteid']}")) {
                            return $this->message('文件写入失败，请检查根目录权限');
                        }
                    }
                    Cache::forget(md5('HingWork:Authorize:Active:'.$_W['siteroot']));
                }
                $res['message'] = '恭喜您，激活成功！';
                $redirect = wurl('');
            }
            SystemLogs::userOperation(
                '激活云服务',
                'setting:active',
                "站点ID：{$activeState['siteid']}",
                $res['type']=='success',
                ['siteid' => $activeState['siteid'], 'name' => $siteInfo['name'] ?? '']
            );
            return $this->message($res['message'], $redirect, $res["type"]);
        }
        return $this->globalView("console.active", ["siteinfo"=>$activeState, "title"=>__('云服务激活')]);
    }

    public function detection(){
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','type','online','version_code','rootpath']);
        if (empty($component)) return $this->message('系统出现致命错误');
        $cloudInfo = $this->checkCloud($component,1,true);
        if (is_error($cloudInfo)){
            return $this->message($cloudInfo['message']);
        }
        return $this->message('检测完成！',wurl('setting'),'success');
    }

    public function updateLog(){
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','type','online','version_code','rootpath']);
        if (empty($component)) return $this->message('系统出现致命错误');
        $cloudInfo = $this->checkCloud($component,1,true);
        $curShow = \request('show', 'compare');
        if (empty($cloudInfo['difference']) && $curShow=='compare') return $this->message('当前系统已经是最新版本');
        $structures = $this->makeStructure($cloudInfo['difference']);
        return $this->globalView("console.structure", array(
            'structures'=>$structures,
            'total'=>count($structures),
            'updateLogs'=>(array)$cloudInfo['updateLogs'],
            'curShow'=>$curShow
        ));
    }

    public function makeStructure($difference,$basedir='',$root='/'){
        $structures = [];
        foreach ($difference as $item){
            if (is_array($item)){
                $files = $this->makeStructure($item[1],$basedir.$item[0].'/',$root);
                $structures = array_merge($structures, $files);
            }else{
                $fileinfo = explode('|',$item);
                $structures[] = $root.$basedir.$fileinfo[0];
            }
        }
        return $structures;
    }

    public function selfUpgrade(){
        try {
            MSService::TerminalSend(['mode'=>'info', 'message'=>'即将同步系统程序源码：']);
            //同步文件
            $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','modulename','online','type','version_code','rootpath']);
            $cloudUpdate = CloudService::CloudUpdate($component['identity'],base_path().'/');
            if (is_error($cloudUpdate)){
                MSService::TerminalSend(['mode'=>'err', 'message'=>'程序同步失败：'.$cloudUpdate['message']]);
                return $this->message($cloudUpdate['message']);
            }
            MSService::TerminalSend(['mode'=>'success', 'message'=>'程序同步完成，更新系统版本信息']);
            //更新版本号
            $cloudInfo = $this->checkCloud($component);
            if (is_error($cloudInfo)) return $this->message($cloudInfo['message']);
            $version_code = $cloudInfo['version_code'] ?? $cloudInfo['releasedate'];
            DB::table('gxswa_cloud')->where('id',$component['id'])->update(array(
                'version'=>$cloudInfo['version'],
                'updatetime'=>TIMESTAMP,
                'dateline'=>TIMESTAMP,
                'version_code'=>$version_code,
                'online'=>serialize(array(
                    'upgradable'=>false,
                    'version'=>$cloudInfo['version'],
                    'version_code'=>$version_code
                ))
            ));
            CloudService::CloudEnv(array("APP_VERSION=".QingVersion,"APP_RELEASE=".QingRelease), array("APP_VERSION={$cloudInfo['version']}","APP_RELEASE={$version_code}"));
            SystemLogs::userOperation('系统同步源码', 'setting:selfupgrade', "版本：{$cloudInfo['version']}", true, ['version' => $cloudInfo['version']]);
        }catch (\Exception $exception){
            MSService::TerminalSend(['mode'=>'err', 'message'=>"程序同步失败：".$exception->getMessage()]);
            SystemLogs::systemRunning(
                '系统同步源码异常',
                'setting:selfupgrade',
                "系统同步源码过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace()
                ]
            );
            return $this->message($exception->getMessage());
        }
        return $this->message('程序同步完成，即将自动更新...', wurl('setting/sysupgrade'),'success');
    }

    public function SystemUpgrade(){
        //升级文件对比
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','type','online','version_code','rootpath']);
        if (!empty($component)){
            $cloudInfo = $this->checkCloud($component);
            if (!is_error($cloudInfo) && !empty($cloudInfo['hasDifference'])){
                if (DEVELOPMENT){
                    dd("以下文件同步失败，请检查文件夹权限：", $cloudInfo['difference']);
                }
                return $this->message('程序同步失败，请检查文件夹权限', wurl('setting'));
            }
        }
        try {
            Artisan::call('self:migrate', ['--no-interaction'=>true]);
            Artisan::call('route:clear');
            Artisan::call('update:service');
            Artisan::call('self:clear');
            CacheService::flush();
            if (file_exists(base_path("needUpdate.bin"))){
                @unlink(base_path("needUpdate.bin"));
            }
            SystemLogs::userOperation('系统升级', 'setting:sysupgrade', '执行系统升级流程');
        }catch (\Exception $exception){
            SystemLogs::error(
                '系统升级异常',
                'setting:sysupgrade',
                "系统升级过程中发生异常：{$exception->getMessage()}",
                $exception->getCode(),
                [
                    'file' => $exception->getFile() . ":" . $exception->getLine(),
                    'trace' => $exception->getTrace()
                ]
            );
            return $this->message($exception->getMessage());
        }
        return $this->message('恭喜您，升级成功！', wurl('setting'),'success');
    }

    public function cloudMarket(){
        global $_GPC;
        $page = max(1, intval($_GPC['page']));
        $cacheKey = "cloud:module_list:$page";
        $res = Cache::get($cacheKey, array());
        if (empty($res)){
            $data = array(
                'r'=>'cloud.packages',
                'pidentity'=>CloudService::$identity,
                'page'=>$page,
                'category'=>1
            );
            $res = CloudService::CloudApi("", $data);
            Cache::put($cacheKey, $res, 600);
        }
        if (is_error($res)){
            return $this->message($res['message']);
        }
        $plugins = array();
        if (!empty($res['servers'])){
            $modulePre = ModuleService::SysPrefix();
            foreach ($res['servers'] as $value){
                $identifie = str_replace($modulePre, "", $value['identity']);
                if (empty($identifie)) continue;
                $release = $value['release'];
                $releaseDate = intval($value['release']['version_code'] ?? $value['release']['releasedate']);
                //本地是否存在
                $com = array(
                    'id'=>0,
                    'name'=>$value['name'],
                    'identifie'=>$identifie,
                    'version'=>$value['release']['version'],
                    'version_code'=>$releaseDate,
                    'ability'=>$value['name'],
                    'description'=>$value['summary'],
                    'author'=>$value['author'],
                    'website'=>$value['website'],
                    'action'=>'',
                    'logo'=>$value['icon'],
                    'cloudInfo'=>[]
                );
                if (mb_strlen($com['description'], 'utf8')>50){
                    $com['description'] = mb_substr($com['description'], 0, 50, 'utf8') . '...';
                }
                $basePath = $value['base_path'] ?: config('system.setting.addon_dir', 'addons');
                if (ModuleService::localExists($identifie)){
                    $module = ModuleService::installCheck($identifie);
                    if (!is_error($module) && $module->installed){
                        //已安装
                        $application = $module->application;
                        if (version_compare($release['version'], $application['version'], '>') || $releaseDate>$application['version_code']){
                            $com['action'] .= '<a href="'.wurl('module/update').'?nid='.$identifie.'" class="layui-btn layui-btn-sm layui-btn-danger confirm" data-text="'.__('升级前请做好数据备份').'">'.__('升级').'</a>';
                        }
                        $com['action'] .= '<a href="'.wurl('module/remove', ['nid'=>$identifie, 'from'=>$basePath]).'" class="layui-btn layui-btn-sm layui-btn-primary confirm" data-text="'.__('uninstallConfirm').'">'.__('uninstall').'</a></div>';
                    }else{
                        if ($module['errno']!=-1){
                            //已存在但未安装
                        }else{
                            $com['action'] = '<a href="'.wurl('module/require', array('nid'=>$value['identity'], 'from'=>$basePath)).'" class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="'.__('installConfirm').'">'.__('install').'</a>';
                        }
                    }
                }else{
                    $com['action'] = '<a href="'.wurl('module/require', array('nid'=>$value['identity'], 'from'=>$basePath)).'" class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="'.__('installConfirm').'">'.__('install').'</a>';
                }
                $plugins[$identifie] = $com;
            }
        }
        return $this->globalView('console.market', array(
            'title'=>__("应用市场"),
            'components'=>$plugins,
            'pager'=>pagination($res['total'], $page)
        ));
    }

    /**
     * @throws \Exception
     */
    public function doWelcome(){
        $welcomePath = resource_path('views/welcomeCustom.blade.php');
        $uniacid = (int)DB::table('uni_settings')->where('bind_domain', \request()->server('HTTP_HOST'))->value('uniacid');
        if (!empty($uniacid) && file_exists(resource_path("views/welcomeCustom{$uniacid}.blade.php"))){
            $welcomePath = resource_path("views/welcomeCustom{$uniacid}.blade.php");
        }

        if (checksubmit('save')){
            $html = \request()->input('welcomeHTML');
            if (!empty($html)){
                $html = htmlspecialchars_decode($html);
            }
            if (empty($html) || !strexists($html, '<html') || !strexists($html, '</html>')){
                return $this->message("无效的HTML内容");
            }
            $res = file_put_contents($welcomePath, $html);
            SystemLogs::userOperation('保存欢迎页', 'setting:welcome', $html, $res);
            if (!$res){
                return $this->message('saveFailed');
            }
            return $this->message('savedSuccessfully', \request()->input('redirect', referer()), 'success');
        }

        $welcomeCustom = false;
        if (file_exists($welcomePath)){
            $welcomeCustom = true;
            $welcomeHTML = file_get_contents($welcomePath);
        }else{
            $welcomeHTML = file_get_contents(resource_path('views/welcome.blade.php'));
        }
        return $this->globalView("console.setting.welcome", array(
            'title'=>__('欢迎页'),
            'isCustom'=>$welcomeCustom,
            'path'=>$welcomePath,
            'redirect'=>referer(),
            'html'=>$welcomeHTML
        ));
    }

    public function doBlacklist()
    {
        $file = storage_path('Blacklist.txt');
        if (\request()->isMethod('post')){
            $Blacklist = trim(\request()->input('blacklist'));
            $res = file_put_contents($file, $Blacklist);
            SystemLogs::userOperation('保存黑名单', 'setting:blacklist', $Blacklist, $res);
            if ($res===false){
                return $this->message('saveFailed');
            }
            $redirect = \request()->input('redirect', wurl('setting'));
            return $this->message('savedSuccessfully', $redirect, 'success');
        }
        $Blacklist = file_exists($file) ? file_get_contents($file) : '';
        return $this->globalView('console.setting.blacklist', array(
            'title'=>__('IP黑名单'),
            'Blacklist'=>$Blacklist,
            'file'=>$file
        ));
    }

    public function index($op='main'){
        global $_W,$_GPC;
        $_W['inSetting'] = true;
        if ($_W['config']['site']['id']==0){
            return redirect("console/active");
        }
        $return = array('title'=>__('系统管理'),'op'=>$op,'components'=>array());
        if (!isset($_W['setting']['page'])){
            $_W['setting']['page'] = $_W['page'];
        }
        if (!isset($_W['setting']['remote'])){
            $_W['setting']['remote'] = array('type'=>0);
        }
        $method = 'do' . ucfirst($op);
        if (method_exists($this, $method)){
            return $this->$method();
        }
        switch ($op) {
            case 'detection':
                return $this->detection();
            case 'selfupgrade':
                return $this->selfUpgrade();
            case 'sysupgrade':
                return $this->SystemUpgrade();
            case 'market':
                return $this->cloudMarket();
            case 'updateLog':
                return $this->updateLog();
            case 'pageset':
                return $this->globalView("console.pageset", $return);
            case 'envdebug':
                $debug = config('system.debugMode');
                if ($debug) {
                    $complete = CloudService::CloudEnv('APP_DEBUG=true', 'APP_DEBUG=false');
                } else {
                    $complete = CloudService::CloudEnv('APP_DEBUG=false', 'APP_DEBUG=true');
                }
                SystemLogs::userOperation('调试模式切换', 'setting:debug', "切换为".($debug ? '关闭' : '开启'), $complete);
                if (!$complete) {
                    return $this->message('文件写入失败，请检查根目录权限');
                }
                return $this->message('successful', wurl('setting'), 'success');
            case 'comcheck':
                $component = DB::table('gxswa_cloud')->where('id', intval($_GPC['cid']))->first(['id', 'identity', 'type', 'online', 'version_code', 'rootpath', 'modulename']);
                if (empty($component)) return $this->message('找不到该服务组件');
                if (empty($component['identity'])){
                    $component['identity'] = ModuleService::SysPrefix($component['modulename']);
                }
                $cloudInfo = $this->checkCloud($component, 1, true);
                if (is_error($cloudInfo)) {
                    return $this->message($cloudInfo['message']);
                }
                //if (empty($cloudInfo['difference'])) return $this->message('该应用已升级到最新版本', "", "success");
                $structures = $this->makeStructure($cloudInfo['difference']);
                return $this->globalView("console.structure", array(
                    'structures' => $structures,
                    'total' => count($structures),
                    'updateLogs'=>(array)$cloudInfo['updateLogs'],
                    'curShow'=>\request('show', 'compare')
                ));
            default:
                $framework = DB::table('gxswa_cloud')->where('type', 0)->first(['id', 'version', 'identity', 'type', 'online', 'version_code', 'rootpath']);
                $return['framework'] = $framework;
                $return['cloudInfo'] = !empty($framework['online']) ? unserialize($framework['online']) : array('upgradable' => false);
                $uniacid = (int)DB::table('uni_settings')->where('bind_domain', \request()->server('HTTP_HOST'))->value('uniacid');
                if (!empty($uniacid) && file_exists(resource_path("views/welcomeCustom{$uniacid}.blade.php"))){
                    $return['welcomeCustom'] = true;
                }else{
                    $return['welcomeCustom'] = file_exists(resource_path('views/welcomeCustom.blade.php'));
                }
                break;
        }
        $return['activeState'] = CloudService::CloudActive(true);
        $return['appSecurityEntrance'] = config('system.safe.security_entrance');
        return $this->globalView('console.setting', $return);
    }

    public function checkCloud($component, $compare=1, $noCache=false){
        $cacheKey = "cloud:structure:{$component['identity']}";
        $upgradeInfo = array();
        $fromCache = true;
        if (!$noCache){
            $upgradeInfo = Cache::get($cacheKey);
        }
        if (empty($upgradeInfo)){
            $fromCache = false;
            $data = array(
                'identity'=>$component['identity'],
                'withLog'=>1
            );
            $upgradeInfo = CloudService::CloudApi('structure',$data);
            if (is_error($upgradeInfo)) return $upgradeInfo;
        }
        if ($compare==0) return $upgradeInfo;
        $structure = $upgradeInfo['structure'];
        $upgradeInfo['upgradable'] = false;
        $upgradeInfo['difference'] = $this->compare($component,$upgradeInfo['structure']);
        $upgradeInfo['hasDifference'] = $this->hasdifference($upgradeInfo['difference'],$component['type']);
        $version_code = $upgradeInfo['version_code'] ?? $upgradeInfo['releasedate'];
        if ($component['version_code']<$version_code && $compare<2){
            $upgradeInfo['upgradable'] = true;
        }
        if ($fromCache){
            $onlineInfo = $component['online'] ? unserialize($component['online']) : array();
            if ($onlineInfo['upgradable']==$upgradeInfo['upgradable']){
                return $upgradeInfo;
            }
        }
        $difference = $upgradeInfo['difference'];
        unset($upgradeInfo['structure'], $upgradeInfo['difference']);
        $update = array('dateline'=>TIMESTAMP,'online'=>serialize($upgradeInfo));
        pdo_update('gxswa_cloud',$update,array('identity'=>$component['identity']));
        $upgradeInfo['structure'] = $structure;
        $upgradeInfo['difference'] = $difference;
        Cache::put($cacheKey,$upgradeInfo,7200);
        return $upgradeInfo;
    }

    public function compare($component,$structure=''){
        if (!$structure) return array();
        $targetPath = base_path($component['rootpath']);
        if ($component['type']==0) $targetPath = base_path() . "/";
        $structures = json_decode(base64_decode($structure), true);
        $ignore = [];
        if (file_exists($targetPath."ignore.json")){
            $JSON = file_get_contents($targetPath."ignore.json");
            if (!empty($JSON)){
                $ignore = (array)json_decode($JSON);
            }
        }
        return CloudService::CloudCompare($structures, $targetPath, '', $ignore);
    }

    public function hasdifference($difference,$type=0){
        if (empty($difference)) return false;
        if ($type==3){
            foreach ($difference as $key=>$value){
                if (!is_array($value)){
                    $fileinfo = explode('|',$value);
                    if ($fileinfo[0]=='composer.json'){
                        unset($difference[$key]);
                        break;
                    }
                }
            }
        }
        if (empty($difference)) return false;
        return true;
    }

    public function save(Request $request){
        global $_W;
        $op = $request->input('op');
        if (!$request->isMethod('post')){
            return $this->message();
        }
        if ($op=='pageset'){
            $data = $request->input('data');
            $config = $_W['setting']['page'];
            if (!empty($data)){
                foreach ($data as $key=>$value){
                    $config[$key] = trim($value);
                }
            }
            $complete = SettingService::Save($config,'page');
            SystemLogs::userOperation('保存页面设置', 'setting:pageset', '保存页面配置', $complete, ['config'=>$config, 'original'=>$_W['setting']['page']]);
            if ($complete){
                return $this->message('savedSuccessfully',wurl('setting'),'success');
            }
        }elseif ($op=='appSecurity'){
            $appSecurityEntrance = env("APP_SECURITY_ENTRANCE");
            $SecurityCode = $request->input('SecurityCode', "");
            $res = true;
            if ($appSecurityEntrance!=$SecurityCode){
                if (is_null($appSecurityEntrance)){
                    $TIMEZONE = env("APP_TIMEZONE");
                    $replace = <<<EOF
APP_TIMEZONE=$TIMEZONE
APP_SECURITY_ENTRANCE=$SecurityCode

EOF;
                    $res = CloudService::CloudEnv("APP_TIMEZONE=$TIMEZONE",$replace);
                }else{
                    $res = CloudService::CloudEnv("APP_SECURITY_ENTRANCE=$appSecurityEntrance","APP_SECURITY_ENTRANCE=".trim($SecurityCode));
                }
            }
            SystemLogs::userOperation('修改安全入口', 'setting:appSecurity', "入口代码已变更", $res);
            if (!$res){
                return $this->message("文件写入失败，请检查根目录权限");
            }
            return $this->message('savedSuccessfully',wurl('setting'),'success');
        }
        return $this->message();
    }

}
