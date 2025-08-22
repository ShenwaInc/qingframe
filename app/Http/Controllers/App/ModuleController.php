<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Utils\WeModule;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    //

    public function entry(Request $request, $moduleName, $do='index'){
        $WeModule = new WeModule();
        try {
            $site = $WeModule->create($moduleName);
        }catch (\Exception $exception){
            return $this->message('模块初始化失败，请联系技术处理');
        }
        $method = "doMobile" . ucfirst($do);
        if (!method_exists($site,$method)){
            return $this->message("模块不支持{$method}()方法");
        }
        return $site->$method($request);
    }

    public function HttpRequest(Request $request, $module, $segment1='index', $segment2='main'){
        global $_W;
        try {
            $WeModule = new WeModule();

            $site = $WeModule->create($module);
            $className = "Addons\\".$module."\app\Controllers\app\\".ucfirst($segment1)."Controller";
            $method = $segment2;
            if (!class_exists($className)){
                $className = "Addons\\".$module."\app\Controllers\app\IndexController";
                $method = $segment1;
                $segment1 = 'index';
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

            $className = "Addons\\".$moduleName."\app\Controllers\api\\".ucfirst($segment1)."Controller";
            $method = $segment2;
            if (!class_exists($className)){
                $className = "Addons\\".$moduleName."\app\Controllers\api\IndexController";
                $method = $segment1;
                $segment1 = 'index';
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
            if ($_W['config']['debugMode'] || DEVELOPMENT){
                throw $exception;
            }
            return $this->message("请求失败(".$exception->getCode().")");
        }
    }

}
