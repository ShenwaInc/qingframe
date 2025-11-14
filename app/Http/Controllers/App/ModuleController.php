<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Utils\WeModule;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    //

    public function entry(Request $request, $moduleName, $do='index'){
        return $this->HttpRequest($request, $moduleName, $do);
    }

    public function HttpRequest(Request $request, $module, $segment1='index', $segment2='main'){
        global $_W;
        try {
            $WeModule = new WeModule();

            $site = $WeModule->create($module);
            if (empty($site)){
                SystemLog::systemRunning(
                    "模块不存在：{$module}",
                    'app:module:entry',
                    "模块不存在：{$module}",
                    false,
                    [
                        'module' => $module,
                        'segment1' => $segment1,
                        'segment2' => $segment2,
                    ]
                );
                abort(404, "Module {$module} not found");
            }

            if (method_exists($site, 'AppRequest')){
                return $site->AppRequest($request, $segment1, $segment2);
            }

            $className = "Addons\\".$module."\app\Controllers\app\\".ucfirst($segment1)."Controller";
            $method = $segment2;
            if (!class_exists($className)){
                $className = "Addons\\".$module."\app\Controllers\app\IndexController";
                if(class_exists($className)){
                    $method = $segment1;
                    $segment1 = 'index';
                }
            }

            if(class_exists($className)) {
                $instance = new $className();
                if (!method_exists($instance, $method)){
                    return $this->message(ucfirst($segment1)."Controller不支持{$method}()方法");
                }
                $instance->moduleSite = $site;
                $instance->moduleConfig = (array)$site->module['config'];
                $_W['moduleController'] = $segment1;
                $_W['moduleMethod'] = $method;
                return $instance->$method($request);
            }else{
                $method = "doMobile" . ucfirst($segment1);
                if (!method_exists($site,$method)){
                    return $this->message("模块不支持{$method}()方法");
                }
                return $site->$method($request);
            }
        }catch (\Exception $exception){
            SystemLog::systemRunning(
                "移动端模块请求异常：{$module}",
                'app:module:HttpRequest',
                "移动端模块请求处理过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace(),
                    'module' => $module,
                    'segment1' => $segment1,
                    'segment2' => $segment2,
                ]
            );
            return $this->message(empty($_W['config']['debugMode'])?'模块初始化失败，请联系技术处理':$exception->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function Api(Request $request, $moduleName, $segment1="index", $segment2="main"){
        define('IN_API', true);
        global $_W;
        $_W['isapi'] = true;
        //判断模块权限，待完善
        try {
            $WeModule = new WeModule();
            $site = $WeModule->create($moduleName);

            if (empty($site)){
                SystemLog::systemRunning(
                    "模块不存在：{$moduleName}",
                    'api:module:entry',
                    "模块不存在：{$moduleName}",
                    false,
                    [
                        'module' => $moduleName,
                        'segment1' => $segment1,
                        'segment2' => $segment2,
                    ]
                );
                abort(404, "Module {$moduleName} not found");
            }

            if (method_exists($site, 'ApiRequest')){
                return $site->ApiRequest($request, $segment1, $segment2);
            }

            $className = "Addons\\".$moduleName."\app\Controllers\api\\" . ucfirst($segment1) . "Controller";
            $method = $segment2;
            if (!class_exists($className)){
                $className = "Addons\\".$moduleName."\app\Controllers\api\IndexController";
                if(class_exists($className)){
                    $method = $segment1;
                    $segment1 = 'index';
                }
            }
            if(class_exists($className)) {
                $instance = new $className();
                if (!method_exists($instance, $method)){
                    return $this->message(ucfirst($segment1)."Controller不支持{$method}()方法");
                }
                $instance->moduleSite = $site;
                $instance->moduleConfig = (array)$site->module['config'];
                $_W['moduleController'] = $segment1;
                $_W['moduleMethod'] = $method;
                return $instance->$method($request);
            }else{
                $method = "doApi" . ucfirst($segment1);
                if (!method_exists($site, $method)){
                    $method = "doMobileApi";
                }
                if (!method_exists($site, $method)){
                    return $this->message("模块不支持$method()方法");
                }
            }
            return $site->$method($request);
        }catch (\Exception $exception){
            SystemLog::systemRunning(
                "模块API请求异常：{$moduleName}",
                'app:module:Api',
                "模块API请求处理过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace(),
                    'module_name' => $moduleName,
                    'segment1' => $segment1,
                    'segment2' => $segment2,
                ]
            );
            if ($_W['config']['debugMode'] || DEVELOPMENT){
                throw $exception;
            }
            return $this->message("请求失败(".$exception->getCode().")");
        }
    }

}
