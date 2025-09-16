<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\CacheService;
use App\Services\CloudService;
use App\Services\ModuleService;
use App\Services\MSService;
use App\Utils\WeModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{

    /**
     * @throws \Exception
     */
    public function entry(Request $request, $moduleName, $do='index'){
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
            if (empty($site)){
                abort(404, "Module {$moduleName} not found");
            }
        }catch (\Exception $exception){
            if (DEVELOPMENT){
                throw $exception;
            }
            return $this->message($exception->getMessage());
        }
        $method = "doWeb" . ucfirst($do);
        DB::table('users_operate_history')->updateOrInsert(
            array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$moduleName),
            array('createtime'=>TIMESTAMP,'type'=>2)
        );
        $res = $site->$method($request);
        if(is_error($res)){
            return $this->message($res['message']);
        }
        return $res;
    }

    public function HttpRequest(Request $request, $moduleName, $segment1='index', $segment2='main'){
        global $_W;
        $WeModule = new WeModule();
        try {
            $site = $WeModule->create($moduleName);
            if (empty($site)){
                abort(404, "Module {$moduleName} not found");
            }

            $className = "Addons\\".$moduleName."\app\Controllers\web\\".ucfirst($segment1)."Controller";
            $method = $segment2;
            if (!class_exists($className)){
                $className = "Addons\\".$moduleName."\app\Controllers\web\IndexController";
                $method = $segment1;
                $segment1 = 'index';
            }
            if(class_exists($className)){
                $instance = new $className();
                if (!method_exists($instance, $method)){
                    abort(404, "Call to undefined member {$method} of {$className}");
                }
                //记录操作日志
                DB::table('users_operate_history')->updateOrInsert(
                    array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$moduleName),
                    array('createtime'=>TIMESTAMP,'type'=>2)
                );
                $instance->moduleName = $moduleName;
                $instance->moduleSite = $site;
                $instance->moduleConfig = (array)$site->module['config'];
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
            }catch (\Exception $exception){
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
        $install = ModuleService::install($identity, 'addons', 'local');
        if (is_error($install)){
            return $this->TerminalError($install['message']);
        }
        return $this->message('installSuccessfully', wurl('module/allocate', ['nid'=>$identity]),'success');
    }

    /**
     * 从本地升级
    */
    public function doUpgrade(Request $request){
        $identity = $request->input('nid', "");
        $complete = ModuleService::upgrade($identity);
        if (is_error($complete)){
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
        $cloudRequire = CloudService::RequireModule($identity);
        if (is_error($cloudRequire)){
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
        $res = CloudService::CloudUpdate($cloudIdentity, $targetPath);
        if (is_error($res)){
            MSService::TerminalSend(["mode"=>"err", "message"=>$res['message']], true);
            return $this->message($res['message'], trim($res['redirect']));
        }
        $moduleUpdate = ModuleService::upgrade($identity, 'cloud');
        if (is_error($moduleUpdate)){
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
        $uninstall = ModuleService::uninstall($identity);
        if (is_error($uninstall)) return $this->TerminalError($uninstall['message']);
        return $this->message('uninstallComplete', wurl('module'),'success');
    }



}
