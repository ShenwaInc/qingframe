<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $moduleName;
    public $moduleSite;

    /**
     * 统一抛出响应
     * @param array|string|null $prompt 抛出内容，可以是提示信息或者数据，支持国际化提示词
     * @param string $redirect 跳转地址
     * @param string $type 提示类型，支持success、error、info、redirect
     * @param array $extra 额外参数
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
    */
    public function message($prompt='operationFailed', $redirect='', $type='error', $extra=array()){
        global $_W;
        if (is_string($prompt) && preg_match('/^([\w\s.]*)([\x{4e00}-\x{9fa5}]*)$/u', $prompt)){
            $prompt = __($prompt);
        }
        if ($redirect && isset($extra['light']) && $extra['light']!=''){
            $prompt = str_replace($extra['light'],'<a href="'.$redirect.'" class="message-light">'.$extra['light'].'</a>',$prompt);
        }
        $return = array('redirect'=>$redirect,'type'=>$type, 'code'=>$extra['code']??0, 'message'=>$prompt, 'data'=>[]);
        if (is_array($prompt)){
            $return['message'] = 'OK';
            $return['data'] = $prompt;
        }
        if(!isset($_W['isajax'])){
            $_W['isajax'] = \request()->ajax() || \request('inajax', 0);
        }
        if ($_W['isajax'] || $_W['isapi']){
            return response()->json($return, 200, [], JSON_NUMERIC_CHECK);
        }else{
            $view = defined("IN_MOBILE") ? 'mmessage' : "message";
            return $this->globalView($view, $return);
        }
    }

    public function success($message="successful", $redirect=""){
        return $this->message($message, $redirect, "success");
    }

    public function globalView($view, $data=array()){
        if (empty($data)) $data = [];
        if (is_array($view)){
            return View::first($view, $data, ['_W'=>$GLOBALS['_W'], '_GPC'=>$GLOBALS['_GPC']]);
        }
        return View::make($view, $data, ['_W'=>$GLOBALS['_W'], '_GPC'=>$GLOBALS['_GPC']]);
    }

    public function moduleView($view, $data=array())
    {
        $basePath = config('system.setting.addon_dir', 'addons');
        if (empty($this->moduleName)){
            $className = static::class;
            if (preg_match('/^Addons\\\\([^\\\\]+)\\\\/', $className)){
                $this->moduleName = preg_replace('/^Addons\\\\([^\\\\]+)\\\\.*$/', '$1', $className);
            }else{
                return $this->message("无效的应用标识");
            }
        }elseif(!empty($this->moduleSite)){
            $basePath = $this->moduleSite->__basePath;
        }
        $viewPath = base_path("$basePath/{$this->moduleName}/views");
        if (!is_dir($viewPath)){
            return $this->message("无效的视图路径：" . $viewPath);
        }
        try {
            View::addNamespace($this->moduleName, $viewPath);
        }catch (\Exception $e){
            app('view')->addNamespace($this->moduleName, $viewPath);
        }
        return View::make($this->moduleName . "::$view", $data, ['_W'=>$GLOBALS['_W'], '_GPC'=>$GLOBALS['_GPC']]);
    }

    public function serverView($view, $data=array())
    {
        if (empty($this->serviceName)){
            $className = static::class;
            if (!preg_match('/^Server\\\\([^\\\\]+)\\\\/', $className)){
                return $this->message("服务不可用");
            }
            $this->serviceName = preg_replace('/^Server\\\\([^\\\\]+)\\\\.*$/', '$1', $className);
        }
        $viewNameSpace = strtolower($this->serviceName);
        $viewPath = base_path("servers/$viewNameSpace/views");
        try {
            View::addNamespace($viewNameSpace, $viewPath);
        }catch (\Exception $e){
            app('view')->addNamespace($viewNameSpace, $viewPath);
        }
        return View::make("$viewNameSpace::$view", $data, ['_W'=>$GLOBALS['_W'], '_GPC'=>$GLOBALS['_GPC']]);
    }

}
