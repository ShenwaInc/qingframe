<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CloudService;
use App\Services\ModuleService;
use App\Services\SocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    //
    public function active($op=''){
        global $_W;
        if(!$_W['isfounder']){
            return $this->message('暂无权限');
        }
        if ($_W['config']['site']['id']==0){
            $activestate = CloudService::CloudActive();
            if ($activestate['status']==1){
                $steps = array('com','module','socket','initsite');
                $op = in_array($op, $steps) ? trim($op) : 'index';
                if ($op=='com'){
                    //获取组件安装
                    $loadcomponent = CloudService::RequireCom();
                    if (is_error($loadcomponent)) return $this->message($loadcomponent['message']);
                    $redirect = url('console/active',array('op'=>'module'));
                    return $this->message('组件加载完成！即将初始化模块...',$redirect,'success');
                }elseif ($op=='module'){
                    //获取模块安装
                    $cloudrequire = CloudService::RequireModule($_W['config']['defaultmodule'],'addons');
                    if (is_error($cloudrequire)) return $this->message($cloudrequire['message']);
                    $redirect = url('console/active',array('op'=>'socket'));
                    return $this->message('模块初始化完成！即将初始化SOCKET...',$redirect,'success');
                }elseif ($op=='socket'){
                    $socketdir = base_path("socket/");
                    if (!is_dir($socketdir)){
                        $cloudrequire = CloudService::CloudRequire("laravel_whotalk_socket", $socketdir);
                        if (is_error($cloudrequire)) return $this->message($cloudrequire['message']);
                        $socketinit = SocketService::Initializer();
                        if (!$socketinit){
                            return $this->message('SOCKET初始化失败');
                        }
                    }
                    $redirect = url('console/active',array('op'=>'initsite'));
                    return $this->message('SOCKET初始化完成！即将激活站点...',$redirect,'success');
                }elseif ($op=='initsite'){
                    $envfile = base_path(".env");
                    $reader = fopen($envfile,'r');
                    $envdata = fread($reader,filesize($envfile));
                    fclose($reader);
                    $envdata = str_replace('APP_SITEID=0',"APP_SITEID={$activestate['siteid']}",$envdata);
                    $writer = fopen($envfile,'w');
                    if(!fwrite($writer,$envdata)){
                        fclose($writer);
                        return $this->message('文件写入失败，请检查根目录权限');
                    }
                    fclose($writer);
                    return $this->message('恭喜您，激活成功！',url('console'),'success');
                }
                return $this->message('站点激活成功！即将加载程序所需的源码库',url('console/active',array('op'=>'com')),'success');
            }else{
                $redirect = CloudService::$cloudactive . $_W['siteroot'];
                return $this->message('您的站点尚未激活，即将进入激活流程...',$redirect,'error');
            }
        }
        return $this->message('您的站点已激活','','success');
    }

    public function index($op='main'){
        global $_W,$_GPC;
        if($op=='pageset'){
            return $this->globalview('console.settingpage');
        }
        $return = array('title'=>'站点设置','op'=>$op,'components'=>array());
        if (!isset($_W['setting']['page'])){
            $_W['setting']['page'] = $_W['page'];
        }
        if ($op=='component'){
            $return['types'] = array('框架','模块','组件','资源');
            $return['colors'] = array('red','blue','green','orange');
            $components = DB::table('gxswa_cloud')->orderByRaw("`type` asc,`id` asc")->get()->toArray();
            if (!empty($components)){
                foreach ($components as &$com){
                    $com['logo'] = asset($com['logo']);
                    $com['lastupdate'] = $com['updatetime'] ? date('Y/m/d H:i',$com['updatetime']) : '初始安装';
                    $com['cloudinfo'] = !empty($com['online']) ? unserialize($com['online']) : array();
                    $com['installtime'] = date('Y/m/d H:i',$com['addtime']);
                }
                $return['components'] = $components;
            }
        }elseif ($op=='comcheck'){
            $component = DB::table('gxswa_cloud')->where('id',intval($_GPC['cid']))->first(['id','identity','type','releasedate','rootpath']);
            if (empty($component)) return $this->message('找不到该服务组件');
            $cloudinfo = $this->checkcloud($component);
            if (is_error($cloudinfo)){
                return $this->message($cloudinfo['message']);
            }
            return $this->message('检测完成！',url('console/setting/component'),'success');
        }elseif ($op=='comupdate'){
            $component = DB::table('gxswa_cloud')->where('id',intval($_GPC['cid']))->first(['id','identity','type','releasedate','rootpath']);
            if (empty($component)) return $this->message('找不到该组件信息');
            $cloudinfo = $this->checkcloud($component,2);
            if ($cloudinfo['isnew']){
                $basepath = $component['type']==2 ? CloudService::com_path() : base_path($component['rootpath']);
                $cloudupdate = CloudService::CloudUpdate($component['identity'],$basepath);
                if (is_error($cloudupdate)){
                    return $this->message($cloudupdate['message']);
                }
            }
            if ($component['type']==1){
                $moduleupdate = ModuleService::upgrade($component['identity']);
                if (is_error($moduleupdate)) return $this->message($moduleupdate['message']);
            }else{
                unset($cloudinfo['structure']);
                $cloudinfo['isnew'] = false;
                DB::table('gxswa_cloud')->where('id',$component['id'])->update(array(
                    'version'=>$cloudinfo['version'],
                    'updatetime'=>TIMESTAMP,
                    'dateline'=>TIMESTAMP,
                    'releasedate'=>$cloudinfo['releasedate'],
                    'online'=>serialize($cloudinfo)
                ));
            }
            return $this->message('恭喜您，升级成功！',url('console/setting/component'),'success');
        }
        return $this->globalview('console.setting', $return);
    }

    public function checkcloud($component,$compare=1,$nocache=false){
        $cachekey = "cloud:structure:{$component['identity']}";
        $ugradeinfo = $nocache ? array() : Cache::get($cachekey,array());
        $fromcache = true;
        if (empty($ugradeinfo)){
            $fromcache = false;
            $data = array(
                'identity'=>$component['identity']
            );
            $ugradeinfo = CloudService::CloudApi('structure',$data);
            if (is_error($ugradeinfo)) return $ugradeinfo;
            Cache::put($cachekey,$ugradeinfo,1800);
        }
        if ($compare==0) return $ugradeinfo;
        $structure = $ugradeinfo['structure'];
        $ugradeinfo['isnew'] = false;
        $ugradeinfo['difference'] = array();
        if ($component['releasedate']<$ugradeinfo['releasedate'] && $compare<2){
            $ugradeinfo['isnew'] = true;
        }else{
            $difference = $this->compare($component,$ugradeinfo['structure']);
            $hasdifference = $this->hasdifference($difference,$component['type']);
            if ($hasdifference){
                $ugradeinfo['isnew'] = true;
                $ugradeinfo['difference'] = $difference;
            }
        }
        if ($fromcache) return $ugradeinfo;
        unset($ugradeinfo['structure']);
        $update = array('dateline'=>TIMESTAMP,'online'=>serialize($ugradeinfo));
        pdo_update('gxswa_cloud',$update,array('identity'=>$component['identity']));
        $ugradeinfo['structure'] = $structure;
        return $ugradeinfo;
    }

    public function compare($component,$structure=''){
        if (!$structure) return array();
        $targetpath = $component['type']==2 ? CloudService::com_path() : $component['rootpath'];
        $structures = json_decode(base64_decode($structure), true);
        return CloudService::CloudCompare($structures,$targetpath);
    }

    public function hasdifference($difference,$type=0){
        if (empty($difference)) return false;
        return true;
    }

    public function save(Request $request){
        global $_W,$_GPC;
        $op = $request->input('op');
        if (!$request->isMethod('post')){
            return $this->message();
        }
    }

}
