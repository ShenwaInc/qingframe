<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\FileService;
use App\Services\ModuleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class modulemake extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {modulename}';
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
        $identity = trim($arguments['modulename']);
        try {
            if (DB::table('modules')->where('name', $identity)->exists()){
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
        $site = file_get_contents(resource_path('stub/module.site.stub'));
        $site = str_replace(array("Dummy","dummy"), array(ucfirst($identity), $identity), $site);
        $site_php = $package."site.php";
        if (!file_put_contents($site_php, $site)){
            return $this->report("Create package faild: may not have permission.");
        }
        $Manifest = file_get_contents(resource_path('stub/module.manifest.stub'));
        $Manifest = str_replace(array("Dummy","dummy","TIMESTAMP"), array(ucfirst($identity), $identity, date("YmdH01", TIMESTAMP)), $Manifest);
        $manifile = $package."manifest.json";
        if (!file_put_contents($manifile, $Manifest)){
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
        $this->info('Create Module '.ucfirst($identity).' successfully.');
        return true;
    }
}
