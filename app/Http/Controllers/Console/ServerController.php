<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\MSService;
use Illuminate\Http\Request;

class ServerController extends Controller
{

    public function HttpRequest($server,$param1='index',$param2=""){
        global $_W;
        $_W['server'] = trim($server);
        $_W['inserver'] = true;
        $_W['basescript'] = "server";
        $ctrl = trim($param1);
        if (!empty($param2)){
            $ctrl = implode("/", array($ctrl, trim($param2)));
        }
        if (!function_exists('tpl_compile')){
            include_once base_path("bootstrap/helper_tpl.php");
        }
        $data = serv($_W['server'])->HttpRequest('web', $ctrl);
        if (is_error($data)){
            return $this->message($data['message']);
        }
        if (isset($data['message']) && isset($data['type'])){
            return $this->message($data["message"], $data['redirect'], $data['type']);
        }
        return $this->globalview("server.".str_replace("/",".", $ctrl),$data);
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
            case "restore" : {
                if (MSService::restore($identity)){
                    return $this->message('操作成功',wurl("server", array('op'=>'stop')),'success');
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
