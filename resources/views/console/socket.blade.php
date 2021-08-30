@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="weui-desktop-page__title">本地SOCKET安装步骤</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
@endif

        <div class="layui-tab fui-tab" style="margin-top: 0">
            <ul class="layui-tab-title">
                <li class="layui-this">1.创建站点</li>
                <li>2.安装Golang</li>
                <li>3.配置站点</li>
                <li class="fr">
                    <a href="https://www.whotalk.com.cn/" target="_blank"><span class="text-blue">安装遇到困难？</span></a>
                </li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-text">1&nbsp;准备一个SOCKET通讯专用的域名并解析到当前服务器</div>
                    <div class="layui-text">2&nbsp;到安全组<strong>开放服务器的3000端口</strong></div>
                    <div class="layui-text">3&nbsp;到宝塔创建新站点:</div>
                    <div class="layui-text">3.1&nbsp;<strong>域名</strong>填写上一步骤准备的域名</div>
                    <div class="layui-text">3.2&nbsp;<strong>根目录</strong>请选择或直接填写&nbsp;<strong class="text-black">{{ str_replace("\\",'/',base_path('socket')) }}</strong></div>
                    <div class="layui-text">3.3&nbsp;<strong>PHP版本<span class="text-red">必须选择纯静态</span></strong></div>
                    @if($_W['ishttps'])
                        <div class="layui-text">4&nbsp;为该站点配置HTTPS并强制开启</div>
                    @endif
                    <img width="593" src="{{ asset('static/installer/inst-socket.png') }}" alt="SOCKET站点创建示意图" />
                </div>
                <div class="layui-tab-item">
                    <div class="layui-text">
                        <strong>使用宝塔面板的【终端】功能或ssh远程管理工具登录服务器后运行如下指令</strong>
                    </div>
                    <pre class="layui-code">
sh {{ str_replace("\\",'/',base_path('socket')) }}/install_socket.sh</pre>
                </div>
                <div class="layui-tab-item">
                    <div class="layui-text">
                        <strong>宝塔面板的【站点】点击编辑该站点，在【配置文件】中参考如下代码设置</strong>
                    </div>
                    <div class="layui-text">
                        <strong>下方以宝塔内的NGINX服务器为例，其它系统请咨询专业技术人员，或联系售后提供有偿安装服务</strong>
                    </div>
                    <pre class="layui-code">
//注：以下内容需要根据站点实际情况调整，未开启https则监听80端口，否则监听443端口
//在【站点】→【配置文件】的 server 代码内加入如下代码,把其中的http://xxxxxxxxx改为您的域名或者IP地址
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
</pre>
                    <img width="720" src="{{ asset('static/installer/inst-socket2.png') }}" alt="SOCKET站点创建示意图" />
                </div>
            </div>
        </div>

@if(!$_W['isajax'])
            </div>
        </div>
    </div>
@endif

@include('common.footer')
