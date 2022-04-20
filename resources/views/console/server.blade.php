@include('common.header')

<div class="main-content fui-content">
    <h2>站点设置</h2>
    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ url('console/setting') }}">站点信息</a>
            </li>
            <li class="layui-this">
                <a href="{{ url('console/server') }}">服务管理</a>
            </li>
            <li>
                <a href="{{ url('console/setting/plugin') }}">应用管理</a>
            </li>
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">{{ $title }}</span>
            <div class="layui-tab fui-tab">
                <ul class="layui-tab-title title_tab">
                    <li @if($op=='index')  class="layui-this" @endif>
                        <a href="{{ wurl('server') }}">已安装</a>
                    </li>
                    <li @if($op=='stop')  class="layui-this" @endif>
                        <a href="{{ wurl('server', array("op"=>"stop")) }}">已停用</a>
                    </li>
                    <li @if($op=='local')  class="layui-this" @endif>
                        <a href="{{ wurl('server', array("op"=>"local")) }}">未安装</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-card-body">
            <table class="layui-table" lay-skin="nob" lay-even>
                <thead>
                <tr>
                    <th>服务名称</th>
                    <th>版本号</th>
                    <th>简介</th>
                    <th class="text-right">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($servers as $key=>$service)
                <tr>
                    <td>
                        <img class="layui-avatar" src="{{ tomedia($service['cover']) }}" height="36" />
                        @if($op=='index' && !empty($service['entry']))
                        <a href="{{ $service['entry'] }}" class="color-default">{{ $service['name'] }}</a>
                        @else
                        <span class="color-default">{{ $service['name'] }}</span>
                        @endif
                        @if($servers['isdelete'])
                        &nbsp;<span class="layui-badge layui-bg-gray">已删除</span>
                        @endif
                    </td>
                    <td>
                        V{{ $service['version'] }}
                        @if(!empty($service['upgrade']))
                        <span class="layui-badge layui-bg-red">发现新版本</span>
                        @endif
                    </td>
                    <td>{{ $service['summary'] }}</td>
                    <td class="text-right">
                        @if(empty($service['binded']))
                        <div class="layui-btn-group text-center">
                            {!! $service['actions'] !!}
                            @if($op!="local")
                                @if($service['status']==1)
                                    <a class="layui-btn layui-btn-sm layui-btn-warm confirm" data-text="确定要停止使用该服务？" href="{{ wurl('server', array("op"=>"disable", "nid"=>$service['identity'])) }}">停用</a>
                                @else
                                    <a class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="确定要恢复该服务？" href="{{ wurl('server', array("op"=>"restore", "nid"=>$service['identity'])) }}">恢复</a>
                                @endif
                                @if(!empty($service['upgrade']))
                                <a class="layui-btn layui-btn-sm layui-btn-danger confirm" data-text="升级前请做好数据备份" lay-tips="该服务可升级至V{{ $service['upgrade']['version'] }}版本" href="{{ wurl('server', array("op"=>"upgrade", "nid"=>$service['identity'])) }}">升级</a>
                                    @endif
                                <a class="layui-btn layui-btn-sm layui-btn-primary confirm" data-text="卸载后该服务相关的数据可能会被删除且不能恢复，是否确定要卸载？" href="{{ wurl('server', array("op"=>"uninstall", "nid"=>$service['identity'])) }}">卸载</a>
                            @endif
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
                @if(empty($servers))
                <tr><td colspan="4" class="text-muted text-center">暂无数据</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

@include('common.footer')
