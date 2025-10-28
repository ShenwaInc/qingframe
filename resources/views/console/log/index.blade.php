@extends('layouts.console')

@section('content')
    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('setting') }}">@lang('站点信息')</a>
            </li>
            <li>
                <a href="{{ wurl('server') }}">@lang('服务管理')</a>
            </li>
            <li>
                <a href="{{ wurl('module') }}">@lang('应用管理')</a>
            </li>
            <li class="layui-this">
                <a href="{{ wurl('logs') }}">@lang('日志管理')</a>
            </li>
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header">
            <form class="layui-form" lay-filter="searchForm" method="get">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <select name="type" lay-search>
                            <option value="">全部日志类型</option>
                            @foreach($logTypes as $key => $name)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="layui-inline">
                        <select name="status">
                            <option value="">全部状态</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>成功</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>失败</option>
                        </select>
                    </div>

                    <div class="layui-inline">
                        <input type="text" name="keyword" placeholder="关键词搜索(标题/内容/IP)"
                               value="{{ request('keyword') }}" class="layui-input">
                    </div>

                    <div class="layui-inline">
                        <input type="date" name="start_time" value="{{ request('start_time') }}" class="layui-input">
                    </div>

                    <div class="layui-inline">
                        <input type="date" name="end_time" value="{{ request('end_time') }}" class="layui-input">
                    </div>

                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="search">筛选</button>
                        <a href="{{ wurl('logs') }}" class="layui-btn layui-btn-primary">重置</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table class="layui-table" lay-skin="nob" lay-even>
                <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>日志类型</th>
                    <th>模块</th>
                    <th>标题</th>
                    <th>操作人</th>
                    <th>IP地址</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>
                            @php
                                // 根据日志类型定义不同样式
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
                        <td>{{ $log->module }}</td>
                        <td>{{ $log->title }}</td>
                        <td>{{ $log->username ?: '系统' }}</td>
                        <td>{{ $log->ip ?: '-' }}</td>
                        <td>
                            <span class="layui-badge {{ $log->status ? 'layui-bg-green' : 'layui-bg-red' }}">
                                {{ $log->status ? '成功' : '失败' }}
                            </span>
                        </td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <a href="{{ wurl('log/' . $log->id) }}" class="text-blue ajaxshow" title="查看日志详情">
                                查看详情
                            </a>
                        </td>
                    </tr>
                @endforeach

                {{-- 空状态处理 --}}
                @if($logs->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center">暂无日志记录</td>
                    </tr>
                @endif
                </tbody>
            </table>

            {{-- 分页 --}}
            <div class="layui-box layui-laypage layui-laypage-default" style="text-align: center;">
                {{ $logs->appends(request()->all())->render() }}
            </div>
        </div>
    </div>
@endsection