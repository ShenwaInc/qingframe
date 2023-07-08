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
    //

    public function entry(Request $request, $modulename, $do='index'){
        global $_W;
        $WeModule = new WeModule();
        try {
            $site = $WeModule->create($modulename);
        }catch (\Exception $exception){
            return $this->message('模块初始化失败，请联系技术处理');
        }
        $method = "doWeb" . ucfirst($do);
        DB::table('users_operate_history')->updateOrInsert(
            array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$modulename),
            array('createtime'=>TIMESTAMP,'type'=>2)
        );
        return $site->$method($request);
    }

    public function index(Request $request, $option='list'){
        global $_W;
        if (empty($_W['config']['site']['id'])){
            return redirect("console/active");
        }
        $method = "do".ucfirst($option);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        $return = array('title'=>'应用管理', 'op'=>'plugin');
        $return['types'] = array('框架','应用','服务','资源');
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
        return $this->globalView('console.module', $return);
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
        return $this->message('恭喜您，安装完成！', url('console/module'),'success');
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
        return $this->message('恭喜您，升级成功！', url('console/module'),'success');
    }

    /**
     * 从云端安装
     */
    public function doRequire(Request $request){
        $identity = $request->input('nid', "");
        $cloudrequire = CloudService::RequireModule($identity);
        if (is_error($cloudrequire)){
            MSService::TerminalSend(["mode"=>"err", "message"=>$cloudrequire['message']], true);
            return $this->message($cloudrequire['message'], trim($cloudrequire['redirect']));
        }
        return $this->message('恭喜您，安装完成！', url('console/module'),'success');
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
        $redirect = url('console/module');
        CacheService::flush();
        return $this->message('恭喜您，升级成功！', $redirect,'success');
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
        return $this->message('卸载完成', url('console/module'),'success');
    }



}
