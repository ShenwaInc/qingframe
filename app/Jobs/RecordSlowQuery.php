<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Schema;

class RecordSlowQuery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 慢查询的完整 SQL
     * @var string
     */
    protected $fullSql;

    /**
     * 查询对象（包含执行时间、连接名等）
     * @var object
     */
    protected $query;
    protected $executionTime = 0;
    protected $connectionName;
    protected $bindings;
    protected $pageUrl = '';

    /**
     * 创建任务实例
     * @param string $fullSql 格式化后的 SQL
     * @param int $executionTime 执行时间（毫秒）
     * @param string $connectionName 数据库连接名
     * @param array $bindings 绑定参数
     */
    public function __construct(string $fullSql, int $executionTime, string $connectionName, array $bindings, string $page = '')
    {
        $this->fullSql = $fullSql;
        $this->executionTime = $executionTime;
        $this->connectionName = $connectionName;
        $this->bindings = $bindings;
        $this->pageUrl = $page?: request()->url();
    }

    /**
     * 执行任务（写入日志）
     */
    public function handle()
    {
        if(!Schema::hasTable('system_logs')){
            return;
        }
        // 写入数据库日志表（使用之前的 SystemLog 模型）
        SystemLog::database(
            $this->pageUrl?:'database:slow_query',
            $this->fullSql,
            $this->bindings,
            'MySQL慢日志',
            intval($this->executionTime)
        );
    }
}