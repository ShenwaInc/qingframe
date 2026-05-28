<?php

namespace App\Console\Commands;

use App\Services\MSService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MicroServerManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microserver:manage {action} {--id=} {--installed=1} {--state=running} {--page=1} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微服务管理工具，action 可选值：install（从本地安装）|require（从云端安装）|update（从本地更新）|upgrade（从云端更新）|uninstall（卸载服务）|remove（删除服务目录）|stop（停止服务）|enable（启用服务）|repair（修复服务）|composer-install（安装 composer 依赖）|composer-update（更新 composer 依赖）|display（显示本地服务列表）|display-cloud（显示云端未安装服务列表）|state（显示服务状态）';

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
        $action = $this->argument('action');
        $MSService = new MSService();
        $id = $this->option('id');
        $TerminalSilence = false;
        if (in_array($action, ['install', 'require', 'update', 'upgrade', 'uninstall', 'remove', 'stop', 'enable', 'composer-install', 'composer-update'])){
            if (empty($id)){
                $this->error("服务标识不能为空");
                return;
            }
            $TerminalSilence = true;
        }
        if ($TerminalSilence || $action=='repair'){
            if (app()->runningInConsole()){
                // 通过命令行直接运行，终端输出改为通过 Command 类输出
                $this->info('Manage servers in terminal client');
                $MSService::$Command = $this;
                global $_W;
                $_W['TerminalSilence'] = true;
            }
        }
        $installed = (int)$this->option('installed');
        $page = (int)$this->option('page') ?: 1;
        $name = trim($this->option('name'));
        switch ($action) {
            case 'display':
                // 显示本地服务列表
                $status = 0;
                if (!empty($installed)){
                    //查看已安装服务
                    $status = $this->option('state')=='running' ? 1 : 0;
                    $servers = $MSService::InitService($status);
                }else{
                    $servers = $MSService::getlocal();
                }
                if (empty($servers)){
                    $this->info(__("empty"));
                    return;
                }
                $rows = $this->getServerRows($servers, $installed, $status);
                $this->table(['identity', 'name', 'version', 'summary', 'installed', 'enabled', 'state'], $rows);
                break;
            case 'display-cloud':
                // 显示云端未安装服务（可以通过 --name 选项进行关键词查询）
                $servers = MSService::cloudServers($page, $name);
                if (empty($servers)){
                    $this->info(__("empty"));
                    return;
                }
                $rows = $this->getServerRows($servers, 0, 0);
                $this->table(['identity', 'name', 'version', 'summary', 'installed', 'enabled', 'state'], $rows);
                break;
            case 'install':
                // 从本地安装服务
                $res = $MSService->install($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('installSuccessfully'));
                break;
            case 'require':
                // 从云端安装服务
                $res = $MSService->cloudInstall($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('installSuccessfully'));
                break;
            case 'update':
                // 从本地更新服务
                $res = $MSService->upgrade($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('upgradeSuccessfully'));
                break;
            case 'upgrade':
                // 从云端更新服务
                $res = $MSService->cloudUpdate($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('upgradeSuccessfully'));
                break;
            case 'uninstall':
                // 卸载服务
                $res = $MSService->uninstall($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('uninstallComplete'));
                break;
            case 'stop':
                // 禁用服务
                $res = $MSService::disable($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('successful'));
                break;
            case 'enable':
                // 启用服务
                $res = $MSService::restore($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('successful'));
                break;
            case 'repair':
                // 修复服务
                try {
                    $this->call('self:repair', ['--server'=>$id?:'']);
                }catch (\Exception $e){
                    $this->error($e->getMessage());
                    return;
                }
                $this->info(__('successful'));
                break;
            case 'remove':
                // 卸载并删除服务目录
                $res = $MSService->uninstall($id);
                if (is_error($res)){
                    $this->error($res['message']);
                    return;
                }
                $this->info(__('uninstallComplete'));
                if (is_dir(base_path('servers/' . $id))){
                    Storage::deleteDirectory('servers/' . $id);
                }
                $this->info(__('successful'));
                break;
        }
    }

    public function getServerRows($servers, $installed=1, $status=1, $from='local')
    {
        return array_map(function($item) use ($installed, $status, $from){
            $state = 'Not installed';
            if ($installed){
                $state = 'Running';
                if (!$status){
                    $state = 'Stopped';
                }elseif (!$item['enabled']){
                    $state = $item['isdelete'] ? 'Deleted' : 'Disabled';
                }
            }
            return [$item['identity'], $item['name'], $item['version'], $item['summary'], $installed?'true':'false', $item['enabled']?'true':'false', $state];
        }, $servers);
    }

}
