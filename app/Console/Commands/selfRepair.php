<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\FileService;
use App\Services\MSService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class selfRepair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:repair';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repair System composer vendors';
    protected $application;
    protected $terminalState = 'console';

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
        //1.解析需要安装的库
        global $_W;
        if(!empty($_W['siteroot'])){
            $this->terminalState = 'web';
            $this->terminalShow("建议使用其它终端工具运行修复指令，<a href='https://www.yuque.com/shenwa/qingru/gqea6plg3g4w99c5#xE7AC' target='_blank'>点此查看详细方法</a>", "line:warm");
        }
        //$this->info("The site origin is : ".$_W['siteroot']);
        //$this->info('Time: '.TIMESTAMP);
        $this->terminalShow('Terminal mode : '.$this->terminalState, "success");
        $composerPath = base_path('composer.json');
        $json = file_get_contents($composerPath);
        $flags = JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES;
        $composer = json_decode($json, true);
        $composerUpdate = false;
        foreach ($composer['require'] as $key=>$value){
            if (\Str::startsWith($key, 'microserver')){
                unset($composer['require'][$key]);
                $composerUpdate = true;
            }
        }
        unset($key, $value);
        $WorkingDirectory = base_path("/");
        //1.更新Composer
        if ($composerUpdate){
            if (!file_put_contents($composerPath, json_encode($composer, $flags))){
                return $this->terminalShow("composer.json modify failed.", "err", true);
            }

            $process = new Process(['composer','update', '--no-dev']);
            $process->setWorkingDirectory($WorkingDirectory);
            $process->setEnv(['COMPOSER_HOME'=>MSService::ComposerHome()]);
            $takes = microtime(true) - LARAVEL_START;
            $maxTime = (int) ini_get('max_execution_time');
            $timeout = $maxTime > 0 ? max(10, $maxTime - $takes) : 300;
            $process->setTimeout($timeout);
            $process->run(function ($type, $buffer) {
                $mode = str_replace('err', 'warm', $type);
                $this->terminalShow($buffer, "line:" . $mode);
            });
            $process->wait();
            if ($process->isSuccessful()) {
                $this->terminalShow("composer update complete.", "success");
            }else{
                return $this->terminalShow('composer update failed.', 'err', true);
            }
        }

        //2.重构微服务Composer
        $servers = FileService::file_tree(base_path('servers'), array('*/composer.json'));
        foreach ($servers as $item){
            $json = file_get_contents($item);
            $composer = json_decode($json, true);
            $basePath = base_path('servers/') . str_replace("microserver/", "", $composer['name']) . "/";
            $this->terminalShow("Composer require {$composer['name']}".(empty($composer['version'])?'':' '.$composer['version'])." from ".$basePath."composer.json", 'info');
            $composerError = $basePath . "composer.error";
            if(DEVELOPMENT){
                if(file_exists($basePath."composer.lock")){
                    @Storage::deleteDirectory($basePath."vendor");
                    @unlink($basePath."composer.lock");
                }
                if (!MSService::ComposerRequire($basePath, $composer['name'])){
                    $this->terminalShow($composer['name']." install failed.");
                    $this->terminalError("composer update", $basePath, $composerError);
                }else{
                    $this->terminalShow($composer['name']." install successfully.", "success");
                    if (file_exists($composerError)){
                        @unlink($composerError);
                    }
                }
            }else{
                $command = ['composer', 'require', $composer['name']];
                if (!empty($composer['version'])){
                    $command[] = $composer['version'];
                }
                $process = MSService::ComposerProcess($command, $WorkingDirectory);
                if ($process->isSuccessful()) {
                    $this->terminalShow($composer['name']." install successfully.", "success");
                    if (file_exists($composerError)){
                        @unlink($composerError);
                    }
                }else{
                    $this->terminalShow($composer['name']." install failed.");
                    $this->terminalError(implode(" ", $command), $WorkingDirectory, $composerError);
                }
            }
        }

        $this->terminalShow("Composer repair successfully.", "success", true);
        return true;
    }

    public function terminalError($command, $basePath='', $composerError=''){
        $this->terminalShow("请使用终端工具手动运行如下指令：");
        if (!empty($basePath)){
            $this->terminalShow("cd ".$basePath, 'line:cmd');
        }
        $this->terminalShow($command, 'line:cmd');
        if (!empty($composerError) && file_exists($composerError)){
            $this->terminalShow("rm -f " . $composerError, 'line:cmd');
        }
    }

    public function terminalShow($message, $mode='err', $finish=false){
        if($mode=='success'){
            $takes = microtime(true) - LARAVEL_START;
            $message .= $message."({$takes} seconds)";
        }
        if ($this->terminalState=='console'){
            //在终端工具
            if ($mode=='err'){
                $this->error($message);
            }elseif($mode=='success' || $mode=='info'){
                $this->info($message);
            }elseif($mode=='warm' || $mode=='line:warm') {
                $this->warn($message);
            }else{
                $this->line($message);
            }
        }else{
            //在模拟终端
            MSService::TerminalSend(["mode"=>str_replace("line:", "", $mode), "message"=>$message], $finish);
        }
        return false;
    }

}
