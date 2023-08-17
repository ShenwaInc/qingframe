<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\FileService;
use App\Services\ModuleService;
use Illuminate\Console\Command;

class modulemake extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {identity} {name?} {type=1}';
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
        $site_php = $package."site.php";
        if (!file_put_contents($site_php, $site)){
            return $this->report("Create package faild: may not have permission.");
        }
        $Manifest = file_get_contents(resource_path('stub/module.manifest.stub'));
        $Manifest = str_replace(array("Dummy","dummy","TIMESTAMP"), array($moduleName, $identity, date("Ymd01", TIMESTAMP)), $Manifest);
        if ($arguments['type']!=1){
            $Manifest = str_replace('"module_type": "1"', '"module_type": "'.$arguments['type'].'"', $Manifest);
        }
        $maniFile = $package."manifest.json";
        if (!file_put_contents($maniFile, $Manifest)){
            return $this->report("Create package faild: may not have permission.");
        }
        FileService::mkdirs($package."/template/");
        FileService::mkdirs($package."/static/");
        $stubInstaller = resource_path('stub/module.install.stub');
        $reader = fopen($stubInstaller, 'r');
        $Installer = fread($reader, filesize($stubInstaller));
        fclose($reader);
        if (!file_put_contents($package."/install.php", $Installer)){
            return $this->report("Create package faild: may not have permission.");
        }
        $this->info('Create Module '.$moduleName.' successfully.');
        return true;
    }
}
