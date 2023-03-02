<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\CloudService;
use App\Services\ModuleService;
use App\Utils\WeModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    //

    public function entry(Request $request, $modulename,$do='index'){
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
        if (!function_exists('message')){
            include_once app_path("Helpers/web.php");
        }
        return $site->$method($request);
    }

    public function index(Request $request, $option='list'){
        $method = "do".ucfirst($option);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        $return = array('title'=>'应用管理', 'op'=>'plugin');
        $return['types'] = array('框架','应用','服务','资源');
        $return['colors'] = array('red','blue','green','orange');
        $return['components'] = CloudService::getPlugins();
        return $this->globalview('console.module', $return);
    }

    /**
     * 从本地安装
     */
    public function doInstall(Request $request){
        $identity = $request->input('nid', "");
        $install = ModuleService::install($identity, 'addons', 'local');
        if (is_error($install)) return $this->message($install['message']);
        return $this->message('恭喜您，安装完成！', url('console/module'),'success');
    }

    /**
     * 从本地升级
    */
    public function doUpgrade(Request $request){
        $identity = $request->input('nid', "");
        $complete = ModuleService::upgrade($identity);
        if (is_error($complete)) return $this->message($complete['message'], trim($complete['redirect']));
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
            return $this->message($res['message']);
        }
        $moduleUpdate = ModuleService::upgrade($identity, 'cloud');
        if (is_error($moduleUpdate)) return $this->message($moduleUpdate['message']);
        $redirect = url('console/module');
        CacheService::flush();
        return $this->message('恭喜您，升级成功！', $redirect,'success');
    }

    /**
     * 卸载
     */
    public function doRemove(Request $request){
        $identity = $request->input('nid', "");
        $uninstall = ModuleService::uninstall($identity);
        if (is_error($uninstall)) return $this->message($uninstall['message']);
        return $this->message('卸载完成', url('console/setting/plugin'),'success');
    }



}
