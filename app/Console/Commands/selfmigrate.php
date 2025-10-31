<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Models\SystemLog;
use App\Services\CacheService;
use App\Services\FileService;
use App\Services\MSService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class selfmigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whotalk framework migrate';
    protected $application;

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
        //操作数据库迁移
        try {
            if (!Schema::hasColumn('uni_settings','notice')){
                Schema::table('uni_settings',function (Blueprint $table){
                    $table->addColumn('text','notice',array('comment'=>'消息通知'));
                });
            }
            if (!Schema::hasColumn('uni_account_users', 'entrance')){
                DB::statement("ALTER TABLE ".tablename('uni_account_users')." ADD `entrance` VARCHAR(100) NOT NULL DEFAULT '' AFTER `rank`;");
            }
            self::call('migrate');
            $MSS = new MSService();
            $MSS->setup();
            $MSS->autoInstall();
            if(is_dir(base_path('socket'))){
                FileService::rmdirs(base_path('socket'));
                DB::table('gxswa_cloud')->where(array('identity'=>'laravel_whotalk_socket'))->update(array('rootpath'=>'swasocket/'));
            }
            //清除无用模块数据
            $query = DB::table('modules')->where('type', 'system')->orWhere('application_type', '=', '0');
            if ($query->exists()){
                $query->delete();
            }
            //动态更新composer.json
            $composer = file_get_contents(base_path('composer.json'));
            $composerJson = json_decode($composer, true);
            $psr4Servers = "Server\\";
            if (!strexists($composer, 'public/addons') || empty($composerJson["autoload"]["psr-4"][$psr4Servers])){
                if (strexists($composer, 'public/addons')){
                    $AddonsKey = "Addons\\";
                    $composerJson["autoload"]["psr-4"][$AddonsKey] = 'public/addons/';
                }
                if (empty($composerJson["autoload"]["psr-4"][$psr4Servers])){
                    $composerJson["autoload"]["psr-4"][$psr4Servers] = 'servers/';
                }
                if (file_put_contents(base_path('composer.json'), json_encode($composerJson, JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES))){
                    $WorkingDirectory = base_path("/");
                    $takes = microtime(true) - LARAVEL_START;
                    $maxTime = (int) ini_get('max_execution_time');
                    $timeout = $maxTime > 0 ? max(10, $maxTime - $takes) : 300;
                    $process = new Process(['composer','update']);
                    $process->setWorkingDirectory($WorkingDirectory);
                    $process->setEnv(['COMPOSER_HOME'=>MSService::ComposerHome()]);
                    $process->setTimeout($timeout);
                    $process->run(function ($type, $buffer) {
                        $this->line($buffer);
                    });
                    $process->wait();
                    if ($process->isSuccessful()) {
                        $this->info('composer.json migrate successfully.');
                    }else{
                        $this->error('composer.json migrate fail, try to run the following command to complete the migration：');
                        $this->line("cd $WorkingDirectory");
                        $this->line("composer update");
                    }
                }else{
                    $this->error('composer.json migrate fail.');
                }
            }
            //动态更新语言包
            $languages = [];
            if (!empty($languages)){
                $languageService = serv('language');
                if ($languageService->enabled){
                    foreach ($languages as $key=>$value){
                        $languageService->langAppend($key, $value);
                    }
                }
            }
            //更新系统缓存
            CacheService::flush();
            $this->info('Qingwork framework migrate successfully.');
        } catch (\Exception $exception){
            $this->error("Migrate fail:".$exception->getMessage());
            if (!Schema::hasTable('system_logs')) return false;
            SystemLog::systemRunning(
                "系统数据库迁移异常",
                'console:selfmigrate',
                "数据库迁移过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_message' => $exception->getMessage(),
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace(),
                ]
            );
        }

        return true;
    }
}
