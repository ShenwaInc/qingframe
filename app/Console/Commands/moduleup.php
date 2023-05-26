<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\ModuleService;
use Illuminate\Console\Command;

class moduleup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:upgrade {module}';

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
        $modulename = $this->argument('module');
        $complete = ModuleService::upgrade($modulename);
        if (is_error($complete)){
            return $this->error($complete['message']);
        }
        $this->info("Module {$modulename} upgrade successfully.");
    }
}
