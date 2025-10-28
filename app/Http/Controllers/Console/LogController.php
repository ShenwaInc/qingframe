<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class LogController extends Controller
{

    public function index(Request $request)
    {
        // 构建查询
        $query = SystemLog::query();

        // 筛选条件
        if ($type = $request->input('type')) {
            $query->ofType($type);
        }
        if ($module = $request->input('module')) {
            $query->ofModule($module);
        }
        if ($status = $request->input('status') !== null) {
            $query->where('status', $request->input('status') === '1');
        }
        if ($userId = $request->input('user_id')) {
            $query->ofUser($userId);
        }
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('ip', 'like', "%{$keyword}%");
            });
        }
        if ($startTime = $request->input('start_time')) {
            $query->where('created_at', '>=', $startTime);
        }
        if ($endTime = $request->input('end_time')) {
            $query->where('created_at', '<=', $endTime . ' 23:59:59');
        }

        // 排序和分页
        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(20) // 每页20条
            ->appends($request->all()); // 保留筛选参数到分页链接

        return $this->globalView('console.log.index', [
            'logs'=>$logs,
            'logTypes'=>$this->getTypes(),
            'title'=>__('日志管理')
        ]);
    }

    public function show(Request $request, $id)
    {
        $log = SystemLog::find($id);
        if (!$log) {
            return $this->message('找不到该日志，可能已被删除');
        }
        return $this->globalView('console.log.detail', [
            'log'=>$log,
            'logTypes'=>$this->getTypes(),
            'title'=>__('查看日志详情')
        ]);
    }

    public function getTypes()
    {
        return  [
            'user_operation' => __('用户操作'),
            'system_running' => __('系统运行'),
            'database' => __('数据库操作'),
            'error' => __('错误日志'),
            'other' => __('其他')
        ];
    }

}