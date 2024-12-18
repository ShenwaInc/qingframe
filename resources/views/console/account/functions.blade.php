@include('common.header')

<div class="main-content fui-content">

    <h2>{{ __('manageData', array('data'=>__('platform'))) }}</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('account/profile',array('uniacid'=>$uniacid)) }}">@lang('基础信息')</a>
            </li>
            <li class="layui-this">
                <a href="{{ wurl('account/functions',array('uniacid'=>$uniacid)) }}">@lang('应用与服务')</a>
            </li>
            @if(in_array($role,['founder','owner']) || $_W['isfounder'])
                <li>
                    <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}">@lang('操作权限')</a>
                </li>
            @endif
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            @if($_W['isfounder'] || $role=='founder')
                <a href="{{ wurl('account/modules',array('uniacid'=>$uniacid), true) }}" class="fr text-blue ajaxshow" title="{{ __('manageData', array('data'=>__('application'))) }}">@lang('manage')</a>
            @endif
            <span class="title">@lang('application')</span>
        </div>
        <div class="layui-card-body">
            @if(empty($components))
                <div class="fui-empty text-center" style="line-height: 150px;">
                    <span class="text-gray" style="font-size: 16px;">@lang('NoAppsAvailable')</span>
                </div>
            @else
            <div class="layui-row layui-col-space15 fui-list card">
                @foreach($components as $item)
                    <div class="layui-col-lg3 layui-col-md4 layui-col-sm6 layui-col-xs12 fui-item arrow">
                        <a target="_blank" href="{{ wurl("m/".$item['identity']) }}" class="fui-content">
                            <div class="fui-info">
                                <img alt="{{ $item['name'] }}" class="radius" src="{{ $item['logo'] }}" />
                                <strong class="card-name">@lang($item['name'])</strong>
                            </div>
                        </a>
                        @if($_W['isfounder'])
                            <a class="js-dropdown" target="_blank" href="{{ wurl("m/".$item['identity']."/system_setting") }}">
                                <span class="layui-icon layui-icon-set text-blue"></span>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">@lang('功能与服务')</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15 fui-list card">
                @foreach($servers as $value)
                    <div class="layui-col-lg3 layui-col-md4 layui-col-sm6 layui-col-xs12 fui-item fui-item-sm arrow">
                        <a target="_blank" href="{{ $value['entrance'] }}" title="@lang('manage')" class="fui-content">
                            <div class="fui-info">
                                <img alt="@lang($value['title'])" class="radius" src="{{ assets($value['cover']) }}" />
                                <strong class="card-name">@lang($value['title'])</strong>
                                <p class="text-cut">@lang($value['summary'])</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@include('common.footer')
