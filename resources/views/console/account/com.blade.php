@include('common.header')

<div class="main-content">

    <h2>{{ $title }}</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('account/profile',array('uniacid'=>$uniacid)) }}">平台信息</a>
            </li>
            <li>
                <a href="{{ wurl('account/setting',array('uniacid'=>$uniacid)) }}">参数设置</a>
            </li>
            <li>
                <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}">管理权限</a>
            </li>
            <li class="layui-this">
                <a href="{{ wurl('account/component',array('uniacid'=>$uniacid)) }}">应用管理</a>
            </li>
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-body">
            <div class="un-padding">
                <div class="layui-row layui-col-space15 fui-list card">
                    @foreach($components as $item)
                    <div class="layui-col-md3 layui-col-xs6 fui-item">
                        <a href="{{ wurl("account/{$uniacid}",array('module'=>$item['identity'])) }}" class="fui-content">
                            <div class="fui-info">
                                <img alt="{{ $item['name'] }}" class="radius" src="{{ tomedia($item['logo']) }}" />
                                <strong class="card-name">{{ $item['name'] }}</strong>
                            </div>
                        </a>
                    </div>
                    @endforeach
                    <div class="layui-col-md3 layui-col-xs6 fui-item">
                        <a href="javascript:;" data-id="#account-component-selector" onclick="showWindow(this)" title="添加应用权限" class="fui-content dashed">
                            <div class="fui-info">
                                <span class="card-icon layui-icon layui-icon-add-1 text-gray"></span>
                                <strong class="card-name text-gray">添加应用</strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="layer-content">
    <div class="layui-hide" id="account-component-selector">
        <div class="layui-row layui-col-space15 fui-list card">
            @foreach($components as $item)
                <div class="layui-col-md4 layui-col-xs6 fui-item">
                    <a href="javascript:;" onclick="moduleSwitch(this)" data-id="{{ $item['identity'] }}" class="fui-content checked">
                        <div class="fui-info">
                            <img alt="{{ $item['name'] }}" class="radius" src="{{ tomedia($item['logo']) }}" />
                            <strong class="card-name">{{ $item['name'] }}</strong>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .fui-content.checked{border-color: #2ABA8E;}
    .fui-content.checked:before{content: ''; width: 0; height: 0; border-bottom: 36px solid #2ABA8E; border-left: 64px solid transparent; position: absolute; display: block; right: -1px; bottom: -1px}
    .fui-content.checked:after{position: absolute; font-family: layui-icon!important; content: "\e605"; right: 0; bottom: 0; font-size: 17px; padding: 4px 6px; font-weight: bold; color: #fff;}
</style>

<script type="text/javascript">
    function moduleSwitch(Elem){
        let identity = $(Elem).attr('data-id');
        if (identity==="{{ $_W['config']['defaultmodule'] }}"){
            return layer.msg('默认组件为必选项',{icon:2});
        }
    }
    function showWindow(Elem){
        let title = Elem.title;
        let id = $(Elem).attr('data-id');
        layer.open({
            title:title,
            shade:0.3,
            shadeClose:true,
            type:1,
            content:$(id).html(),
            area: '960px',
            skin:'fui-layer',
            btnAlign:'c',
            btn:['确定','获取更多应用'],
            yes: function(index, layero){
                //do something
                layer.close(index); //如果设定了yes回调，需进行手工关闭
            },
            btn2:function (){
                window.location.href = '{{ url('console/setting/component') }}';
            }
        });
    }
</script>

@include('common.footer')
