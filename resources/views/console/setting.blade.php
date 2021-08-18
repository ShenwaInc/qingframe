@include('common.header')

<div class="main-content">

    <h2 class="weui-desktop-page__title">站点设置</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li @if($op=='main')  class="layui-this" @endif>
                <a href="{{ url('console/setting') }}">站点信息</a>
            </li>
            <li @if($op=='socket')  class="layui-this" @endif>
                <a href="{{ url('console/setting/socket') }}">SOCKET配置</a>
            </li>
            <li @if($op=='attach')  class="layui-this" @endif>
                <a href="{{ url('console/setting/attach') }}">附件设置</a>
            </li>
            <li @if($op=='component')  class="layui-this" @endif>
                <a href="{{ url('console/setting/component') }}">服务组件</a>
            </li>
        </ul>
    </div>

    @if($op=='main')
    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <a href="{{ url('console/setting/page') }}" class="fr text-blue ajaxshow" title="编辑站点信息"><i class="glyphicon glyphicon-edit"></i></a>
            <span class="title">站点信息</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="100" />
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><span class="fui-table-lable">站点名称</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['title'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">站点LOGO</span></td>
                            <td class="soild-after">
                                <img class="radius" src="{{ asset($_W['setting']['page']['logo']) }}" width="120" />
                            </td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">系统图标</span></td>
                            <td class="soild-after">
                                <img class="radius" src="{{ asset($_W['setting']['page']['icon']) }}" width="36" />
                            </td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">SEO关键字</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['keywords'] ? $_W['setting']['page']['keywords'] : '暂未设置' }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">SEO描述</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['description'] ? $_W['setting']['page']['description'] : '暂无描述' }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">底部版权信息</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['copyright'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">底部导航连接</span></td>
                            <td class="soild-after text-blue">
                                @php
                                echo $_W['setting']['page']['links'];
                                @endphp
                            </td>
                            <td class="text-right soild-after"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <a href="" class="fr text-blue" title="修改系统设置"><i class="glyphicon glyphicon-edit"></i></a>
            <span class="title">系统设置</span>
        </div>
    </div>
    <div class="fui-card layui-card @if(!$_W['isfounder']) layui-hide @endif ">
        <div class="layui-card-header nobd">
            <span class="title">系统参数</span>
        </div>
    </div>
    @else
        <div class="fui-card layui-card">
            @if($op=='attach')
                <div class="layui-card-header nobd">
                    <span class="title">附件设置</span>
                </div>
            @elseif($op=='socket')
                <div class="layui-card-header nobd">
                    <span class="title">SOCKET设置</span>
                </div>
            @elseif($op=='component')
                <div class="layui-card-header nobd">
                    <span class="title">服务组件</span>
                </div>
            @endif
        </div>
    @endif

</div>

<script type="text/javascript">
    layui.use(['element'],function (){
        var element = layui.element;
    });
</script>

@include('common.footer')
