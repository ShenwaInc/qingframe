<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear {table} {key=id} {timeKey?} {timeType?} {plan?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear database data based on current plan';
    protected $plan = 'time:6months|limit:50000';
    protected $primaryKey = 'id';
    protected $timeKey = 'created_at';
    protected $timeType = 'datetime';

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
     * @return void
     */
    public function handle()
    {
        //
        $table = $this->argument('table');
        if (empty($table)){
            $this->error('Argument "table" need a value.');
            return;
        }
        if (!Schema::hasTable($table)){
            $this->error("Base table or view not found: 1146 Table " . tablename($table) . " doesn't exis");
            return;
        }
        $arguments = $this->arguments();
        $plan = $arguments['plan'] ?: $this->plan;
        $primaryKey = $arguments['key'] ?: $this->primaryKey;
        $timeKey = $arguments['timeKey'] ?: $this->timeKey;
        $timeType = $arguments['timeType'] ?: $this->timeType;
        $total = DB::table($table)->count();
        $this->info("开始清理表 {$table} 数据（共 {$total} 条），清理计划：{$plan}");
        $plans = explode('|', $plan);
        $deleteCount = 0;
        foreach ($plans as $p) {
            list($type, $value) = explode(':', $p);
            switch ($type) {
                case 'time':
                    if($timeType=='datetime'){
                        $deleteCount += DB::table($table)->where($timeKey, '<', date('Y-m-d H:i:s', strtotime("-{$value}")))->delete();
                    }else{
                        $deleteCount += DB::table($table)->where($timeKey, '<', strtotime("-{$value}"))->delete();
                    }
                    break;
                case 'limit':
                    $keepCount = (int)$value;
                    if($keepCount>0 && $total>$keepCount){
                        $keepMinId = DB::table($table)->orderBy($timeKey, 'desc')
                            ->orderBy($primaryKey, 'desc') // 增加主键排序确保结果唯一
                            ->take($keepCount)
                            ->pluck($primaryKey)
                            ->last();
                        $deleteCount += DB::table($table)->where($primaryKey, '<', $keepMinId)->delete();
                    }
                    break;
            }
        }
        $this->info("清理完成，共清理{$deleteCount}条数据");
    }
}
