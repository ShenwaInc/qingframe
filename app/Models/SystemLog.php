<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SystemLog extends Model
{
    protected $table = 'system_logs';

    protected $fillable = [
        'type', 'module', 'title', 'content', 'user_id', 'username',
        'ip', 'url', 'method', 'status', 'error_code', 'cost_ms', 'extra'
    ];

    protected $casts = [
        'extra' => 'array',
        'status' => 'boolean',
    ];

    // 关闭updated_at自动更新（日志创建后不修改）
    public $timestamps = true;
    const UPDATED_AT = null;


    /*
    |--------------------------------------------------------------------------
    | 日志创建快捷方法（静态）
    |--------------------------------------------------------------------------
    */

    /**
     * 记录用户操作日志
     * @param string $module 模块（如：user、order）
     * @param string $title 标题
     * @param string|null $content 详情
     * @param bool $status 状态（成功/失败）
     * @param array $extra 扩展信息
     * @param Request|null $request 请求对象（自动获取IP/URL等）
     * @return self
     */
    public static function userOperation(
        string $title,
        string $module = 'core:console',
        ?string $content = null,
        bool $status = true,
        array $extra = [],
        ?Request $request = null
    ): self {
        $request = $request ?? request(); // 默认为当前请求
        $user = auth()->user();

        return self::create([
            'type' => 'user_operation',
            'module' => $module,
            'title' => $title,
            'content' => $content,
            'user_id' => $user->uid ?? 0,
            'username' => $user->username ?? '未登录用户',
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status' => $status,
            'extra' => array_merge($extra, [
                'user_agent' => $request->header('User-Agent'), // 自动补充用户代理
            ]),
        ]);
    }

    /**
     * 记录系统运行日志
     * @param string $module 模块（如：cron、server）
     * @param string $title 标题
     * @param string|null $content 详情
     * @param bool $status 状态
     * @param array $extra 扩展信息（如进程ID、内存占用）
     * @return self
     */
    public static function systemRunning(
        string $title,
        string $module = 'system',
        ?string $content = null,
        bool $status = true,
        array $extra = []
    ): self {
        return self::create([
            'type' => 'system_running',
            'module' => $module,
            'title' => $title,
            'content' => $content,
            'ip' => gethostbyname(gethostname()), // 服务器IP
            'status' => $status,
            'extra' => array_merge($extra, [
                'process_id' => getmypid(), // 进程ID
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s.u'),
            ]),
        ]);
    }

    /**
     * 记录数据库操作日志
     * @param string $module 模块
     * @param string $sql SQL语句
     * @param array $bindings 绑定参数
     * @param int $costMs 耗时（毫秒）
     * @param int $rowsAffected 影响行数
     * @return self
     */
    public static function database(
        string $module,
        string $sql,
        array $bindings = [],
        string $title = '',
        int $costMs = 0,
        bool $status = true,
        string $content = null,
        int $rowsAffected = 0
    ): self {
        $user = auth()->user();

        return self::create([
            'type' => 'database',
            'module' => $module,
            'title' => $title ?: '数据库操作',
            'content' => $content ?: "SQL执行完成（耗时{$costMs}ms）",
            'user_id' => $user->uid ?? 0,
            'username' => $user->username ?? '系统',
            'status' => $status,
            'cost_ms' => $costMs,
            'extra' => [
                'sql' => $sql,
                'bindings' => $bindings,
                'fullSql' => self::formatSql($sql, $bindings),
                'rows_affected' => $rowsAffected,
            ],
        ]);
    }

    /**
     * 记录错误日志
     * @param string $title 错误标题
     * @param string $module 模块
     * @param string|null $content 错误详情
     * @param string $errorCode 错误码
     * @param array $extra 扩展信息（如堆栈、请求参数）
     * @param Request|null $request 请求对象
     * @return self
     */
    public static function error(
        string $title,
        string $module = 'core:console',
        ?string $content = null,
        string $errorCode = '',
        array $extra = [],
        ?Request $request = null
    ): self {
        $request = $request ?? request();
        $user = auth()->user();

        return self::create([
            'type' => 'error',
            'module' => $module,
            'title' => $title,
            'content' => $content,
            'user_id' => $user->uid ?? 0,
            'username' => $user->username ?? '未登录用户',
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status' => false,
            'error_code' => $errorCode,
            'extra' => array_merge($extra, [
                'request_params' => $request->all(), // 自动记录请求参数
            ]),
        ]);
    }

    public static function formatSql(string $sql, array $bindings): string
    {
        $bindings = array_map(function ($binding) {
            if (is_string($binding)) {
                return "'" . addslashes($binding) . "'";
            }
            if (is_bool($binding)) {
                return $binding ? '1' : '0';
            }
            if ($binding === null) {
                return 'NULL';
            }
            return $binding;
        }, $bindings);

        // 替换 ? 为实际参数（vsprintf 在 PHP7.2 中正常支持）
        return vsprintf(str_replace('?', '%s', $sql), $bindings);
    }


    /*
    |--------------------------------------------------------------------------
    | 查询作用域（快速筛选日志）
    |--------------------------------------------------------------------------
    */

    /**
     * 筛选指定类型的日志
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type 日志类型（如：user_operation）
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 筛选指定模块的日志
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $module 模块名
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * 筛选指定用户的日志
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId 用户ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 筛选失败的日志
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', false);
    }

    /**
     * 筛选指定时间范围内的日志
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startTime 开始时间（Y-m-d H:i:s）
     * @param string $endTime 结束时间（Y-m-d H:i:s）
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenTimes($query, string $startTime, string $endTime)
    {
        return $query->whereBetween('created_at', [$startTime, $endTime]);
    }

    /**
     * 筛选今天的日志
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }


    /*
    |--------------------------------------------------------------------------
    | 批量操作方法
    |--------------------------------------------------------------------------
    */

    /**
     * 清理指定天数前的日志
     * @param int $days 保留最近$days天的日志
     * @return int 被删除的记录数
     */
    public static function cleanOldLogs(int $days): int
    {
        $threshold = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');
        return self::where('created_at', '<', $threshold)->delete();
    }

    /**
     * 统计指定类型的日志数量
     * @param array $types 日志类型数组（如：['error', 'user_operation']）
     * @return array 类型=>数量的映射
     */
    public static function countByTypes(array $types): array
    {
        $result = self::whereIn('type', $types)
            ->groupBy('type')
            ->selectRaw('type, count(*) as count')
            ->get()
            ->keyBy('type')
            ->map->count
            ->toArray();

        // 确保返回所有请求的类型（即使数量为0）
        foreach ($types as $type) {
            if (!isset($result[$type])) {
                $result[$type] = 0;
            }
        }
        return $result;
    }
}