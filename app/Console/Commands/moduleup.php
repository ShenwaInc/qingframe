<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\CloudService;
use App\Services\ModuleService;
use Illuminate\Console\Command;

class moduleup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:upgrade {module} {from?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade Module';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $buildapp = new App();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $arguments = $this->argument();
        $identity = $arguments['module'];
        if ($arguments['from']=='cloud'){
            $cloudIdentity = ModuleService::SysPrefix($identity);
            $targetPath = public_path("addons/$identity/");
            $res = CloudService::CloudUpdate($cloudIdentity, $targetPath);
            if (is_error($res)){
                $this->error($res['message']);
                return false;
            }
        }
        $complete = ModuleService::upgrade($identity);
        if (is_error($complete)){
            $this->error($complete['message']);
            return false;
        }
        $this->info("Module {$identity} upgrade successfully.");
        return true;
    }
}
