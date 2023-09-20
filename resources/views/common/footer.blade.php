@if(!$_W['isajax'])
<div class="fui-footer">
    <div class="fui-footer-info soild-after">
        <div class="fui-footer-link">
            {!! $_W['page']['links'] !!}
        </div>
    </div>
    <div class="fui-footer-extra">
        <p class="fui-footer-copyright">
            @if(!empty($_W['setting']['languages']))
                @php $locale = config('app.locale', 'zh'); @endphp
                <span style="cursor: pointer" class="fr js-languages text-blue margin-left">{{ $_W['setting']['languages'][$locale]['name'] }}&nbsp;<i style="font-size: 12px" class="layui-icon layui-icon-down text-gray"></i></span>
            @endif
            <span class="fr layui-hide-xs">{{ now() }}@if($debugInfo = debugInfo()), Processed in {{ $debugInfo['runtime'] }} second(s)@endif</span>
            {!! $_W['page']['copyright'] !!}
        </p>
    </div>
</div>
<script type="text/javascript">
    var layform, layupload, laydropdown;
    require.config({
        baseUrl: '/static/js',
        paths:{
            'clipboard':'clipboard.min'
        }
    });
    layui.use(['element','form','laydate','upload','code','dropdown'],function (){
        var form = layui.form,element = layui.element, upload = layui.upload, dropdown = layui.dropdown;
        form.on('radio(ctrls)', function(data){
            var target = $(data.elem).data('target');
            $(target).addClass('layui-hide');;
            $(target+'.form-item'+data.value).removeClass('layui-hide');
        });
        EventInit($('body'));
        if(typeof (FormRender)=='function'){
            FormRender(form);
        }
        if(typeof (UploadRender)=='function'){
            UploadRender(upload);
        }
        if(typeof (DropRender)=='function'){
            DropRender(dropdown);
        }
        @if(!empty($_W['setting']['languages']))
        dropdown.render({
            elem: ".js-languages",
            data:[
                @foreach($_W['setting']['languages'] as $key=>$value)
                {title:"{{ $value['name'] }}", id:"{{ $key }}"},
                @endforeach
            ],
            click:function (e) {
                checkLocale(e.id);
            }
        });
        @endif
        layform = form;
        layupload = upload;
        laydropdown = dropdown;
    });
    function checkLocale(locale) {
        Core.post('server/language/checkout', function (res) {
            if(res.type==="success"){
                window.location.reload();
            }else{
                Core.report(res);
            }
        }, {locale: locale})
    }
    function DateInit(Obj){
        if (Obj.find('.layui-input-laydate').length>0){
            Obj.find('.layui-input-laydate').each(function(index, element) {
                let type = 'date', format = 'yyyy-MM-dd';
                if (typeof($(element).attr('data-format')) != 'undefined'){
                    type = 'datetime';
                    format = $(element).attr('data-format');
                }
                layui.laydate.render({
                    elem: element //指定元素
                    ,type: type
                    ,format:format
                });
            });
        }
    }
    function EventInit(Obj){
        Obj.find('[lay-tips]').each(function (index,element) {
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
        Obj.find('.showmenu').click(function(){
            $(this).dropdown();
            return false;
        });
        Obj.find('a.confirm').not('.ajaxshow').click(function(){
            var comfirmText = $(this).data('text');
            var redirect = $(this).attr('href');
            layer.confirm(comfirmText, {icon: 3, title:'@lang("confirm")', btn:['@lang("确定")', '@lang("取消")']}, function(index){
                window.location.href = redirect;
                layer.close(index);
            });
            return false;
        });
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
                    return Core.cacheclear();
                case 'showqrcode' :
                    var qrcode = $(this).data('url');
                    let title = typeof($(this).data('title'))=='undefined' ? '@lang("WeChatToScanCode")' : $(this).data('title');
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
                        "title": "@lang('preview')", //相册标题
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
        Obj.find('.ajaxshow').click(function(){
            if($(this).hasClass('layui-disabled')) return false;
            let geturl = $(this).attr('href');
            let title = typeof($(this).attr('title'))!='undefined' ? $(this).attr('title') : $(this).text();
            let width = typeof($(this).attr('data-width'))!='undefined' ? $(this).attr('data-width') + 'px' : '990px';
            let confirmText = typeof($(this).attr('data-text'))=='undefined' ? '' : $(this).attr('data-text');
            let callBack = function (){
                Core.get(geturl,function(Html){
                    if(Core.isJsonString(Html)){
                        var obj = jQuery.parseJSON(Html);
                        return Core.report(obj);
                    }else{
                        let WindowId = 'ajaxwindow' + Wrandom(6);
                        layer.open({type:1,content:Html,id:WindowId,title:title,shade:0.3,area:width,shadeClose:true,skin:'fui-layer'});
                        let Ajaxwindow = $('#'+WindowId);
                        if(Ajaxwindow.find('form.layui-form').length>0){
                            var filter = Ajaxwindow.find('form.layui-form').attr('lay-filter');
                            FormInit(filter);
                        }
                        if(Ajaxwindow.find('.layui-code').length>0){
                            layui.code();
                        }
                        EventInit(Ajaxwindow);
                    }
                },{inajax:1},'html',true);
            }
            if(confirmText!==''){
                layer.confirm(confirmText, {icon: 3, title:'@lang("confirm")'}, function(index){
                    layer.close(index);
                    callBack();
                });
                return false;
            }
            callBack();
            return false;
        });
        Obj.find(".js-clip").each(function () {
            ClipInit(this, $(this).attr("data-url"))
        })
        DateInit(Obj);
    }
    function ClipInit(Elem, text=''){
        require(["clipboard"], function (clip) {
            var e = new clip(Elem, {
                text: function () {
                    return text;
                }
            });
            e.on("success", function (t) {
                layer.msg("@lang('copySuccessfully')",{icon:1});
            });
            e.on("error", function (t) {
                layer.msg("@lang('copyFailed')",{icon:2});
            })
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
    function FormInit(filter){
        layui.form.render(null, filter);
    }
</script>
@include('common.footerbase')
@endif
