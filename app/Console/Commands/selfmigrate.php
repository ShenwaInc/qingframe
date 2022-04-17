<?php

namespace App\Console\Commands;

use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
            if (!Schema::hasTable("microserver")){
                Schema::create('microserver', function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('identity', 20);
                    $table->string('name', 20);
                    $table->string('cover', 255)->default("");
                    $table->text("summary")->nullable();
                    $table->string("version",10)->default("");
                    $table->string("releases",20)->default("");
                    $table->string("drive", 10)->default("php");
                    $table->string("entrance", 255)->default("");
                    $table->mediumtext("datas")->nullable();
                    $table->mediumtext("configs")->nullable();
                    $table->boolean('status')->default(1);
                    $table->integer("addtime")->default(0)->unsigned();
                    $table->integer("dateline")->default(0)->unsigned();
                });
            }
            if(is_dir(base_path('socket'))){
                FileService::rmdirs(base_path('socket'));
                DB::table('gxswa_cloud')->where(array('identity'=>'laravel_whotalk_socket'))->update(array('rootpath'=>'swasocket/'));
            }
            $this->info('Whotalk framework migrate successfully.');
        } catch (\Exception $exception){
            $this->error("Migrate fail:".$exception->getMessage());;
        }

        //系统无用文件清理
        Artisan::call('self:clear');
        return true;
    }
}
