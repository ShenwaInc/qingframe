@include('install.header')
<div class="installer">
    <div class="layui-card shadows">
        <div class="layui-card-header fui-step">
            <div class="layui-container">
                <div class="layui-row text-center">
                    <div class="layui-col-md4 layui-col-lg4 fui-item cur">
                        数据库
                        <span class="num"></span>
                    </div>
                    <div class="layui-col-md4 layui-col-lg4 fui-item">
                        安装完成
                        <span class="num">2</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <form action="{{ url('installer/database') }}" method="post" class="layui-form">
                <input type="hidden" name="csrf_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="savedbset" value="true">
                <div class="layui-form-item layui-hide">
                    <label class="layui-form-label">数据库连接</label>
                    <div class="layui-input-block">
                        <input type="radio" lay-filter="ctrls" data-target=".dbconnect" value="0" name="dbconnect" title="全新安装" checked="checked" />
                        <input type="radio" disabled lay-filter="ctrls" data-target=".dbconnect" value="1" name="dbconnect" title="指定微擎数据库" />
                    </div>
                </div>
                <div class="layui-form-item must">
                    <label class="layui-form-label">主机名(HOST)</label>
                    <div class="layui-input-block">
                        <input type="text" required lay-verify="required" name="db[host]" value="{{$database['host']}}" placeholder="请输入主机名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item must">
                    <label class="layui-form-label">端口(PORT)</label>
                    <div class="layui-input-block">
                        <input type="number" required lay-verify="required" name="db[port]" value="{{$database['port']}}" placeholder="请输入数据库的开放端口，一般是3306" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item must">
                    <label class="layui-form-label">数据库名(DB)</label>
                    <div class="layui-input-block">
                        <input type="text" required lay-verify="required" name="db[database]" value="{{$database['database']}}" placeholder="请输入数据库的名称" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item must">
                    <label class="layui-form-label">用户名(USER)</label>
                    <div class="layui-input-block">
                        <input type="text" required lay-verify="required" name="db[username]" value="{{$database['username']}}" placeholder="请输入连接此数据库的用户名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item must">
                    <label class="layui-form-label">数据库密码</label>
                    <div class="layui-input-block">
                        <input type="password" required lay-verify="required" name="db[password]" value="" placeholder="请输入该数据库的连接密码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="dbconnect form-item0">
                    <div class="layui-form-item must">
                        <label class="layui-form-label">数据表前缀</label>
                        <div class="layui-input-block">
                            <input type="text" name="db[prefix]" value="{{$database['prefix']}}" placeholder="请输入数据表前缀，微擎数据库默认为ims_" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="dbconnect form-item1 layui-hide">
                    <div class="layui-form-item must">
                        <label class="layui-form-label">创始人密码</label>
                        <div class="layui-input-block">
                            <input type="password" name="founderpwd" value="" placeholder="请输入微擎站点创始人的登录密码，以确认您的身份" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <label class="layui-form-label">微擎安全码</label>
                        <div class="layui-input-block">
                            <input type="password" name="authkey" value="{{$authkey}}" placeholder="请输入微擎系统的安全码" autocomplete="off" class="layui-input">
                            <div class="layui-word-aux">位于微擎站点根目录下的&nbsp;<strong>/data/config.php</strong>&nbsp;文件，变量名为&nbsp;<strong>$config['setting']['authkey']</strong>&nbsp;的字符串</div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" type="submit" value="true" name="savedata">下一步</button>
                        <a class="layui-btn layui-btn-primary" href="{{url('installer')}}?reset=1">上一步</a>
                        <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    layui.use(['form'],function(){
        var form = layui.form;
        form.on('radio(ctrls)', function(data){
            var target = $(data.elem).data('target');
            $(target).addClass('layui-hide');;
            $(target+'.form-item'+data.value).removeClass('layui-hide');
        });
        form.on('submit(formDemo)',function (data){
            let dbconfig = data.field;
            Core.post('installer.database',function (res){
                if (res.type!=='success') return Core.report(res);
                window.location.href = "{{url('installer/render')}}";
            },{dbconfig:dbconfig});
            return false;
        });
    });
</script>
@include('install.footer')
