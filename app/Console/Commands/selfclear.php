<?php

namespace App\Console\Commands;

use App\Services\FileService;
use Illuminate\Console\Command;

class selfclear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whotalk framework clean';

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
        //清理无用文件
        $unused = array(
            app_path('Console/Commands/repwd.php'),
            database_path('migrations/2021_08_10_113449_create_uni_settings_table.php')
        );
        foreach ($unused as $file){
            if (file_exists($file)){
                @unlink($file);
            }
        }
        //清理无用文件夹
        $undirs = array(
            base_path('socket')
        );
        foreach ($undirs as $dir){
            if (is_dir($dir)){
                FileService::rmdirs($dir);
            }
        }
        $this->info('FrameWork Clean successfully.');
        return true;
    }
}
