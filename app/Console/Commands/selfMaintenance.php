<?php

namespace App\Console\Commands;

use App\Services\ModuleService;
use Illuminate\Console\Command;

class selfMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:maintenance {identity} {state=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set application cloud service switch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $identity = $this->argument('identity');
        $state = $this->argument('state');

        if (ModuleService::maintenance($identity, $state)){
            $this->info('Cloud service configuration successfully.');
        }else{
            $this->error('Application cloud service configuration failed');
        }
    }
}
