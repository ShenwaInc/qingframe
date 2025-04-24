@include('common.header')
<div class="layui-fluid">
    <div class="main-content">

        <h2>@lang('systemManagement')</h2>

        <div class="layui-tab fui-tab margin-bottom-xl">
            <ul class="layui-tab-title title_tab">
                <li>
                    <a href="{{ wurl('setting') }}">@lang('siteInformation')</a>
                </li>
                <li>
                    <a href="{{ wurl('server') }}">@lang('服务管理')</a>
                </li>
                <li class="layui-this">
                    <a href="{{ wurl('module') }}">@lang('applications')</a>
                </li>
            </ul>
        </div>

        @if(empty($activeState['hasDomain']))
            <div class="layui-elem-quote margin-bottom-xl" style="border-color: #FF5722; background-color: #fadbd9;">
                <p class="text-red">{!! __('domainNotify', ['domain'=>$_SERVER['HTTP_HOST']]) !!}&nbsp;&nbsp;<a href="{{ wurl('active') }}" class="text-blue">@lang('重置云服务')</a></p>
            </div>
        @endif

        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="{{ wurl('module/passcode') }}" data-width="680" class="fr layui-btn layui-btn-sm layui-btn-danger ajaxshow margin-left-sm">@lang('卡密安装')</a>
                @if($activeState['hasDomain'] && $activeState['status'])
                <a href="{{ wurl('setting/market') }}" data-width="1340" class="fr layui-btn layui-btn-sm layui-btn-normal ajaxshow">@lang('appStore')</a>
                @endif
                <span class="title">@lang('application')</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-even lay-skin="nob">
                        <thead>
                        <tr>
                            <th>@lang('app')</th>
                            <th class="layui-hide-xs">@lang('installTime')</th>
                            <th class="layui-hide-xs">@lang('lastUpdate')</th>
                            <th class="layui-hide-xs">@lang('云服务')</th>
                            <th><div class="text-right">@lang('action')</div></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(empty($components))
                            <tr>
                                <td colspan="5" class="text-center">@lang('empty')</td>
                            </tr>
                        @else
                            @foreach ($components as $com)
                                <tr>
                                    <td>
                                        <img src="{{ $com['logo'] }}" class="fl bg-gray radius margin-right-sm" height="48" />
                                        <div class="fui-table-name">
                                            <a href="{{$com['website']}}" class="text-blue" title="@lang($com['description'])" target="_blank">@lang($com['name'])</a><br/>
                                            <span title="Release {{ $com['releasedate'] }}">V{{$com['version']}}</span>
                                        </div>
                                    </td>
                                    <td class="layui-hide-xs">{!! $com['installTime'] !!}</td>
                                    <td class="layui-hide-xs">{!! $com['lastUpdated'] !!}</td>
                                    <td class="layui-hide-xs">
                                        @if(empty($com['cloudInfo']))
                                            -
                                        @else
                                            V{{ $com['cloudInfo']['version'] }}&nbsp;&nbsp;Release {{ $com['cloudInfo']['releasedate'] }}
                                            @if($com['cloudInfo']['upgradable'])
                                                @if($com['cloudInfo']['releasedate']==$com['releasedate'])
                                                    <span class="layui-badge-dot" lay-tips="@lang('当前系统源码与云端对比有变动')"></span>
                                                @else
                                                    <span class="layui-badge-dot" lay-tips="@lang('发现新版本')"></span>
                                                @endif
                                            @endif
                                            @if(empty($com['maintenance']) && !empty($com['installed']) && empty($com['cloudInfo']['isLocal']))
                                                &nbsp;&nbsp;<a href="{!! wurl('module/maintenance', array('nid'=>$com['identifie'])) !!}" data-text="@lang('停用云服务后将不再提示云端更新版本')" class="ajaxshow confirm text-blue">@lang('disable')</a>
                                            @elseif(!empty($com['maintenance']))
                                                &nbsp;&nbsp;<span class="text-gray">@lang('terminated')</span>
                                            @endif
                                            @if($com['expireDate'] && empty($com['maintenance']))
                                                <p class="margin-top-xs">{!! $com['expireDate'] !!}</p>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="layui-btn-group">
                                            @if(empty($com['maintenance']) && !empty($com['id']))
                                                <a href="{{ wurl('setting/comcheck', array('cid'=>$com['id'])) }}" class="layui-btn layui-btn-sm layui-btn-normal ajaxshow">@lang('检测更新')</a>
                                            @endif
                                            @if($com['cloudInfo']['upgradable'])
                                                <a href="{{ wurl(empty($com['cloudInfo']['isLocal'])?'module/update':'module/upgrade', array('nid'=>$com['modulename'])) }}" data-text="@lang('upgradeConfirm')" class="layui-btn layui-btn-sm layui-btn-danger js-terminal">@lang('upgrade')</a>
                                            @endif
                                            {!! $com['action'] !!}
                                        </div>
                                        <div class="layui-btn-group layui-hide"></div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@include('console.terminal')
@include('common.footer')
