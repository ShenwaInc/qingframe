<?php

namespace App\Console\Commands;

use App\Services\MSService;
use Illuminate\Console\Command;
use App\Http\Middleware\App;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:service {id?}';
    protected $application = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Servers AutoUpdate';

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
        $MSS = new MSService();
        $identity = $this->argument('id');
        if(!empty($identity)){
            $res = $MSS->upgrade($identity);
            if (is_error($res)){
                $this->error($res['message']);
            }else{
                $this->info("Service {$identity} upgraded successfully.");
            }
            return true;
        }
        $MSS->setup();
        $res = $MSS->autoInstall();
        $this->info("Add {$res['install']} service,update {$res['upgrade']}, faild {$res['faild']}, found {$res['servers']} packages.");
        return true;
    }
}
