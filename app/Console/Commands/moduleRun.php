<?php

namespace App\Console\Commands;

use App\Models\SystemLog;
use Illuminate\Console\Command;

class moduleRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:run {id} {params*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the built-in method of the module application through the command line';

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
        $moduleId = $this->argument('id');
        $className = "Addons\\". $moduleId. "\Terminal";
        if (class_exists($className)){
            try {
                $instance = new $className;
                $params = $this->argument('params');
                $instance->run($this, ...$params);
            }catch (\Exception $exception){
                SystemLog::systemRunning(
                    "模块运行异常：{$moduleId}",
                    'console:moduleRun',
                    "模块执行过程中发生异常：{$exception->getMessage()}",
                    false,
                    [
                        'exception_message' => $exception->getMessage(),
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                        'exception_code' => $exception->getCode(),
                        'exception_trace' => $exception->getTrace(),
                        'module_id' => $moduleId,
                        'params' => $params ?? [],
                    ]
                );
                $this->error($exception->getMessage());
            }
        }else{
            $this->line("Module $moduleId has no runnable script.");
        }
        return true;
    }
}
