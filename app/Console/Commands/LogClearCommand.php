<?php

namespace App\Console\Commands;

use App\Models\SystemLog;
use Illuminate\Console\Command;

class LogClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear {all?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理系统日志（支持清理所有日志或按自动清理规则执行）';

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
        // 判断是否传入 "all" 参数
        if ($this->argument('all') === 'all') {
            SystemLog::where('id', '>', 0)->delete();
        }else{
            SystemLog::autoClear();
        }

        $this->info('Logs cleared!');

        return true;
    }
}
