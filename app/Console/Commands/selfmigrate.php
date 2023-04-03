<?php

namespace App\Console\Commands;

use App\Services\FileService;
use App\Services\MSService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class selfmigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whotalk framework migrate';

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
        //操作数据库迁移
        try {
            if (!Schema::hasColumn('uni_settings','notice')){
                Schema::table('uni_settings',function (Blueprint $table){
                    $table->addColumn('text','notice',array('comment'=>'消息通知'));
                });
            }
            if (!Schema::hasColumn('uni_account_users', 'entrance')){
                DB::statement("ALTER TABLE ".tablename('uni_account_users')." ADD `entrance` VARCHAR(100) NOT NULL DEFAULT '' AFTER `rank`;");
            }
            $MSS = new MSService();
            $MSS->setup();
            $MSS->autoinstall();
            if(is_dir(base_path('socket'))){
                FileService::rmdirs(base_path('socket'));
                DB::table('gxswa_cloud')->where(array('identity'=>'laravel_whotalk_socket'))->update(array('rootpath'=>'swasocket/'));
            }
            $this->info('Whotalk framework migrate successfully.');
        } catch (\Exception $exception){
            $this->error("Migrate fail:".$exception->getMessage());;
        }

        return true;
    }
}
