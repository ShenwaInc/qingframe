<?php

namespace App\Console\Commands;

use App\Models\SystemLog;
use Illuminate\Console\Command;

class serverRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:run {id} {params*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the built-in method of the microservice through the command line';

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
        $service = serv($this->argument('id'));
        if (!$service->enabled){
            $this->error('The service is unavailable.');
            return false;
        }
        try {
            $params = $this->argument('params');
            $service->Terminal($this, ...$params);
        }catch (\Exception $exception){
            SystemLog::systemRunning(
                "微服务运行异常：{$this->argument('id')}",
                'console:serverRun',
                "微服务执行过程中发生异常：{$exception->getMessage()}",
                false,
                [
                    'exception_message' => $exception->getMessage(),
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                    'exception_code' => $exception->getCode(),
                    'exception_trace' => $exception->getTrace(),
                    'server_id' => $this->argument('id'),
                    'params' => $params ?? [],
                ]
            );
            $this->error($exception->getMessage());
        }
        return true;
    }
}
