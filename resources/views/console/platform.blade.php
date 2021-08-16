@include('common.header')

<script type="text/javascript">
    layui.use(['element'],function (){
        var element = layui.element;
        layer.ready();
        @if($_W['isfounder'])
        $('#layui-admin-usermenu').addClass('layui-show');
        @endif
    });
</script>

@include('common.footer')
