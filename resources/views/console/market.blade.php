@include('common.header')
<div class="layui-fluid unpadding">
    <div{{ $_W['isajax'] ? '' : ' class=main-content' }}>

            <div class="{{ $_W['isajax'] ? '' : 'fui-card layui-card' }}">
                @if(!$_W['isajax'])<div class="layui-card-header"><span class="title">{{ $title }}</span></div>@endif
                <div class="layui-card-body">
                    <div class="un-padding">
                        <table class="layui-table fui-table lines" lay-even lay-skin="nob">
                            <thead>
                            <tr>
                                <th>组件</th>
                                <th class="layui-hide-xs">说明</th>
                                <th><div class="text-right">操作</div></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(empty($components))
                                <tr>
                                    <td colspan="3" class="text-center">暂无数据</td>
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
                                        <td class="layui-hide-xs">
                                            <p class="text-ellip">{{ $com['description'] }}</p>
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
                <div class="text-right pager">{!! $pager !!}</div>
            </div>

    </div>
</div>
@include('common.footer')
