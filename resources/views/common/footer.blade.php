@if(!$_W['isajax'])
<div class="fui-footer">
    <div class="fui-footer-info soild-after">
        <div class="fui-footer-link">
            @php
                echo htmlspecialchars_decode($_W['page']['links'], ENT_QUOTES);
            @endphp
        </div>
    </div>
    <div class="fui-footer-extra">
        <p class="fui-footer-copyright">{{ $_W['page']['copyright'] }}</p>
    </div>
</div>
<script type="text/javascript">
    layui.use(['element','form','laydate','upload','code'],function (){
        var form = layui.form,element = layui.element;
        form.on('radio(ctrls)', function(data){
            var target = $(data.elem).data('target');
            $(target).addClass('layui-hide');;
            $(target+'.form-item'+data.value).removeClass('layui-hide');
        });
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
        EventInit($('body'));
        UploadInit();
    });
    $('.ajaxshow').on('click',this,function(){
        let geturl = $(this).attr('href');
        let title = typeof($(this).attr('title'))!='undefined' ? $(this).attr('title') : $(this).text();
        let width = typeof($(this).attr('data-width'))!='undefined' ? $(this).attr('data-width') + 'px' : '990px';
        Core.get(geturl,function(Html){
            if(Core.isJsonString(Html)){
                var obj = jQuery.parseJSON(Html);
                return Core.report(obj);
            }else{
                let WindowId = 'ajaxwindow' + Wrandom(6);
                layer.open({type:1,content:Html,id:WindowId,title:title,shade:0.3,area:width,shadeClose:true});
                let Ajaxwindow = $('#'+WindowId);
                if(Ajaxwindow.find('form.layui-form').length>0){
                    var filter = Ajaxwindow.find('form.layui-form').attr('lay-filter');
                    let initdate = Ajaxwindow.find('.layui-input-laydate').length>0 ? Ajaxwindow : false;
                    FormInit(filter,initdate);
                }
                if(Ajaxwindow.find('.layui-code').length>0){
                    layui.code();
                }
                if(Ajaxwindow.find('.js-uploadimg').length>0){
                    UploadInit(Ajaxwindow);
                }
                EventInit(Ajaxwindow);
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
    function EventInit(Obj){
        Obj.find('[layadmin-event]').click(function () {
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
                case 'previewimg' :
                    let src = typeof($(this).attr('src'))!='undefined' ? $(this).attr('src') : $(this).attr('data-src');
                    let imgtitle = typeof($(this).attr('title'))!='undefined' ? $(this).attr('title') : $(this).attr('data-alt');
                    let potos = {
                        "title": "图片预览", //相册标题
                        "id": 1, //相册id
                        "start": 0, //初始显示的图片序号，默认0
                        "data": [   //相册包含的图片，数组格式
                            {
                                "alt": imgtitle,
                                "pid": 1, //图片id
                                "src": src, //原图地址
                                "thumb": src //缩略图地址
                            }
                        ]
                    }
                    layer.photos({
                        photos: potos
                    });
                    break;
                default :
                    return true;
            }
            return false;
        });
    }
    function UploadInit(Obj){
        if (typeof(Obj)!='object'){
            Obj = $('body');
        }
        Obj.find('.js-uploadimg').each(function (index,element){
            layui.upload.render({
                elem: element
                ,url: '{{ url("console/util/upload") }}' //必填项
                ,accept:'images'
                ,acceptMime:'images'
                ,exts:"{{ implode('|',$_W['setting']['upload']['image']['extentions']) }}"
                ,data:{_token:"{{ csrf_token() }}"}
                ,done:function (res, index, upload){
                    if(typeof(res.type)!='undefined' && typeof(res.message)!='undefined') return Core.report(res);
                    if(res.state!=='SUCCESS') return layer.msg('上传失败，请重试',{icon:2});
                    let Elem = $(element);
                    Elem.prev().attr('data-src',res.url);
                    Elem.parent().parent().find('input.layui-input').val(res.attachment);
                }
            });
        });
    }
    function Wrandom(len=8, id){
        let codes = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        let maxPos = codes.length;
        let code = '';
        for (let i = 0; i < len; i++) {
            code += codes.charAt(Math.floor(Math.random() * maxPos));
        }
        if(typeof(id) !='undefined'){
            $(id).val(code);
        }
        return code;
    }
    function FormInit(filter,Initdate=false){
        layui.form.render(null, filter);
        if(typeof(Initdate)=='object'){
            Initdate.find('.layui-input-laydate').each(function(index, element) {
                layui.laydate.render({
                    elem: element //指定元素
                    ,format:'yyyy-MM-dd'
                });
            });
        }
    }
</script>
@include('common.footerbase')
@endif
