@include('common.header')

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
