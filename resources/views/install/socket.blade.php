@include('install.header')
<div class="installer">
    <div class="layui-card shadows">
        <div class="layui-card-header fui-step">
            <div class="layui-container">
                <div class="layui-row text-center">
                    <div class="layui-col-md4 layui-col-lg4 fui-item done">
                        数据库
                        <span class="num"></span>
                    </div>
                    <div class="layui-col-md4 layui-col-lg4 fui-item cur">
                        SOCKET
                        <span class="num"></span>
                    </div>
                    <div class="layui-col-md4 layui-col-lg4 fui-item">
                        安装完成
                        <span class="num">3</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <form action="{{ url('installer/socket') }}" method="post" class="layui-form">
                <input type="hidden" name="csrf_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="saveskset" value="true">
                <div class="layui-form-item">
                    <label class="layui-form-label">连接方式</label>
                    <div class="layui-input-block">
                        <input type="radio" lay-filter="ctrls" data-target=".wsconnect" value="remote" name="wstype" title="远程SOCKET" checked="checked" />
                        <input type="radio" disabled lay-filter="ctrls" data-target=".wsconnect" value="local" name="wstype" title="本地SOCKET" />
                        <div class="layui-word-aux">本地SOCKET请激活后再使用</div>
                    </div>
                </div>
                <div class="layui-form-item must">
                    <label class="layui-form-label">SOCKET域名</label>
                    <div class="layui-input-block">
                        <input type="text" required lay-verify="required" name="ws_server" value="{{$socket['server']}}" placeholder="请输入SOCKET服务器地址" autocomplete="off" class="layui-input">
                        <div class="layui-word-aux wsconnect form-itemlocal layui-hide">请修改为您本地的自定义SOCKET域名，<strong class="text-black">必须为ws{{$_W['ishttps']?'s':''}}://开头</strong></div>
                    </div>
                </div>
                <div class="layui-form-item must">
                    <label class="layui-form-label">WEB推送接口</label>
                    <div class="layui-input-block">
                        <input type="text" required lay-verify="required" name="ws_webapi" value="{{$socket['webapi']}}" placeholder="请输入WEB消息推送接口" autocomplete="off" class="layui-input">
                        <div class="layui-word-aux wsconnect form-itemlocal layui-hide">请修改为您本地的自定义接口地址(只需要修改域名部分)，<strong class="text-black">必须为http{{$_W['ishttps']?'s':''}}://开头</strong></div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" type="submit" value="true" name="savedata">下一步</button>
                        <a class="layui-btn layui-btn-primary" href="{{url('installer/render')}}">跳过</a>
                        <a class="layui-btn layui-btn-primary" href="{{url('installer/database')}}">上一步</a>
                        <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/js/swasocket.js?v={{ $_W['config']['release'] }}"></script>
<script type="text/javascript">
    var ishttps = {{$_W['ishttps']?'true':'false'}},UserSign = "{{$usersign}}",CheckWs=false;
    layui.use(['form'],function(){
        var form = layui.form;
        form.on('submit(formDemo)',function (data){
            let wsserver = data.field.ws_server;
            let doNext = function (){
                Core.post('installer.socket',function (res){
                    if (res.type!=='success') return Core.report(res);
                    //Swaws.io.close();
                    window.location.href = "{{url('installer/render')}}";
                },{wsconfig:data.field});
            }
            if (CheckWs){
                //检测SOCKET连接状态
                Swaws.init(UserSign, wsserver,function (res){
                    if(res.type==='User/Connect'){
                        doNext();
                    }
                },function (){
                    Core.report({type:'error',redirect:'',message:'服务器连接失败,请重试'});
                })
            }else {
                doNext();
            }
            return false;
        });
    });
</script>
@include('install.footer')
