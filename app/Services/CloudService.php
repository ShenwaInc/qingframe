<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CloudService
{

    static $identity = 'swa_framework_laravel';
    static $cloudApi = 'https://chat.gxit.org/app/index.php?i=4&c=entry&m=swa_supersale&do=api';
    static $apiList = array('rmcom'=>'cloud.vendor.remove','require'=>'cloud.install','structure'=>'cloud.structure','upgrade'=>'cloud.makepatch');

    static function RequireModule($identity,$path='addons'){
        $modulePre = ModuleService::SysPrefix();
        $moduleName = str_replace($modulePre, "", $identity);
        $targetpath = base_path("public/$path/$moduleName");
        $from = 'local';
        if (!is_dir($targetpath)){
            MSService::TerminalSend(['mode'=>'info', 'message'=>'即将从云端获取模块']);
            $result = self::CloudRequire($identity,$targetpath);
            if(is_error($result)) return $result;
            $from = 'cloud';
        }
        //进入模块安装流程
        return ModuleService::install($moduleName,$path,$from);
    }

    static function getPlugins(){
        $plugins = [];
        $condition = array('type'=>1);
        //获取已安装模块(从云端表中)
        $components = DB::table('gxswa_cloud')->where($condition)->orderByRaw("`id` desc")->get()->toArray();
        if (!empty($components)){
            foreach ($components as $com){
                $cloudInfo = !empty($com['online']) ? unserialize($com['online']) : array('upgradable'=>false);
                $cloudInfo['isLocal'] = false;
                $cloudInfo['expired'] = false;
                $cloudInfo['upgradable'] = (bool)$cloudInfo['upgradable'];
                $selfMaintenance = (bool)$com['maintenance'];
                $com['action'] = '';
                if (!empty($com['modulename'])){
                    $local = ModuleService::installCheck($com['modulename']);
                    if (is_error($local)){
                        $com['isDelete'] = true;
                    }
                }
                $com['logo'] = asset($com['logo']);
                $com['lastUpdated'] = $com['updatetime'] ? date('Y/m/d H:i',$com['updatetime']) : __('installFirstTime');
                $com['installTime'] = date('Y/m/d H:i',$com['addtime']);
                $com['expireDate'] = '';
                $com['cloudInfo'] = $cloudInfo;
                $com['maintenance'] = $selfMaintenance;
                $com['installed'] = true;
                $plugins[$com['modulename']] = $com;
            }
        }
        //获取本地模块
        $modules = FileService::file_tree(public_path('addons'), array('*/manifest.json'));
        if (!empty($modules)){
            foreach ($modules as $value){
                $identity = str_replace(array(public_path('addons/'),"/manifest.json"),'', $value);
                if (empty($identity)) continue;
                try {
                    $ManiFest = ModuleService::getManifest($identity);
                    if (is_error($ManiFest)) continue;
                    $com = $ManiFest['application'];
                }catch (\Exception $exception){
                    continue;
                }
                $comCloud = $plugins[$identity] ?? [];
                if (empty($com['modulename'])){
                    $com['modulename'] = $com['identifie'];
                }
                $com['logo'] = asset($com['logo']);
                $com['website'] = $com['url'];
                $com['installTime'] = '<span class="layui-badge layui-bg-orange">'.__('readyToInstall').'</span>';
                $com['addtime'] = 0;
                $com['installed'] = !empty($comCloud);
                $com['expireDate'] = !empty($comCloud) ? $comCloud['expireDate'] : '';
                $actions = $comCloud['action']??'';
                //已安装
                if ($ManiFest['installed']){
                    $com['installed'] = true;
                    if (!empty($comCloud)){
                        //从云端安装
                        $com['installTime'] = $comCloud['installTime'];
                        $com['lastUpdated'] = $comCloud['lastUpdated'];
                        $com['cloudInfo'] = $comCloud['cloudInfo'];
                    }else{
                        //从本地安装
                        $com['installTime'] = __('appLocal');
                        $com['lastUpdated'] = '-';
                        $com['cloudInfo'] = $comCloud ? $comCloud['cloudInfo'] : array('upgradable'=>false, 'isLocal'=>true);
                    }
                    $com['addtime'] = $com['releasedate'];
                    if (DEVELOPMENT){
                        $Module = ModuleService::fetch($com['identifie']);
                        if (!empty($Module) && !is_error($Module)){
                            if (version_compare($com['version'], $Module['version'], '>')){
                                $tips = __('应用可升级至V:version', ['version'=>$com['version']]);
                                $actions .= '<a href="'. wurl('module/upgrade', ['nid'=>$com['modulename']]) .'" data-text="'. __('upgradeConfirm') .'" class="layui-btn layui-btn-sm layui-btn-warm js-terminal" lay-tips="'.$tips.'">'. __('本地升级') .'</a>';
                            }
                        }
                    }
                    $actions .= '<a href="'.wurl('module/allocate', array('nid'=>$identity)).'" title="'.__('分配应用权限').'" class="layui-btn layui-btn-sm ajaxshow">'.__('分配').'</a>';
                    $actions .= '<a href="'.wurl('module/remove', array('nid'=>$identity)).'" class="layui-btn layui-btn-sm layui-btn-primary js-terminal" data-text="'.__('uninstallConfirm').'">'.__('uninstall').'</a></div>';
                }else{
                    $com['lastUpdated'] = '-';
                    if(DEVELOPMENT){
                        $actions .= '<a href="'.wurl('module/install', array('nid'=>$identity)).'" class="layui-btn layui-btn-sm layui-btn-normal js-terminal" data-text="'.__('installConfirm').'">'.__('install').'</a>';
                    }
                }
                $com = array_merge($comCloud, $com);
                $com['action'] = $actions;
                $plugins[$identity] = $com;
            }
        }
        //获取云端未安装模块
        $cacheKey = "cloud:module_list:1";
        $res = Cache::get($cacheKey, array());
        if (empty($res)){
            $data = array(
                'r'=>'cloud.packages',
                'pidentity'=>config('system.identity'),
                'page'=>1,
                'category'=>1,
                'authorize'=>1
            );
            $res = CloudService::CloudApi("", $data);
            Cache::put($cacheKey, $res, 600);
        }
        if (!is_error($res) && !empty($res['servers'])){
            $modulePre = ModuleService::SysPrefix();
            foreach ($res['servers'] as $value){
                $identify = str_replace($modulePre, "", $value['identity']);
                if (empty($identify) || empty($value['release'])) continue;
                $releaseDate = intval($value['release']['releasedate']);
                if (isset($plugins[$identify])){
                    //已安装
                    $local = $plugins[$identify];
                    if ($local['addtime']==0 || !empty($local['maintenance'])) continue;
                    $cloudInfo = array('upgradable'=>$local['cloudInfo']['upgradable'], 'expired'=>false, 'isLocal'=>$local['cloudInfo']['isLocal'],'version'=>$value['release']['version'],'releasedate'=>$releaseDate);
                    $cloudInfo['id'] = $value['identity'];
                    $local['expireDate'] = '';
                    if (!$cloudInfo['isLocal']){
                        if (!is_error($value['authorize'])){
                            if($value['authorize']['expiretime']==0){
                                $local['expireDate'] = '<span class="text-green">'.__('longtime').'</span>';
                            }elseif ($value['authorize']['expiretime']<=TIMESTAMP){
                                $local['expireDate'] = '<span class="text-red">'.__('已到期').'</span>';
                                $cloudInfo['expired'] = true;
                            }else{
                                $toDay = ($value['authorize']['expiretime'] - TIMESTAMP)/86400;
                                $local['expireDate'] = '<span class="'.($toDay>30?'text-gray':'text-orange').'">'.__('expiresOn', array('date'=>date('Y-m-d', $value['authorize']['expiretime']))).'</span>';
                            }
                        }else{
                            $local['expireDate'] = '<span class="text-red">'.__($value['authorize']['message']).'</span>';
                        }
                    }
                    if (version_compare($local['version'], $value['release']['version'], '<') || $local['releasedate']<$releaseDate){
                        //可升级至云端最新版本
                        $cloudInfo['upgradable'] = true;
                        $tips = __('应用可升级至V:version', ['version'=>$value['release']['version']]);
                        $action = '<a href="'. wurl('module/update', ['nid'=>$local['modulename']]) .'" data-text="'. __('upgradeConfirm') .'" class="layui-btn layui-btn-sm layui-btn-danger js-terminal" lay-tips="'.$tips.'">'. __('upgrade') .'</a>';
                        $local['action'] = $action . $local['action'];
                    }
                    $local['cloudInfo'] = $cloudInfo;
                    $local['installed'] = true;
                    $plugins[$identify] = $local;
                }else{
                    //未安装
                    $com = array(
                        'id'=>0,
                        'name'=>$value['name'],
                        'identify'=>$identify,
                        'version'=>$value['release']['version'],
                        'releasedate'=>$releaseDate,
                        'ability'=>$value['name'],
                        'description'=>$value['summary'],
                        'author'=>$value['author'],
                        'website'=>$value['website'],
                        'logo'=>$value['icon'],
                        'installed'=>false
                    );
                    $com['lastUpdated'] = '-';
                    $com['expireDate'] = '';
                    $com['cloudInfo'] = array(
                        'id'=>$value['identity'],
                        'version'=>$value['release']['version'],
                        'releasedate'=>$releaseDate,
                        'upgradable'=>false,
                        'isLocal'=>false,
                        'expired'=>false
                    );
                    $com['installTime'] = '<span class="layui-badge layui-bg-orange">'.__('readyToInstall').'</span>';
                    $com['action'] = '<a href="'.wurl('module/require', array('nid'=>$value['identity'])).'" class="layui-btn layui-btn-sm layui-btn-normal js-terminal" data-text="'.__('installConfirm').'">'.__('install').'</a>';
                    $plugins[$identify] = $com;
                }
            }
        }
        //dd($plugins);
        return $plugins;
    }

    static function MoveDir($from, $to, $overWrite = false){
        $aimDir = rtrim($from, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $oldDir = rtrim($to, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!is_dir($oldDir)) {
            return false;
        }
        if (!is_dir($aimDir)) {
            FileService::mkdirs($aimDir);
        }
        @ $dirHandle = opendir($oldDir);
        if (!$dirHandle) {
            return false;
        }
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (!is_dir($oldDir . $file)) {
                FileService::file_move($oldDir . $file,$aimDir . $file);
            } else {
                self::MoveDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
        }
        closedir($dirHandle);
        return FileService::rmdirs($oldDir);
    }

    static function copyDir($from, $to, $overwrite=false): bool
    {
        if (!is_dir($to)) {
            FileService::mkdirs($to);
        }
        $from = rtrim($from, DIRECTORY_SEPARATOR);
        $to = rtrim($to, DIRECTORY_SEPARATOR);
        @$dirHandle = opendir($from);
        if (!$dirHandle) {
            return false;
        }
        while (false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $sourceFile = $from . '/'. $file;
            $destinationFile = $to . '/'. $file;
            if (is_file($sourceFile)) {
                if(file_exists($destinationFile) && $overwrite){
                    @unlink($destinationFile);
                }
                @copy($sourceFile, $destinationFile);
            } else {
                self::copyDir($sourceFile, $destinationFile, $overwrite);
            }
        }
        closedir($dirHandle);
        return true;
    }

    static function CloudRequire($identity,$targetpath,$patch=''){
        $data = array(
            'identity'=>$identity,
            'fp'=>config('system.identity')
        );
        $zipContent = self::CloudApi('require',$data,true);
        if (is_error($zipContent)) return $zipContent;
        if (empty($zipContent)) return error(-1,__('requestFailed'));
        $isJson = json_decode($zipContent, true);
        if (!empty($isJson)){
            $result = error(-1, $isJson['message']);
            $result['redirect'] = trim($isJson['redirect']);
            if (DEVELOPMENT){
                dd($result);
            }
            return $result;
        }
        if (!$patch){
            $patch = base_path("storage/patch/");
        }
        if (!is_dir($patch)){
            FileService::mkdirs($patch);
        }
        $filename = FileService::file_random_name($patch,'zip');
        if (!file_put_contents($patch . $filename, $zipContent)) {
            return error(-1,__('saveFailed'));
        }

        $zip = new \ZipArchive();
        $openRes = $zip->open($patch.$filename);
        if ($openRes === TRUE) {
            $zip->extractTo($targetpath);
            $zip->close();
            //删除补丁包
            @unlink($patch.$filename);
        }else{
            $zip->close();
            @unlink($patch.$filename);
            return error(-1,__('unzipFailed'));
        }

        //如果解压包内嵌则操作搬移
        if (is_dir($targetpath.$identity.'/')){
            if (is_dir($patch.$identity)){
                FileService::rmdirs($patch.$identity);
            }
            self::MoveDir($targetpath.$identity,$patch);
            self::CloudPatch($targetpath,$patch.$identity.'/',true);
            FileService::rmdirs($patch.$identity, true);
        }
        return true;
    }

    static function CloudUpdate($identity,$targetpath,$patch=''){
        MSService::TerminalSend(['mode'=>'info', 'message'=>'从云端对比程序源码...']);
        $data = array(
            'identity'=>$identity,
            'fp'=>config('system.identity')
        );
        $upgradeInfo = self::CloudApi('structure',$data);
        if (is_error($upgradeInfo)) return $upgradeInfo;
        MSService::TerminalSend(['mode'=>'info', 'message'=>'获取到云端程序信息：V'.$upgradeInfo['version']]);
        if (!empty($upgradeInfo['versionBase'])){
            if (version_compare($upgradeInfo['versionBase'], QingVersion, '>')){
                $upgradeText = '&nbsp;&nbsp;<a href="/console/setting" class="text-blue">'.__('upgradeNow').'</a>';
                return error(-1, __('应用最低兼容版本', array('version'=>$upgradeInfo['versionBase'])) . $upgradeText);
            }
        }
        $structures = json_decode(base64_decode($upgradeInfo['structure']), true);
        $difference = self::CloudCompare($structures,$targetpath);
        if (empty($difference)) return true;
        MSService::TerminalSend(['mode'=>'info', 'message'=>'从云端同步应用程序...']);
        $data = array(
            'identity'=>$identity,
            'fp'=>config('system.identity'),
            'releasedate'=>$upgradeInfo['releasedate'],
            'difference'=>base64_encode(json_encode($difference))
        );
        $zipContent = self::CloudApi('upgrade',$data,true);
        if (is_error($zipContent)) return $zipContent;
        if (empty($zipContent)){
            MSService::TerminalSend(['mode'=>'err', 'message'=>'云端程序同步失败，请更新缓存后再试']);
            return error(-1,__('patchFailed'));
        }
        if (!$patch){
            $patch = base_path('storage/patch/');
        }
        if (!is_dir($patch)){
            FileService::mkdirs($patch);
        }
        $filename = FileService::file_random_name($patch,'zip');
        $fullName = $patch.$filename;
        if (!file_put_contents($fullName, $zipContent)) {
            MSService::TerminalSend(['mode'=>'err', 'message'=>'云端程序同步失败，请检查文件权限']);
            return error(-1,__('saveFailed'));
        }
        $patchPath = $patch.$identity.$upgradeInfo['releasedate'].'/';
        if (is_dir($patchPath)){
            FileService::rmdirs($patchPath);
        }
        $zip = new \ZipArchive();
        $openRes = $zip->open($fullName);
        if ($openRes === TRUE) {
            $zip->extractTo($patchPath);
            $zip->close();
            @unlink($fullName);
        }else{
            @unlink($fullName);
            MSService::TerminalSend(['mode'=>'err', 'message'=>'补丁包解压失败，请检查文件夹权限']);
            return error(-1,__('unzipFailed'));
        }
        MSService::TerminalSend(['mode'=>'info', 'message'=>'更新程序源码...']);
        //5、将补丁文件更新到本地
        self::CloudPatch($targetpath,$patchPath,true);
        FileService::rmdirs($patchPath);
        return true;
    }

    /**
     * 文件结构对比，通过文件md5对比本地文件夹和给定的文件结构是否有偏差
     * @param array $structures 文件结构
     * @param string $target 本地文件夹路径，须以 / 结尾
     * @param string|null $basedir 本次对比的子目录，须以 / 结尾
     * @param array|null $ignore 忽略文件
    */
    static function CloudCompare($structures,$target,$basedir='', $ignore=[]){
        if (empty($structures) || !$target) return false;
        if (!is_dir($target)) return  $structures;
        $difference = array();
        foreach ($structures as $item){
            if (is_array($item)){
                //文件夹，进一步扫描对比
                $folder = $basedir.$item[0];
                if (in_array($folder, $ignore) || in_array($folder."/", $ignore)){
                    //已注明的忽略文件夹
                    continue;
                }
                $dirDiff = array();
                if (!is_dir($target.$folder)){
                    $dirDiff = $item;
                }else{
                    $structure = self::CloudCompare($item[1],$target,$folder.'/');
                    if (!empty($structure)){
                        $dirDiff = array($item[0],$structure);
                    }
                }
                if (!empty($dirDiff)){
                    $difference[] = $dirDiff;
                }
            }else{
                list($filename, $fileMD5) = explode('|',$item);
                if (\Str::startsWith($filename, '.')){
                    //忽略对比以.开头的文件
                    continue;
                }
                $filepath = $basedir.$filename;
                if (in_array($filepath, $ignore) || in_array("/".$filepath, $ignore)){
                    //已注明的忽略文件
                    continue;
                }
                if (!file_exists($target.$filepath)){
                    $difference[] = $item;
                }else{
                    $md5 = md5_file($target.$filepath);
                    $hash = substr($md5,0,4).substr($md5,-4);
                    if($hash!=$fileMD5){
                        $difference[] = $item;
                    }
                }
            }
        }
        return $difference;
    }

    static function CloudPatch($target,$source,$overwrite=false): bool
    {
        if (!$target || !$source) return false;
        if (!is_dir($target)){
            if ($overwrite){
                FileService::mkdirs($target);
            }else{
                return false;
            }
        }
        if (!is_dir($source)) return  false;
        $handle = dir($source);
        if ($dh = opendir($source)){
            while ($entry = $handle->read()) {
                $ignores = array(".", "..", ".svn", ".git", ".gitignore");
                if (in_array($entry, $ignores)){
                    continue;
                }
                $new = $source.$entry;
                if(is_dir($new)) {
                    @Storage::makeDirectory($target.$entry);
                    self::CloudPatch($target.$entry.'/',$source.$entry.'/',$overwrite);
                }else{
                    if(file_exists($target.$entry)){
                        if(!$overwrite) {
                            if (md5_file($target . $entry) == md5_file($new)) continue;
                        }
                        @unlink($target.$entry);
                    }
                    @copy($new, $target.$entry);
                }
            }
            closedir($dh);
        }
        return true;
    }

    static function CloudApi($route, $data=array(), $return=false){
        global $_W;
        if (!$data['appsecret']) $data['appsecret'] = self::AppSecret();
        if (!isset($data['r'])){
            $data['r'] = self::$apiList[$route];
        }
        if (!isset($data['fp'])){
            $data['fp'] = config('system.identity');
        }
        $data['t'] = TIMESTAMP;
        $data['siteroot'] = $_W['siteroot'];
        $data['siteid'] = $_W['config']['site']['id'];
        $data['devmode'] = env('APP_DEVELOPMENT',0);
        $data['versionBase'] = QingVersion;
        $data['sign'] = self::GetSignature($data['appsecret'],$data);
        $CloudApi = env('APP_CLOUD_API', self::$cloudApi);
        $res = HttpService::ihttp_post($CloudApi,$data);
        if (is_error($res)) return $res;
        if($return){
            if(strexists($res['content'], 'error')){
                $result = json_decode($res['content'],true);
                if (json_last_error() != JSON_ERROR_NONE || empty($result['message'])) return $res['content'];
                $response = error(-1,$result['message']);
                if (!empty($result['redirect'])){
                    $response['redirect'] = $result['redirect'];
                }
                return $response;
            }
            return $res['content'];
        }
        $result = json_decode($res['content'],true);
        if (isset($result['message']) && isset($result['type'])){
            if ($result['type']!='success' && !is_array($result['message'])){
                $response = error(-1,$result['message']);
                if (!empty($result['redirect'])){
                    $response['redirect'] = $result['redirect'];
                }
                return $response;
            }
        }
        return $result;
    }

    static function CloudActive($cache=false){
        global $_W;
        $default = array('state'=>__('未开始激活'),'siteid'=>0,'siteroot'=>$_W['siteroot'],'expiretime'=>0,'status'=>0,'uid'=>0,'mobile'=>"",'name'=>$_W['setting']['page']['title']);
        $cacheKey = md5('HingWork:Authorize:Active:'.$_W['siteroot']);
        $authorize = Cache::get($cacheKey,$default);
        if ($cache && isset($authorize['hasDomain'])){
            return $authorize;
        }
        $res = self::CloudApi('',array('r'=>'cloud.active.state', 'siteName'=>$authorize['name'],'identity'=>config('system.identity')));
        if (!empty($res['redirect'])){
            $authorize['redirect'] = trim($res['redirect']);
        }
        if (is_error($res)){
            $authorize['state'] = $res['message'];
            return $authorize;
        }
        if (!isset($res['siteinfo'])){
            $authorize['state'] = __('激活状态查询失败');
            return $authorize;
        }
        if (is_error($res['siteinfo'])){
            $authorize['state'] = $res['siteinfo']['message'];
            return $authorize;
        }
        if ($res['siteinfo']['status']==1){
            $authorize['name'] = $res['siteinfo']['name'];
        }

        $authorize['siteid'] = $res['siteinfo']['id'];
        $authorize['uid'] = $res['siteinfo']['uid'];
        $authorize['mobile'] = $res['siteinfo']['mobile'];
        $authorize['status'] = $res['siteinfo']['status'];
        $authorize['reDomain'] = (int)$res['authorizes'];
        $authorize['hasDomain'] = (bool)$res['siteinfo']['hasDomain'];
        if (!$authorize['hasDomain']){
            $authorize['siteroot'] = $res['siteinfo']['url'];
        }
        if ($res['siteinfo']['status']==1){
            $authorize['state'] = __('activated');
        }

        Cache::put($cacheKey, $authorize, 3600);

        return $authorize;
    }

    static function CloudEnv($search, $replace){
        if (empty($search) || empty($replace)) return false;
        $envFile = base_path(".env");
        $reader = fopen($envFile,'r');
        $envData = fread($reader,filesize($envFile));
        fclose($reader);
        $envData = str_replace($search, $replace, $envData);
        $writer = fopen($envFile,'w');
        $complete = fwrite($writer,$envData);
        fclose($writer);
        return $complete;
    }

    static function AppSecret(){
        global $_W;
        return sha1($_W['config']['setting']['authkey'].'-'.$_W['siteroot'].'-'.$_W['config']['site']['id']);
    }

    static function GetSignature($appsecret='',$data=array()){
        if (!$appsecret) return false;
        unset($data['sign'],$data['appsecret']);
        ksort($data);
        $string = base64_encode(http_build_query($data)).$appsecret;
        return md5($string);
    }

}
