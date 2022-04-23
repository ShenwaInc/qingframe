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
        $MMS = new MSService();
        $arguments = $this->argument();
        $identity = trim($arguments['servername']);
        if ($MMS::isexist($identity)){
            $this->error("MicroServer $identity already exists!");
            return false;
        }
        if ($MMS::localexist($identity)){
            $this->error("Package $identity already exists!");
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
        $this->info('Create MicroServer '.ucfirst($identity).' successfully.');
        return true;
    }
}
