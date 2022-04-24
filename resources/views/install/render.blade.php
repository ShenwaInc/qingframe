@include('install.header')
<div class="installer">
    <div class="layui-card shadows">
        <div class="layui-card-header fui-step">
            <div class="layui-container">
                <div class="layui-row text-center">
                    <div class="layui-col-md4 layui-col-lg4 fui-item done">
                        数据库选项
                        <span class="num"></span>
                    </div>
                    <div class="layui-col-md4 layui-col-lg4 fui-item cur">
                        安装系统
                        <span class="num">3</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="inst-render">
                <div class="layui-row">
                    <form action="{{ url('installer/render') }}" method="post" class="layui-form">
                        <input type="hidden" name="csrf_token" value="{{ csrf_token() }}" />
                        <input type="hidden" name="saverdset" value="true">
                        <div class="layui-col-md12">
                            <fieldset class="layui-elem-field layui-field-title site-title">
                                <legend><a href="{{ url('installer/database') }}">数据库配置&nbsp;<i class="layui-icon layui-icon-edit text-blue"></i></a></legend>
                            </fieldset>
                            <div class="padding-lr">
                                <table class="layui-table" lay-skin="nob" lay-even>
                                    <colgroup>
                                        <col width="120">
                                        <col>
                                    </colgroup>
                                    <tbody>
                                    <tr>
                                        <td class="text-black text-bold">连接方式</td>
                                        <td>{{ $dbconnect ? '连接现有微擎数据库' : '全新安装' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-black text-bold">数据库主机</td>
                                        <td>{{ $database['host'] }}:{{ $database['port'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-black text-bold">数据库名</td>
                                        <td>{{ $database['database'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-black text-bold">用户名</td>
                                        <td>{{ $database['username'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-black text-bold">密码</td>
                                        <td>*********</td>
                                    </tr>
                                    <tr>
                                        <td class="text-black text-bold">表前缀</td>
                                        <td>{{ $database['prefix'] }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="layui-col-md12">
                            <div class="padding">
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">系统名称</label>
                                    <div class="layui-input-block">
                                        <input type="text" required lay-verify="required" name="appname" value="Whotalk" placeholder="请输入系统软件名称" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                @if($dbconnect==0)
                                    <fieldset class="layui-elem-field layui-field-title site-title">
                                        <legend><a name="passport"></a></legend>
                                    </fieldset>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">超管账号</label>
                                        <div class="layui-input-block">
                                            <input type="text" required lay-verify="required" name="username" value="admin" placeholder="请输入超级管理员(创始人)登录账号" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">登录密码</label>
                                        <div class="layui-input-block">
                                            <input type="text" required lay-verify="required" name="password" value="123456" placeholder="请输入超级管理员登录密码" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                @endif
                                <div class="text-center">
                                    <button type="submit" lay-submit lay-filter="formDemo" class="layui-btn layui-btn-lg layui-btn-normal">立即安装</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    layui.use(['form'],function(){
        var form = layui.form;
        form.on('submit(formDemo)',function (data){
            let postdata = data.field;
            Core.confirm('安装过程大约需要3~5分钟，中途请不要退出网页',function (){
                Core.post('installer.render',function (res){
                    if (res.type!=='success') return Core.report(res);
                    layer.msg('恭喜您，安装完成！',{icon:1});
                    setTimeout(function (){
                        window.location.href = "{{ url('') }}";
                    },1200);
                },{render:postdata},'json',true);
            });
            return false;
        });
    });
</script>
@include('install.footer')
