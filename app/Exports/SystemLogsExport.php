<?php

namespace App\Exports;

use App\Models\SystemLog;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class SystemLogsExport implements FromQuery, WithHeadings, WithMapping
{

    protected $query;

    public function setQuery(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * 查询要导出的数据
     * 此处返回 system_logs 表的查询构建器
     */
    public function query()
    {
        // 可根据需求添加筛选条件（如时间范围）
        return $this->query ?? SystemLog::query();
    }

    /**
     * 定义Excel表头
     */
    public function headings(): array
    {
        return [
            'ID',
            '类型',
            '模块',
            '标题',
            '内容',
            '用户ID',
            '用户名',
            '状态',
            '耗时(ms)',
            '额外信息',
            '创建时间',
            '更新时间',
        ];
    }

    /**
     * 映射数据行（将数据库字段对应到表头）
     * @param mixed $log 单条日志记录
     */
    public function map($log): array
    {
        return [
            $log->id,
            $log->type,
            $log->module,
            $log->title,
            $log->content,
            $log->user_id ?? '无', // 处理空值
            $log->username ?? '系统',
            $log->status ? '成功' : '失败', // 转换布尔值为文字
            $log->cost_ms ?? '0',
            // 额外信息（如JSON格式，转为字符串）
            is_array($log->extra) ? json_encode($log->extra, JSON_UNESCAPED_UNICODE) : $log->extra,
            Carbon::parse($log->created_at)->format('Y-m-d H:i:s'), // 格式化时间
            Carbon::parse($log->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}