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
        //
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
        $MSS = new MSService();
        $MSS->autoinstall();
        $this->info('Whotalk framework migrate successfully.');
    }
}
