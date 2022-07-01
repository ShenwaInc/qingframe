<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ $_W ? tomedia($_W['setting']['page']['icon']) : asset('/favicon.ico') }}" />
    <title>后台管理系统</title>
    @php
        !(defined('TIMESTAMP')) && define('TIMESTAMP', time());
    @endphp
    <link rel="stylesheet" href="{{ asset('/static/bootstrap/css/bootstrap.min.css') }}?v={{ TIMESTAMP }}" />
    <link rel="stylesheet" href="{{ asset('/static/layui/css/layui.css') }}?v={{ TIMESTAMP }}" />
    <link rel="stylesheet" href="{{ asset('/static/fului/fului-for-lay.css') }}?v={{ TIMESTAMP }}" />
    <script type="text/javascript" src="{{ asset('/static/layui/layui.js') }}?v={{ TIMESTAMP }}"></script>
    <script type="text/javascript" src="{{ asset('/static/js/jquery-1.11.1.min.js') }}?v={{ TIMESTAMP }}"></script>
    <script type="text/javascript" src="{{ asset('/static/js/core.jquery.js') }}?v={{ TIMESTAMP }}"></script>
    <link rel="stylesheet" href="{{ asset('/static/css/auth.css') }}?v={{ TIMESTAMP }}" />
</head>

<body layadmin-themealias="ocean-header" class="layui-layout-body" style="position:inherit !important;">
    <div class="layui-layout layui-layout-admin">
        <!-- 主体内容 -->
        <div class="layui-body" style="left: 0; top: 0;">
            <div class="layadmin-user-login layadmin-user-display-show">

                <div class="layadmin-user-login-main">
                    <div class="layadmin-user-login-box layadmin-user-login-header">
                        <p class="text-lg">{{ empty($account) ? env('APP_NAME','Whotalk') : $account['name'] }}后台管理系统</p>
                    </div>
                    <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
                        <form action="{{ url('auth/login') }}" id="loginform" method="post">
                            @csrf
                            @if(!empty($account))
                                <input type="hidden" name="uniacid" value="{{ $account['uniacid'] }}" />
                            @endif
                            <div class="layui-form-item">
                                <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                                <input type="text" name="username" value="{{ old('email') }}" autocomplete="username" required lay-verify="required" placeholder="用户名" class="layui-input @error('username') is-invalid @enderror" autofocus />
                            </div>
                            <div class="layui-form-item">
                                <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                                <input type="password" name="password" value="" autocomplete="current-password" required lay-verify="required" placeholder="密码" class="layui-input @error('password') is-invalid @enderror" />
                            </div>
                            <div class="layui-form-item" style="margin-bottom: 20px;">
                                <input type="checkbox" name="remember" id="remember" lay-skin="primary" title="记住身份" {{ old('remember') ? 'checked' : '' }} /><div class="layui-unselect layui-form-checkbox" lay-skin="primary"><span>记住身份</span><i class="layui-icon layui-icon-ok"></i></div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn layui-btn-normal layui-btn-fluid layui-btn-submit js-login" type="submit" lay-submit value="true" name="submit" lay-filter="formLogin">登 入</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
    <script type="text/javascript">
        layui.use(['form'],function (){
            var form = layui.form;
            form.on('submit(formLogin)',function (data){
                Core.post('auth.login',function (res){
                    if (res.type!=='success') return Core.report(res);
                    layer.msg(res.message,{icon:1});
                    setTimeout(function (){
                        window.location.href = '{{ url($_GPC['referer']) }}';
                    },1200);
                },data.field);
                return false;
            });
        });
    </script>
</body>
</html>
