<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class MSService
{
    public static $tablename = 'microserver';
    public $devmode = true;

    public static function getmanifest($identity, $app=false){
        $manifest = MICRO_SERVER.$identity."/manifest.json";
        $inextra = false;
        if (!file_exists($manifest) && defined('MSERVER_EXTRA')){
            $manifest = MSERVER_EXTRA."/manifest.json";
            $inextra = true;
        }
        if (!file_exists($manifest)) return error(-1,'找不到安装文件：'.$manifest);
        $service = json_decode(@file_get_contents($manifest), true);
        if (!isset($service['application']) || !isset($service['drive'])) return error(-1,'安装包解析失败');
        if ($app) return $service['application'];
        $service['inextra'] = $inextra;
        return $service;
    }

    /**
     * @param array $keys
     * @param $manifest
     * @return array
     */
    public function getApplication(array $keys, $manifest)
    {
        $application = post_var($keys, $manifest['application']);
        $application['drive'] = $manifest['drive'];
        $application['entrance'] = $manifest['entrance'];
        $datas = post_var(array('apis', 'methods', 'components', 'resources', 'events'), $manifest);
        if (!empty($datas)) {
            $application['datas'] = serialize($datas);
        }
        return $application;
    }

    public static function getservers($status=1){
        return pdo_getall(self::$tablename, array('status'=>intval($status)));
    }

    public static function getone($identity, $simple=true){
        $fields = array('id','identity','name','version','drive','status','releases');
        if (!$simple){
            $fields = array_merge($fields, array("cover","summary","entrance","datas","configs"));
        }
        $service = pdo_get(self::$tablename, array('identity'=>$identity), $fields);
        if (!empty($service) && !$simple){
            $service['datas'] = empty($service['datas']) ? array() : unserialize($service['datas']);
            $service['configs'] = empty($service['configs']) ? array() : unserialize($service['configs']);
        }
        return $service;
    }

    public function getcloud($identity){
        //获取云端服务
        return array('id'=>$identity);
    }

    public static function isexist($identity){
        if ($identity=='weengine') return true;
        return (int)pdo_getcolumn(self::$tablename, array('identity'=>trim($identity)),'id') > 0;
    }

    public function localexist($identity){
        $path = dirname(MICRO_SERVER.$identity."/");
        if (!is_dir($path)){
            if(!defined('MSERVER_EXTRA')) return false;
            $extrapath = dirname(MSERVER_EXTRA.$identity."/");
            if (!is_dir($extrapath)) return false;
        }
        return true;
    }

    public static function InitService($status=1){
        $servers = self::getservers($status);
        if ($status!=1) return $servers;
        if (empty($servers)) return array();
        foreach ($servers as &$server){
            $server['actions'] = '';
            if($server['status']!=1) continue;
            $server['entry'] = serv($server['identity'])->getEntry();
            if (!empty($server['entry']) && !is_error($server['entry'])){
                $server['actions'] .= '<a class="layui-btn layui-btn-sm layui-btn-normal" target="_blank" href="'.$server['entry'].'">管理</a>';
            }
            if (DEVELOPMENT){
                if (!empty(serv($server['identity'])->getMethods())){
                    $server['actions'] .= '<a class="layui-btn layui-btn-sm" target="_blank" href="'.wurl("server/methods/{$server['identity']}").'">调用方法</a>';
                }
                $apis = serv($server['identity'])->getApis();
                if (!empty($apis['wiki']) || !empty($apis['schemas'])){
                    $server['actions'] .= '<a class="layui-btn layui-btn-sm" href="'.wurl("server/apis/{$server['identity']}").'" target="_blank">接口文档</a>';
                }
            }
            $server['upgrade'] = array();
            $manifest = self::getmanifest($server['identity'], true);
            if (!is_error($manifest)){
                if(version_compare($manifest['version'], $server['version'], '>')){
                    $server['upgrade'] = array('version'=>$manifest['version'],'canup'=>true);
                }
            }
        }
        return $servers;
    }

    public function checkrequire($requires){
        if (empty($requires)) return true;
        $unpaids = array();
        foreach ($requires as $value){
            if ($this->isexist($value['id'])){
                //已安装
                continue;
            }
            if ($this->localexist($value['id'])){
                //未安装，但是本地存在则直接安装
                $complete = $this->install($value['id']);
                if (is_error($complete)) return $complete;
            }
            //本地不存在则从云端获取
            $cloudserver = $this->getcloud($value['id']);
            if (is_error($cloudserver)) return $cloudserver;
            if ($cloudserver['price']<=0 || !empty($cloudserver['authorized'])){
                //已授权或免费服务直接安装
                $complete = $this->installcloud($cloudserver);
                if (is_error($complete)) return $complete;
            }else{
                $unpaids[] = $cloudserver;
            }
        }
        if (!empty($unpaids)){
            //存在付费服务，进入购买引导
            //Todo something
            return false;
        }
        return true;
    }

    public function install($identity){
        if ($this->isexist($identity)) return true;
        $service = $this->getmanifest($identity);
        if (is_error($service)) return $service;
        //判断依赖服务
        $requires = $this->checkrequire($service['require']);
        if (is_error($requires)){
            return $requires;
        }
        //构造服务信息
        $keys = array('identity','name','version','cover','summary','releases');
        $application = $this->getApplication($keys, $service);
        $configs = post_var(array('uninstall'), $service);
        if (!empty($configs)){
            $application['configs'] = serialize($configs);
        }
        //加载所需资源，待完善
        //运行安装脚本
        if (!empty($service['install'])){
            try {
                script_run($service['install'], MICRO_SERVER.$identity);
            }catch (Exception $exception){
                return error(-1,"安装失败：".$exception->getMessage());
            }
        }
        //操作入库
        $application['status'] = 1;
        $application['addtime'] = $application['dateline'] = TIMESTAMP;
        if (!pdo_insert(self::$tablename, $application)){
            return error(-1,'安装失败，请重试');
        }
        $this->getEvents(true);
        if (!$this->devmode){
            if ($service['inextra'] && defined('MSERVER_EXTRA')){
                //移动文件夹
                file_movedir(MSERVER_EXTRA.$identity, MICRO_SERVER.$identity);
            }
            //删除安装包文件
            @unlink(MICRO_SERVER.$identity."/manifest.json");
        }
        return true;
    }

    public function upgrade($identity){
        //判断依赖服务
        $service = $this->getone($identity, false);
        if (empty($service)) return $this->install($identity);
        $manifest = $this->getmanifest($identity);
        if (is_error($manifest)) return $manifest;
        if($manifest['application']['identity']!=$service['identity']){
            return error(-1, "安装包的Identity不匹配");
        }
        if(version_compare($manifest['application']['version'],$service['version'],'>')){
            //判断依赖服务
            $requires = $this->checkrequire($service['require']);
            if (is_error($requires)){
                return $requires;
            }
            //构造服务信息
            $keys = array('name','version','cover','summary','releases');
            $application = $this->getApplication($keys, $manifest);
            $service['configs']['uninstall'] = $manifest['uninstall'];
            $application['configs'] = serialize($service['configs']);
            //运行升级脚本
            if (!empty($manifest['upgrade'])){
                try {
                    script_run($manifest['upgrade'], MICRO_SERVER.$identity);
                }catch (Exception $exception){
                    return error(-1,"安装失败：".$exception->getMessage());
                }
            }
            //操作入库
            $application['status'] = 1;
            $application['dateline'] = TIMESTAMP;
            if (!pdo_update(self::$tablename, $application, array('identity'=>$service['identity']))){
                return error(-1,'更新失败，请重试');
            }
            $this->getEvents(true);
            if (!$this->devmode){
                //删除安装包文件
                @unlink(MICRO_SERVER.$identity."/manifest.json");
            }
            return true;
        }
        return error(-1,"当前服务已经是最新版本");
    }

    public function uninstall($identity){
        $service = self::getone($identity, false);
        if (empty($service)) return error(-1,'该服务尚未安装');
        if (!empty($service['configs']['uninstall'])){
            try {
                script_run($service['configs']['uninstall'], MICRO_SERVER.$identity);
            }catch (Exception $exception){
                return error(-1,"卸载失败：".$exception->getMessage());
            }
        }
        if (!pdo_delete(self::$tablename,array('id'=>$service['id']))){
            return error(-1,'卸载失败，请重试');
        }
        $this->getEvents(true);
        if (!$this->devmode){
            //删除服务安装包
            return false;
        }
        return true;
    }

    public static function disable($identity){
        return pdo_update(self::$tablename, array('status'=>0,'dateline'=>TIMESTAMP), array('identity'=>trim($identity)));
    }

    public static function restore($identity){
        return pdo_update(self::$tablename, array('status'=>1,'dateline'=>TIMESTAMP), array('identity'=>trim($identity)));
    }

    public function installcloud($server){
        return $server;
    }

    public static function showparams($params=array(),$inuse=false){
        if (empty($params)) return '';
        $data = array();
        foreach ($params as $key=>$value){
            $param = '$'.$key;
            if ($inuse && !empty($value[1]) && strpos($value[1],'null')!==false){
                $format = explode('|', $value[1])[0];
                switch ($format){
                    case 'string':{
                        $param .= "=''";
                        break;
                    }
                    case 'numeric':{
                        $param .= "=0";
                        break;
                    }
                    case 'array':{
                        $param .= "=array()";
                        break;
                    }
                    default:{
                        break;
                    }
                }
            }
            $data[] = $param;
        }
        return empty($data) ? '' : implode(", ",$data);
    }

    public static function getlocal($path=''){
        $servers = array();
        $serverpath = MICRO_SERVER;
        if(!empty($path)){
            $serverpath = $path;
        }
        $manifests = FileService::file_tree($serverpath,array('*/manifest.json'));
        if ($manifests){
            foreach ($manifests as $manifest){
                $service = json_decode(@file_get_contents($manifest), true);
                if (!empty($service) && isset($service['application'])){
                    if (self::isexist($service['application']['identity'])) continue;
                    $servers[] = $service['application'];
                }
            }
        }
        if (empty($path) && defined('MSERVER_EXTRA')){
            $extraserver = self::getlocal(MSERVER_EXTRA);
            return array_merge($extraserver, $servers);
        }
        return $servers;
    }

    public static function getEvents($rebuild=false){
        $events = array();
        $servers = self::getservers();
        if (!empty($servers)){
            foreach ($servers as $serv){
                $service = serv($serv['identity']);
                if (!method_exists($service, "getMethods")) continue;
                $event = $service->service['events'];
                if (!empty($event)){
                    foreach ($event as $ev){
                        if (!isset($events[$ev])){
                            $events[$ev] = array($serv['identity']);
                        }else{
                            $events[$ev][] = $serv['identity'];
                        }
                    }
                }
            }
        }
        if ($rebuild){
            $cachekey = 'GLOBALS_EVENTS';
            $globalEvents = Cache::get($cachekey, array());
            $globalEvents['microserver'] = $events;
            $globalEvents['dateline'] = TIMESTAMP;
            Cache::put($cachekey, $globalEvents, 7*86400);
        }
        return $events;
    }

}

class MSS extends MSService {}
