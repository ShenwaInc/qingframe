<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class accountRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:restore {uniacid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the deleted platform.';

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
        $uniacid = (int)$this->argument('uniacid');
        if (empty($uniacid)){
            $this->error('Invalid platform ID.');
            return false;
        }
        $complete = DB::table('account')->where('uniacid', $uniacid)->update(array('isdeleted'=>0));
        if ($complete){
            $this->info('Restored successfully');
        }else{
            $this->error('Operation failed. Please try again.');
        }
        return true;
    }
}
