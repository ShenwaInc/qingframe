<script type="text/javascript" src="{{ asset('/static/js/swasocket.js') }}?v={{ QingRelease }}"></script>
<style>
    .fui-terminal .layui-code-h3{display: none;}
    .fui-terminal .layui-code-ol li{color: #FFFFFF !important; background: #000000 !important; list-style: none; margin-left: 0;}
    .fui-terminal .layui-code-ol .err{color: #e54d42 !important;}
    .fui-terminal .layui-code-ol .warm{color: #fbbd08 !important;}
    .fui-terminal .layui-code-ol .success{color: #39b54a !important; font-weight: bold;}
</style>
<script type="text/javascript">
    var terminalState = false, terminalPrefix = "[{{ $_W['user']['username']."@" }}{{ str_replace(['https://','http://','/'], '', $_W['siteroot']) }}]# ";
    function SocketReceive(data){
        console.log("接收到终端消息", data);
        if(data.type==="terminal"){
            terminalShow(data.message, data.mode);
        }
    }
    function terminalInit(url="", show=false){
        if(terminalState) return true;
        terminalState = true;
        let html = '<div class="layui-code layui-code-notepad unpadding" id="TerminalInfo" style="margin: 0; height: 480px; width: 960px;">'+terminalPrefix+'正在连接终端服务器...</div>';
        layer.open({
            type: 1,
            skin: 'fui-layer fui-terminal', //样式类名
            closeBtn: 0,
            anim: 2,
            title:"轻如云终端",
            shadeClose: false, //开启遮罩关闭
            content: html,
            success:function (layero, index){
                layui.code();
                if(show){
                    terminalShow(show.message, show.mode);
                }else{
                    terminalShow("请不要关闭或刷新浏览器，否则可能会造成进程中断。如果因超时而失去响应，请增大程序最大运行时间（当前设置：{{ ini_get('max_execution_time') }}秒）", "warm");
                }
                if(url){
                    Core.get(url, function (res){
                        Core.report(res, 2000);
                    }, {inajax:1, _token:"{{ $_W['token'] }}"});
                }
            }
        });
    }
    function terminalShow(message, mode='info'){
        if(!terminalState) return terminalInit("", {message:message, mode:mode});
        let TerminalOl = $("#TerminalInfo").find('ol.layui-code-ol');
        TerminalOl.append('<li class="'+mode+'">'+terminalPrefix+message+'</li>');
        $('.fui-terminal .layui-layer-content').scrollTop(TerminalOl.height());
    }
    $(function (){
        window.Swaws.init("{{ $socket['userSign'] }}", "{{ $socket['server'] }}", SocketReceive);
        $('.js-terminal').click(function (Elem){
            let postUrl = $(this).attr('href');
            let confirmText = $(this).attr('data-text');
            if(typeof(confirmText)=='undefined' || !confirmText){
                terminalInit(postUrl);
            }else {
                Core.confirm(confirmText, function (){terminalInit(postUrl);});
            }
            return false;
        });
    });
</script>
