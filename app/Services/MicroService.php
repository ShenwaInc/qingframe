<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\Process\Process;

class MicroService
{

    public $service = array();
    public $identity;
    public $serverPath = MICRO_SERVER;
    public $tableName = 'microserver';
    public $CompileDrive = "smarty";
    public $Unique = false;
    public $framework = "laravel";
    public $events = [];

    function __construct($name){
        $this->identity = $name;
        $this->initServer();
    }

    //初始化服务
    public function initServer(){
        $fields = array('id','identity','name','version','drive','status','releases');
        if (defined('IN_SYS')){
            $fields = array_merge($fields, array("cover","summary","entrance","datas","configs"));
        }
        $service = pdo_get($this->tableName, array('identity'=>$this->identity), $fields);
        if (defined('IN_SYS')){
            $service['datas'] = empty($service['datas']) ? array() : unserialize($service['datas']);
            if (!empty($service['datas'])){
                foreach ($service['datas'] as $key=>$value){
                    $service[$key] = $value;
                }
            }
        }
        $this->service = $service;
    }

    public function SettingLoad($key = '', $uniacid=0){
        if (!empty($uniacid)){
            return SettingService::uni_load($key, $uniacid);
        }
        return SettingService::Load($key);
    }

    public function SettingSave($key, $data, $uniacid=0){
        if (!empty($uniacid)){
            if (is_array($data)){
                $data = serialize($data);
            }
            return SettingService::uni_save($uniacid, $key, $data);
        }
        return SettingService::Save($data, $key);
    }

    /**
     * 获取该服务的API接口
     * @param array|null $data 附加接口数据
     * @return array API接口
     */
    public function getApis($data=array()){
        $apis = $this->service['apis'];
        $apis['schemas'] = array_merge($apis['schemas'], $data);
        return $apis;
    }

    /**
     * 获取该服务的内置方法
     * @param array|null $data 附加内置方法
     * @return array 内置方法数据
     */
    public function getMethods($data=array()){
        $methods = array();
        if (!empty($this->service['methods'])){
            $methods = $this->service['methods'];
            if (empty($methods['wiki']) && isset($methods['wiki'])){
                unset($methods['wiki']);
            }
        }
        return array_merge($methods, $data);
    }

    /**
     * 获取该服务的后台入口
     * @param string|null $entrance;
     * @return string 后台入口URL
     */
    public function getEntry($entrance="", $full=true){
        if(empty($entrance)){
            $entrance = $this->service['entrance'];
        }
        if (strpos($entrance,'http')===0) return $entrance;
        if ($this->service['drive']=='php'){
            if ($entrance=='' && file_exists($this->serverPath.$this->identity."/web/IndexController.php")){
                $entrance = 'index';
            }
        }
        if (!empty($entrance) && $full){
            return $this->url($entrance);
        }
        return $entrance;
    }

    /**
     * 返回错误信息
     * @param string $msg 说明
     * @param int|null $code 状态码
     * @param array 统一错误格式
     */
    public function error($msg, $code=-1){
        return error($code, $msg);
    }

    /**
     * 返回成功信息
     * @param string|array|object $msg 说明或者数据内容
     * @param string|null $redirect 成功后跳转地址
     * @param string|null $type 状态码，success/error
     * @return array 返回成功输出
     */
    public function success($msg, $redirect="", $type="success"){
        if(defined("IN_SYS")){
            if ($redirect=="refresh"){
                $redirect = referer();
            }
            if($redirect=="home"){
                $redirect = $this->getEntry();
            }
        }
        return array(
            'message'=>$msg,
            'redirect'=>trim($redirect),
            'type'=>$type,
            'code'=>0
        );
    }

    /**
     * 生成API URL
     * @param string $route 路由名称
     * @param array|null $query URL参数
     * @param string|null $platform 接口通道
     * @return string API接口
     */
    public function api($route='', $query=array(), $platform="api"){
        global $_W;
        $basic = $this->identity;
        $ctrl = str_replace(".", "/", $route);
        if (!empty($ctrl)) {
            $basic .= "/".$ctrl;
        }
        if (!empty($_W['uniacid']) && $this->Unique){
            $query['i'] = $_W['uniacid'];
        }
        if (!empty($query)){
            $basic .= "?";
        }
        return $_W['siteroot'] . "$platform/server/$basic" . http_build_query($query);
    }

    /**
     * 生成后台URL
     * @param string|null $route 路由名称
     * @param array|null $query URL参数
     * @param bool|null $full 是否完整拼接
     * @return string 后台URL
     */
    public function url($route='', $query=array(), $full=false){
        global $_W;
        $url = 'server/'.$this->identity;
        if ($full){
            $url = $_W['siteroot'] . $url;
        }else{
            $url = '/' . $url;
        }
        if (strexists($route,'.')){
            $route = str_replace('.','/',$route);
        }
        if (!empty($route)){
            $url .= '/' . $route;
        }
        if (!empty($query)) {
            $queryString = http_build_query($query);
            $url .= '?' . $queryString;
        }
        return $url;
    }

    /**
     * 接管路由
     * @param string|null $platform 路由通道，可选web、app、api及自定义通道
     * @param string|null $route 路由名称
     * @return array|\error 返回接口数据或报错信息
     * @throws \Exception
     */
    public function HttpRequest($platform="web", $route=""){
        global $_GPC, $_W;
        if (empty($route)){
            $route = empty($_GPC['ctrl']) ? 'index' : trim($_GPC['ctrl']);
        }
        $route = str_replace(".","/",$route);
        list($controller, $method) = explode("/", $route);
        if (empty($method)) $method = 'main';

        //定义运行目录
        $basepath = $this->serverPath . $this->identity;
        //定义控制器
        $ctrl = "$basepath/$platform/".ucfirst($controller)."Controller.php";
        if (!file_exists($ctrl)){
            if ($controller!='index' && !empty($controller) && $method!='main'){
                throw new \Exception("Warning: include_once(): Failed opening '$ctrl'");
            }
            $ctrl = "$basepath/$platform/IndexController.php";
            $method = $controller;
            $controller = 'index';
        }
        if (!file_exists($ctrl)){
            throw new \Exception("Warning: include_once(): Failed opening '$ctrl'");
        }
        $_W['controller'] = $controller;
        $_W['action'] = $method;

        //引用控制器
        include_once $ctrl;
        $class = ucfirst($controller)."Controller";
        if (!class_exists($class)) return error(-1,"找不到控制器$class");
        $instance = new $class();
        if (!method_exists($instance,$method)) return error(-1,"找不到指定方法$class::$method()");
        return $instance->$method();
    }

    /**
     * 事件广播
     * @param string $listener 监听器名称，一般是英文名
     * @param array|null $data 广播数据
     * @param array|null|mixed 服务构造参数
     * @return bool
     */
    public function Event($listener, $data=array(), $param=null){
        if (empty($this->events)){
            $globalEvents = Cache::get("GLOBALS_EVENTS", array());
            if(empty($globalEvents)){
                $this->events = MSService::getEvents(true);
            }else{
                $this->events = $globalEvents['microserver'];
            }
        }
        if (empty($this->events)) return true;
        if (empty($this->events[$listener])){
            return true;
        }
        $servers = $this->events[$listener];
        foreach ($servers as $serv){
            try {
                serv($serv, $param)->Processor($listener, $data);
            }catch (\Exception $exception){
                //Todo something
            }
        }
        return true;
    }

    /**
     * 事件处理器
     * @param string $listener 监听器名称，一般是英文名
     * @param array|null $data 广播数据
     * @return bool
     */
    public function Processor($listener, $data=array()){
        return true;
    }

    /**
     * 视图编译
     * @param array $data 模板数据
     * @param string $template 模板名称
     * @return bool
     */
    public function View($data, $template=''){
        global $_W,$_GPC;
        if (is_error($data)){
            $this->message($data['message']);
        }
        if ($_W['isapi']){
            die(json_encode($data));
        }
        if(isset($data['type']) && isset($data['message'])){
            $this->message($data['message'], $data['redirect'], $data['type']);
        }
        $platform = defined('IN_SYS') ? 'web' : 'app';
        if ($this->CompileDrive=='smarty'){
            if (!empty($data)){
                foreach ($data as $key=>$value){
                    $$key = $value;
                }
            }
            if (empty($template)){
                $template = tpl_build($_W['controller'], $_W['action'], MICRO_SERVER.$this->identity."/template/$platform");
            }
            $template = str_replace(".","/", $template);
            $source = MICRO_SERVER.$this->identity."/template/$platform/$template.html";
            if (!file_exists($source)){
                $this->message("Error: template source '$template' is not exist!", "", "error");
            }
            $compile = storage_path("framework/tpls/$platform") . "/severs/".$this->identity."/$template.tpl.php";
            tpl_compile($source, $compile);
            if (!file_exists($compile)){
                $this->message("Warning: include_once(): Failed opening '$compile'","","error");
            }
            include $compile;
            session_exit();
        }elseif ($this->CompileDrive=='blade'){
            return false;
        }
        return true;
    }

    public function message($msg, $redirect = '', $type = 'error'){
        global $_W, $_GPC;
        $data = array('message'=>$msg,'redirect'=>$redirect,'type'=>$type);
        ob_clean();
        if ($_W['isajax']){
            echo json_encode($data);
        }else{
            View::share('_W',$_W);
            View::share('_GPC',$_GPC);
            echo response()->view('message',$data)->content();
        }
        session()->save();
        exit;
    }

    /**
     * 自动加载
     * @return bool|void
     */
    public function Composer(){
        $composer = MICRO_SERVER.$this->identity."/composer.json";
        $requireName = "microserver/".$this->identity;
        $composerErr = "";
        if (!file_exists($composer)) return true;
        if (DEVELOPMENT){
            //开发者模式
            $autoloader = MICRO_SERVER.$this->identity."/vendor/autoload.php";
            if (file_exists($autoloader)){
                require_once $autoloader;
            }else{
                if (!file_exists(MICRO_SERVER.$this->identity."/composer.lock")){
                    $res = MSService::ComposerRequire(MICRO_SERVER.$this->identity."/", $requireName);
                    if ($res){
                        require_once $autoloader;
                        return true;
                    }
                }
                $WorkingDirectory = str_replace("\\", "/", MICRO_SERVER.$this->identity."/");
            }
        }else{
            if (file_exists(MICRO_SERVER.$this->identity."/composer.error")){
                $WorkingDirectory = base_path("/");
                $composerObj = json_decode(file_get_contents($composer), true);
                $composerVer = $composerObj['version'] ?? "";
                $composerErr = MICRO_SERVER.$this->identity."/composer.error";
            }
        }
        if (!empty($WorkingDirectory)){
            global $_W;
            if ($_W['isajax']){
                $this->message("请先安装依赖组件包");
            }
            $title = "安装依赖组件包";
            include tpl_include("web/composer");
            session_exit();
        }
        return true;
    }

}
