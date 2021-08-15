@include('common.headerbase')
<link rel="stylesheet" href="{{ asset('/static/css/console.css') }}?v={{ $_W['config']['release'] }}" />
<body layadmin-themealias="ocean-header" class="layui-layout-body" style="position:inherit !important;">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo layui-hide-xs">Whotalk</div>

        <ul class="layui-nav layui-layout-left layui-hide">
        </ul>

        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item layui-hide layui-show-md-inline-block">
                <a href="{{url('user/profile')}}">
                    {{$_W['user']['username']}}
                </a>
                <dl class="layui-nav-child layui-anim layui-anim-upbit">
                    <dd><a href="">Your Profile</a></dd>
                    <dd><a href="">Settings</a></dd>
                    <dd><a href="javascript:Core.logout();">Sign out</a></dd>
                </dl>
            </li>
        </ul>
    </div>
    <div class="layui-body">
