<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CloudService;
use Illuminate\Http\Request;

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
                    return $this->message('模块初始化完成！即将获取SOCKET源码...',$redirect,'success');
                }elseif ($op=='socket'){
                    $socketdir = base_path("socket/");
                    if (!is_dir($socketdir)){
                        $cloudrequire = CloudService::CloudRequire("laravel_whotalk_socket", $socketdir);
                        if (is_error($cloudrequire)) return $this->message($cloudrequire['message']);
                        //安装SOCKET白名单，待完善
                    }
                    $redirect = url('console/active',array('op'=>'initsite'));
                    return $this->message('SOCKET源码获取成功！即将激活站点...',$redirect,'success');
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
        global $_W;
        if($op=='page'){
            return $this->globalview('console.settingpage');
        }
        $return = array('title'=>'站点设置','op'=>$op);
        if (!isset($_W['setting']['page'])){
            $_W['setting']['page'] = $_W['page'];
        }
        return $this->globalview('console.setting', $return);
    }

}
