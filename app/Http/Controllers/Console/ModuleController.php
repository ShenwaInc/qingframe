<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\SystemLogs;
use App\Services\AccountService;
use App\Services\CacheService;
use App\Services\CloudService;
use App\Services\ModuleService;
use App\Services\MSService;
use App\Utils\WeModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{

    /**
     * @throws \Exception
     */
    public function entry(Request $request, $moduleName, $do='index'){
        return $this->HttpRequest($request, $moduleName, $do);
    }

    public function HttpRequest(Request $request, $moduleName, $segment1='index', $segment2='main'){
        global $_W;
        if (empty($_W['uniacid'])){
            return $this->account($request, $moduleName);
        }
        $moduleExist = ModuleService::fetch($moduleName);
        if (empty($moduleExist)){
            return $this->message("Module {$moduleName} not found");
        }
        $WeModule = new WeModule();
        try {
            $site = $WeModule->create($moduleName);
            $basePath = $site->__basePath;
            $nameSpace = $basePath == 'apps' ? 'Apps' : 'Addons';
            if (empty($site)){
                SystemLogs::systemRunning(
                    "模块请求异常：{$moduleName}",
                    'console:module:HttpRequest',
                    "模块请求处理过程中发生异常：模块不存在",
                    false,
                    [
                        'module_name' => $moduleName,
                        'segment1' => $segment1,
                        'segment2' => $segment2,
                    ]
                );
                abort(404, "Module {$moduleName} not found");
            }
            //记录操作日志
            DB::table('users_operate_history')->updateOrInsert(
                array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$moduleName),
                array('createtime'=>TIMESTAMP,'type'=>2)
            );

            if (method_exists($site, 'ConsoleRequest')){
                return $site->ConsoleRequest($request, $segment1, $segment2);
            }

            $className = $nameSpace . "\\".$moduleName."\app\Controllers\web\\".ucfirst($segment1)."Controller";
            $method = $segment2;
            if (!class_exists($className)){
                $className = $nameSpace . "\\".$moduleName."\app\Controllers\web\IndexController";
                if (class_exists($className)){
                    $method = $segment1;
                    $segment1 = 'index';
                }
            }
            if(class_exists($className)){
                $instance = new $className();
                if (!method_exists($instance, $method)){
                    abort(404, "Call to undefined member {$method} of {$className}");
                }
                $instance->moduleName = $moduleName;
                $instance->moduleSite = $site;
                $instance->moduleConfig = $site->module['config'] ? (array)$site->module['config'] : [];
                $instance->uniacid = $_W['uniacid'];
                $_W['moduleController'] = $segment1;
                $_W['moduleMethod'] = $method;
                return $instance->$method($request);
            }else{
                $method = "doWeb" . ucfirst($segment1);
                if (!method_exists($site, $method)){
                    abort(404 );
                }
                $_W['moduleController'] = $segment1;
                $_W['moduleMethod'] = $method;
                DB::table('users_operate_history')->updateOrInsert(
                    array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$moduleName),
                    array('createtime'=>TIMESTAMP,'type'=>2)
                );
                return $site->$method($request);
            }
        }catch (\Exception $exception){
            if (DEVELOPMENT){
                throw $exception;
            }
            SystemLogs::systemRunning(
                "模块请求异常：{$moduleName}",
                'module:HttpRequest',
                "模块请求处理过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace(),
                    'module_name' => $moduleName,
                    'segment1' => $segment1,
                    'segment2' => $segment2,
                ]
            );
            return $this->message($exception->getMessage());
        }
    }

    public function account(Request $request, $moduleName)
    {
        global $_W;
        $data = array(
            'refresh'=>$_W['siteurl'],
            'uniacid'=>intval($_W['uniacid']),
            'platforms'=>AccountService::OwnerAccounts(array(), -1, true),
        );
        return $this->globalView("console.server.platform",$data);
    }

    public function doQuickCreate(Request $request)
    {
        global $_W;
        if (!$_W['isfounder']){
            return $this->message('暂无权限');
        }
        $uniacid = $request->input('uniacid', 0);
        if ($request->isMethod('POST')){
            $module = $request->input('module');
            $identifier = trim($module['identifier']);
            $moduleExist = ModuleService::localExists($identifier, "apps");
            if ($moduleExist){
                return $this->message('该应用标识已被使用');
            }
            if (!preg_match('/^[a-z][a-z0-9_]+$/', $identifier)){
                return $this->message('应用标识只能由英文小写字母、数字和下划线组成');
            }
            $module['name'] = trim($module['name']);
            if (empty($module['name'])){
                return $this->message('请填写应用名称');
            }
            $data = $request->input('data', []);
            $data['WebIndex'] = trim($data['WebIndex']);
            if (empty($data['WebIndex'])){
                return $this->message('请填写应用单点登录地址');
            }
            $module['logo'] = trim($module['logo']);
            if (empty($module['logo'])){
                $module['logo'] = asset('static/images/microserver.png');
            }
            $module['description'] = trim($module['description']);
            try {
                Artisan::call('make:module', [
                    'identity'=>$identifier,
                    'name'=>$module['name'],
                    'type'=>2,
                    '--logo' => tomedia($module['logo']),
                    '--description' => $module['description'],
                    '--ssoUrl' => $data['WebIndex'],
                ]);
                if (!ModuleService::localExists($identifier)){
                    return $this->message('创建应用失败，请重试');
                }
                $install = ModuleService::install($identifier, 'addons', 'local');
                if (!$install || is_error($install)){
                    return $this->message($install['message']??'应用安装失败，请重试');
                }
                if (!empty($uniacid)){
                    //为平台分配应用
                    $moduleInfo = array(
                        'name'=>$module['name'],
                        'identity'=>$identifier,
                        'logo'=>$module['logo'],
                        'profile'=>'default'
                    );
                    $moduleList = AccountService::ExtraModules($uniacid, false);
                    if (empty($moduleList[$identifier])){
                        $modules = array_values($moduleList);
                        $modules[] = $moduleInfo;
                    }else{
                        $moduleList[$identifier] = $moduleInfo;
                        $modules = array_values($moduleList);
                    }
                    DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$uniacid), array('modules'=>serialize($modules)));
                }
                $WeModule = new WeModule();
                $WeModule->modulename = $identifier;
                $WeModule->saveSettings($data);
                //清理应用缓存
                CacheService::flush();
                return $this->message('应用创建成功', wurl("m/".$identifier), 'success');
            }catch (\Exception $exception){
                return $this->message($exception->getMessage());
            }
        }
        return $this->globalView("console.module.quickCreate", ['title'=>__('创建第三方应用'), 'uniacid'=>$uniacid]);
    }

    public function index(Request $request, $option='list'){
        global $_W;
        if (empty($_W['config']['site']['id'])){
            return redirect("console/active");
        }
        $_W['inSetting'] = true;
        $method = "do".ucfirst($option);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        $return = array('title'=>__('applications'), 'op'=>'plugin');
        $return['colors'] = array('red','blue','green','orange');
        $return['components'] = CloudService::getPlugins();
        $swaSocket = serv('websocket');
        $return['socket'] = [
            'server'=>"wss://socket.whotalk.com.cn/wss",
            'userSign'=>md5($_W['config']['setting']['authkey'].":terminal:{$_W['uid']}"),
            'userId'=>$_W['uid']
        ];
        if ($swaSocket->enabled){
            $return['socket']['server'] = $swaSocket->settings['server'];
        }
        $return['activeState'] = CloudService::CloudActive(true);
        $requireModule = $request->input('require');
        $requireModuleUrl = "";
        if (!empty($requireModule)){
            $moduleExists = ModuleService::localExists($requireModule);
            if ($moduleExists){
                //本地已存在
                $moduleInstalled = ModuleService::installCheck($requireModule);
                if (is_error($moduleInstalled) || !$moduleInstalled['installed']){
                    //本地未安装
                    $requireModuleUrl = wurl("module/install", ['nid'=>$requireModule]);
                }
            }else{
                //云端未安装
                $requireModuleUrl = wurl("module/require", ['nid'=>ModuleService::SysPrefix($requireModule)]);
            }
        }
        $return['requireModuleUrl'] = $requireModuleUrl;
        return $this->globalView('console.module', $return);
    }

    //分配应用模块到平台
    public function doAllocate(Request $request){
        $identity = $request->input('nid', "");
        if (empty($identity)){
            return $this->message("无效的应用标识");
        }
        $module = ModuleService::fetch($identity);
        list($platforms, $total) = AccountService::OwnerAccounts([], -1);
        if ($request->isMethod('post')){
            if (empty($platforms)){
                return $this->message(__('noPlatformAvailable'));
            }
            $ids = $request->input('ids', []);
            $moduleInfo = array(
                'name'=>$module['title'],
                'identity'=>$module['name'],
                'logo'=>$module['logo'],
                'profile'=>'default'
            );
            try {
                foreach ($platforms as $item){
                    $moduleList = AccountService::ExtraModules($item['uniacid'], false);
                    $allocated = !empty($ids[$item['uniacid']]);
                    if ($allocated){
                        //分配应用
                        if (!empty($moduleList[$identity])) continue;
                        $modules = array_values($moduleList);
                        $modules[] = $moduleInfo;
                        DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$item['uniacid']), array('modules'=>serialize($modules)));
                    }else{
                        //取消分配应用
                        if (empty($moduleList[$identity])) continue;
                        unset($moduleList[$identity]);
                        DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$item['uniacid']), array('modules'=>serialize(array_values($moduleList))));
                    }
                }
                CacheService::flush();
                $platformCount = count($ids);
                SystemLogs::userOperation('分配应用模块', 'module:allocate', "模块：{$identity}，分配到{$platformCount}个平台", true, ['module' => $identity, 'platform_count' => $platformCount]);
            }catch (\Exception $exception){
                SystemLogs::systemRunning(
                    '分配应用模块异常',
                    'module:allocate',
                    "分配应用模块过程中发生异常：{$exception->getMessage()}",
                    false,
                    [
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                        'exception_code' => $exception->getCode(),
                        'exception_trace' => $exception->getTrace(),
                        'module' => $identity,
                    ]
                );
                return $this->message($exception->getMessage());
            }
            $redirect = $request->input('referer', referer());
            return $this->message("successful", $redirect, "success");
        }
        if (!empty($platforms)){
            foreach ($platforms as &$item){
                $moduleList = AccountService::ExtraModules($item['uniacid'], false);
                $item['moduleExist'] = isset($moduleList[$identity]);
            }
        }
        return $this->globalView('console.module.allocate', [
            'title'=>__('分配应用权限'),
            'identity'=>$identity,
            'platforms'=>$platforms,
            'total'=>$total,
            'module'=>$module
        ]);
    }

    //通过卡密安装
    public function doPasscode(Request $request){
        $operation = $request->input('op', '');
        if (!empty($operation)){
            switch ($operation){
                case 'query':
                    $passcode = $request->input('code');
                    if (empty($passcode)) return $this->message('无效的卡密或兑换码');
                    $data = array(
                        'r'=>'cloud.package',
                        'identity'=>'',
                        'frompage'=>'passcode',
                        'code'=>$passcode
                    );
                    $res = CloudService::CloudApi("", $data);
                    if(is_error($res) || !isset($res['application'])){
                        return $this->message(is_error($res)?$res['message']:"应用解析失败");
                    }
                    return $this->message($res, "", "success");
                case 'consume' :
                    $passcode = $request->input('code');
                    if (empty($passcode)) return $this->message('无效的卡密或兑换码');
                    $data = array(
                        'r'=>'cloud.package.consume',
                        'identity'=>"",
                        'remark'=>'Install with a passcode',
                        'code'=>$passcode
                    );
                    $res = CloudService::CloudApi("", $data);
                    if(is_error($res) || !isset($res['application'])){
                        return $this->message(is_error($res)?$res['message']:"应用解析失败");
                    }
                    $moduleId = $res['moduleId'];
                    //判断本地是否已安装
                    $res['terminalUrl'] = wurl('module/require', ['nid'=>$moduleId]);
                    $moduleIdLocal = str_replace(ModuleService::SysPrefix(), "", $moduleId);
                    $ManiFest = ModuleService::installCheck($moduleIdLocal);
                    if (!is_error($ManiFest)){
                        //已安装
                        $res['terminalUrl'] = wurl('module/update', ['nid'=>$moduleIdLocal]);
                    }
                    return $this->message($res, "", "success");
                default :
                    return $this->message();
            }
        }
        return $this->globalView('console.module.passcode', ['title'=>__('兑换券')]);
    }

    /**
     * 停用云服务
    */
    public function doMaintenance(Request $request){
        $identity = $request->input('nid', "");
        if (!ModuleService::maintenance($identity)){
            return $this->message();
        }
        return $this->success('successful', referer());
    }

    /**
     * 从本地安装
     */
    public function doInstall(Request $request){
        $identity = $request->input('nid', "");
        $from = $request->input('from', 'apps');
        $install = ModuleService::install($identity, $from, 'local');
        $status = !is_error($install);
        SystemLogs::userOperation('安装应用模块', 'module:install', "模块：{$identity}（本地安装）", $status, ['module' => $identity]);
        if (!$status){
            return $this->TerminalError($install['message']);
        }
        return $this->message('installSuccessfully', wurl('module/allocate', ['nid'=>$identity]),'success');
    }

    /**
     * 从本地升级
    */
    public function doUpgrade(Request $request){
        $identity = $request->input('nid', "");
        $complete = ModuleService::upgrade($identity, 'local', $request->input('from', 'apps'));
        $status = !is_error($complete);
        SystemLogs::userOperation('升级应用模块', 'module:upgrade', "模块：{$identity}（本地升级）", $status, ['module' => $identity]);
        if (!$status){
            return $this->TerminalError($complete['message']);
        }
        CacheService::flush();
        return $this->message('upgradeSuccessfully', wurl('module'),'success');
    }

    /**
     * 从云端安装
     */
    public function doRequire(Request $request){
        $identity = $request->input('nid', "");
        $from = $request->input('from', 'apps');
        $cloudRequire = CloudService::RequireModule($identity, $from);
        $status = !is_error($cloudRequire);
        SystemLogs::userOperation('安装应用模块', 'module:require', "模块：{$identity}（云端安装）", $status, ['module' => $identity]);
        if (!$status){
            MSService::TerminalSend(["mode"=>"err", "message"=>$cloudRequire['message']], true);
            return $this->message($cloudRequire['message'], trim($cloudRequire['redirect']));
        }
        $realNid = str_replace(ModuleService::SysPrefix(), "", $identity);
        return $this->message('installSuccessfully', wurl('module/allocate', ['nid'=>$realNid]),'success');
    }

    /**
     * 从云端升级
     */
    public function doUpdate(Request $request){
        $identity = $request->input('nid', "");
        $cloudIdentity = ModuleService::SysPrefix($identity);
        $targetPath = public_path("addons/$identity/");
        if (!is_dir($targetPath)){
            $targetPath = base_path("apps/$identity/");
        }
        $res = CloudService::CloudUpdate($cloudIdentity, $targetPath);
        if (is_error($res)){
            MSService::TerminalSend(["mode"=>"err", "message"=>$res['message']], true);
            return $this->message($res['message'], trim($res['redirect']));
        }
        $moduleUpdate = ModuleService::upgrade($identity, 'cloud');
        $status = !is_error($moduleUpdate);
        SystemLogs::userOperation('升级应用模块', 'module:update', "模块：{$identity}（云端升级）", $status, ['module' => $identity]);
        if (!$status){
            return $this->TerminalError($moduleUpdate['message']);
        }
        $redirect = wurl('module');
        CacheService::flush();
        return $this->message('upgradeSuccessfully', $redirect,'success');
    }

    public function TerminalError($message){
        MSService::TerminalSend(["mode"=>"err", "message"=>$message], true);
        return $this->message($message);
    }

    /**
     * 卸载
     */
    public function doRemove(Request $request){
        $identity = $request->input('nid', "");
        $uninstall = ModuleService::uninstall($identity, $request->input('from', 'apps'));
        $status = !is_error($uninstall);
        SystemLogs::userOperation('卸载应用模块', 'module:remove', "模块：{$identity}", $status, ['module' => $identity]);
        if (!$status) return $this->TerminalError($uninstall['message']);
        return $this->message('uninstallComplete', wurl('module'),'success');
    }



}
