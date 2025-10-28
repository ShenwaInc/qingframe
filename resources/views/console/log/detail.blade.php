@extends('layouts.console')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            {{-- 基础信息 --}}
            <table class="layui-table">
                <tr>
                    <th>ID</th>
                    <td>{{ $log->id }}</td>
                </tr>
                <tr>
                    <th>日志类型</th>
                    <td>
                        @php
                            $typeClass = 'layui-bg-gray';
                            switch($log->type) {
                                case 'user_operation': $typeClass = 'layui-bg-blue'; break;
                                case 'system_running': $typeClass = 'layui-bg-cyan'; break;
                                case 'database': $typeClass = 'layui-bg-green'; break;
                                case 'error': $typeClass = 'layui-bg-red'; break;
                            }
                        @endphp
                        <span class="layui-badge {{ $typeClass }}">
                            {{ $logTypes[$log->type] ?? $log->type }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>相关模块</th>
                    <td>{{ $log->module }}</td>
                </tr>
                <tr>
                    <th>日志标题</th>
                    <td>{{ $log->title }}</td>
                </tr>
                <tr>
                    <th>日志内容</th>
                    <td>
                        <div class="layui-card layui-bg-gray" style="margin-top: 5px; width: calc(100% - 125px);">
                            <div class="layui-card-body pre-wrap">
                                {{ $log->content ?: '无内容' }}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>操作人</th>
                    <td>
                        @if($log->user_id)
                            <a href="{{ wurl('logs', ['user_id'=>$log->user_id]) }}" class="text-blue" title="查看{{ $log->username }}相关的日志">{{ $log->username }}</a>&nbsp;(UID: {{ $log->user_id }})
                        @else
                            <span class="layui-badge layui-bg-gray">系统</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>IP地址</th>
                    <td>{{ $log->ip }}</td>
                </tr>
                <tr>
                    <th>请求信息</th>
                    <td>
                        @if($log->url)
                            <span class="layui-badge layui-bg-blue">{{ $log->method ?: 'GET' }}</span> {{ $log->url }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td>
                        <span class="layui-badge {{ $log->status ? 'layui-bg-green' : 'layui-bg-red' }}">
                            {{ $log->status ? '成功' : '失败' }}
                        </span>
                        @if($log->error_code)
                            <span class="layui-badge layui-bg-orange ml-2">
                                错误码: {{ $log->error_code }}
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>耗时</th>
                    <td>
                        {{ $log->cost_ms ? $log->cost_ms . ' 毫秒' : '-' }}
                    </td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td>
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                    </td>
                </tr>
                @if(!empty($log->extra))
                <tr>
                    <th>扩展信息</th>
                    <td>
                        <div class="layui-card layui-bg-gray" style="margin-top: 5px; width: calc(100% - 125px);">
                            <div class="layui-card-body">
                                <pre class="pre-wrap">{{ json_encode($log->extra, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    <style>
        .layui-table{
            max-width: 100%;
        }
        .layui-table th{
            min-width: 80px;
        }
    </style>
@endsection