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
                            <option value="">@lang('全部类型')</option>
                            @foreach($logTypes as $key => $name)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="layui-inline">
                        <select name="status">
                            <option value="">@lang('全部状态')</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('成功')</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>@lang('失败')</option>
                        </select>
                    </div>

                    <div class="layui-inline" style="min-width: 320px;">
                        <input type="text" name="keyword" placeholder="@lang('关键词搜索(标题/内容/IP/模块/用户)')" value="{{ request('keyword') }}" class="layui-input">
                    </div>

                    <div class="layui-inline">
                        <input type="date" name="start_time" value="{{ request('start_time') }}" class="layui-input">
                    </div>

                    <div class="layui-inline">
                        <input type="date" name="end_time" value="{{ request('end_time') }}" class="layui-input">
                    </div>

                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="search">@lang('搜索')</button>
                        <a href="{{ wurl('logs') }}" class="layui-btn layui-btn-primary">@lang('reset')</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table class="layui-table" lay-skin="nob" lay-even>
                <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>@lang('类型')</th>
                    <th>@lang('module')</th>
                    <th>@lang('标题')</th>
                    <th>@lang('用户')</th>
                    <th>IP</th>
                    <th>@lang('state')</th>
                    <th>@lang('发生时间')</th>
                    <th>@lang('action')</th>
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
                        <td>{{ $log->username ?: '-' }}</td>
                        <td>{{ $log->ip ?: '-' }}</td>
                        <td>
                            <span class="layui-badge {{ $log->status ? 'layui-bg-green' : 'layui-bg-red' }}">
                                {{ __($log->status ? '成功' : '失败') }}
                            </span>
                        </td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <a href="{{ wurl('log/' . $log->id) }}" class="text-blue ajaxshow">
                                @lang('查看详情')
                            </a>
                        </td>
                    </tr>
                @endforeach

                {{-- 空状态处理 --}}
                @if($logs->isEmpty())
                    <tr>
                        <td colspan="9" class="text-center">@lang('暂无数据')</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="9">{{ __('共找到:total条日志', ['total'=>$logs->total()]) }}</td>
                    </tr>
                @endif
                </tbody>
            </table>

            {{-- 分页 --}}
            <div class="text-center">
                {{ $logs->appends(request()->all())->render() }}
            </div>
        </div>
    </div>
@endsection