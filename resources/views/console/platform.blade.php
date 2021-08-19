@include('common.header')

<div class="main-content">

    <div class="fui-card layui-card">
        <div class="layui-row layui-col-space15 fui-list card">
            @foreach($list as $key=>$item)
            <div class="layui-col-md3 layui-col-xs6 fui-item">
                <a href="{{ url("console/account",array('uniacid'=>$item['uniacid'])) }}" class="fui-content">
                    <div class="fui-info">
                        <img src="{{ asset($item['logo']) }}" />
                        <strong class="card-name">{{ $item['name'] }}</strong>
                    </div>
                </a>
            </div>
            @endforeach
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
