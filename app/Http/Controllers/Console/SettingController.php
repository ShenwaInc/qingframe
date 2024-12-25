<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
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
                if ($_W['config']['site']['id']==0){
                    $_W['config']['site']['id'] = $activeState['siteid'];
                    if (!CloudService::CloudEnv('APP_SITEID=0', "APP_SITEID={$activeState['siteid']}")){
                        return $this->message('文件写入失败，请检查根目录权限');
                    }
                    Artisan::call('server:update');
                    CacheService::flush();
                }
                $res['message'] = '恭喜您，激活成功！';
                $redirect = wurl('');
            }
            return $this->message($res['message'], $redirect, $res["type"]);
        }
        return $this->globalView("console.active", ["siteinfo"=>$activeState, "title"=>"云服务激活"]);
    }

    public function detection(){
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','type','online','releasedate','rootpath']);
        if (empty($component)) return $this->message('系统出现致命错误');
        $cloudInfo = $this->checkCloud($component,1,true);
        if (is_error($cloudInfo)){
            return $this->message($cloudInfo['message']);
        }
        return $this->message('检测完成！',wurl('setting'),'success');
    }

    public function updateLog(){
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','type','online','releasedate','rootpath']);
        if (empty($component)) return $this->message('系统出现致命错误');
        $cloudInfo = $this->checkCloud($component,1,true);
        if (empty($cloudInfo['difference'])) return $this->message('当前系统已经是最新版本');
        $structures = $this->makeStructure($cloudInfo['difference']);
        return $this->globalView("console.structure", array(
            'structures'=>$structures,
            'total'=>count($structures),
            'updateLogs'=>(array)$cloudInfo['updateLogs'],
            'curShow'=>\request()->input('show', 'compare')
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
            $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','modulename','online','type','releasedate','rootpath']);
            $cloudUpdate = CloudService::CloudUpdate($component['identity'],base_path().'/');
            if (is_error($cloudUpdate)){
                MSService::TerminalSend(['mode'=>'err', 'message'=>'程序同步失败：'.$cloudUpdate['message']]);
                return $this->message($cloudUpdate['message']);
            }
            MSService::TerminalSend(['mode'=>'success', 'message'=>'程序同步完成，更新系统版本信息']);
            //更新版本号
            $cloudInfo = $this->checkCloud($component);
            if (is_error($cloudInfo)) return $this->message($cloudInfo['message']);
            DB::table('gxswa_cloud')->where('id',$component['id'])->update(array(
                'version'=>$cloudInfo['version'],
                'updatetime'=>TIMESTAMP,
                'dateline'=>TIMESTAMP,
                'releasedate'=>$cloudInfo['releasedate'],
                'online'=>serialize(array(
                    'upgradable'=>false,
                    'version'=>$cloudInfo['version'],
                    'releasedate'=>$cloudInfo['releasedate']
                ))
            ));
            CloudService::CloudEnv(array("APP_VERSION=".QingVersion,"APP_RELEASE=".QingRelease), array("APP_VERSION={$cloudInfo['version']}","APP_RELEASE={$cloudInfo['releasedate']}"));
        }catch (\Exception $exception){
            MSService::TerminalSend(['mode'=>'err', 'message'=>"程序同步失败：".$exception->getMessage()]);
            return $this->message($exception->getMessage());
        }
        return $this->message('程序同步完成，即将自动更新...', wurl('setting/sysupgrade'),'success');
    }

    public function SystemUpgrade(){
        //升级文件对比
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','type','online','releasedate','rootpath']);
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
            Artisan::call('self:migrate');
            Artisan::call('route:clear');
            Artisan::call('server:update');
            Artisan::call('self:clear');
            CacheService::flush();
        }catch (\Exception $exception){
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
                $releaseDate = intval($value['release']['releasedate']);
                //本地是否存在
                $com = array(
                    'id'=>0,
                    'name'=>$value['name'],
                    'identifie'=>$identifie,
                    'version'=>$value['release']['version'],
                    'releasedate'=>$releaseDate,
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
                if (ModuleService::localExists($identifie)){
                    $module = ModuleService::installCheck($identifie);
                    if (!is_error($module) && $module->installed){
                        //已安装
                        $application = $module->application;
                        if (version_compare($release['version'], $application['version'], '>') || $releaseDate>$application['releasedate']){
                            $com['action'] .= '<a href="'.wurl('module/update').'?nid='.$identifie.'" class="layui-btn layui-btn-sm layui-btn-danger confirm" data-text="升级前请做好源码和数据备份，避免升级故障导致系统无法正常运行">升级</a>';
                        }
                        $com['action'] .= '<a href="'.wurl('module/remove').'?nid='.$identifie.'" class="layui-btn layui-btn-sm layui-btn-primary confirm" data-text="即将卸载该应用并删除应用产生的所有数据，是否确定要卸载？">卸载</a></div>';
                    }else{
                        if ($module['errno']!=-1){
                            //已存在但未安装
                        }else{
                            $com['action'] = '<a href="'.wurl('module/require', array('nid'=>$value['identity'])).'" class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="确定要安装该应用？">安装</a>';
                        }
                    }
                }else{
                    $com['action'] = '<a href="'.wurl('module/require', array('nid'=>$value['identity'])).'" class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="确定要安装该应用？">安装</a>';
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
        if (checksubmit('save')){
            $html = \request()->input('welcomeHTML');
            if (!empty($html)){
                $html = htmlspecialchars_decode($html);
            }
            if (empty($html) || !strexists($html, '<html') || !strexists($html, '</html>')){
                return $this->message("无效的HTML内容");
            }
            if (!file_put_contents($welcomePath, $html)){
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

    public function index($op='main'){
        global $_W,$_GPC;
        $_W['inSetting'] = true;
        if ($_W['config']['site']['id']==0){
            return redirect("console/active");
        }
        $return = array('title'=>'系统管理','op'=>$op,'components'=>array());
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
                $debug = env('APP_DEBUG', false);
                if ($debug) {
                    $complete = CloudService::CloudEnv('APP_DEBUG=true', 'APP_DEBUG=false');
                } else {
                    $complete = CloudService::CloudEnv('APP_DEBUG=false', 'APP_DEBUG=true');
                }
                if (!$complete) {
                    return $this->message('文件写入失败，请检查根目录权限');
                }
                return $this->message('successful', wurl('setting'), 'success');
            case 'comcheck':
                $component = DB::table('gxswa_cloud')->where('id', intval($_GPC['cid']))->first(['id', 'identity', 'type', 'online', 'releasedate', 'rootpath', 'modulename']);
                if (empty($component)) return $this->message('找不到该服务组件');
                if (empty($component['identity'])){
                    $component['identity'] = ModuleService::SysPrefix($component['modulename']);
                }
                $cloudInfo = $this->checkCloud($component, 1, true);
                if (is_error($cloudInfo)) {
                    return $this->message($cloudInfo['message']);
                }
                if (empty($cloudInfo['difference'])) return $this->message('该应用已升级到最新版本', "", "success");
                $structures = $this->makeStructure($cloudInfo['difference']);
                return $this->globalView("console.structure", array(
                    'structures' => $structures,
                    'total' => count($structures),
                    'updateLogs'=>(array)$cloudInfo['updateLogs'],
                    'curShow'=>\request('show', 'compare')
                ));
            default:
                $framework = DB::table('gxswa_cloud')->where('type', 0)->first(['id', 'version', 'identity', 'type', 'online', 'releasedate', 'rootpath']);
                $return['framework'] = $framework;
                $return['cloudInfo'] = !empty($framework['online']) ? unserialize($framework['online']) : array('upgradable' => false);
                $return['welcomeCustom'] = file_exists(resource_path('views/welcomeCustom.blade.php'));
                break;
        }
        $return['activeState'] = CloudService::CloudActive(true);
        $return['appSecurityEntrance'] = env("APP_SECURITY_ENTRANCE");
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
        if ($component['releasedate']<$upgradeInfo['releasedate'] && $compare<2){
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
            if ($complete){
                return $this->message('savedSuccessfully',wurl('setting'),'success');
            }
        }elseif ($op=='appSecurity'){
            $appSecurityEntrance = env("APP_SECURITY_ENTRANCE");
            $SecurityCode = $request->input('SecurityCode', "");
            if ($appSecurityEntrance!=$SecurityCode){
                if (is_null($appSecurityEntrance)){
                    $TIMEZONE = env("APP_TIMEZONE");
                    $replace = <<<EOF
APP_TIMEZONE=$TIMEZONE
APP_SECURITY_ENTRANCE=$SecurityCode

EOF;
                    if (!CloudService::CloudEnv("APP_TIMEZONE=$TIMEZONE",$replace)){
                        return $this->message("文件写入失败，请检查根目录权限");
                    }
                }else{
                    if (!CloudService::CloudEnv("APP_SECURITY_ENTRANCE=$appSecurityEntrance","APP_SECURITY_ENTRANCE=".trim($SecurityCode))){
                        return $this->message("文件写入失败，请检查根目录权限");
                    }
                }
            }
            return $this->message('savedSuccessfully',wurl('setting'),'success');
        }
        return $this->message();
    }

}
