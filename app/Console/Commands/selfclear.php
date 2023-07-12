<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\CloudService;
use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class selfclear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:clear {mode?}';
    protected $application;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whotalk framework clean';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->application = new App();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //清理无用文件
        $unused = array(
            app_path('Console/Commands/repwd.php'),
            database_path('migrations/2021_08_10_113449_create_uni_settings_table.php'),
            database_path('migrations/2021_08_10_113449_create_uni_account_menus_table.php'),
            base_path('bootstrap/helpers.php'),
            base_path('.env.example'),
            base_path("servers/weengine/function/web.func.php"),
            app_path('Services/AttachmentService.php'),
            app_path('Services/SocketService.php'),
            app_path('Services/NoticeService.php'),
            app_path('Services/MenuService.php'),
            app_path('Services/PaymentService.php'),
            app_path('Services/PayService.php'),
            app_path('Utils/WeEngine.php'),
            app_path('Http/Controllers/Api/WechatController.php'),
            resource_path('views/console/socket.blade.php'),
            resource_path('views/install/socket.blade.php'),
            resource_path('views/console/account/com.blade.php'),
            resource_path('views/console/account/setting.blade.php'),
            base_path('.docker.env'),
            base_path('manifest.yaml')
        );
        foreach ($unused as $file){
            if (file_exists($file)){
                @unlink($file);
            }
        }
        //清理无用文件夹
        $undirs = array(
            base_path('socket/'),
            base_path('bootstrap/functions/'),
            base_path('bootstrap/wemod/'),
            resource_path('views/console/extra/'),
            resource_path('views/console/set/'),
            storage_path('framework/testing/')
        );
        foreach ($undirs as $dir){
            if (is_dir($dir)){
                FileService::rmdirs($dir);
            }
        }
        $arguments = $this->argument();
        if ($arguments['mode']=='release'){
            $gitIgnores = FileService::file_tree(base_path("/"), array('*/.gitignore','*/*/.gitignore','.gitignore','*/*/*/.gitignore','*/*/*/*/.gitignore'));
            if (!empty($gitIgnores)){
                foreach ($gitIgnores as $file){
                    if (!file_exists($file)) continue;
                    @unlink($file);
                }
            }
            $this->info("Clean ".count($gitIgnores)." files.");
        }
        $this->info('FrameWork Clean successfully.');
        return true;
    }
}
