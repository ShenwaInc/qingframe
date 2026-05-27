<?php

namespace App\Console\Commands;

use App\Services\MSService;
use Illuminate\Console\Command;

class MicroServerManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microserver:manage {action} {--id=} {--installed=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微服务管理工具，action 可选值：install（从本地安装）|require（从云端安装）|update（从本地更新）|upgrade（从云端更新）|uninstall（卸载服务）|remove（删除服务目录）|stop（停止服务）|restore（恢复服务）|repair（修复服务）|composer-install（安装 composer 依赖）|composer-update（更新 composer 依赖）|display（显示本地服务列表）|display-cloud（显示云端服务列表）|state（显示服务状态）';

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
        $id = $this->option('id');
        $installed = $this->option('installed');
        $MSService = new MSService();
        switch ($action) {
            case 'install':
                break;
            case 'require':
                break;
            case 'update':
                break;
            case 'upgrade':
                break;
            case 'uninstall':
                break;
        }
    }
}
