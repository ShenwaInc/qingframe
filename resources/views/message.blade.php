@include('common.headerbase')
<style>
    body{background-color: #f6f8f9; min-width: 768px;}
    a:hover{text-decoration: none;}
    .layui-layout-admin .layui-header{background-color: #FFFFFF; box-sizing: border-box}
    .layui-nav, .layui-nav .layui-nav-item a{color: #333 !important;}
    .layui-layout-admin .layui-logo{box-shadow: none; color: #606060 !important;}
    .message-card{width: 90%; max-width: 1600px; margin: 0 auto 0; padding-top: 88px;}
    .message-main{box-shadow: 0 1px 5px 0 rgb(0 0 0 / 5%);  min-height: 450px; background: #FFFFFF;}
    .message-card .layui-card{border: none !important; padding-top: 150px; box-shadow: none;}
    .message-icon{line-height: 1.5; color: #47b449;}
    .message-icon.message-error{color: #FF5722;}
    .message-icon .layui-icon{font-size: 72px;}
    .message-text{color: #353535; font-size: 16px; font-weight: 400; padding-bottom: 6px}
    .message-light{color: #01AAED;}
    .message-redirect{font-size: 11px;}
</style>

<body layadmin-themealias="ocean" class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <div class="layui-logo layui-hide-xs">Whotalk</div>

            <ul class="layui-nav layui-layout-left layui-hide">
            </ul>

            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item layui-hide layui-show-md-inline-block">
                    <a href="javascript:;">
                        {{$_W['user']['username']}}
                    </a>
                    <dl class="layui-nav-child layui-anim layui-anim-upbit">
                        <dd><a href="">Your Profile</a></dd>
                        <dd><a href="">Settings</a></dd>
                        <dd><a href="">Sign out</a></dd>
                    </dl>
                </li>
            </ul>
        </div>

        <div class="message-card">
            <div class="message-main text-center">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="message-icon message-{{$type}}">
                            <i class="layui-icon layui-icon-{{$type=='success'?'ok-circle':'about'}}"></i>
                        </div>
                        <div class="message-text">
                            @php
                            echo $message;
                            @endphp
                        </div>
                        @if($redirect!='')
                        <div class="message-redirect">
                            <a href="javascript:location.href=Redirect;" class="message-light">即将自动跳转...</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        @php
        echo "var Redirect = '{$redirect}';";
        @endphp

        layui.use(['element'],function (){
            var element = layui.element;
            @if($redirect!='')
            setTimeout(function (){
                window.location.href = Redirect;
            },3*1e3);
            @endif
        });
    </script>
</body>

@include('common.footerbase')
