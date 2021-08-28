@include('common.header')

<div class="main-content">

    <div class="fui-card layui-card" style="min-height: 480px;">
        <div class="layui-row layui-col-space15 fui-list card">
            @foreach($list as $key=>$item)
            <div class="layui-col-md3 layui-col-xs6 fui-item">
                <a href="{{ url("console/account",array('uniacid'=>$item['uniacid'])) }}" class="fui-content">
                    <div class="fui-info">
                        <img src="{{ tomedia($item['logo']) }}" />
                        <strong class="card-name">{{ $item['name'] }}</strong>
                    </div>
                </a>
                <div class="js-dropdown layui-nav-item">
                    <dl class="layui-nav-child layui-anim layui-anim-upbit js-dropdown-menu">
                        <dd><a href="{{ wurl('account/profile') }}?uniacid={{ $item['uniacid'] }}">详情</a></dd>
                        <dd><a href="{{ wurl('account/remove') }}?uniacid={{ $item['uniacid'] }}" class="text-red confirm" data-text="删除后前台将无法使用，是否确定要删除？">删除</a></dd>
                    </dl>
                    <span class="layui-icon layui-icon-down text-gray"></span>
                </div>
            </div>
            @endforeach
            <div class="layui-col-md3 layui-col-xs6 fui-item">
                <a href="{{ wurl("account/create") }}" title="创建新平台" class="fui-content dashed">
                    <div class="fui-info">
                        <span class="card-icon layui-icon layui-icon-add-1 text-gray"></span>
                        <strong class="card-name text-gray">新建平台</strong>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    layui.use(['element'],function (){
        var element = layui.element;
        layer.ready(function (){
            @if($_W['isfounder'] && $_W['config']['site']['id']==0)
            $('#layui-admin-usermenu').addClass('layui-show');
            @endif
        });
    });
</script>

@include('common.footer')
