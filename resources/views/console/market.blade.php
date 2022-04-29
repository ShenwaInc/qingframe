@include('common.header')
<div class="layui-fluid unpadding">
    <div{{ $_W['isajax'] ? '' : ' class="main-content"' }}>

        @if(!$_W['isajax'])<h2>{{ $title }}</h2>@endif

            <div{{ $_W['isajax'] ? '' : ' class="fui-card layui-card"' }}>

                <div class="layui-card-body">
                    <div class="un-padding">
                        <table class="layui-table fui-table lines" lay-even lay-skin="nob">
                            <thead>
                            <tr>
                                <th>组件</th>
                                <th class="layui-hide-xs">安装时间</th>
                                <th class="layui-hide-xs">上次更新</th>
                                <th class="layui-hide-xs">线上版本</th>
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
                                        <td class="layui-hide-xs">{{ $com['installtime'] }}</td>
                                        <td class="layui-hide-xs">{!! $com['lastupdate'] !!}</td>
                                        <td class="layui-hide-xs">
                                            @if(empty($com['cloudinfo']))
                                                -
                                            @else
                                                V{{ $com['cloudinfo']['version'] }}&nbsp;&nbsp;Release{{ $com['cloudinfo']['releasedate'] }}
                                                @if($com['cloudinfo']['isnew'])
                                                    <span class="layui-badge layui-bg-red">{{ $com['cloudinfo']['releasedate']==$com['releasedate'] ? '有改动' : '有更新' }}</span>
                                                @else
                                                    <span class="layui-badge layui-bg-green">最新</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            {!! $com['action'] !!}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-right pager">{!! $pager !!}</div>
            </div>

    </div>
</div>
@include('common.footer')
