@include('common.header')
<div class="layui-fluid">
    <div class="main-content">

        <h2>系统管理</h2>

        <div class="layui-tab fui-tab margin-bottom-xl">
            <ul class="layui-tab-title title_tab">
                <li class="layui-this">
                    <a href="{{ url('console/setting') }}">站点信息</a>
                </li>
                <li>
                    <a href="{{ url('console/server') }}">服务管理</a>
                </li>
                <li>
                    <a href="{{ url('console/module') }}">应用管理</a>
                </li>
            </ul>
        </div>

        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="{{ url('console/setting/pageset') }}" class="fr text-blue ajaxshow" title="编辑站点信息"><i
                        class="fa fa-edit"></i></a>
                <span class="title">站点信息</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-skin="nob">
                        <colgroup>
                            <col width="120"/>
                            <col/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td><span class="fui-table-lable">站点名称</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['title'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">站点LOGO</span></td>
                            <td class="soild-after">
                                <img class="radius" src="{{ tomedia($_W['setting']['page']['logo']) }}"
                                     width="120"/>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">系统图标</span></td>
                            <td class="soild-after">
                                <img class="radius" src="{{ tomedia($_W['setting']['page']['icon']) }}" width="36"/>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">SEO关键字</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['keywords'] ? $_W['setting']['page']['keywords'] : '暂未设置' }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">SEO描述</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['description'] ? $_W['setting']['page']['description'] : '暂无描述' }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">底部版权信息</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['copyright'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">底部导航连接</span></td>
                            <td class="soild-after text-blue">
                                @php
                                    echo $_W['setting']['page']['links'];
                                @endphp
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="" class="fr text-blue layui-hide" title="修改系统设置"><i class="fa fa-edit"></i></a>
                <span class="title">系统设置</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-skin="nob">
                        <colgroup>
                            <col width="120"/>
                            <col/>
                            <col width="230"/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td><span class="fui-table-lable">站点状态</span></td>
                            <td class="soild-after">{{ $_W['setting']['close']['status']==1 ? '关闭' : '开启' }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        @if($_W['setting']['close']['status']==1)
                            {
                            <tr>
                                <td><span class="fui-table-lable">关闭原因</span></td>
                                <td class="soild-after">{{ $_W['setting']['close']['reson'] }}</td>
                                <td class="text-right soild-after"></td>
                            </tr>
                        @endif
                        <tr>
                            <td><span class="fui-table-lable">调试模式</span></td>
                            <td class="soild-after">{{ env('APP_DEBUG') ? '开启' : '关闭' }}</td>
                            <td class="text-right soild-after">
                                @if(env('APP_DEBUG'))
                                    <a href="{{ url("console/setting/envdebug") }}?state=off"
                                       class="text-blue confirm" data-text="确定要关闭调试模式吗？">关闭调试</a>
                                @else
                                    <a href="{{ url("console/setting/envdebug") }}?state=on"
                                       class="text-blue confirm" data-text="确定要开启调试模式吗？">开启调试</a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">系统版本</span></td>
                            <td class="soild-after">
                                V{{ QingVersion }} Release{{ QingRelease }}
                                @if($cloudinfo['isnew'])
                                    &nbsp;&nbsp;<span class="layui-badge layui-bg-red"
                                                      title="V{{ $cloudinfo['version'] }} Release{{ $cloudinfo['releasedate'] }}">{{ $cloudinfo['releasedate']==QingRelease ? '文件有改动' : '发现新版本' }}</span>
                                @endif
                            </td>
                            <td class="text-right soild-after">
                                @if($cloudinfo['isnew'])
                                    <a href="{{ wurl('setting/selfupgrade') }}" class="text-red confirm"
                                       data-text="升级前请做好源码和数据备份，避免升级故障导致系统无法正常运行">一键升级</a>&nbsp;&nbsp;
                                    <a href="{{ wurl('setting/updateLog') }}" class="text-blue ajaxshow">更新说明</a>
                                    &nbsp;&nbsp;
                                @endif
                                <a href="{{ wurl('setting/detection') }}" class="text-blue ajaxshow">检测更新</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="fui-card layui-card @if(!$_W['isfounder']) layui-hide @endif ">
            <div class="layui-card-header nobd">
                <span class="title">系统参数</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-skin="nob">
                        <colgroup>
                            <col width="120"/>
                            <col/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td><span class="fui-table-lable">系统名称</span></td>
                            <td class="soild-after">{{ $_W['config']['name'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">容器信息</span></td>
                            <td class="soild-after">{{ php_uname() }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">运行环境</span></td>
                            <td class="soild-after">{{ $_SERVER['SERVER_SOFTWARE'] ?  : php_sapi_name() }}&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">基准时区</span></td>
                            <td class="soild-after">{{ $_W['config']['setting']['timezone'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        @if($_W['isfounder'])
                            <tr>
                                <td><span class="fui-table-lable">云服务</span></td>
                                <td class="soild-after">
                                    @if($activeState['status']==1)
                                        {{$activeState['name']}}（站点ID：<span class="text-blue js-clip" data-url="{{ $activeState['siteid'] }}">{{ $activeState['siteid'] }}</span>）
                                    @else
                                        {{ $activeState['state'] }}
                                    @endif
                                </td>
                                <td class="text-right soild-after">
                                    <a href="{{ url('console/active') }}" class="text-blue">重新激活</a>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="fui-table-lable">系统内核</span></td>
                                <td class="soild-after">QingWork(Based on Laravel 6.2.0)</td>
                                <td class="text-right soild-after"></td>
                            </tr>
                            <tr>
                                <td><span class="fui-table-lable">版权所有</span></td>
                                <td class="soild-after">广西神蛙网络科技有限公司</td>
                                <td class="text-right soild-after"></td>
                            </tr>
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
