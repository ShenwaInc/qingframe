<?php

namespace App\Console\Commands;

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
        $className = "Addons\{$moduleId}\Terminal";
        if (class_exists($className)){
            try {
                $instance = new $className;
                $params = $this->argument('params');
                $instance->run($this, ...$params);
            }catch (\Exception $exception){
                $this->error($exception->getMessage());
            }
        }else{
            $this->info("Module $moduleId has no runnable script.");
        }
        return true;
    }
}
