@if(!$_W['isajax'])
@include('common.headerbase')
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
                            <dd><a href="{{ wurl('user/profile') }}">@lang('accountManagement')</a></dd>
                            <dd><a href="javascript:Core.cacheclear();">@lang('refreshCache')</a></dd>
                            <hr />
                            <dd><a href="javascript:Core.logout();">@lang('logout')</a></dd>
                        </dl>
                    </li>
                    @if($_W['isfounder'])
                        <li class="layui-nav-item">
                            @if($_W['config']['site']['id']==0)
                                <a href="{{url('console/active')}}">@lang('systemActivation')<span class="layui-badge-dot"></span></a>
                            @else
                                <a href="{{url('console/setting')}}">@lang('systemManagement')</a>
                                <dl id="layui-admin-sysmenu" class="layui-nav-child layui-anim layui-anim-upbit">
                                    <dd><a href="{{ url('console/setting') }}">@lang('siteInformation')</a></dd>
                                    <dd><a href="{{ url('console/server') }}">@lang('microServers')</a></dd>
                                    <dd><a href="{{ url('console/module') }}">@lang('applications')</a></dd>
                                </dl>
                            @endif
                        </li>
                        <li class="layui-nav-item{{ $_W['inReport']?' layui-this':'' }}">
                            <a href="{{url('console/report')}}">@lang('workOrder')</a>
                        </li>
                    @endif
                    <li class="layui-nav-item layui-hide-xs js-fullscreen" lay-unselect>
                        <a href="javascript:;" layadmin-event="fullscreen">
                            <i class="layui-icon layui-icon-screen-full"></i>
                        </a>
                    </li>
                @else
                    <li class="layui-nav-item">
                        <a href="{{ url('login') }}">@lang('login')</a>
                    </li>
                    <li class="layui-nav-item layui-hide">
                        <a href="{{ url('register') }}">@lang('register')</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <div class="layui-body">
@endif
