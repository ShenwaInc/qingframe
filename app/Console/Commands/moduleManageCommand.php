<?php

namespace App\Console\Commands;

use App\Services\AccountService;
use Illuminate\Console\Command;
use App\Services\ModuleService;
use App\Services\CloudService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;
use App\Services\MSService;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\ArgvInput;

class moduleManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:manage {operation} {--module=default} {--path=apps} {--passcode=} {--uniacid=0} {--maintenance-state=1} {--name=} {--logo=} {--description=} {--sso-url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '应用模块管理工具，operation 可选值：display（展示所有应用）, displayFromCloud（展示云端应用）, install（安装）, upgrade（升级）, uninstall（卸载）, require（从云端请求）, update（从云端升级）, maintenance（关闭云服务）, allocate（分配应用到租户）, cancelAllocate（取消分配到租户）';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        if ($this->input instanceof ArgvInput) {
            // 通过命令行直接运行，终端输出改为通过 Command 类输出
            $this->info('Manage modules in terminal client');
            MSService::$Command = $this;
            global $_W;
            $_W['TerminalSilence'] = true;
        }
        $operation = $this->argument('operation');
        $module = $this->option('module');
        $path = $this->option('path');
        $passcode = $this->option('passcode');
        if (empty($path)) $path = 'apps';
        switch ($operation){
            case 'install' : {
                // 从本地安装
                $res = ModuleService::install($module, $path, 'local');
                if (is_error($res)){
                    $this->error($res['message']);
                }else{
                    $this->info(__('installSuccessfully'));
                }
                break;
            }
            case 'upgrade' : {
                // 从本地升级
                $res = ModuleService::upgrade($module, 'local', $path);
                if (is_error($res)){
                    $this->error($res['message']);
                }else {
                    $this->info(__('upgradeSuccessfully'));
                }
                break;
            }
            case 'uninstall' : {
                $res = ModuleService::uninstall($module, $path);
                if (is_error($res)){
                    $this->error($res['message']);
                }else{
                    $this->info(__('uninstallComplete'));
                }
                break;
            }
            case 'require' : {
                // 从云端安装
                if (!empty($passcode)){
                    // 核销卡密
                    $consume = $this->consumeCode($passcode);
                    if (is_error($consume)){
                        $this->error($consume['message']);
                        return;
                    }
                }
                $res = CloudService::RequireModule($module, $path);
                if (is_error($res)){
                    $this->error($res['message']);
                }else{
                    $this->info(__('installSuccessfully'));
                }
                break;
            }
            case 'update' : {
                // 从云端升级
                if (!empty($passcode)){
                    // 核销卡密
                    $consume = $this->consumeCode($passcode);
                    if (is_error($consume)){
                        $this->error($consume['message']);
                        return;
                    }
                }
                $cloudIdentity = ModuleService::SysPrefix($module);
                $targetPath = base_path($path . "/$module/");
                $res = CloudService::CloudUpdate($cloudIdentity, $targetPath);
                if (is_error($res)){
                    $this->error($res['message']);
                }else{
                    $this->info(__('upgradeSuccessfully'));
                }
                break;
            }
            case 'maintenance' : {
                // 切换为自维护模式，不再从云端获取新版本
                $maintenanceState = $this->option('maintenance-state') ?? 1;
                if (!ModuleService::maintenance($module, $maintenanceState)){
                    $this->error(__('operationFailed'));
                    return;
                }
                $this->info(__('successful'));
                break;
            }
            case 'display': {
                $moduleList = CloudService::getPlugins();
                if (!empty($moduleList)){
                    $rows = array_map(function ($module) {
                        $identify = $module['identify'] ?? $module['modulename'];
                        $fromLocal = !empty($module['cloudInfo']['isLocal']);
                        $basePath = $module['base_path'] ?: 'public/addons';
                        $description = trim($module['description']);
                        if (mb_strlen($description, 'utf8')>30){
                            $description = mb_substr($description, 0, 30, 'utf8').'...';
                        }
                        return [$identify, $module['name'], $module['description'], $module['author'], $module['version'], $module['installed']?'true':'false', $fromLocal?'local':'cloud', $basePath];
                    }, $moduleList);
                    $this->table(array('identifier', 'name', 'description', 'author', 'version', 'installed', 'from', 'path'), $rows);
                }else{
                    $this->info(__('empty'));
                }
                break;
            }
            case 'displayFromCloud':{
                $cacheKey = "cloud:module_list:1";
                $res = Cache::get($cacheKey, array());
                if (empty($res)){
                    $data = array(
                        'r'=>'cloud.packages',
                        'pidentity'=>CloudService::$identity,
                        'page'=>$page,
                        'category'=>1
                    );
                    $res = CloudService::CloudApi("", $data);
                    Cache::put($cacheKey, $res, 600);
                }
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                if (!empty($res['servers'])){
                    $modulePre = ModuleService::SysPrefix();
                    $rows = [];
                    foreach ($res['servers'] as $module){
                        $identify = str_replace($modulePre, "", $module['identity']);
                        if (empty($identify)) continue;
                        $installed = false;
                        $base_path = $module['base_path']?:'public/addons';
                        $localExist = ModuleService::localExists($identify, $base_path);
                        if ($localExist){
                            $moduleInfo = ModuleService::installCheck($identify, $base_path);
                            if (!is_error($moduleInfo) && $moduleInfo->installed){
                                $installed = true;
                            }
                        }
                        $summary = $module['summary'] ? trim($module['summary']) : '';
                        if (mb_strlen($summary, 'utf8')>30){
                            $summary = mb_substr($summary, 0, 30, 'utf8') . '...';
                        }
                        $rows[] = [$identify, $module['name'], $summary, $module['author'], !empty($module['release'])?$module['release']['version']:'未发布', $installed?'true':'false', $localExist?'true':'false', $base_path];
                    }
                    $this->table(array('identifier', 'name', 'description', 'author', 'version', 'installed', 'localExist', 'path'), $rows);
                }else{
                    $this->info(__('empty'));
                }
                break;
            }
            case 'cancelAllocate' :
            case 'allocate' : {
                // 分配应用到指定租户
                $moduleInfo = ModuleService::fetch($module);
                if (empty($moduleInfo)){
                    $this->error(__('applicationNotInstall'));
                    return;
                }
                $uniacid = $this->option('uniacid');
                $platform = AccountService::FetchUni($uniacid);
                if (empty($platform)){
                    $this->error(__('platformNotFound'));
                    return;
                }
                $moduleList = AccountService::ExtraModules($platform['uniacid'], false);
                if ($operation=='cancelAllocate'){
                    // 取消分配
                    if (!empty($moduleList[$module])){
                        unset($moduleList[$module]);
                        DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$platform['uniacid']), array('modules'=>serialize(array_values($moduleList))));
                    }
                }else{
                    // 分配应用
                    if (empty($moduleList[$module])){
                        $moduleData = array(
                            'name'=>$moduleInfo['title'],
                            'identity'=>$moduleInfo['name'],
                            'logo'=>$moduleInfo['logo'],
                            'profile'=>'default'
                        );
                        $modules = array_values($moduleList);
                        $modules[] = $moduleData;
                        DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$platform['uniacid']), array('modules'=>serialize($modules)));
                    }
                }
                CacheService::flush();
                $this->info(__('successful'));
                break;
            }
        }
        return;
    }

    public function consumeCode($code)
    {
        $data = array(
            'r'=>'cloud.package.consume',
            'identity'=>"",
            'remark'=>'Install with a passcode',
            'code'=>$code
        );
        return CloudService::CloudApi("", $data);
    }


}
