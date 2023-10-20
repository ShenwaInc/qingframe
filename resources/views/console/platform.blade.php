@include('common.header')

<div class="main-content">

    <div class="fui-card layui-card" style="min-height: 480px;">
        @if(empty($list) && !$cancreate)
            <div class="fui-empty text-center" style="line-height: 480px;">
                <span class="text-gray" style="font-size: 22px;">@lang('noPlatformAvailable')</span>
            </div>
        @else
        <div class="layui-row layui-col-space15 fui-list card">
            @foreach($list as $key=>$item)
            <div class="layui-col-md3 layui-col-sm4 layui-col-xs12 fui-item">
                <a href="{{ wurl("account") }}/{{ $item['uniacid'] }}" class="fui-content">
                    <div class="fui-info">
                        <img alt="{{ $item['name'] }}" class="radius" src="{{ tomedia($item['logo']) }}" />
                        <strong class="card-name">{{ $item['name'] }}</strong>
                    </div>
                </a>
                <div class="js-dropdown layui-nav-item">
                    <dl class="layui-nav-child layui-anim layui-anim-upbit js-dropdown-menu">
                        <dd><a href="{{ wurl('account/profile') }}?uniacid={{ $item['uniacid'] }}">@lang('manage')</a></dd>
                        @if(in_array($item['user_role'], ['founder','owner']) || $_W['isfounder'])
                        <dd><a href="{{ wurl('account/remove') }}?uniacid={{ $item['uniacid'] }}" class="text-red confirm" data-text="@lang('deletePlatformRemain')">@lang('delete')</a></dd>
                        @endif
                    </dl>
                    <span class="layui-icon layui-icon-down text-gray"></span>
                </div>
            </div>
            @endforeach
            @if($cancreate)
            <div class="layui-col-md3 layui-col-sm4 layui-col-xs12 fui-item">
                <a href="{{ wurl("account/create") }}" title="{{ __('createNewData', array('data'=>__('platform'))) }}" class="fui-content dashed ajaxshow">
                    <div class="fui-info">
                        <span class="card-icon layui-icon layui-icon-add-1 text-gray"></span>
                        <strong class="card-name text-gray">{{ __('createNewData', array('data'=>__('platform'))) }}</strong>
                    </div>
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

</div>

@include('common.footer')
