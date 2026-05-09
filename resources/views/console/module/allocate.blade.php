@include('common.header')

<div class="main-content fui-content{{ $_W['isajax'] ? ' unpadding' : '' }}">

    @if(empty($_W['isajax']))
        <h2>{{ $title }}</h2>
    @endif

    <div class="layui-card fui-card">
        <div class="layui-card-header">
            <span class="title">@lang('将应用【:app】分配给指定平台（可多选）', ['app'=>$module['title']])</span>
        </div>
        <div class="layui-card-body margin-top">
            @if(empty($platforms))
                <div class="fui-empty text-center" style="line-height: 150px;">
                    <span class="text-gray" style="font-size: 16px;">@lang('noPlatformAvailable')</span>
                    &nbsp;<a href="{{ wurl("account/create") }}" class="layui-btn layui-btn-sm layui-btn-normal ajaxshow">{{ __('createNewData', array('data'=>__('platform'))) }}</a>
                </div>
            @else
                <form lay-filter="platforms" action="{{ wurl('module/allocate') }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="nid" value="{{ $identity }}">
                    <input type="hidden" name="referer" value="{{ referer() }}">
                    <div class="layui-row layui-col-space15 fui-list radio card" id="platforms">
                        @foreach($platforms as $key=>$item)
                            <div class="layui-col-md4 layui-col-sm6 layui-col-xs12 fui-item">
                                <div class="fui-content{{ empty($item['moduleExist'])?'':' checked' }}">
                                    <input type="hidden" name="ids[{{ $item['uniacid'] }}]" value="{{ empty($item['moduleExist'])?0:1 }}">
                                    <div class="fui-info">
                                        <img alt="@lang($item['name'])" class="radius" src="{{ globalMedia($item['logo']) }}" />
                                        <strong class="card-name">@lang($item['name'])</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center padding">
                        <button type="submit" lay-submit class="layui-btn layui-btn-normal">@lang('save')</button>
                        @if(empty($_W['isajax']))
                            <a href="{{ wurl('module') }}" class="layui-btn layui-btn-primary">@lang('取消')</a>
                        @else
                        <button type="button" onclick="layer.closeAll()" lay-submit class="layui-btn layui-btn-primary">@lang('取消')</button>
                        @endif
                    </div>
                </form>
            @endif
        </div>
    </div>

</div>
<script type="text/javascript">
    $('#platforms').find('.fui-content').click(function (){
        if($(this).hasClass('checked')){
            //取消
            $(this).removeClass('checked').find('input').val(0);
        }else{
            //选择
            $(this).addClass('checked').find('input').val(1);
        }
    });
</script>
@include('common.footer')
