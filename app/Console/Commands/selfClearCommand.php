<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\CloudService;
use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class selfClearCommand extends Command
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
    protected $description = 'Framework unused files clean';

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
        $cleanFiles = $cleanFolders = $dropTables = 0;
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
            app_path('Models/CorePaylog.php'),
            resource_path('views/console/socket.blade.php'),
            resource_path('views/install/socket.blade.php'),
            resource_path('views/console/account/com.blade.php'),
            resource_path('views/console/account/setting.blade.php'),
            base_path('.docker.env'),
            base_path('manifest.yaml'),
            resource_path('template/web/composer.html'),
            app_path('Console/Commands/ModuleCustomRouteCommand.php'),
            app_path('Console/Commands/selfclear.php'),
            app_path('Console/Commands/modulemake.php'),
            public_path('web/resource/images/favicon.ico'),
            base_path('database/migrations/2021_08_10_113449_create_activity_clerks_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_core_sessions_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_uni_verifycode_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_system_welcome_binddomain_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_stat_visit_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_stat_visit_ip_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_stat_fans_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_core_cache_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_mc_credits_record_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_mc_member_fields_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_mc_groups_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_mc_members_table.php'),
            base_path('database/migrations/2021_08_10_113449_create_core_paylog_table.php'),
            base_path('database/migrations/2024_12_19_000000_add_balance_after_to_mc_credits_record_table.php'),
            app_path('Console/Commands/serverup.php'),
            app_path('Console/Commands/servermake.php'),
            app_path('Console/Commands/ServerInstallCommand.php'),
            app_path('Console/Commands/serverRun.php'),
            app_path('Console/Commands/ServerUpdateCommand.php'),
            app_path('Console/Commands/selfup.php')
        );
        foreach ($unused as $file){
            if (file_exists($file)){
                $cleanFiles += @unlink($file) ? 1 : 0;
            }
        }
        //清理无用文件夹
        $unusedDirs = array(
            base_path('socket/'),
            base_path('bootstrap/functions/'),
            base_path('bootstrap/wemod/'),
            resource_path('views/console/extra/'),
            resource_path('views/console/set/'),
            public_path('web/resource/home/')
        );
        foreach ($unusedDirs as $dir){
            if (is_dir($dir)){
                $cleanFolders += FileService::rmdirs($dir) ? 1 : 0;
            }
        }
        $this->info("Clean $cleanFolders folders.");
        //清理无用数据表
        $installed = file_exists(base_path('storage/installed.bin'));
        if ($installed){
            $unusedTables = array(
                'core_cache',
                'core_sessions',
                'stat_fans',
                'stat_visit',
                'stat_visit_ip',
                'uni_verifycode',
                'activity_clerks',
                'system_welcome_binddomain',
            );
            foreach ($unusedTables as $table){
                try {
                    if (Schema::hasTable($table)){
                        @Schema::dropIfExists($table);
                        $dropTables += 1;
                    }
                }catch (\Exception $e){
                    $this->error("Drop table[{$table}] failed: " . $e->getMessage());
                }
            }
        }
        $this->info("Drop $dropTables tables.");
        $arguments = $this->argument();
        if ($arguments['mode']=='release' || $arguments['mode']=='res'){
            $gitIgnores = FileService::file_tree(base_path("/"), array('*/.gitignore','*/*/.gitignore','.gitignore','*/*/*/.gitignore','*/*/*/*/.gitignore', '*/README.md', '*/*/README.md', 'README.md', 'README_*.md'));
            if (!empty($gitIgnores)){
                foreach ($gitIgnores as $file){
                    if (!file_exists($file)) continue;
                    $cleanFiles += @unlink($file) ? 1 : 0;
                }
            }
            if (!FileService::rmdirs(storage_path('framework/testing/'))){
                $this->error("Remove dir failed: ".storage_path('framework/testing/'));
            }
            if (!FileService::rmdirs(storage_path('framework/cache/'), true)){
                $this->error("Remove dir failed: ".storage_path('framework/cache/'));
            }
            if (!FileService::rmdirs(base_path('docs/'))){
                $this->error("Remove dir failed: ".base_path('docs/'));
            }
        }
        $this->info("Clean $cleanFiles files.");
        $this->info('FrameWork Clean successfully.');
        return true;
    }
}
