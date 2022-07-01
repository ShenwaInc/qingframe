@if(!$_W['isajax'])
@include('common.headerbase')
<link rel="stylesheet" href="{{ asset('/static/css/console.css') }}?v={{ $_W['config']['release'] }}" />
<body layadmin-themealias="ocean-header" class="layui-layout-body" style="position:inherit !important;">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="fui-header-sm">
            <div class="layui-logo">
                <a href="{{ $_W['consolePage'] }}">{{ $_W['page']['title'] }}</a>
            </div>

            <ul class="layui-nav layui-layout-right">
                @if($_W['uid']>0)
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <img src="{{ tomedia($_W['user']['avatar']) }}" class="layui-nav-img user-avatar" />
                            {{$_W['user']['username']}}
                        </a>
                        <dl id="layui-admin-usermenu" class="layui-nav-child layui-anim layui-anim-upbit">
                            <dd><a href="{{ wurl('user/profile') }}">账户管理</a></dd>
                            @if($_W['isfounder'])
                                @if($_W['config']['site']['id']==0)
                                    <dd>
                                        <a href="{{url('console/active')}}">系统激活<span class="layui-badge-dot"></span></a>
                                    </dd>
                                @else
                                    <dd><a href="{{url('console/setting')}}">系统管理</a></dd>
                                @endif
                            @endif
                            <dd><a href="javascript:Core.cacheclear();">更新缓存</a></dd>
                            <hr />
                            <dd><a href="javascript:Core.logout();">退出账户</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item layui-hide-xs js-fullscreen" lay-unselect>
                        <a href="javascript:;" layadmin-event="fullscreen">
                            <i class="layui-icon layui-icon-screen-full"></i>
                        </a>
                    </li>
                @else
                    <li class="layui-nav-item">
                        <a href="{{ url('login') }}">登录</a>
                    </li>
                    <li class="layui-nav-item layui-hide">
                        <a href="{{ url('register') }}">注册</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <div class="layui-body">
@endif
