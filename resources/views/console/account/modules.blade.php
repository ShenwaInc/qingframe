@include('common.header')

<div class="main-content fui-content unpadding">

    <div class="fui-card layui-card">
        <div class="layui-card-body">
            @if(empty($modules))
                <div class="fui-empty text-center" style="line-height: 150px;">
                    <span class="text-gray" style="font-size: 16px;">暂无可用应用</span>
                </div>
            @else
                <form lay-filter="extramodules" action="{{ wurl('account/modules',array('uniacid'=>$_W['uniacid'], 'post'=>1)) }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="layui-row layui-col-space15 fui-list card" id="extraModules">
                        @foreach($modules as $key=>$item)
                            <div class="layui-col-md4 layui-col-sm6 layui-col-xs12 fui-item">
                                <div class="fui-content{{ empty($extras[$key])?'':' checked' }}">
                                    <input type="hidden" name="extras_modules[{{ $key }}]" value="{{ empty($extras[$key])?'0':'1' }}">
                                    <div class="fui-info">
                                        <img alt="{{ $item['title'] }}" class="radius" src="{{ tomedia($item['logo']) }}" />
                                        <strong class="card-name">{{ $item['title'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center padding">
                        <button type="submit" lay-submit class="layui-btn layui-btn-normal">完成</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

</div>
<script type="text/javascript">
    $('#extraModules').find('.fui-content').click(function (){
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
