<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class userrestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:restore {uid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Framework User restore';

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
        $uid = $this->argument('uid');
        if (empty($uid)) return $this->error("Pleace enter your User_id") || false;
        DB::table('users')->where('uid',intval($uid))->update(array('status'=>2,'starttime'=>time()));
        $this->info('User restore successfully.');
    }
}
