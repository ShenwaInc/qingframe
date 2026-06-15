<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\SystemLogs;
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
                SystemLogs::systemRunning(
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
                if (method_exists($instance, 'init')){
                    $instance->init($request);
                }
                return $instance->$method($request);
            }else{
                $method = "doMobile" . ucfirst($segment1);
                if (!method_exists($site,$method)){
                    return $this->message("模块不支持{$method}()方法");
                }
                return $site->$method($request);
            }
        }catch (\Exception $exception){
            SystemLogs::systemRunning(
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
                SystemLogs::systemRunning(
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
                if (method_exists($instance, 'init')){
                    $instance->init($request);
                }
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
            SystemLogs::systemRunning(
                "模块API请求异常：{$moduleName}",
                'app:module:Api',
                "模块API请求处理过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'code' => $exception->getCode(),
                    'module_name' => $moduleName,
                    'segment' => $segment1 . '/' . $segment2,
                ]
            );
            if ($_W['config']['debugMode'] || DEVELOPMENT){
                throw $exception;
            }
            return $this->message("请求失败(".$exception->getCode().")");
        }
    }

    public function show(Request $request, string $name, string $path)
    {

        // 1. 安全校验：禁止路径遍历
        if (strpos($name, '..') !== false || strpos($name, '/') !== false) {
            abort(404);
        }


        if (empty($path)){
            // 3. 支持的 LOGO 文件名（按优先级）
            $logoFiles = ['logo.png', 'logo.jpg', 'logo.svg', 'icon.png', 'icon.jpg'];
            $logoPath = null;
            foreach ($logoFiles as $file) {
                $candidate = app_assetPath($file, $name);
                if (file_exists($candidate)) {
                    $logoPath = $candidate;
                    break;
                }
            }
        }else{
            $logoPath = app_assetPath($path, $name);
        }

        if (!$logoPath) {
            // 可返回默认占位图，或 404
            return response()->file(public_path('static/icon200.jpg'));
        }

        // 4. 返回图片响应（自动处理 Content-Type）
        return response()->file($logoPath, [
            'Cache-Control' => 'public, max-age=86400', // 缓存一天
        ]);

    }

}
