<?php

namespace App\Services;

use App\Models\SystemLog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class MicroService
{

    public $service = array();
    public $identity;
    public $serverPath = MICRO_SERVER;
    public $tableName = 'microserver';
    public $CompileDrive = "smarty";
    public $Unique = false;
    public $framework = "laravel";
    public $enabled = true;
    public $events = [];
    public $serviceId;
    public $accountUrl = "";

    function __construct($name){
        $this->identity = $name;
        $this->accountUrl = wurl('server/account');
        $this->initServer();
    }

    //初始化服务
    public function initServer(){
        $fields = array('id','identity','name','version','drive','status','releases');
        if (defined('IN_SYS')){
            $fields = array_merge($fields, array("cover","summary","entrance","datas","configs"));
        }
        $service = pdo_get($this->tableName, array('identity'=>$this->identity), $fields);
        if (empty($service['status'])){
            $this->enabled = false;
            throw new \Exception("Service {$service['identity']} is unavailable.");
        }
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

    public function getAllSetting($uniacid=0, $cache=true){
        if ($cache){
            $settings = cache_read("MicroServerSetting$uniacid");
            if (is_error($settings)) return [];
            if (!empty($settings)) return $settings;
        }
        $settings = pdo_getall("microserver_data", array("uniacid"=>intval($uniacid)), array('name', 'data'));
        $data = [];
        if (!empty($settings)){
            foreach ($settings as $value){
                $data[$value['name']] = unserialize($value['data']);
            }
        }
        cache_write("MicroServerSetting$uniacid", empty($data)?error(-1, "Empty"):$data);
        return $data;
    }

    public function SettingLoad($key = '', $uniacid=0){
        $settings = $this->getAllSetting($uniacid);
        if (empty($settings) || (is_string($key) && !isset($settings[$key]))){
            if (!empty($uniacid)){
                return SettingService::uni_load($key, $uniacid);
            }
            return SettingService::Load($key);
        }
        if (empty($key)) return $settings;
        if (is_array($key)){
            return post_var($key, $settings);
        }
        return array($key=>$settings[$key]);
    }

    public function SettingSave($key, $data, $uniacid=0){
        $isExists = pdo_get("microserver_data", array("uniacid"=>intval($uniacid), "name"=>$key), array('id'));
        if ($isExists){
            $complete = pdo_update("microserver_data", array("data"=>serialize($data), "dateline"=>TIMESTAMP), array("id"=>$isExists['id']));
        }else{
            $complete = pdo_insert("microserver_data", array("uniacid"=>intval($uniacid), "name"=>$key, "data"=>serialize($data), "addtime"=>TIMESTAMP, "dateline"=>TIMESTAMP));
        }
        if ($uniacid==0){
            SettingService::Save($data, $key);
        }
        if (!$complete) return false;
        $this->getAllSetting($uniacid, false);
        return true;
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
            //获取默认入口
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
     * 获取完整的服务资源文件URL
     * @param string $res 文件相对路径
    */
    public function res($res): string
    {
        $res = preg_replace('/^\//', '', $res);
        $realPath = public_path("resource/server/" . $this->identity . "/" . $res);
        if (!file_exists($realPath) && base_path('servers/'. $this->identity . "/res/" . $res)){
            //自动搬运静态资源文件
            $baseDir = dirname($realPath);
            if (!is_dir($baseDir)){
                FileService::mkdirs($baseDir);
            }
            @copy(base_path('servers/'. $this->identity . "/res/" . $res), $realPath);
        }
        return asset("/resource/server/" . $this->identity . "/" . $res);
    }

    /**
     * 返回错误信息
     * @param string $msg 说明
     * @param int|null $code 状态码
     * @param array 统一错误格式
     */
    public function error($message, $code=-1){
        return error($code, $message);
    }

    /**
     * 返回成功信息
     * @param string|array|object $msg 说明或者数据内容
     * @param string|null $redirect 成功后跳转地址
     * @param string|null $type 状态码，success/error
     * @return array 返回成功输出
     */
    public function success($msg, $redirect="", $type="success", $code=0){
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
            'code'=>$code
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
     * HTTP方式访问
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

        $class = "Server\\{$this->identity}\\{$platform}\\" . ucfirst($controller) . "Controller";
        if (!class_exists($class)){
            $class = "Server\\{$this->identity}\\{$platform}\IndexController";
            $method = $controller;
            $controller = 'index';
        }
        if (!class_exists($class)){
            //定义运行目录
            $basePath = $this->serverPath . $this->identity;

            //定义控制器
            $ctrl = "$basePath/$platform/".ucfirst($controller)."Controller.php";
            if (!file_exists($ctrl)){
                if ($controller!='index' && !empty($controller) && $method!='main'){
                    throw new \Exception("Warning: include_once(): Failed opening '$ctrl'");
                }
                $ctrl = "$basePath/$platform/IndexController.php";
                $method = $controller;
                $controller = 'index';
            }
            if (!file_exists($ctrl)){
                throw new \Exception("Warning: include_once(): Failed opening '$ctrl'");
            }

            //引用控制器
            include_once $ctrl;
            $class = ucfirst($controller)."Controller";
        }

        $_W['controller'] = $controller;
        $_W['action'] = $method;

        if (!class_exists($class)) return error(-1,__('controllerNotFound', ['ctrl'=>$class]));
        $instance = new $class();
        if (!method_exists($instance,$method)) return error(-1,"Method $class::$method() dose not exist!");
        if (empty($instance->serviceName)){
            $instance->serviceName = $this->identity;
        }
        return $instance->$method();
    }

    /**
     * 命令行方式访问
     * Command $command
    */
    public function Terminal(Command $command, ...$params){
        $command->info("Welcome to use the MicroServer Terminal.");
        $command->info("Params: " . json_encode($params));
    }

    /**
     * 事件广播
     * @param string $listener 监听器名称，一般是英文名
     * @param array|null $data 广播数据
     * @param string|null 服务构造参数
     * @return bool
     */
    public function Event($listener, $data=array(), $channel='global'){
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
                serv($serv)->Processor($listener, $data);
            }catch (\Exception $exception){
                SystemLog::systemRunning(
                    '微服务事件处理器异常',
                    'service:MicroService',
                    "执行微服务事件处理器时发生异常：{$exception->getMessage()}",
                    false,
                    [
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                        'exception_code' => $exception->getCode(),
                        'exception_trace' => $exception->getTrace(),
                        'listener' => $listener,
                        'server' => $serv,
                    ]
                );
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
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function View($data, $template='', $drive=""){
        global $_W, $_GPC, $inService;
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
        if (!function_exists('tpl_build')){
            include_once app_path("Helpers/smarty.php");
        }
        $template = str_replace(".","/", $template);
        if (!empty($drive)){
            $this->CompileDrive = $drive;
        }
        $inService = $this;
        if ($this->CompileDrive=='smarty'){
            if (empty($template)){
                $template = tpl_build($_W['controller'], $_W['action'], $this->serverPath.$this->identity."/template/$platform");
            }
            if (!empty($data)){
                foreach ($data as $key=>$value){
                    $$key = $value;
                }
            }
            $source = $this->serverPath.$this->identity."/template/$platform/$template.html";
            if (!file_exists($source)){
                $this->message("Error: template source '$template' is not exist!", "", "error");
            }
            $compile = storage_path("framework/tpls/$platform") . "/severs/".$this->identity."/$template.tpl.php";
            if (DEVELOPMENT || !file_exists($compile) || filemtime($source) > filemtime($compile)){
                tpl_compile($source, $compile);
            }
            if (!file_exists($compile)){
                $this->message("Warning: include_once(): Failed opening '$compile'","","error");
            }
            include $compile;
            session_exit();
        }elseif ($this->CompileDrive=='blade'){
            if (empty($template)){
                $template = tpl_build($_W['controller'], $_W['action'], $this->serverPath.$this->identity."/views/$platform");
            }
            if (empty($data)){
                $data = array();
            }
            $data['_W'] = $_W;
            $data['_GPC'] = $_GPC;
            $data['inService'] = $inService;
            View::share($data);
            $source = $this->serverPath.$this->identity."/views/$platform/$template.blade.php";
            if (!file_exists($source)){
                abort(404, "view $template dose not exist.");
            }
            return View::file($source);
        }
        return response()->json($data);
    }

    public static function message($msg='', $redirect = '', $type = 'error'){
        global $_W, $_GPC;
        if (empty($msg)){
            $msg = $type=='success' ? ($_W['isapi']?'OK':'successful') : 'operationFailed';
        }
        $data = array('message'=>$msg,'redirect'=>$redirect,'type'=>$type);
        if (is_string($data['message']) && preg_match('/^([\w\s.]*)([\x{4e00}-\x{9fa5}]*)$/u', $data['message'])){
            $data['message'] = __($data['message']);
        }
        ob_clean();
        if ($_W['isajax']){
            $data['data'] = [];
            $data['code'] = $type=='success' ? 0 : -1;
            if (is_array($data['message'])){
                $data['data'] = $data['message'];
                $data['message'] = 'OK';
            }
            return response()->json($data);
        }else{
            View::share('_W',$_W);
            View::share('_GPC',$_GPC);
            $view = defined('IN_SYS') ? 'message' : 'mmessage';
            return response()->view($view, $data)->content();
        }
    }

    /**
     * 自动加载
     * @return bool|void
     */
    public function Composer(){
        $composer = $this->serverPath.$this->identity."/composer.json";
        $requireName = "microserver/".$this->identity;
        $composerErr = $composerVer = "";
        if (!file_exists($composer)) return true;
        if (file_exists($this->serverPath.$this->identity."/composer.error")){
            $composerErr = $this->serverPath.$this->identity."/composer.error";
        }
        if (DEVELOPMENT){
            //开发者模式
            $autoloader = $this->serverPath.$this->identity."/vendor/autoload.php";
            if (file_exists($autoloader)){
                require_once $autoloader;
            }else{
                if (!file_exists($this->serverPath.$this->identity."/composer.lock")){
                    $res = MSService::ComposerRequire($this->serverPath.$this->identity."/", $requireName);
                    if ($res){
                        require_once $autoloader;
                        return true;
                    }
                }
                $WorkingDirectory = str_replace("\\", "/", $this->serverPath.$this->identity."/");
            }
        }elseif($composerErr){
            $WorkingDirectory = base_path() . "/";
            $composerObj = json_decode(file_get_contents($composer), true);
            $composerVer = $composerObj['version'] ?? "";
        }
        if (!empty($WorkingDirectory)){
            global $_W;
            if ($_W['isajax']){
                $this->message("Vendor package missing: $requireName");
            }
            MSService::ComposerPage(array(
                'composerVer'=>$composerVer,
                'composerErr'=>$composerErr,
                'WorkingDirectory'=>$WorkingDirectory,
                'requireName'=>$requireName,
                'composerNext'=>''
            ), $this);
        }
        return true;
    }

}
