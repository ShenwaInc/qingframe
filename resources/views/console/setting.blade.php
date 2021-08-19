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
            <a href="{{ url('console/setting/pageset') }}" class="fr text-blue ajaxshow" title="编辑站点信息"><i class="glyphicon glyphicon-edit"></i></a>
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
    @elseif($op=='attach')
        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="{{ url('console/setting/attachset') }}" class="fr text-blue ajaxshow" title="修改上传设置"><i class="glyphicon glyphicon-edit"></i></a>
                <span class="title">上传设置</span>
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
                                <td><span class="fui-table-lable">图片格式</span></td>
                                <td class="soild-after">{{ $_W['setting']['upload']['image']['extentions'] }}</td>
                                <td class="text-right soild-after"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <span class="title">远程附件</span>
            </div>
        </div>
    @else
        <div class="fui-card layui-card">
            @if($op=='socket')
                <div class="layui-card-header nobd">
                    <a href="{{ url('console/setting/socketset') }}" class="fr text-blue ajaxshow" title="修改SOCKET配置"><i class="glyphicon glyphicon-edit"></i></a>
                    <span class="title">SOCKET配置</span>
                </div>
                <div class="layui-card-body">
                    <div class="un-padding">
                        <table class="layui-table fui-table lines" lay-skin="nob">
                            <colgroup>
                                <col width="150" />
                                <col />
                                <col width="150" />
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td><span class="fui-table-lable">链接方式</span></td>
                                    <td class="soild-after">{{ $_W['setting']['swasocket']['type']=='local' ? '本地SOCKET' : '远程SOCKET' }}</td>
                                    <td class="text-right soild-after">
                                        @if($_W['setting']['swasocket']['type']=='local')
                                            <a href="{{ url('console/setting/sockethelp') }}" class="text-blue ajaxshow" title="本地SOCKET安装步骤">本地安装步骤</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="fui-table-lable">SOCKET服务器</span></td>
                                    <td class="soild-after">{{ $_W['setting']['swasocket']['server'] }}</td>
                                    <td class="text-right soild-after">
                                        <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" onclick="WsDetect()">测试连接</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="fui-table-lable">推送接口</span></td>
                                    <td class="soild-after">{{ $_W['setting']['swasocket']['api'] }}</td>
                                    <td class="text-right soild-after"></td>
                                </tr>
                                <tr>
                                    <td><span class="fui-table-lable">域名白名单</span></td>
                                    <td class="soild-after">
                                        @php
                                        echo implode("; ",$_W['setting']['swasocket']['whitelist']);
                                        @endphp
                                    </td>
                                    <td class="text-right soild-after">
                                        <a href="javascript:WsException();" class="text-blue">添加</a>
                                        <a href="javascript:WsRestore();" class="text-red">重置</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <script type="text/javascript" src="{{ asset("/static/js/swasocket.js") }}?v={{ $_W['config']['release'] }}"></script>
                <script type="text/javascript">
                    var UserSign = "{{$usersign}}";
                    function WsRestore(){
                        return Core.confirm('重置后将只保留当前域名，是否确定要重置?',function (){
                            Core.post('console.setting',function (res){
                                return Core.report(res);
                            },{
                                op:'resetwhitelist'
                            });
                        },false,{title:'重置域名白名单'})
                    }
                    function WsException(){
                        layer.prompt({title:'添加新域名'},function(value, index, elem){
                            layer.close(index);
                            if(value!==''){
                                Core.post('console.setting',function (res){
                                    return Core.report(res);
                                },{
                                    op:'savewhitelist',
                                    domain:value
                                });
                            }
                        });
                    }
                    function WsDetect(){
                        //检测SOCKET连接状态
                        Swaws.init(UserSign, '{{ $_W['setting']['swasocket']['server'] }}',function (res){
                            if(res.type==='User/Connect'){
                                layer.msg("SOCKET服务器连接成功",{icon:1});
                                Swaws.io.close();
                            }
                        },function (){
                            Core.report({type:'error',redirect:'',message:'服务器连接失败,请重试'});
                        })
                    }
                </script>
            @elseif($op=='component')
                <div class="layui-card-header nobd">
                    <a href="https://www.whotalk.com.cn/" target="_blank" class="fr layui-btn layui-btn-sm layui-btn-normal">获取更多组件</a>
                    <span class="title">服务组件</span>
                </div>
                <div class="layui-card-body">
                    <div class="un-padding">
                        <table class="layui-table fui-table lines" lay-even lay-skin="nob">
                            <colgroup>
                                <col width="280" />
                                <col width="200" />
                                <col width="130" />
                                <col width="130" />
                                <col />
                                <col width="120" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>组件</th>
                                    <th>路径</th>
                                    <th>安装时间</th>
                                    <th>上次更新</th>
                                    <th>线上版本</th>
                                    <th><div class="text-right">操作</div></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($components as $com)
                                <tr>
                                    <td>
                                        <img src="{{ $com['logo'] }}" class="fl bg-gray radius margin-right-sm" height="48" />
                                        <div class="fui-table-name">
                                            <a href="{{$com['website']}}" class="text-blue" target="_blank">{{$com['name']}}</a><br/>
                                            V{{$com['version']}}&nbsp;<span class="layui-badge layui-bg-{{ $colors[$com['type']] }}">{{ $types[$com['type']] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ !empty($com['rootpath']) ? '/'.$com['rootpath'] : '根目录' }}</td>
                                    <td>{{ $com['installtime'] }}</td>
                                    <td>{{ $com['lastupdate'] }}</td>
                                    <td>
                                        @if(empty($com['cloudinfo']))
                                            未开始检测
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
                                        @if(!empty($com['cloudinfo']) && $com['cloudinfo']['isnew'])
                                            <a href="{{ url('console/setting/comupdate') }}?cid={{ $com['id'] }}" class="text-red confirm" data-text="升级前请做好源码和数据备份，避免升级故障导致系统无法正常运行">升级</a>
                                        @endif
                                        <a href="{{ url('console/setting/comcheck') }}?cid={{ $com['id'] }}" class="text-blue ajaxshow">{{ empty($com['cloudinfo']) ? '检测更新' : '重新检测' }}</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
