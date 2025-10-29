<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AppRuntime;
use App\Models\SystemLog;

class HttpController extends Controller
{

    public function ServerApi($server, $segment1='index', $segment2=''){
        global $_W;
        $_W['isapi'] = true;
        $data = $this->HttpRequest($server, $segment1, $segment2, 'api');
        if (is_error($data)) return $this->message($data['message'], trim($data['redirect']));
        if (!is_array($data)) return $data;
        if (isset($data['message']) && isset($data['type'])){
            return $this->message($data["message"], trim($data['redirect']), $data['type']);
        }
        return response()->json($data);
    }

    public function HttpRequest($server, $segment1='index', $segment2='', $platform='app'){
        $ctrl = trim($segment1);
        if (!empty($segment2)){
            $ctrl = implode("/", array($ctrl, trim($segment2)));
        }
        try {
            $service = serv($server);
        } catch (\Exception $e) {
            SystemLog::systemRunning(
                '微服务调用异常',
                'http:HttpRequest',
                "获取微服务实例时发生异常：{$e->getMessage()}",
                false,
                [
                    'exception_file' => $e->getFile(),
                    'exception_line' => $e->getLine(),
                    'exception_code' => $e->getCode(),
                    'exception_trace' => $e->getTrace(),
                    'server' => $server,
                    'segment1' => $segment1,
                    'segment2' => $segment2,
                ]
            );
            return $this->message($e->getMessage());
        }
        if (is_error($service) || !$service->enabled){
            abort(404);
        }
        $uniacid = request()->input('i', SITEACID);
        if ($service->Unique){
            (new AppRuntime())->Runtime($uniacid, request()->header('x-auth-token'));
        }
        return $service->HttpRequest($platform, $ctrl);
    }

    public function ServerApp($server, $segment1='index', $segment2=''){
        $data = $this->HttpRequest($server, $segment1, $segment2);
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
        $controller = trim($segment);
        $method = "main";

        try {
            $service = serv('payment');
            if (!$service->enabled){
                return $this->message($service->error);
            }
            $className = "Server\\{$server}\\run\\" . ucfirst($controller) . "Controller";
            if (!class_exists($className)){
                $className = "Server\\{$server}\\run\IndexController";
                $method = $controller;
            }
            if (!class_exists($className)){
                $ctrl = MICRO_SERVER.strtolower($server)."/run/".ucfirst($controller)."Controller.php";
                if (!file_exists($ctrl)){
                    $ctrl = MICRO_SERVER.strtolower($server)."/run/IndexController.php";
                    $method = $controller;
                    $controller = "index";
                }
                if (!file_exists($ctrl)){
                    abort(404);
                    session_exit();
                }

                include_once $ctrl;
                $className = ucfirst($controller)."Controller";
            }

            if (!class_exists($className)) return $this->message(__('controllerNotFound', array('ctrl'=>$className)));

            $instance = new $className();
            return $instance->$method();
        }catch (\Exception $exception){
            SystemLog::systemRunning(
                '微服务运行异常',
                'http:ServerRun',
                "微服务执行过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace(),
                    'server' => $server,
                    'controller' => $controller,
                ]
            );
            if (DEVELOPMENT){
                throw new $exception;
            }
            return $this->message($exception->getMessage());
        }
    }

}
