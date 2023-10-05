@include('common.header')
<div class="layui-fluid">
    <div class="main-content">

        <h2>@lang('systemManagement')</h2>

        <div class="layui-tab fui-tab margin-bottom-xl">
            <ul class="layui-tab-title title_tab">
                <li>
                    <a href="{{ url('console/setting') }}">@lang('siteInformation')</a>
                </li>
                <li>
                    <a href="{{ url('console/server') }}">@lang('microServers')</a>
                </li>
                <li class="layui-this">
                    <a href="{{ url('console/module') }}">@lang('applications')</a>
                </li>
            </ul>
        </div>

        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="{{ wurl('setting/market') }}" data-width="1340" class="fr layui-btn layui-btn-sm layui-btn-normal ajaxshow">@lang('appStore')</a>
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
                                            <a href="{{$com['website']}}" class="text-blue" target="_blank">{{$com['name']}}</a><br/>
                                            V{{$com['version']}}
                                        </div>
                                    </td>
                                    <td class="layui-hide-xs">{!! $com['installtime'] !!}</td>
                                    <td class="layui-hide-xs">{!! $com['lastupdate'] !!}</td>
                                    <td class="layui-hide-xs">
                                        @if(empty($com['cloudinfo']))
                                            -
                                        @else
                                            V{{ $com['cloudinfo']['version'] }}&nbsp;&nbsp;Release{{ $com['cloudinfo']['releasedate'] }}
                                            @if($com['cloudinfo']['isnew'])
                                                <span class="layui-badge-dot" lay-tips="{{ $com['cloudinfo']['releasedate']==$com['releasedate'] ? __('sourceCodeChanged') : __('versionNew') }}"></span>
                                            @endif
                                            @if($com['expireDate'])
                                                <p class="margin-top-xs">{!! $com['expireDate'] !!}</p>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="layui-btn-group">{!! $com['action'] !!}</div>
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
