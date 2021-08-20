<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CloudService;
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
                    $complete = CloudService::CloudEnv('APP_SITEID=0', "APP_SITEID={$activestate['siteid']}");
                    if (!$complete){
                        return $this->message('文件写入失败，请检查根目录权限');
                    }
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
        $ajaxviews = array('socketset'=>'socketset','pageset'=>'settingpage','sockethelp'=>'socket','attachset'=>'settingupload');
        $return = array('title'=>'站点设置','op'=>$op,'components'=>array());
        if (!isset($_W['setting']['page'])){
            $_W['setting']['page'] = $_W['page'];
        }
        if (isset($ajaxviews[$op])){
            return $this->globalview("console.{$ajaxviews[$op]}",$return);
        }
        if (!isset($_W['setting']['swasocket']['whitelist'])){
            $_W['setting']['swasocket']['whitelist'] = CloudService::CloudSocket('',1);
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
        if ($op=='socket'){
            $return['usersign'] = md5("{$_W['uid']}-{$_W['config']['setting']['authkey']}-{$_W['config']['site']['id']}");
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
            $component = DB::table('gxswa_cloud')->where('id',intval($_GPC['cid']))->first(['id','identity','type','online','releasedate','rootpath']);
            if (empty($component)) return $this->message('找不到该服务组件');
            $cloudinfo = $this->checkcloud($component);
            if (is_error($cloudinfo)){
                return $this->message($cloudinfo['message']);
            }
            return $this->message('检测完成！',url('console/setting/component'),'success');
        }elseif ($op=='comupdate'){
            $component = DB::table('gxswa_cloud')->where('id',intval($_GPC['cid']))->first(['id','identity','modulename','type','releasedate','rootpath']);
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
                //模块升级
                $identity = !empty($component['modulename']) ? $component['modulename'] : $component['identity'];
                $moduleupdate = ModuleService::upgrade($identity);
                if (is_error($moduleupdate)) return $this->message($moduleupdate['message']);
            }else{
                if ($component['type']==0){
                    //框架升级，未完善
                    //Artisan::call('whotalk:upgrade');
                    //更新路由
                    Artisan::call('route:clear');
                }
                if ($component['identity']=='laravel_whotalk_socket'){
                    //更新SOCKET初始化
                    SocketService::InitShell();
                }
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
        $fromcache = true;
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
        $ugradeinfo['isnew'] = false;
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
        $structures = json_decode(base64_decode($structure), true);
        return CloudService::CloudCompare($structures,$targetpath);
    }

    public function hasdifference($difference,$type=0){
        if (empty($difference)) return false;
        if ($type==3){
            foreach ($difference as $key=>$value){
                if(!is_array($value)){
                    $fileinfo = explode('|', $value);
                    if ($fileinfo[0]=='install_socket.sh' && file_exists(base_path('socket/install_socket.sh'))){
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
        if ($op=='savewhitelist'){
            $active = CloudService::CloudActive();
            if ($active['status']!=1){
                return $this->message('该功能已暂停使用');
            }
            $complete = CloudService::CloudSocket(trim($_GPC['domain']));
            if ($complete){
                return $this->message('新域名添加成功',url('console/setting/socket'),'success');
            }
        }elseif ($op=='resetwhitelist'){
            $active = CloudService::CloudActive();
            if ($active['status']!=1){
                return $this->message('该功能已暂停使用');
            }
            $complete = CloudService::CloudSocket('',2);
            if ($complete){
                return $this->message('重置成功',url('console/setting/socket'),'success');
            }
        }elseif ($op=='socketset'){
            $active = CloudService::CloudActive();
            if ($active['status']!=1){
                return $this->message('该功能已暂停使用');
            }
            $data = $_GPC['data'];
            $data['type'] = in_array($data['type'],array('local','remote')) ? $data['type'] : 'remote';
            if (empty($data['server'])) return $this->message('SOCKET服务器不能为空');
            if (empty($data['api'])) return $this->message('WEB推送接口不能为空');
            if (!\Str::endsWith($data['api'],'/api/message/sendMessageToUser')) return $this->message('推送接口格式不正确');
            $config = $_W['setting']['swasocket'];
            $config['type'] = $data['type'];
            $config['server'] = $data['server'];
            $config['api'] = $data['api'];
            $complete = SettingService::Save($config,'swasocket');
            if ($complete){
                return $this->message('保存成功',url('console/setting/socket'),'success');
            }
        }elseif ($op=='attachset'){
            $config = $_W['setting']['upload'];
            $config['image']['extentions'] = !empty($_GPC['imgextentions']) ? explode(',',trim($_GPC['imgextentions'])) : array();
            $config['image']['limit'] = intval($_GPC['imglimit']);
            $config['image']['zip_percentage'] = intval($_GPC['imgzip']);
            $config['image']['zip_percentage'] = min(100,max(0,$config['image']['zip_percentage']));
            if ($config['image']['zip_percentage']==0) $config['image']['zip_percentage'] = 100;
            $config['media']['extentions'] = !empty($_GPC['mediaext']) ? explode(',',trim($_GPC['mediaext'])) : array();
            $config['media']['limit'] = intval($_GPC['medialimit']);
            $complete = SettingService::Save($config,'upload');
            if ($complete){
                return $this->message('保存成功',url('console/setting/attach'),'success');
            }
        }elseif ($op=='pageset'){
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
