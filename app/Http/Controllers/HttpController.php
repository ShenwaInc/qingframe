<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AppRuntime;

class HttpController extends Controller
{

    public function ServerApi($server, $segment1='index', $segment2=''){
        global $_W;
        $_W['isapi'] = true;
        $ctrl = trim($segment1);
        if (!empty($segment2)){
            $ctrl = implode("/", array($ctrl, trim($segment2)));
        }
        $service = serv($server);
        if (is_error($service) || !$service->enabled){
            abort(404);
        }
        $uniacid = request()->input('i', SITEACID);
        if ($service->Unique){
            (new AppRuntime())->Runtime($uniacid, request()->header('x-auth-token'));
        }
        $data = $service->HttpRequest('api', $ctrl);
        if (is_error($data)) return $this->message($data['message'], trim($data['redirect']));
        if (!is_array($data)) return $data;
        if (isset($data['message']) && isset($data['type'])){
            return $this->message($data["message"], trim($data['redirect']), $data['type']);
        }
        return response()->json($data);
    }

    public function ServerApp($server, $segment1='index', $segment2=''){
        $ctrl = trim($segment1);
        if (!empty($segment2)){
            $ctrl = implode("/", array($ctrl, trim($segment2)));
        }
        $service = serv($server);
        if (is_error($service) || !$service->enabled){
            abort(404);
        }
        $uniacid = request()->input('i', SITEACID);
        if ($service->Unique){
            (new AppRuntime())->Runtime($uniacid, request()->header('x-auth-token'));
        }
        $data = $service->HttpRequest('app', $ctrl);
        if (!is_error($data)) {
            if (!is_array($data)) return $data;
            if (isset($data['message']) && isset($data['type'])) {
                return $this->message($data["message"], trim($data['redirect']), $data['type']);
            }
            return $data;
        }
        return $this->message($data['message'], trim($data['redirect']));
    }

    /**
     * @throws \Exception
     */
    public function ServerRun($server, $segment='index'){
        $ctroller = trim($segment);
        $method = "main";
        $serverName = ucfirst($server) . 'Service';
        if (!class_exists($serverName)){
            require_once MICRO_SERVER.strtolower($server)."/$serverName.php";
        }
        $ctrl = MICRO_SERVER.strtolower($server)."/run/".ucfirst($ctroller)."Controller.php";
        if (!file_exists($ctrl)){
            $ctrl = MICRO_SERVER.strtolower($server)."/run/IndexController.php";
            $method = $ctroller;
            $ctroller = "index";
        }
        if (!file_exists($ctrl)){
            abort(404);
            session_exit();
        }

        try {
            include_once $ctrl;
            $className = ucfirst($ctroller)."Controller";
            if (!class_exists($className)) return $this->message(__('controllerNotFound', array('ctrl'=>$className)));
            $instance = new $className();
            return $instance->$method();
        }catch (\Exception $exception){
            if (DEVELOPMENT){
                throw new $exception;
            }
            return $this->message($exception->getMessage());
        }
    }

}
