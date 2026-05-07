<?php

namespace App\Console\Commands;

use App\Services\MSService;
use Illuminate\Console\Command;

class InstallServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install or update a server from local repository';

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
        $ServerName = $this->argument('name');
        if (empty($ServerName)){
            $this->error('Argument name need a  value.');
            return false;
        }

        $MSService = new MSService();

        $isExist = $MSService->isExist($ServerName);

        if ($isExist){
            $res = $MSService->upgrade($ServerName);
        }else{
            if (!$MSService->localExist($ServerName)){
                $this->error('Server '.$ServerName.' not exist in local repository.');
                return false;
            }
            $res = $MSService->install($ServerName);
        }

        if (is_error($res)){
            $this->error($res['message']);
        }else{
            $this->info('Server '.$ServerName.' ' . ($isExist?'upgrade':'install') . ' success.');
        }


        return true;
    }
}
