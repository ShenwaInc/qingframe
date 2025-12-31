<?php

namespace App\Console\Commands;

use App\Models\SystemLog;
use App\Services\FileService;
use App\Services\ModuleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ModuleRemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:remove {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a local module';

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
        $name = $this->argument('name');
        $ManiFest = ModuleService::getManifest($name);
        if (is_error($ManiFest)){
            $this->error($ManiFest['message']);
            return;
        }
        $this->info("Removing Module $name");
        //执行卸载脚本
        if (!empty($ManiFest['uninstall'])){
            try {
                define('MODULE_UNINSTALL', 1);
                script_run($ManiFest['uninstall'], public_path("addons/$name/"));
            } catch (\Exception $exception){
                SystemLog::systemRunning(
                    '模块卸载脚本执行异常',
                    'command:ModuleRemove',
                    "执行模块卸载脚本时发生异常：{$exception->getMessage()}",
                    false,
                    [
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                        'exception_code' => $exception->getCode(),
                        'exception_trace' => $exception->getTrace(),
                        'module_identity' => $name,
                    ]
                );
                $this->error("模块卸载脚本执行异常：{$exception->getMessage()}");
                return;
            }
        }
        DB::table('modules')->where('name', $name)->delete();
        $component = ModuleService::SysComponent($name);
        $modulePath = public_path("addons/$name");
        if (!empty($component)){
            DB::table('gxswa_cloud')->where('id',$component['id'])->delete();
            $modulePath = $component['rootpath']?base_path($component['rootpath']):$modulePath;
        }
        //删除安装包
        FileService::rmdirs($modulePath);
        $this->info("Module $name Removed");
        SystemLog::systemRunning(
            '模块移除成功',
            'command:ModuleRemove',
            "模块移除成功 {$name}",
            true,
            [
                'module_identity' => $name,
            ]
        );
        return ;
    }
}
