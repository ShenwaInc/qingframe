<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Models\SystemLog;
use App\Services\FileService;
use App\Services\ModuleService;
use Illuminate\Console\Command;

class MakeModuleCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {identity} {name?} {type=1} {--description=description} {--logo=logo} {--ssoUrl=default}';
    public $Application;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->Application = new App();
    }

    public function report($msg){
        $this->error($msg);
        return false;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        //
        $arguments = $this->argument();
        $options = $this->options();
        $arguments['type'] = intval($arguments['type']);
        if (empty($arguments['type'])) $arguments['type'] = 1;
        $identity = trim($arguments['identity']);
        $moduleName = $arguments['name'] ? trim($arguments['name']) : ucfirst($identity);
        $this->info("Identity:$identity, moduleName:$moduleName");
        try {
            if (pdo_fetch("select mid from " . tablename('modules'). " where `name`=:name", array(':name'=>$identity))){
                return $this->report("Module $identity already installed!");
            }
            if (ModuleService::localExists($identity)){
                return $this->report("Package $identity already exists!");
            }
        }catch (\Exception $exception){
            SystemLog::systemRunning(
                "模块创建异常：{$identity}",
                'console:modulemake',
                "模块创建过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_message' => $exception->getMessage(),
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace(),
                    'arguments' => $arguments,
                ]
            );
            return $this->report($exception->getMessage());
        }
        $package = public_path("addons/$identity/");
        if (!is_dir($package)){
            if (!FileService::mkdirs($package)){
                return $this->report("Create package faild: may not have permission.");
            }
        }
        $siteStub = $arguments['type']==2 ? 'stub/module.siteQuick.stub' : 'stub/module.site.stub';
        $site = file_get_contents(resource_path($siteStub));
        $site = str_replace(array("Dummy","dummy"), array(ucfirst($identity), $identity), $site);
        if ($arguments['type']==2 && !empty($options['ssoUrl']) && $options['ssoUrl']!='default'){
            $site = str_replace("WebIndex = ''", "WebIndex = '{$options['ssoUrl']}'", $site);
        }
        $site_php = $package."site.php";
        if (!file_put_contents($site_php, $site)){
            return $this->report("Create package faild: may not have permission.");
        }
        $Manifest = file_get_contents(resource_path('stub/module.manifest.stub'));
        $Manifest = str_replace(array("Dummy","dummy","TIMESTAMP"), array($moduleName, $identity, date("Ymd01", TIMESTAMP)), $Manifest);
        if ($arguments['type']!=1){
            $Manifest = str_replace('"module_type": "1"', '"module_type": "'.$arguments['type'].'"', $Manifest);
        }
        if (!empty($options['description']) && $options['description']!='description'){
            $Manifest = str_replace("Your Module", $options['description'], $Manifest);
        }
        if (!empty($options['logo']) && $options['logo']!='logo'){
            $Manifest = str_replace("/static/images/microserver.png", $options['logo'], $Manifest);
        }
        $maniFile = $package."manifest.json";
        if (!file_put_contents($maniFile, $Manifest)){
            return $this->report("Create package failed: may not have permission.");
        }
        FileService::mkdirs($package."/views/");
        FileService::mkdirs($package."/static/");
        $Installer = file_get_contents(resource_path('stub/module.install.stub'));
        if (!file_put_contents($package."/install.php", str_replace('dummy', $identity, $Installer))){
            return $this->report("Create package failed: may not have permission.");
        }
        $IndexController = file_get_contents(resource_path('stub/module.IndexController.stub'));
        if ($IndexController && $arguments['type']==1){
            FileService::mkdirs($package."/app/Controllers/web/");
            @file_put_contents($package."/app/Controllers/web/IndexController.php", str_replace('dummy', $identity, $IndexController));
        }
        $this->info('Create Module '.$moduleName.' successfully.');
        return true;
    }
}
