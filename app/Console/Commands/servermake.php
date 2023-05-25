<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\FileService;
use App\Services\MSService;
use Illuminate\Console\Command;

class servermake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {servername}';
    public $Application;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new MicroServer Package';

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

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        //
        $arguments = $this->argument();
        $identity = trim($arguments['servername']);
        try {
            $MMS = new MSService();
            if ($MMS::isexist($identity)){
                $this->error("MicroServer $identity already exists!");
                return false;
            }
            if ($MMS::localExist($identity)){
                $this->error("Package $identity already exists!");
                return false;
            }
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
            return false;
        }
        $package = MICRO_SERVER.$identity."/";
        if (!is_dir($package)){
            if (!FileService::mkdirs($package)){
                $this->error("Create package faild: may not have permission.");
                return false;
            }
        }
        $stub = resource_path('stub/service.stub');
        $reader = fopen($stub, 'r');
        $Service = fread($reader, filesize($stub));
        fclose($reader);
        $Service = str_replace(array("Dummy","dummy"), array(ucfirst($identity), $identity), $Service);
        $php = $package.ucfirst($identity)."Service.php";
        if (!file_put_contents($php, $Service)){
            $this->error("Create package faild: may not have permission.");
            return false;
        }
        $stubManifest = resource_path('stub/service.manifest.stub');
        $reader = fopen($stubManifest, 'r');
        $Manifest = fread($reader, filesize($stubManifest));
        fclose($reader);
        $Manifest = str_replace(array("Dummy","dummy","TIMESTAMP"), array(ucfirst($identity), $identity, date("YmdH", TIMESTAMP)), $Manifest);
        $manifile = $package."manifest.json";
        if (!file_put_contents($manifile, $Manifest)){
            $this->error("Create package faild: may not have permission.");
            return false;
        }
        FileService::mkdirs($package."/web/");
        $stubController = resource_path('stub/service.controller.stub');
        $reader = fopen($stubController, 'r');
        $Controller = fread($reader, filesize($stubController));
        fclose($reader);
        $Controller = str_replace("Dummy", ucfirst($identity), $Controller);
        if (!file_put_contents($package."/web/IndexController.php", $Controller)){
            $this->error("Create package faild: may not have permission.");
            return false;
        }
        $this->info('Create MicroServer '.ucfirst($identity).' successfully.');
        return true;
    }
}
