@include('common.header')

<div class="main-content">

    <div class="fui-card layui-card" style="min-height: 480px;">
        @if(empty($list) && !$creatable)
            <div class="fui-empty text-center" style="line-height: 480px;">
                <span class="text-gray" style="font-size: 22px;">@lang('noPlatformAvailable')</span>
            </div>
        @else
        <div class="layui-row layui-col-space15 fui-list card">
            @foreach($list as $key=>$item)
            <div class="layui-col-md3 layui-col-sm4 layui-col-xs12 fui-item platform-item" data-id="{{ $item['uniacid'] }}">
                <a href="{{ wurl("account") }}/{{ $item['uniacid'] }}" class="fui-content">
                    <div class="fui-info">
                        <img alt="{{ $item['name'] }}" class="radius" src="{{ globalMedia($item['logo']) }}" />
                        <strong class="card-name">@lang($item['name'])</strong>
                    </div>
                </a>
            </div>
            @endforeach
            @if($creatable)
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
@php
$tplStr = '{{d.title}}';
@endphp
<script type="text/javascript">
    function DropRender(dropdown) {
        $('.platform-item').each(function (i){
            let Elem = $(this);
            let uniacid = Elem.data('id');
            let menus = [
                {title:"@lang('manage')",id:"profile"},
                {title:"@lang('delete')",id:"remove",templet:'<span class="text-red">{{ $tplStr }}</span>'}
            ];
            dropdown.render({
                elem:this,
                data:menus,
                trigger:"contextmenu",
                click:function (obj){
                    switch (obj.id) {
                        case "remove":{
                            layer.confirm("@lang('deletePlatformRemain')", {icon: 3, title:'@lang("confirm")', btn:['@lang("delete")', '@lang("取消")']}, function(index){
                                window.location.href = '{{ wurl('account/remove') }}?uniacid=' + uniacid;
                                layer.close(index);
                            });
                            break;
                        }
                        default : {
                            window.location.href = '{{ wurl('account/profile') }}?uniacid=' + uniacid;
                            break;
                        }
                    }
                }
            });
        });
    }
</script>

@include('common.footer')
