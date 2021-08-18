@if(!$_W['isajax'])
<div class="fui-footer">
    <div class="fui-footer-info soild-after">
        <div class="fui-footer-link">
            @php
                echo $_W['setting']['page']['links'];
            @endphp
        </div>
    </div>
    <div class="fui-footer-extra">
        <p class="fui-footer-copyright">{{ $_W['page']['copyright'] }}</p>
    </div>
</div>
<script type="text/javascript">
    $('.ajaxshow').on('click',this,function(){
        let geturl = $(this).attr('href');
        let title = typeof($(this).attr('title'))!='undefined' ? $(this).attr('title') : $(this).text();
        let width = typeof($(this).attr('data-width'))!='undefined' ? $(this).attr('data-width') + 'px' : '990px';
        Core.get(geturl,function(Html){
            if(Core.isJsonString(Html)){
                var obj = jQuery.parseJSON(Html);
                if(typeof(obj.message)!='undefined'){
                    layer.msg(obj.message,{icon:0});
                }
                setTimeout(function(){
                    if(typeof(obj.url)!='undefined'){
                        window.location.href = obj.url;
                    }
                    if(typeof(obj.redirect)!='undefined'){
                        window.location.href = obj.redirect;
                    }
                },1500);
            }else{
                let WindowId = 'ajaxwindow' + Wrandom(6);
                layer.open({type:1,content:Html,id:WindowId,title:title,shade:0.3,area:width,shadeClose:true});
                let Ajaxwindow = $('#'+WindowId);
                if(Ajaxwindow.find('form.layui-form').length>0){
                    var filter = Ajaxwindow.find('form.layui-form').attr('lay-filter');
                    let initdate = Ajaxwindow.find('.layui-input-laydate').length>0;
                    FormInit(filter,initdate);
                }
            }
        },{inajax:1},'html',true);
        return false;
    });
    $('.showmenu').on('click',this,function(){
        $(this).dropdown();
    });
    $('a.confirm').on('click',this,function(){
        var comfirmText = $(this).data('text');
        var redirect = $(this).attr('href');
        layer.confirm(comfirmText, {icon: 3, title:'提示'}, function(index){
            window.location.href = redirect;
            layer.close(index);
        });
        return false;
    });
    $(function (){
        $('.layui-fluid [lay-tips]').each(function (index,element) {
            $(element).on({
                mouseenter:function () {
                    var tipstr = jQuery(this).attr('lay-tips');
                    laytips = layer.tips(tipstr,this,{tips:1,time:0});
                },
                mouseleave:function () {
                    layer.close(laytips);
                }
            });
        });
        $('[layadmin-event]').click(function () {
            let layevent = $(this).attr('layadmin-event');
            let WinBody = $('body');
            switch (layevent) {
                case 'flexible' :
                    WinBody.addClass('layadmin-side-spread-sm');
                    break;
                case 'shade' :
                    WinBody.removeClass('layadmin-side-spread-sm');
                    break;
                case 'closedp' :
                    var datapicker = $(this).data('dp');
                    $(datapicker).val(''),$(datapicker+'-i').val('').dropdown('toggle').focus();
                    break;
                case 'updateCache' :
                    layui.use('layer', function(){
                        var index = layer.load(0,{shade: false,time: 3000});
                        $.post('./index.php?c=system&a=updatecache&do=updatecache', {}, function(data) {
                            console.log(data);
                            layer.close(index);
                            layer.msg('缓存更新成功',{icon:1})
                        })
                    });
                    break;
                case 'showqrcode' :
                    var qrcode = $(this).data('url');
                    let title = typeof($(this).data('title'))=='undefined' ? '使用微信扫描二维码' : $(this).data('title');
                    layer.open({
                        title:title,
                        content: '<div style="width: 200px; height: 200px; margin: 0 auto;"><img src="'+qrcode+'" height="200" width="200" /></div>',
                        shade:0.5,
                        shadeClose:true
                    });
                    break;
                case 'fullscreen' :
                    let doc=document.documentElement
                    if(doc.webkitRequestFullScreen){
                        doc.webkitRequestFullScreen();
                        document.webkitCancelFullScreen()
                    }
                    if(doc.mozRequestFullScreen){
                        doc.mozRequestFullScreen()
                        document.mozCancelFullScreen()
                    }
                    if(doc.msRequestFullscreen){
                        doc.msRequestFullscreen()
                        document.msExitFullscreen()
                    }
                    if($(this).find('.layui-icon-screen-full').length>0){
                        $(this).find('.layui-icon-screen-full').removeClass('layui-icon-screen-full').addClass('layui-icon-screen-restore');
                    }else {
                        $(this).find('.layui-icon-screen-restore').addClass('layui-icon-screen-full').removeClass('layui-icon-screen-restore');
                    }
                    break;
                default :
                    return true;
            }
        });
    });
    function Wrandom(len=8){
        let codes = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        let maxPos = codes.length;
        let code = '';
        for (let i = 0; i < len; i++) {
            code += codes.charAt(Math.floor(Math.random() * maxPos));
        }
        return code;
    }
    function FormInit(filter,initdate=false){
        let FormRender = function (){
            layui.form.render(null, filter);
            if(initdate){
                jQuery('.layui-input-laydate').each(function(index, element) {
                    layui.laydate.render({
                        elem: element //指定元素
                        ,format:'yyyy-MM-dd'
                    });
                });
            }
        }
        if(typeof(layui.form)=='undefined' || (initdate && typeof(layui.laydate)=='undefined')){
            let user = [];
            if(typeof(layui.form)=='undefined') user.push('form');
            if(initdate && typeof(layui.laydate)=='undefined') user.push('laydate');
            return layui.use(user, function(){
                layer.ready(FormRender)
            });
        }
        FormRender();
    }
</script>
@include('common.footerbase')
@endif
