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
    protected $description = '清理数据库表数据，支持两种计划：
    - time:X（删除X时间前的数据，如time:6months）
    - limit:Y（保留最新Y条数据，如limit:50000）
    参数说明：
        table: 表名（必填）
        key: 主键字段（默认id）
        timeKey: 时间字段（默认created_at）
        timeType: 时间类型（datetime/timestamp，默认datetime）
        plan: 清理计划（默认time:6months|limit:50000）';
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
        if (!Schema::hasColumn($table, $timeKey)) {
            $this->error("表 {$table} 不存在字段 {$timeKey}");
            return;
        }
        $total = DB::table($table)->count();
        $this->info("开始清理表 {$table} 数据（共 {$total} 条），清理计划：{$plan}");
        $plans = explode('|', $plan);
        $deleteCount = 0;
        foreach ($plans as $p) {
            $parts = explode(':', $p);
            if (count($parts) !== 2) {
                $this->error("无效的计划格式: {$p}，跳过该计划");
                continue;
            }
            list($type, $value) = $parts;
            switch ($type) {
                case 'time':
                    $timeThreshold = strtotime("-{$value}");
                    if ($timeThreshold === false) {
                        $this->error("无效的时间值: {$value}，跳过时间策略");
                        break;
                    }
                    while (true){
                        if($timeType=='datetime'){
                            $deleted = DB::table($table)->where($timeKey, '<', date('Y-m-d H:i:s', $timeThreshold))->delete();
                        }else{
                            $deleted = DB::table($table)->where($timeKey, '<', $timeThreshold)->delete();
                        }
                        if($deleted==0){
                            break;
                        }
                        $deleteCount += $deleted;
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
                        while (true){
                            $deleted = DB::table($table)->where($primaryKey, '<', $keepMinId)->limit(1000)->delete();
                            if($deleted==0){
                                break;
                            }
                            $deleteCount += $deleted;
                        }
                    }
                    break;
            }
        }
        $this->info("清理完成，共清理{$deleteCount}条数据");
    }
}
