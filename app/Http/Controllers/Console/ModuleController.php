<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
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
    public function entry(Request $request, $modulename, $do='index'){
        global $_W;
        $WeModule = new WeModule();
        try {
            $site = $WeModule->create($modulename);
        }catch (\Exception $exception){
            if (DEVELOPMENT){
                throw $exception;
            }
            return $this->message($exception->getMessage());
        }
        $method = "doWeb" . ucfirst($do);
        DB::table('users_operate_history')->updateOrInsert(
            array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$modulename),
            array('createtime'=>TIMESTAMP,'type'=>2)
        );
        return $site->$method($request);
    }

    public function HttpRequest(Request $request, $moduleName, $segment1='index', $segment2='main'){
        global $_W;
        $WeModule = new WeModule();
        try {
            $site = $WeModule->create($moduleName);

            $className = "Addons\\".$moduleName."\app\Controllers\web\\".$segment1."Controller";
            $method = $segment2;
            if (!class_exists($className)){
                $className = "Addons\\".$moduleName."\app\Controllers\web\IndexController";
                $method = $segment1;
                $segment1 = 'index';
            }
            if(class_exists($className)){
                $instance = new $className();
                $instance->moduleName = $moduleName;
                $instance->moduleSite = $site;
                $instance->moduleConfig = (array)$site->module['config'];
                if (!method_exists($instance, $method)){
                    abort(404 );
                }
                DB::table('users_operate_history')->updateOrInsert(
                    array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$moduleName),
                    array('createtime'=>TIMESTAMP,'type'=>2)
                );
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
        return $this->message('installSuccessfully', wurl('module'),'success');
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
        return $this->message('installSuccessfully', wurl('module'),'success');
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
