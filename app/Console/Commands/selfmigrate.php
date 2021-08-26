<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $this->info('Whotalk framework migrate successfully.');
        return true;
    }
}
