<?php

namespace App\Http\Controllers\Console;

use App\Exports\SystemLogsExport;
use App\Http\Controllers\Controller;
use App\Models\SystemLogs;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogController extends Controller
{

    public function index(Request $request)
    {
        $query = $this->getQurey($request);

        // 排序和分页
        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->all());

        return $this->globalView('console.log.index', [
            'logs'=>$logs,
            'logTypes'=>$this->getTypes(),
            'title'=>__('日志管理'),
            'total'=>$query->count()
        ]);
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->founder_groupid){
            return $this->message('暂无权限');
        }
        $log = SystemLogs::find($id);
        if (!$log) {
            return $this->message('找不到该日志，可能已被删除');
        }
        return $this->globalView('console.log.detail', [
            'log'=>$log,
            'logTypes'=>$this->getTypes(),
            'title'=>__('查看日志详情')
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        // 定义导出文件名（包含当前日期，避免重复）
        $fileName = '系统日志_' . date('YmdHis') . '.xlsx';

        $export = new SystemLogsExport();
        $export->setQuery($this->getQurey($request));

        // 调用导出类，返回下载响应
        return Excel::download(
            new SystemLogsExport(),
            $fileName,
            \Maatwebsite\Excel\Excel::XLSX // 指定文件格式为xlsx
        );
    }

    public function getQurey(Request $request)
    {
        if (!$request->user()->founder_groupid){
            return $this->message('暂无权限');
        }

        // 构建查询
        $query = SystemLogs::query();

        // 筛选条件
        if ($type = $request->input('type')) {
            $query->ofType($type);
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
                    ->orWhere('module', 'like', "%{$keyword}%")
                    ->orWhere('ip', 'like', "%{$keyword}%");
            });
        }
        if ($startTime = $request->input('start_time')) {
            $query->where('created_at', '>=', $startTime);
        }
        if ($endTime = $request->input('end_time')) {
            $query->where('created_at', '<=', $endTime . ' 23:59:59');
        }
        return $query;
    }

    public function getTypes()
    {
        return  [
            'user_operation' => __('用户操作'),
            'system_running' => __('系统运行'),
            'database' => __('数据库操作'),
            'error' => __('错误日志'),
            'other' => __('其它')
        ];
    }

}