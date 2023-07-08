@include('common.header')
<div class="layui-fluid">
    <div class="main-content">
        <div class="layui-card fui-card round">
            <div class="layui-card-header text-center">
                <h1 class="margin-bottom-xl">轻松上云，一步之遥</h1>
            </div>
            <div class="layui-card-body">
                <form class="layui-form js-post margin-top" lay-filter="console_cloud_active" action="{{ url('console/active') }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="site[uid]" id="userId" value="{{ $siteinfo['uid'] }}" />
                    <div class="layui-form-item">
                        <div class="layui-form-label">站点域名</div>
                        <div class="layui-input-block">
                            <input type="text" name="siteroot" id="siteRoot" class="layui-input layui-bg-gray radius" required lay-verify="required" readonly value="{{ $siteinfo['siteroot'] }}" />
                            <div class="layui-word-aux">
                                @if(!$siteinfo['hasDomain'])<strong class="text-red">当前的域名{{ $_W['siteroot'] }}与站点激活信息不符，云服务使用可能受限</strong>@endif
                                <p>您还有<strong>{{ $siteinfo['reDomain'] }}</strong>次修改域名额度，<a href="javascript:;" class="text-blue js-reDomain">修改域名</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <div class="layui-form-label">站点名称</div>
                        <div class="layui-input-block">
                            <input type="text" name="site[name]" class="layui-input radius" required lay-verify="required" value="{{ $siteinfo['name'] }}" />
                        </div>
                    </div>
                    <div class="layui-form-item @if(empty($siteinfo['mobile'])) must @endif">
                        <div class="layui-form-label">手机号</div>
                        <div class="layui-input-block">
                            <div class="input-group">
                                <input type="text" name="site[mobile]" id="mobile" required lay-verify="required" placeholder="请输入常用手机号" @if(!empty($siteinfo['mobile'])) readonly @endif class="layui-input radius" value="{{ $siteinfo['mobile'] }}" />
                                <span class="js-sendcode input-group-addon radius"><span class="text-blue">获取验证码</span></span>
                            </div>
                            @if(!empty($siteinfo['mobile']))
                            <div class="layui-word-aux">如需更换手机号请联系客服</div>
                            @endif
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <div class="layui-form-label">验证码</div>
                        <div class="layui-input-block">
                            <input type="text" name="site[verify_code]" placeholder="请输入您收到的验证码" required lay-verify="required" class="layui-input radius" value="" />
                        </div>
                    </div>
                    <div class="layui-form-item must js-password layui-hide">
                        <div class="layui-form-label">云端密码</div>
                        <div class="layui-input-block">
                            <input type="password" name="site[password]" placeholder="请设置一个云端安全密码，用于管理云服务" required lay-verify="required" class="layui-input radius" value="" />
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button type="submit" lay-submit class="layui-btn layui-btn-fluid radius layui-btn-normal layui-btn-lg margin-top" lay-filter="cloud_active_submit">立即激活</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .layui-card.fui-card{width: 640px; margin: 0 auto; border-radius: 15px;}
    .layui-form-label{width: 80px;}
    .layui-input-block{margin-left: 110px;}
    .input-group .radius:first-child{border-radius: 5px 0 0 5px;}
    .input-group .radius + .radius{border-radius: 0 5px 5px 0; cursor: pointer;}
    .layui-btn-fluid{box-sizing: border-box;}
    .main-content{padding-top: 100px;}
    input[readonly]{background-color: #FAFAFA!important;}

    @media screen and (max-width: 768px){
        .main-content{padding-top: 0;}
        .layui-card.fui-card{width: 100%;}
        .layui-form-label{width: 70px; padding: 9px 5px;}
        .layui-input-block{margin-left: 80px;}
        .main-content *{box-sizing: border-box;}
        .layui-card-body{padding: 10px 0;}
    }
</style>
<script type="text/javascript">
    $(function () {
        let codeBtn = $(".js-sendcode");
        codeBtn.click(function () {
            if(codeBtn.hasClass('layui-disabled')) return false;
            let codeInterval = null, timeout=120,Input = $('#mobile');
            let mobile = Input.val();
            if (!mobile.match(/^(\+)?(86)?0?1\d{10}$/)) return layer.msg('请输入正确的手机号',{icon:2}),Input.focus();
            codeBtn.addClass('layui-disabled');
            let data = {inajax:1,mobile:mobile,_token:"{{ $_W['token'] }}",sendcode:"true"};
            Core.post("console/util/cloudcode", function (res) {
                if(res.type==='success'){
                    if(res.data.memberexists===1){
                        $('.js-password').addClass('layui-hide').find('input[type="password"]').removeAttr('lay-verify').removeAttr('required');
                    }else{
                        $('.js-password').removeClass('layui-hide').find('input[type="password"]').attr('lay-verify','required').attr('required',true);
                    }
                    if(typeof(res.data.uid)!='undefined'){
                        $('#userId').val(parseInt(res.data.uid));
                    }
                    layer.msg('验证码已发送',{icon:1});
                    codeInterval = setInterval(function(){
                        if(timeout===0){
                            clearInterval(codeInterval);
                            codeInterval = null;
                            $(codeBtn).removeClass('layui-disabled').find('span').text('获取验证码');
                            return;
                        }else{
                            $(codeBtn).find('span').text(timeout+'s');
                        }
                        timeout--
                    },1e3);
                }else{
                    $(codeBtn).removeClass('layui-disabled').find('span').text('获取验证码');
                    layer.msg(res.message,{icon:2});
                }
            }, data, 'json', true)
            return false;
        });
        $('.js-reDomain').click(function () {
            @if(empty($siteinfo['reDomain']))
                layer.msg("您的域名修改额度不足，请联系客服", {icon: 5});
            @else
                let siteRoot = $("#siteRoot");
                siteRoot.removeAttr("readonly").removeClass("layui-bg-gray");
                @if(!$siteinfo['hasDomain'])
                    siteRoot.val("{{ $_W['siteroot'] }}");
                @endif
                siteRoot.focus();
            @endif
            return false;
        });
    });
</script>
@include('common.footer')
