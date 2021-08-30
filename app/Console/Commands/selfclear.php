<?php

namespace App\Console\Commands;

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
        //清理无用文件
        $unused = array(
            app_path('Console/Commands/repwd.php')
        );
        foreach ($unused as $file){
            if (file_exists($file)){
                @unlink($file);
            }
        }
        $this->info('FrameWork Clean successfully.');
        return true;
    }
}
