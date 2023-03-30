@include('common.header')
<div class="layui-fluid">
    <div class="main-content">

        <h2>系统管理</h2>

        <div class="layui-tab fui-tab margin-bottom-xl">
            <ul class="layui-tab-title title_tab">
                <li>
                    <a href="{{ url('console/setting') }}">站点信息</a>
                </li>
                <li>
                    <a href="{{ url('console/server') }}">服务管理</a>
                </li>
                <li class="layui-this">
                    <a href="{{ url('console/module') }}">应用管理</a>
                </li>
            </ul>
        </div>

        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="{{ wurl('setting/market') }}" data-width="1340" class="fr layui-btn layui-btn-sm layui-btn-normal ajaxshow">应用市场</a>
                <span class="title">应用模块</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-even lay-skin="nob">
                        <thead>
                        <tr>
                            <th>应用</th>
                            <th class="layui-hide-xs">安装时间</th>
                            <th class="layui-hide-xs">上次更新</th>
                            <th class="layui-hide-xs">云端版本</th>
                            <th><div class="text-right">操作</div></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(empty($components))
                            <tr>
                                <td colspan="5" class="text-center">暂无数据</td>
                            </tr>
                        @else
                            @foreach ($components as $com)
                                <tr>
                                    <td>
                                        <img src="{{ $com['logo'] }}" class="fl bg-gray radius margin-right-sm" height="48" />
                                        <div class="fui-table-name">
                                            <a href="{{$com['website']}}" class="text-blue" target="_blank">{{$com['name']}}</a><br/>
                                            V{{$com['version']}}
                                        </div>
                                    </td>
                                    <td class="layui-hide-xs">{!! $com['installtime'] !!}</td>
                                    <td class="layui-hide-xs">{!! $com['lastupdate'] !!}</td>
                                    <td class="layui-hide-xs">
                                        @if(empty($com['cloudinfo']))
                                            本地应用
                                        @else
                                            V{{ $com['cloudinfo']['version'] }}&nbsp;&nbsp;Release{{ $com['cloudinfo']['releasedate'] }}
                                            @if($com['cloudinfo']['isnew'])
                                                <span class="layui-badge-dot" lay-tips="{{ $com['cloudinfo']['releasedate']==$com['releasedate'] ? '本地源码有改动' : '云端程序有更新' }}"></span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="layui-btn-group">{!! $com['action'] !!}</div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@include('console.terminal')
@include('common.footer')
