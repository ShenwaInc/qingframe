@include('common.header')
<div class="layui-fluid unpadding">
    <div class="{{ $_GPC['inajax'] ? '' : 'main-content' }}">
        <div class="{{ $_GPC['inajax'] ? '' : 'layui-card fui-card' }}">
            <div class="layui-card-header layui-hide-layer">
                <span class="title">选择平台</span>
                <p class="layui-word-aux">当前服务的管理需要区分不同平台</p>
            </div>
            <div class="layui-card-body">
                @if(empty($platforms) && !$cancreate)
                    <div class="fui-empty text-center" style="line-height: 480px;">
                        <span class="text-gray" style="font-size: 22px;">暂无可用平台</span>
                    </div>
                @else
                    <div class="layui-row layui-col-space15 fui-list card">
                        @foreach($platforms as $key=>$item)
                            <div class="layui-col-md{{ $_GPC['inajax'] ? '4' : '3' }} layui-col-xs12 fui-item">
                                <a href="{{ wurl('server/account', array('uniacid'=>$item['uniacid'])) }}" class="fui-content{{ $item['uniacid']==$uniacid ? ' checked':'' }}">
                                    <div class="fui-info">
                                        <img alt="{{ $item['name'] }}" class="round" src="{{ tomedia($item['logo']) }}" />
                                        <strong class="card-name">{{ $item['name'] }}</strong>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@include('common.footer')
