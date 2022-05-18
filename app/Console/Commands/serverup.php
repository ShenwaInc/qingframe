<?php

namespace App\Console\Commands;

use App\Services\MSService;
use Illuminate\Console\Command;
use App\Http\Middleware\App;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class serverup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:update';
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
        $res = $MSS->autoinstall();
        $this->info("Add {$res['install']} service,update {$res['upgrade']}, faild {$res['faild']}, found {$res['servers']} packages.");
        return true;
    }
}
