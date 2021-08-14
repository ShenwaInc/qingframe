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
                <div class="wsconnect form-itemlocal layui-hide">
                    <div class="layui-form-item must">
                        <label class="layui-form-label">本地安装步骤</label>
                        <div class="layui-input-block">
                            <div class="layui-tab fui-tab" style="height: 320px; overflow:hidden; overflow-y: auto">
                                <ul class="layui-tab-title">
                                    <li class="layui-this">1.创建站点</li>
                                    <li>2.安装Golang</li>
                                    <li>3.配置站点</li>
                                    <li class="fr">
                                        <a href="https://www.whotalk.com.cn/" target="_blank" class="text-blue">安装遇到困难？</a>
                                    </li>
                                </ul>
                                <div class="layui-tab-content">
                                    <div class="layui-tab-item layui-show">
                                        <div class="layui-text">1&nbsp;准备一个SOCKET通讯专用的域名并解析到当前服务器</div>
                                        <div class="layui-text">2&nbsp;到安全组开放服务器的3000端口</div>
                                        <div class="layui-text">3&nbsp;到宝塔创建新站点:</div>
                                        <div class="layui-text">3.1&nbsp;<strong>域名</strong>填写上一步骤准备的域名</div>
                                        <div class="layui-text">3.2&nbsp;<strong>根目录</strong>必须选择或直接填写&nbsp;<strong class="text-black">{{ str_replace("\\",'/',base_path('socket')) }}</strong></div>
                                        <div class="layui-text">3.3&nbsp;<strong>PHP版本</strong>请选择&nbsp;<strong>纯静态</strong></div>
                                        @if($_W['ishttps'])
                                        <div class="layui-text">4&nbsp;为该站点配置HTTPS并强制开启</div>
                                        @endif
                                        <img width="593" src="/static/installer/inst-socket.png" alt="SOCKET站点创建示意图" />
                                    </div>
                                    <div class="layui-tab-item">
                                        <div class="layui-text">
                                            <strong>使用宝塔面板的【终端】功能或ssh远程管理工具登录服务器后运行如下指令</strong>
                                        </div>
                                        <pre class="layui-code">
sh {{ str_replace("\\",'/',base_path('socket')) }}/install_socket.sh</pre>
                                    </div>
                                    <div class="layui-tab-item">
                                        <strong>宝塔面板的【站点】点击编辑该站点，在【配置文件】中参考如下代码设置</strong>
                                        <pre class="layui-code">
//注：以下内容需要根据站点实际情况调整，未开启https则监听80端口，否则监听443端口
server {
    listen 443;
    server_name xxxxxxxxx.cn;
    ssl on;
    ssl_certificate  /etc/nginx/conf.d/cert/socket.whotalk.com.cn.pem;
    ssl_certificate_key /etc/nginx/conf.d/cert/socket.whotalk.com.cn.key;
    ssl_session_timeout 5m;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;
    client_header_buffer_size 16k;
    large_client_header_buffers 4 64k;
    location /api {
          proxy_pass http://xxxxxxxxx:3000;
          proxy_http_version 1.1;
          proxy_set_header Upgrade $http_upgrade;
          proxy_set_header Connection keep-alive;
          proxy_set_header Host $host;
          proxy_cache_bypass $http_upgrade;
          proxy_set_header X-Real-IP $remote_addr;
          proxy_set_header X-Forwarded-Proto  $scheme;
    }
    location /wss{
 proxy_pass http://xxxxxxxxxxxxxxxxxxxx:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
     proxy_read_timeout 1200s;
 }
}</pre>
                                        <img width="720" src="/static/installer/inst-socket2.png" alt="SOCKET站点创建示意图" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/js/swasocket.js?v={{ $_W['config']['release'] }}"></script>
<script type="text/javascript">
    var ishttps = {{$_W['ishttps']?'true':'false'}},UserSign = "{{$usersign}}";
    layui.use(['form','code'],function(){
        var form = layui.form;
        layui.code();
        form.on('radio(ctrls)', function(data){
            var target = $(data.elem).data('target');
            $(target).addClass('layui-hide');;
            $(target+'.form-item'+data.value).removeClass('layui-hide');
        });
        form.on('submit(formDemo)',function (data){
            let wsserver = data.field.ws_server;
            Swaws.init(UserSign, wsserver,function (res){
                if(res.type==='User/Connect'){
                    Core.post('installer.socket',function (res){
                        if (res.type!=='success') return Core.report(res);
                        //Swaws.io.close();
                        window.location.href = "{{url('installer/render')}}";
                    },{wsconfig:data.field});
                }
            },function (){
                Core.report({type:'error',redirect:'',message:'服务器连接失败,请重试'});
            })
            return false;
        });
    });
</script>
@include('install.footer')
