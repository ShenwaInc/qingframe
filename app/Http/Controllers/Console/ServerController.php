<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\MSService;
use Illuminate\Http\Request;

class ServerController extends Controller
{

    public function HttpRequest($server,$segment1='',$segment2=""){
        global $_W, $_GPC;
        $_W['server'] = trim($server);
        $_W['inserver'] = true;
        $_W['basescript'] = "server";
        if (!function_exists('tpl_compile')){
            include_once app_path("Helpers/smarty.php");
        }
        $service = serv($_W['server']);
        if (!method_exists($service, 'initServer')){
            return $this->message($service->error);
        }
        $ctrl = trim($segment1);
        if (!empty($segment2)){
            $ctrl = implode("/", array($ctrl, trim($segment2)));
        }
        if ($service->Unique && empty($_W['uniacid'])){
            if (!empty($_GPC['i'])){
                $_W['uniacid'] = intval($_GPC['i']);
            }
            if (!empty($_GPC['uniacid'])){
                $_W['uniacid'] = intval($_GPC['uniacid']);
            }
            if (empty($_W['uniacid'])){
                return $this->checkout($service->url($ctrl));
            }
            $service->uniacid = $_W['uniacid'];
            session()->put('uniacid',$_W['uniacid']);
        }
        $data = $service->HttpRequest('web', $ctrl);
        if (is_error($data)){
            return $this->message($data['message']);
        }
        if (isset($data['message']) && isset($data['type'])){
            return $this->message($data["message"], $data['redirect'], $data['type']);
        }
        return $this->globalview("server.".str_replace("/",".", $ctrl),$data);
    }

    public function checkout($refresh=''){
        global $_GPC, $_W;
        if (!empty($_GPC['uniacid'])){
            if (empty($refresh)){
                $refresh = referer();
            }
            session()->put('uniacid',intval($_GPC['uniacid']));
            $refresh = preg_replace('/[\?|\&]uniacid=[\d]+/', "", $refresh);
            header("location:$refresh");
            session_exit();
        }
        $data = array(
            'refresh'=>$refresh,
            'uniacid'=>intval($_W['uniacid']),
            'platforms'=>AccountService::OwnerAccounts(array(), -1, true)
        );
        return $this->globalview("console.server.platform",$data);
    }

    public function index(Request $request){
        $op = $request->get("op","index");
        $identity = $request->get("nid", "");
        $return = array("title"=>"微服务管理", "op"=>$op);
        $MSS = new MSService();
        switch ($op){
            case "stop" : {
                $return['title'] .= " - 已停用";
                $return['servers'] = MSService::InitService(0);
                break;
            }
            case "local" : {
                $return['title'] .= " - 未安装";
                $return['servers'] = MSService::getlocal();
                $cloudservers = MSService::cloudservers();
                if (!empty($cloudservers)){
                    $return['servers'] = array_merge($return['servers'], $cloudservers);
                }
                break;
            }
            case "install" : {
                $res = $MSS->install($identity);
                if (is_error($res)){
                    return $this->message($res['message']);
                }
                return $this->message("安装成功", wurl("server"), "success");
            }
            case "uninstall" :{
                $res = $MSS->uninstall($identity);
                if (is_error($res)){
                    return $this->message($res['message']);
                }
                return $this->message('服务已卸载完成',wurl("server"),'success');
            }
            case "disable" : {
                if (MSService::disable($identity)){
                    return $this->message('操作成功',wurl("server"),'success');
                }
                return $this->message();
            }
            case "upgrade" : {
                $res = $MSS->upgrade($identity);
                if (is_error($res)){
                    return $this->message($res['message']);
                }
                return $this->message("升级成功", wurl("server"), "success");
            }
            case "cloudup" : {
                $res = $MSS->cloudUpdate($identity);
                if (is_error($res)){
                    return $this->message($res['message']);
                }
                return $this->message("升级成功", wurl("server"), "success");
            }
            case "cloudinst" : {
                $res = $MSS->cloudInstall($identity);
                if (is_error($res)){
                    return $this->message($res['message']);
                }
                return $this->message("安装成功", wurl("server"), "success");
            }
            case "restore" : {
                if (MSService::restore($identity)){
                    return $this->message('操作成功',wurl("server", array('op'=>'stop')),'success');
                }
                return $this->message();
            }
            case "cloudChk" : {
                $cloudServer = $MSS->cloudserver($identity);
                if (!is_error($cloudServer)){
                    $service = $MSS::getone($identity);
                    $release = $cloudServer['release'];
                    if (version_compare($release['version'], $service['version'], '>') || $release['releasedate']>$service['releases']){
                        return '<a class="layui-btn layui-btn-sm layui-btn-danger confirm" data-text="升级前请做好数据备份" lay-tips="该服务可升级至V'.$release['version'].'Release'.$release['releasedate'].'" href="'.wurl('server', array('op'=>'cloudup', 'nid'=>$service['identity'])).'">升级</a>';
                    }
                }
                return $this->message();
            }
            default : {
                $return['op'] = "index";
                $return['servers'] = MSService::InitService();
            }
        }
        return $this->globalview("console.server", $return);
    }

    public function Methods($server=""){
        $server = serv($server);
        $methods = $server->getMethods();
        if (is_error($methods)) message($methods['message'],"","error");
        if (!empty($methods['wiki']) && count($methods)==1){
            header("location:{$methods['wiki']}");
            exit();
        }
        $wiki = $methods['wiki'];
        unset($methods['wiki']);
        $service = $server->service;
        return $this->globalview("console.server.method", array(
            'title'=>$service['name'],
            'service'=>$service,
            'classname' => ucfirst($service['identity']),
            'wiki'=>$wiki,
            'methods'=>$methods
        ));
    }

    public function Apis($server=""){
        $service = serv($server);
        $apis = $service->getApis();
        if(empty($apis['schemas'])){
            if (!empty($apis['wiki'])){
                header("location:{$apis['wiki']}");
                exit();
            }else{
                message("该服务未提供任何接口");
            }
        }
        return $this->globalview("console.server.api", array(
            'title'=>$service->service['name'],
            'schemas'=>$apis['schemas']
        ));
    }

}
