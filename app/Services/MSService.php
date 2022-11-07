<?php

namespace App\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class MSService
{
    public static $tableName = 'microserver';
    public static $devMode = DEVELOPMENT;

    public static function setup(){
        if (!Schema::hasTable("microserver")){
            Schema::create('microserver', function (Blueprint $table) {
                $table->increments('id');
                $table->string('identity', 20);
                $table->string('name', 20);
                $table->string('cover', 255)->default("");
                $table->text("summary")->nullable();
                $table->string("version",10)->default("");
                $table->string("releases",20)->default("");
                $table->string("drive", 10)->default("php");
                $table->string("entrance", 255)->default("");
                $table->mediumtext("datas")->nullable();
                $table->mediumtext("configs")->nullable();
                $table->boolean('status')->default(1);
                $table->integer("addtime")->default(0)->unsigned();
                $table->integer("dateline")->default(0)->unsigned();
            });
        }
        if (!Schema::hasTable('microserver_unilink')){
            Schema::create('microserver_unilink', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 20);
                $table->string('title', 20);
                $table->string('cover', 255)->default("");
                $table->string("summary", 255)->default("");
                $table->string("entry", 255)->default("");
                $table->mediumtext("perms")->nullable();
                $table->boolean('status')->default(1);
                $table->integer("addtime")->default(0)->unsigned();
                $table->integer("dateline")->default(0)->unsigned();
            });
        }
    }

    public static function getmanifest($identity, $app=false){
        $manifest = MICRO_SERVER.$identity."/manifest.json";
        $inextra = false;
        if (!file_exists($manifest) && defined('MSERVER_EXTRA')){
            $manifest = MSERVER_EXTRA."/manifest.json";
            $inextra = true;
        }
        if (!file_exists($manifest)) return error(-1,'找不到安装文件');
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
    public function getApplication($keys, $manifest)
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
        return pdo_getall(self::$tableName, array('status'=>intval($status)));
    }

    public static function getone($identity, $simple=true){
        $fields = array('id','identity','name','version','drive','status','releases');
        if (!$simple){
            $fields = array_merge($fields, array("cover","summary","entrance","datas","configs"));
        }
        $service = pdo_get(self::$tableName, array('identity'=>$identity), $fields);
        if (!empty($service) && !$simple){
            $service['datas'] = empty($service['datas']) ? array() : unserialize($service['datas']);
            $service['configs'] = empty($service['configs']) ? array() : unserialize($service['configs']);
        }
        return $service;
    }

    public function cloudUpdate($identity){
        $service = $this->cloudInfo($identity);
        if (is_error($service)) return $service;
        //获取线上文件结构
        $cloudIdentity = "microserver_".$identity;
        $cachekey = "cloud:structure:$cloudIdentity{$service['release']['releasedate']}";
        $cloudInfo = Cache::get($cachekey, array());
        if (empty($cloudInfo)){
            $cloudInfo = CloudService::CloudApi('structure',array(
                'identity'=>$cloudIdentity
            ));
            if (is_error($cloudInfo)) return $cloudInfo;
            Cache::put($cachekey, $cloudInfo, 7*86400);
        }
        //对比文件结构
        $serverpath = MICRO_SERVER.$identity."/";
        $structures = json_decode(base64_decode($cloudInfo['structure']), true);
        $difference = CloudService::CloudCompare($structures, $serverpath);
        if (!empty($difference)){
            //文件存在差异，获取补丁包
            $cloudUpdate = CloudService::CloudUpdate($cloudIdentity, $serverpath);
            if (is_error($cloudUpdate)) return $cloudUpdate;
        }
        return $this->upgrade($identity);
    }

    public function cloudInfo($identity){
        //获取应用信息
        $service = self::cloudserver($identity, true);
        if (is_error($service)){
            return $service;
        }
        //验证是否收费
        if ($service['product']['price']>0){
            //验证授权是否生效
            if (is_error($service['authorize'])){
                if (!empty($service['product']['buyurl'])){
                    header("location:{$service['product']['buyurl']}");
                    session_exit();
                }else{
                    return $service['authorize'];
                }
            }
        }
        return $service;
    }

    public function cloudInstall($identity, $autoInstall=false){
        $service = $this->cloudInfo($identity);
        if (is_error($service)) return $service;
        $cloudIdentity = "microserver_".$identity;
        $require = CloudService::CloudRequire($cloudIdentity, MICRO_SERVER.$identity."/");
        if (is_error($require)) return $require;
        return $this->install($identity, true, $autoInstall);
    }

    public static function cloudserver($identity, $nocache=false){
        //获取云端服务
        $cloudInfo = $nocache ? array() : Cache::get("microserver".$identity, array());
        if (!empty($cloudInfo)) return $cloudInfo;
        $data = array(
            'r'=>'cloud.package',
            'identity'=>"microserver_".$identity,
            'frompage'=>'list'
        );
        if (self::isexist($identity)){
            $data['frompage'] = 'local';
        }
        $res = CloudService::CloudApi("", $data);
        if(is_error($res) || !isset($res['application'])){
            if (!is_error($res)){
                $res = error(-1, "应用解析失败");
            }
            Cache::put("microserver".$identity, $res, 600);
            return $res;
        }
        Cache::put("microserver".$identity, $res);
        return $res;
    }

    public static function cloudservers($page=1, $keyword=""){
        $cachekey = "cloud:microserver_list";
        $res = Cache::get($cachekey, array());
        if (empty($res)){
            $data = array(
                'r'=>'cloud.packages',
                'compate'=>'laravel',
                'page'=>$page,
                'keyword'=>$keyword
            );
            $res = CloudService::CloudApi("", $data);
            Cache::put($cachekey, $res, 1800);
        }
        if (is_error($res)) return [];
        $servers = array();
        if (!empty($res['servers'])){
            foreach ($res['servers'] as $value){
                $identity = str_replace("microserver_","",$value['identity']);
                if (self::localexist($identity, DEVELOPMENT)) continue;
                $service = array(
                    'cover'=>$value['icon'],
                    'identity'=>$identity,
                    'name'=>$value['name'],
                    'isdelete'=>false,
                    'summary'=>$value['summary'],
                    'upgrade'=>[],
                    'entry'=>'',
                    'version'=>$value['release']['version'],
                    'releases'=>$value['release']['releasedate']
                );
                $service['actions'] = '<a class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="确定要安装该服务？" href="'.wurl('server', array("op"=>"cloudinst", "nid"=>$identity)).'">安装</a>';
                $servers[] = $service;
            }
        }
        return $servers;
    }

    public static function isexist($identity){
        return (int)pdo_getcolumn(self::$tableName, array('identity'=>trim($identity)),'id') > 0;
    }

    public static function localexist($identity, $manifest=true){
        $service = MICRO_SERVER.$identity."/".ucfirst($identity)."Service.php";
        if (!file_exists($service)){
            if(!defined('MSERVER_EXTRA')) return false;
            $extraServer = dirname(MSERVER_EXTRA.$identity."/".ucfirst($identity)."Service.php");
            if (!file_exists($extraServer)) return false;
        }
        if (!$manifest){
            return true;
        }
        $manifest = MICRO_SERVER.$identity."/manifest.json";
        if (!file_exists($manifest)){
            if(!defined('MSERVER_EXTRA')) return false;
            $extraPath = dirname(MSERVER_EXTRA.$identity."/manifest.json");
            if (!file_exists($extraPath)) return false;
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
            if (self::$devMode){
                if (!empty(serv($server['identity'])->getMethods())){
                    $server['actions'] .= '<a class="layui-btn layui-btn-sm" target="_blank" href="'.wurl("server/methods/{$server['identity']}").'">调用方法</a>';
                }
                $apis = serv($server['identity'])->getApis();
                if (!empty($apis['wiki']) || !empty($apis['schemas'])){
                    $server['actions'] .= '<a class="layui-btn layui-btn-sm" href="'.wurl("server/apis/{$server['identity']}").'" target="_blank">接口文档</a>';
                }
            }
            $server['upgrade'] = array();
            $server['isdelete'] = false;
            $server['islocal'] = true;
            if (!self::localexist($server['identity'], DEVELOPMENT)){
                $server['isdelete'] = true;
            }
            $manifest = self::getmanifest($server['identity'], true);
            if (!is_error($manifest)){
                if(version_compare($manifest['version'], $server['version'], '>')){
                    $server['upgrade'] = array('version'=>$manifest['version'],'canup'=>true);
                }
            }
            if (empty($server['upgrade'])){
                $cloudServer = Cache::get("microserver".$server['identity'], error(-1, "Has no cache"));
                if (is_error($cloudServer) || empty($cloudServer)) continue;
                $release = $cloudServer['release'];
                if (version_compare($release['version'], $server['version'], '>') || $release['releasedate']>$server['releases']){
                    $server['actions'] .= '<a class="layui-btn layui-btn-sm layui-btn-danger confirm js-upgrade" data-text="升级前请做好数据备份" lay-tips="该服务可升级至V'.$release['version'].'Release'.$release['releasedate'].'" href="'.wurl('server', array('op'=>'cloudup', 'nid'=>$server['identity'])).'">升级</a>';
                    $server['upgrade'] = array('version'=>$release['version'],'canup'=>false);
                }
            }
        }
        return $servers;
    }

    public function checkRequire($requires){
        if (empty($requires)) return true;
        $servers = [];
        foreach ($requires as $value){
            $identity = is_array($value) ? $value['id'] : $value;
            $servers[] = $identity;
            if ($this->isexist($identity)){
                //已安装
                continue;
            }
            if ($this->localexist($identity)){
                //未安装，但是本地存在则直接安装
                $install = $this->install($identity, false, true);
                if (is_error($install)) return error(-1, "安装依赖的服务({$identity})时发生异常：{$install['message']}");
            }else{
                //本地不存在则从云端安装
                $installCloud = $this->cloudInstall($identity, true);
                if (is_error($installCloud)) return error(-1, "安装依赖的服务({$identity})时发生异常：{$installCloud['message']}");
            }
        }
        return $servers;
    }

    public static function checkDepend($identity, $return=false){
        //判断服务依赖
        $servers = DB::table(self::$tableName)->select(array('id','identity','name','configs'))->where('configs', 'LIKE', "%$identity%")->get()->toArray();
        if (!empty($servers)){
            $depends = array();
            foreach ($servers as $value){
                $configs = $value['configs'] ? unserialize($value['configs']) : [];
                if (!empty($configs['require']) && in_array($identity, $configs['require'])){
                    $depends[$value['identity']] = $value['name'];
                }
            }
            if ($return) return $depends;
            if (!empty($depends)){
                return error(-1, "操作失败：该服务正在被其它服务依赖（".implode('、', $depends)."）");
            }
        }
        return [];
    }

    public function autoinstall(){
        $servers = $this->InitService(1);
        $return = array("upgrade"=>0, "install"=>0, "faild"=>0, "servers"=>0);
        if (!empty($servers)){
            $return['servers'] = count($servers);
            foreach ($servers as $value){
                if (!empty($value['upgrade'])){
                    try {
                        $res = $this->upgrade($value['identity']);
                        if (!is_error($res)){
                            $return['upgrade'] += 1;
                            continue;
                        }
                    }catch (\Exception $exception){
                    }
                    $return['faild'] += 1;
                }
            }
        }
        $locals = $this->getlocal();
        if (!empty($locals)){
            $return['servers'] += count($locals);
            foreach ($locals as $value){
                try {
                    $res = $this->install($value['identity']);
                    if (!is_error($res)){
                        $return['install'] += 1;
                        continue;
                    }
                }catch (\Exception $exception){
                }
                $return['faild'] += 1;
            }
        }
        return $return;
    }

    public function install($identity, $fromCloud=false, $autoInstall=false){
        if ($this->isexist($identity)) return true;
        $service = $this->getmanifest($identity);
        if (is_error($service)) return $service;
        //判断依赖服务
        $requires = $this->checkRequire($service['require']);
        if (is_error($requires)){
            return $requires;
        }
        //构造服务信息
        $keys = array('identity','name','version','cover','summary','releases');
        $application = $this->getApplication($keys, $service);
        $configs = post_var(array('uninstall'), $service);
        $configs['require'] = (array)$requires;
        if ($fromCloud){
            $configs['packagefrom'] = 'cloud';
        }
        if (!empty($configs)){
            $application['configs'] = serialize($configs);
        }
        //加载所需资源，待完善
        //运行安装脚本
        if (!empty($service['install'])){
            try {
                script_run($service['install'], MICRO_SERVER.$identity);
            }catch (\Exception $exception){
                if (!self::$devMode){
                    //删除服务安装包
                    FileService::rmdirs(MICRO_SERVER.$identity."/");
                }
                return error(-1,"安装失败：".$exception->getMessage());
            }
        }
        //操作入库
        $application['status'] = 1;
        $application['addtime'] = $application['dateline'] = TIMESTAMP;
        if (!pdo_insert(self::$tableName, $application)){
            try {
                script_run($configs['uninstall'], MICRO_SERVER.$identity);
                if (!self::$devMode){
                    //删除服务安装包
                    FileService::rmdirs(MICRO_SERVER.$identity."/");
                }
            }catch (\Exception $exception){
                //Todo something
            }
            return error(-1,'安装失败，请重试');
        }
        $this->getEvents(true);
        $this->uniLink($service);
        if (!self::$devMode){
            if ($service['inextra'] && defined('MSERVER_EXTRA')){
                CloudService::MoveDir(MSERVER_EXTRA.$identity, MICRO_SERVER.$identity);
            }
            if ($service['bindcloud']){
                @unlink(MICRO_SERVER.$identity."/manifest.json");
            }
        }
        if (file_exists(MICRO_SERVER.$identity."/composer.json") && $autoInstall){
            self::ComposerFail($identity, "");
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
        if(version_compare($manifest['application']['version'],$service['version'],'>') || $manifest['application']['releases']>$service['releases']){
            //判断依赖服务
            $requires = $this->checkRequire($service['require']);
            if (is_error($requires)){
                return $requires;
            }
            //构造服务信息
            $keys = array('name','version','cover','summary','releases');
            $application = $this->getApplication($keys, $manifest);
            $service['configs']['uninstall'] = $manifest['uninstall'];
            $service['configs']['require'] = $requires;
            $application['configs'] = serialize($service['configs']);
            //运行升级脚本
            if (!empty($manifest['upgrade'])){
                try {
                    script_run($manifest['upgrade'], MICRO_SERVER.$identity);
                }catch (\Exception $exception){
                    return error(-1,"安装失败：".$exception->getMessage());
                }
            }
            //操作入库
            $application['status'] = 1;
            $application['dateline'] = TIMESTAMP;
            if (!pdo_update(self::$tableName, $application, array('identity'=>$service['identity']))){
                return error(-1,'更新失败，请重试');
            }
            $this->getEvents(true);
            $this->uniLink($manifest);
            if (!self::$devMode){
                //删除安装包文件
                @unlink(MICRO_SERVER.$identity."/manifest.json");
            }
            return true;
        }
        return error(-1,"当前服务已经是最新版本");
    }

    public function uniLink($manifest){
        if (isset($manifest['uniLink'])){
            $uniLink = post_var(array('title','entry','summary','cover'), $manifest['uniLink']);
            if (empty($uniLink['cover'])) $uniLink['cover'] = $manifest['application']['cover'];
            if (empty($uniLink['entry'])) $uniLink['entry'] = $manifest['entrance'];
            if (empty($uniLink['title'])) $uniLink['title'] = $manifest['application']['name'];
            if (empty($uniLink['summary'])) $uniLink['summary'] = $manifest['application']['summary'];
            if (!empty($manifest['uniLink']['perms'])){
                $uniLink['perms'] = serialize($manifest['uniLink']['perms']);
            }
            $uniLink['status'] = 1;
            $uniLink['dateline'] = TIMESTAMP;
            $isExists = (int)pdo_getcolumn("microserver_unilink", array('name'=>$manifest['application']['identity']), 'id');
            if (empty($isExists)){
                $uniLink['addtime'] = TIMESTAMP;
                $uniLink['name'] = $manifest['application']['identity'];
                return pdo_insert("microserver_unilink", $uniLink);
            }
            return pdo_update("microserver_unilink", $uniLink, array('id'=>$isExists));
        }
        return pdo_delete("microserver_unilink", array("name"=>$manifest['application']['identity']));
    }

    public function uninstall($identity){
        $service = self::getone($identity, false);
        if (empty($service)) return error(-1,'该服务尚未安装');
        $depends = self::checkDepend($identity);
        if (is_error($depends)) return $depends;
        try {
            script_run($service['configs']['uninstall'], MICRO_SERVER.$identity);
        }catch (\Exception $exception){
            return error(-1,"卸载失败：".$exception->getMessage());
        }
        if (!pdo_delete(self::$tableName,array('id'=>$service['id']))){
            return error(-1,'卸载失败，请重试');
        }
        $this->getEvents(true);
        pdo_delete("microserver_unilink", array("name"=>$identity));
        $composerExists = file_exists(MICRO_SERVER.$identity."/composer.json");
        if ($composerExists){
            self::ComposerRemove("microserver/".$identity);
        }
        if (!self::$devMode){
            //删除服务安装包
            FileService::rmdirs(MICRO_SERVER.$identity."/");
        }
        return true;
    }

    /**
     * 自动安装Composer依赖
     * @param string $basePath composer.json路径
     * @param string $name 包名称
     * @return bool 安装结果
    */
    public static function ComposerRequire($basePath, $name){
        $composer = $basePath."composer.json";
        if (!file_exists($composer)) return true;
        $WorkingDirectory = base_path() . "/";
        if (DEVELOPMENT){
            if (file_exists($basePath."composer.lock")){
                return self::ComposerUpdate($basePath, $name);
            }
            $WorkingDirectory = $basePath;
            $command = ['composer', 'update'];
        }else{
            $JSON = file_get_contents($composer);
            $composerObj = json_decode($JSON, true);
            $composerVer = trim($composerObj['version']);
            $LOCK = file_get_contents($WorkingDirectory."composer.lock");
            $lockObj = json_decode($LOCK, true);
            if (!empty($lockObj['packages'])){
                foreach ($lockObj['packages'] as $package){
                    if ($package['name']==$name){
                        if (!empty($composerVer) && version_compare($composerVer, $package['version'], '>')){
                            return self::ComposerUpdate($basePath, $name, $composerVer);
                        }
                        return true;
                    }
                }
            }
            $command = ['composer', 'require', $name];
            if (!empty($composerVer)){
                $command[] = $composerVer;
            }
        }
        try {
            $process = new Process($command);
            $process->setWorkingDirectory($WorkingDirectory);
            $process->setEnv(['COMPOSER_HOME'=>self::ComposerHome()]);
            $process->start();
            $process->wait();
            if ($process->isSuccessful()) {
                return true;
            }else{
                self::ComposerFail($name, $process->getOutput());
            }
        }catch (\Exception $exception){
            //Todo something
            self::ComposerFail($name, $exception->getMessage());
        }
        return false;
    }

    public static function ComposerUpdate($basePath, $name, $composerVer=''){
        if (DEVELOPMENT){
            $WorkingDirectory = $basePath;
            $command = ['composer', 'update'];
        }else{
            $WorkingDirectory = base_path("/");
            if (empty($composerVer)){
                $composerVer = "";
                $composer = $basePath."composer.json";
                $JSON = file_get_contents($composer);
                $composerObj = json_decode($JSON, true);
                if (isset($composerObj['version'])){
                    $composerVer = $composerObj['version'];
                }
            }
            $command = ['composer', 'require', $name];
            if (!empty($composerVer)){
                $command[] = $composerVer;
            }
        }
        try {
            $process = new Process($command);
            $process->setWorkingDirectory($WorkingDirectory);
            $process->setEnv(['COMPOSER_HOME'=>self::ComposerHome()]);
            $process->start();
            $process->wait();
            if ($process->isSuccessful()) {
                return true;
            }else{
                self::ComposerFail($name, $process->getOutput(), $command);
            }
        }catch (\Exception $exception){
            //Todo something
            self::ComposerFail($name, $exception->getMessage(), $command);
        }
        return false;
    }

    public static function ComposerRemove($require){
        if (DEVELOPMENT) return true;
        $WorkingDirectory = base_path("/");
        try {
            $process = new Process(["composer", "remove", $require]);
            $process->setWorkingDirectory($WorkingDirectory);
            $process->setEnv(['COMPOSER_HOME'=>self::ComposerHome()]);
            $process->start();
            $process->wait();
            if ($process->isSuccessful()) {
                return true;
            }
        }catch (\Exception $exception){
            //Todo something
        }
        return false;
    }

    public static function ComposerFail($name, $output, $command=[]){
        $logPath = MICRO_SERVER . str_replace("microserver/", "", $name) . "/composer.error";
        if (!empty($command)){
            $output = implode(" ", $command) . "：" . $output;
        }
        file_put_contents($logPath, $output);
    }

    public static function ComposerHome(){
        $php_uname = php_uname();
        if (strexists($php_uname, "Windows")){
            return "C:\Users\<user>\AppData\Roaming\Composer";
        }elseif (strexists($php_uname, "Linux")){
            return "/root/.composer";
        }elseif (strexists($php_uname, "nux")){
            return "/home/<user>/.composer";
        }elseif (strexists($php_uname, 'OSX')){
            return '/Users/<user>/.composer';
        }
        return "";
    }

    public static function disable($identity){
        $depends = self::checkDepend($identity);
        if (is_error($depends)){
            return $depends;
        }
        if (pdo_update(self::$tableName, array('status'=>0,'dateline'=>TIMESTAMP), array('identity'=>trim($identity)))){
            pdo_update('microserver_unilink', array('status'=>0,'dateline'=>TIMESTAMP), array('name'=>trim($identity)));
            return true;
        }
        return false;
    }

    public static function restore($identity){
        if (pdo_update(self::$tableName, array('status'=>1,'dateline'=>TIMESTAMP), array('identity'=>trim($identity)))){
            pdo_update('microserver_unilink', array('status'=>1,'dateline'=>TIMESTAMP), array('name'=>trim($identity)));
            return true;
        }
        return false;
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
                        $param .= "=[]";
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
                    $serv = $service['application'];
                    $serv['actions'] = '<a class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="确定要安装该服务？" href="'.wurl('server', array("op"=>"install", "nid"=>$serv['identity'])).'">安装</a>';
                    $serv['status'] = -1;
                    $serv['isdelete'] = false;
                    $servers[$serv['identity']] = $serv;
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
