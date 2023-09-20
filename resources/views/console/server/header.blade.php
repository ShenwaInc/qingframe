@if(!$_W['isajax'])
@include('common.headerbase')
<script type="text/javascript">var BaseApiUrl = '{{ url("site/entry", array("do"=>"index","m"=>"swa_microserver")) }}',BaseController="{{ $_W['controller'] }}",BaseAction="{{ $_W['action'] }}",BaseRoute="{{ $_W['routePath'] }}";</script>
<script type="text/javascript">
    if(typeof(navigator.appName)!='undefined' && navigator.appName === 'Microsoft Internet Explorer'){
        if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
            alert('@lang("lowBrowserVersion")');
        }
    }

    window.sysinfo = {
        'uniacid':{{ $_W["uniacid"] }},
        'acid':{{ intval($_W['acid']) }},
        'openid':'{{ $_W['openid'] }}',
        'uid':{{ $_W['uid'] }},
        'isfounder':{{ $_W['isfounder']?'true':false }},
        'family': '{{ QingVersion }}',
        'siteroot': '{{ $_W['siteroot'] }}',
        'siteurl': '{{ $_W['siteurl'] }}',
        'attachurl': '{{ $_W['attachurl'] }}',
        'attachurl_local': '{{ $_W['attachurl_local'] }}',
        'attachurl_remote': '{{ $_W['attachurl_remote'] }}',
        'module' : {'url' : '{{ defined("MODULE_URL") ? MODULE_URL : "" }}', 'name' : '@if(defined('IN_MODULE')){{ IN_MODULE }}@endif'},
        'cookie' : {'pre': '{{ $_W['config']['cookie']['pre'] }}'},
        'account' : {!! json_encode($_W['account']) !!},
        'server' : {'php' : '{{ phpversion() }}'}
    }
</script>
<body layadmin-themealias="ocean-header" class="layui-layout-body" style="position:inherit !important;">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-header">
            <div class="fui-header-sm">
                <div class="layui-logo">
                    @if($_W['inserver'])
                    <a href="{{ $inService->getEntry()?:'javascript:;' }}">@lang($inService->service['name'])</a>
                    @else
                    <a href="{{ wurl('server') }}">@lang('microServers')</a>
                    @endif
                    <a href="{{ wurl('') }}" class="fui-homepage margin-left-sm" title="@lang('backConsole')"><span class="layui-icon layui-icon-home"></span></a>
                </div>

                <ul class="layui-nav layui-layout-right">
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <i class="layui-icon-username layui-icon"></i>
                            {{ $_W['username'] }}
                        </a>
                        <dl class="layui-nav-child layui-anim layui-anim-upbit" style="padding: 0;">
                            <dd><a href="{{ wurl('user/profile') }}" target="_blank"><span class="layui-icon layui-icon-username"></span>&nbsp;@lang('accountManagement')</a></dd>
                            @if($inService->Unique)
                            <dd><a href="{{ wurl('account/profile', ['uniacid'=>$_W['uniacid']]) }}"><span class="layui-icon layui-icon-website"></span>&nbsp;{{ __('manageData', array('data'=>__('platform'))) }}</a></dd>
                            @endif
                            <hr style="margin: 5px 0;">
                            <dd><a href="javascript:;" layadmin-event="updateCache"><span class="layui-icon layui-icon-refresh"></span>&nbsp;@lang('refreshCache')</a></dd>
                            <dd><a href="{{ wurl("") }}"><span class="layui-icon layui-icon-console"></span>&nbsp;@lang('backConsole')</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item layui-hide-xs js-fullscreen" lay-unselect>
                        <a href="javascript:;" layadmin-event="fullscreen">
                            <i class="layui-icon layui-icon-screen-full"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @if(!empty($_W['_systemMenu']))
    <!-- 侧边菜单 -->
    <div class="layui-side layui-side-menu fui-side-menu">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                @foreach($_W['_systemMenu'] as $key=>$value)
                <li class="layui-nav-item@if($value['name']==$_W['controller'] || $value['name']==$_W['basescript'] || ($_W['isplugin'] && $value['name']=='plugin')) layui-nav-itemed@endif">
                    <a data-name="{{ $value['name'] }}" href="{{ !empty($value['list']) ? 'javascript:' : $value['jump'] }}" lay-tips="{{ $value['title'] }}" lay-direction="2">
                        <i class="{{ $value['icon'] }}"></i>
                        <cite>{{ $value['title'] }}</cite>
                    </a>
                    @if(!empty($value['list']))
                    <dl class="layui-nav-child">
                        @foreach($value['list'] as $k=>$val)
                        <dd data-name="{{ $value['name'] }}"@if(($val['name']==$_W['action'] && $value['name']==$_W['controller']) || $_W['siteurl']==$val['jump'] || (!$value['list'][$_W['action']] && $k=='index' && $value['name']==$_W['controller'])) class="{{ $_W['action'] }} layui-this"@endif>
                        <a href="{{ $val['jump'] }}"@if(!empty($val['target'])) target="{{ $val['target'] }}"@endif>{{ $val['title'] }}</a>
                        </dd>
                        @endforeach
                    </dl>
                    @endif
                </li>
                @endforeach
                <li class="layui-nav-item layui-hide">
                    <a href="{{ wurl("") }}" lay-tips="@lang('backConsole')">
                        <i class="layui-icon layui-icon-return"></i>
                        <cite>@lang('backConsole')</cite>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    @endif
    <!-- 主体内容 -->
    <div class="layui-body">
@endif
