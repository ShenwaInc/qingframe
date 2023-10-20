@include('install.header')
    <div class="wp">
        <div class="layui-card shadows">
            <div class="layui-card-header bg-gray">软件安装使用服务协议</div>
            <div class="layui-card-body">
                @include('agreement')
            </div>
            <div class="layui-card-header layui-form solid-top" id="w-agree">
                <div class="layui-form-mid fr" style="padding-top: 5px !important;">
                    <a href="javascript:;" onclick="doNext()" class="layui-btn layui-btn-normal layui-btn-sm fui-btn layui-btn-disabled">下一步</a>
                </div>
                <div class="layui-form-mid">
                    <input type="checkbox" lay-filter="agreement" name="agreement" title="已阅读并同意轻如云系统安装使用协议" lay-skin="primary">
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    var isagree = false, agreeElem = $('#w-agree'),NextUrl = '{{ $_W['siteroot'] }}installer/database';
    function doNext(){
        if(!isagree) return layer.msg('请先仔细阅读并同意安装使用协议',{icon:2});
        Core.post('installer/agreement',function (res){
            if(res.type==='success'){
                window.location.href = NextUrl;
            }else {
                Core.report(res);
            }
        },{isagree:1});
    }
    layui.use(['form'],function(){
        var form = layui.form;
        form.on('checkbox(agreement)',function (data){
            isagree = data.elem.checked;
            if(isagree){
                agreeElem.find('.layui-btn').removeClass('layui-btn-disabled');
            }else {
                agreeElem.find('.layui-btn').addClass('layui-btn-disabled');
            }
        });
    });
</script>
@include('install.footer')
