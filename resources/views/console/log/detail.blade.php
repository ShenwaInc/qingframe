@extends('layouts.console')

@section('titleExtra')
    &nbsp;&nbsp;<a href="{{ wurl('logs') }}" class="layui-btn layui-btn-primary">@lang('back')</a>
@endsection

@section('content')
    <div class="log-detail layui-card">
        <div class="layui-card-body">
            <table class="layui-table">
                <tr>
                    <th>ID</th>
                    <td>{{ $log->id }}</td>
                </tr>
                <tr>
                    <th>@lang('类型')</th>
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
                    <th>@lang('module')</th>
                    <td>{{ $log->module }}</td>
                </tr>
                <tr>
                    <th>@lang('标题')</th>
                    <td>{{ $log->title }}</td>
                </tr>
                <tr>
                    <th>@lang('内容')</th>
                    <td>
                        <div class="layui-card layui-bg-gray margin-sm">
                            <div class="layui-card-body pre-wrap" style="max-width: 785px; overflow: hidden;">
                                {{ $log->content ?: __('暂无数据') }}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>@lang('user')</th>
                    <td>
                        @if($log->user_id)
                            <a href="{{ wurl('logs', ['user_id'=>$log->user_id]) }}" class="text-blue">{{ $log->username }}</a>&nbsp;(UID: {{ $log->user_id }})
                        @else
                            <span class="layui-badge layui-bg-gray">-</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>IP</th>
                    <td>{{ $log->ip }}</td>
                </tr>
                <tr>
                    <th>URL</th>
                    <td>
                        @if($log->url)
                            <span class="layui-badge layui-bg-blue">{{ $log->method ?: 'GET' }}</span> {{ $log->url }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>@lang('state')</th>
                    <td>
                        <span class="layui-badge {{ $log->status ? 'layui-bg-green' : 'layui-bg-red' }}">
                            {{ __($log->status ? '成功' : '失败') }}
                        </span>
                        @if($log->error_code)
                            <span class="layui-badge layui-bg-orange ml-2">
                                @lang('错误码'): {{ $log->error_code }}
                            </span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>@lang('发生时间')</th>
                    <td>
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                        @if($log->cost_ms)
                            <span class="layui-badge layui-bg-orange">{{ __('takesTime', ['time'=>$log->cost_ms/1000]) }}</span>
                        @endif
                    </td>
                </tr>
                @if(!empty($log->extra))
                    <tr>
                        <th>@lang('扩展信息')</th>
                        <td>
                            <div class="layui-card layui-bg-gray margin-sm">
                                <div class="layui-card-body">
                                    <pre class="pre-wrap" style="max-width: 785px; overflow: hidden;">{{ json_encode($log->extra, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
    <style>
        .log-detail .layui-table{
            max-width: 100%;
        }
        .log-detail .layui-table th{
            min-width: 80px;
        }
    </style>
@endsection