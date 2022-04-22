<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\CloudService;
use App\Services\FileService;
use App\Services\ModuleService;
use App\Services\SettingService;
use App\Services\SocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
                $_W['config']['site']['id'] = $activestate['siteid'];
                $complete = CloudService::CloudEnv('APP_SITEID=0', "APP_SITEID={$activestate['siteid']}");
                if (!$complete){
                    return $this->message('文件写入失败，请检查根目录权限');
                }
                return $this->message('恭喜您，激活成功！',url('console'),'success');
            }else{
                $redirect = CloudService::$cloudactive . $_W['siteroot'];
                return $this->message('您的站点尚未激活，即将进入激活流程...',$redirect,'error');
            }
        }
        return $this->message('您的站点已激活',url('console'),'success');
    }

    public function detection(){
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','type','online','releasedate','rootpath']);
        if (empty($component)) return $this->message('系统出现致命错误');
        $cloudinfo = $this->checkcloud($component,1,true);
        if (is_error($cloudinfo)){
            return $this->message($cloudinfo['message']);
        }
        return $this->message('检测完成！',url('console/setting'),'success');
    }

    public function selfupgrade(){
        try {
            Artisan::call('self:update');
        }catch (\Exception $exception){
            return $this->message($exception->getMessage());
        }
        return $this->message('恭喜您，升级成功！', url('console/setting'),'success');
    }

    public function index($op='main'){
        global $_W,$_GPC;
        if($op=='detection'){
            return $this->detection();
        }elseif ($op=='selfupgrade'){
            return $this->selfupgrade();
        }
        $return = array('title'=>'站点设置','op'=>$op,'components'=>array());
        if (!isset($_W['setting']['page'])){
            $_W['setting']['page'] = $_W['page'];
        }
        if (!isset($_W['setting']['remote'])){
            $_W['setting']['remote'] = array('type'=>0);
        }
        $return['attachs'] = array('关闭','FTP','阿里云存储','七牛云存储','腾讯云存储','亚马逊S3');
        if ($op=='pageset'){
            return $this->globalview("console.pageset",$return);
        }
        if ($op=='envdebug'){
            $debug = env('APP_DEBUG',false);
            if ($debug){
                $complete = CloudService::CloudEnv('APP_DEBUG=true','APP_DEBUG=false');
            }else{
                $complete = CloudService::CloudEnv('APP_DEBUG=false','APP_DEBUG=true');
            }
            if (!$complete){
                return $this->message('文件写入失败，请检查根目录权限');
            }
            return $this->message('操作成功！',url('console/setting'),'success');
        }
        if ($op=='plugin'){
            $return['types'] = array('框架','应用','服务','资源');
            $return['colors'] = array('red','blue','green','orange');
            $return['components'] = CloudService::getPlugins();
        }elseif ($op=='comcheck'){
            $component = DB::table('gxswa_cloud')->where('id',intval($_GPC['cid']))->first(['id','identity','type','online','releasedate','rootpath']);
            if (empty($component)) return $this->message('找不到该服务组件');
            $cloudinfo = $this->checkcloud($component);
            if (is_error($cloudinfo)){
                return $this->message($cloudinfo['message']);
            }
            $redirect = url('console/setting/plugin');
            return $this->message('检测完成！',$redirect,'success');
        }elseif ($op=='comupdate'){
            $component = DB::table('gxswa_cloud')->where('id',intval($_GPC['cid']))->first(['id','identity','modulename','type','releasedate','rootpath']);
            if (empty($component)) return $this->message('找不到该组件信息');
            $cloudinfo = $this->checkcloud($component,2);
            if ($cloudinfo['isnew']){
                $basepath = $component['type']==2 ? CloudService::com_path() : base_path($component['rootpath']);
                if ($component['type']==0) $basepath = base_path() . "/";
                $cloudupdate = CloudService::CloudUpdate($component['identity'],$basepath);
                if (is_error($cloudupdate)){
                    return $this->message($cloudupdate['message']);
                }
            }
            if ($component['type']==1){
                //模块升级
                $identity = !empty($component['modulename']) ? $component['modulename'] : $component['identity'];
                $moduleupdate = ModuleService::upgrade($identity);
                if (is_error($moduleupdate)) return $this->message($moduleupdate['message']);
            }else{

                unset($cloudinfo['structure'],$cloudinfo['difference']);
                $cloudinfo['isnew'] = false;
                DB::table('gxswa_cloud')->where('id',$component['id'])->update(array(
                    'version'=>$cloudinfo['version'],
                    'updatetime'=>TIMESTAMP,
                    'dateline'=>TIMESTAMP,
                    'releasedate'=>$cloudinfo['releasedate'],
                    'online'=>serialize($cloudinfo)
                ));
            }
            $redirect = url('console/setting/plugin');
            CacheService::flush();
            return $this->message('恭喜您，升级成功！', $redirect,'success');
        }elseif($op=='comremove'){
            $component = DB::table('gxswa_cloud')->where('id',intval($_GPC['cid']))->first(['id','identity','modulename','type','releasedate','rootpath']);
            if ($component['type']==1){
                $identity = !empty($component['modulename']) ? $component['modulename'] : $component['identity'];
                $uninstall = ModuleService::uninstall($identity);
                if (is_error($uninstall)) return $this->message($uninstall['message']);
            }else{
                return $this->message("暂不支持此类应用卸载");
            }
            if (!DEVELOPMENT){
                FileService::rmdirs(base_path($component['rootpath']));
            }
            return $this->message('卸载完成', url('console/setting/plugin'),'success');
        }elseif($op=='plugininst'){
            $install = ModuleService::install(trim($_GPC['nid']), 'addons', 'local');
            if (is_error($install)) return $this->message($install['message']);
            return $this->message('恭喜您，安装完成！', url('console/setting/plugin'),'success');
        }elseif ($op=='pluginrm'){
            $uninstall = ModuleService::uninstall(trim($_GPC['nid']));
            if (is_error($uninstall)) return $this->message($uninstall['message']);
            return $this->message('卸载完成', url('console/setting/plugin'),'success');
        }elseif ($op=='cloudinst'){
            $cloudrequire = CloudService::RequireModule(trim($_GPC['nid']));
            if (is_error($cloudrequire)) return $this->message($cloudrequire['message']);
            return $this->message('恭喜您，安装完成！', url('console/setting/plugin'),'success');
        }else{
            $framework = DB::table('gxswa_cloud')->where('type',0)->first(['id','version','identity','type','online','releasedate','rootpath']);
            $return['framework'] = $framework;
            $return['cloudinfo'] = !empty($framework['online']) ? unserialize($framework['online']) : array('isnew'=>false);
        }
        return $this->globalview('console.setting', $return);
    }

    public function checkcloud($component,$compare=1,$nocache=false){
        $cachekey = "cloud:structure:{$component['identity']}";
        $ugradeinfo = array();
        $fromcache = true;
        if (!$nocache){
            $ugradeinfo = Cache::get($cachekey);
        }
        if (empty($ugradeinfo)){
            $fromcache = false;
            $data = array(
                'identity'=>$component['identity']
            );
            $ugradeinfo = CloudService::CloudApi('structure',$data);
            if (is_error($ugradeinfo)) return $ugradeinfo;
        }
        if ($compare==0) return $ugradeinfo;
        $structure = $ugradeinfo['structure'];
        $ugradeinfo['difference'] = $this->compare($component,$ugradeinfo['structure']);
        if ($component['releasedate']<$ugradeinfo['releasedate'] && $compare<2){
            $ugradeinfo['isnew'] = true;
        }else{
            $ugradeinfo['isnew'] = $this->hasdifference($ugradeinfo['difference'],$component['type']);
        }
        if ($fromcache){
            $onlineinfo = $component['online'] ? unserialize($component['online']) : array();
            if ($onlineinfo['isnew']==$ugradeinfo['isnew']){
                return $ugradeinfo;
            }
        }
        $difference = $ugradeinfo['difference'];
        unset($ugradeinfo['structure'],$ugradeinfo['difference']);
        $update = array('dateline'=>TIMESTAMP,'online'=>serialize($ugradeinfo));
        pdo_update('gxswa_cloud',$update,array('identity'=>$component['identity']));
        $ugradeinfo['structure'] = $structure;
        $ugradeinfo['difference'] = $difference;
        Cache::put($cachekey,$ugradeinfo,1800);
        return $ugradeinfo;
    }

    public function compare($component,$structure=''){
        if (!$structure) return array();
        $targetpath = $component['type']==2 ? CloudService::com_path() : base_path($component['rootpath']);
        if ($component['type']==0) $targetpath = base_path() . "/";
        $structures = json_decode(base64_decode($structure), true);
        return CloudService::CloudCompare($structures,$targetpath);
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
        global $_W,$_GPC;
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
                return $this->message('保存成功',url('console/setting'),'success');
            }
        }
        return $this->message();
    }

}
