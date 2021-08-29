@include('common.header')

<div class="main-content">

    <h2>平台配置</h2>
    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('account/profile',array('uniacid'=>$uniacid)) }}">平台信息</a>
            </li>
            <li class="layui-this">
                <a href="{{ wurl('account/setting',array('uniacid'=>$uniacid)) }}">参数设置</a>
            </li>
            @if(in_array($role,['founder','owner']) || $_W['isfounder'])
                <li>
                    <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}">管理权限</a>
                </li>
            @endif
            @if($_W['isfounder'])
                <li>
                    <a href="{{ wurl('account/component',array('uniacid'=>$uniacid)) }}">应用管理</a>
                </li>
            @endif
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">支付设置</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="200" />
                    </colgroup>
                    <tbody class="layui-form">
                        <tr>
                            <td><span class="fui-table-lable">余额支付</span></td>
                            <td class="soild-after">
                                <input type="checkbox" lay-filter="js-switch" name="payment.credit.pay_switch" lay-skin="switch" lay-text="开启|关闭"{{ $setting['payment']['credit']['pay_switch']==1 ? ' checked' : '' }} />
                            </td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">支付宝</span></td>
                            <td class="soild-after">
                                <input type="checkbox" lay-filter="js-switch" name="payment.alipay.pay_switch" lay-skin="switch" lay-text="开启|关闭"{{ $setting['payment']['alipay']['pay_switch']==1 ? ' checked' : '' }} />
                            </td>
                            <td class="text-right soild-after">
                                <a href="javascript:;" data-id="#layer-payment-alipay" title="支付宝配置" onclick="showSet(this)" class="text-blue">接口配置</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">微信支付</span></td>
                            <td class="soild-after">
                                <input type="checkbox" lay-filter="js-switch" name="payment.wechat.pay_switch" lay-skin="switch" lay-text="开启|关闭"{{ $setting['payment']['wechat']['pay_switch']==1 ? ' checked' : '' }} />
                            </td>
                            <td class="text-right soild-after">
                                @if($account['isconnect']==1)
                                    <a href="javascript:;" data-id="#layer-payment-wechat" title="微信支付配置" onclick="showSet(this)" class="text-blue">接口配置</a>
                                @else
                                    <a href="javascript:;" title="微信支付配置" onclick="layer.msg('未接入微信公众号',{icon:2})" class="text-blue">接口配置</a>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">消息通知</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="200" />
                    </colgroup>
                    <tbody class="layui-form">
                        <tr>
                            <td><span class="fui-table-lable">短信通知</span></td>
                            <td class="soild-after">
                                <input type="checkbox" lay-filter="js-switch" disabled name="notice.sms.switch" lay-skin="switch" lay-text="开启|关闭"{{ $setting['notice']['sms']['switch']==1 ? ' checked' : '' }} />
                            </td>
                            <td class="text-right soild-after">
                                <a href="javascript:;" title="短息通知配置" onclick="layer.msg('敬请期待',{icon:7})" class="text-blue">短信配置</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">邮件通知</span></td>
                            <td class="soild-after">
                                <input type="checkbox" lay-filter="js-switch" disabled name="notice.email.switch" lay-skin="switch" lay-text="开启|关闭"{{ $setting['notice']['email']['switch']==1 ? ' checked' : '' }} />
                            </td>
                            <td class="text-right soild-after">
                                <a href="javascript:;" title="邮件通知配置" onclick="layer.msg('敬请期待',{icon:7})" class="text-blue">邮件配置</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">附件设置</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="200" />
                    </colgroup>
                    <tbody class="layui-form">
                    <tr>
                        <td><span class="fui-table-lable">上传设置</span></td>
                        <td class="soild-after">
                            跟随系统
                        </td>
                        <td class="text-right soild-after">
                            <a href="javascript:;" onclick="layer.msg('敬请期待',{icon:7})" class="text-blue">上传设置</a>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="fui-table-lable">远程附件</span></td>
                        <td class="soild-after">
                            跟随系统
                        </td>
                        <td class="text-right soild-after">
                            <a href="javascript:;" onclick="layer.msg('敬请期待',{icon:7})" class="text-blue">远程附件</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="layer-content">
    @if($account['isconnect']==1)
    <div id="layer-payment-wechat" class="layui-hide">
        <form class="layui-form" method="POST" action="{{ wurl('account/setting',array('uniacid'=>$uniacid)) }}">
            @csrf
            <input type="hidden" name="op" value="save-wechat">
            <div class="layui-form-item must">
                <label class="layui-form-label">微信支付商户号</label>
                <div class="layui-input-block">
                    <input type="text" required lay-verify="required" name="wechat[mchid]" value="{{ $setting['payment']['wechat']['mchid'] }}" placeholder="请输入您的微信支付商户号" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-form-item must">
                <label class="layui-form-label">微信支付密钥</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline" style="width: 70%">
                        <input type="text" id="wechatapikey" required lay-verify="required" name="wechat[apikey]" value="{{ $setting['payment']['wechat']['apikey'] }}" placeholder="请输入您的微信支付密钥" class="layui-input" />
                    </div>
                    <a href="javascript:;" onclick="setRandom(this)" class="layui-btn">随机生成</a>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">保存</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                </div>
            </div>
        </form>
    </div>
    @endif
    <div id="layer-payment-alipay" class="layui-hide">
        <form class="layui-form" method="POST" action="{{ wurl('account/setting',array('uniacid'=>$uniacid)) }}">
            @csrf
            <input type="hidden" name="op" value="save-alipay">
            <div class="layui-form-item must">
                <label class="layui-form-label">支付宝账号</label>
                <div class="layui-input-block">
                    <input type="text" required lay-verify="required" name="alipay[account]" value="{{ $setting['payment']['alipay']['account'] }}" placeholder="请输入您的支付宝收款账号" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-form-item must">
                <label class="layui-form-label">合作者ID</label>
                <div class="layui-input-block">
                    <input type="text" required lay-verify="required" name="alipay[partner]" value="{{ $setting['payment']['alipay']['partner'] }}" placeholder="请输入您的支付宝合作者ID，一般是2088开头的数字" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-form-item must">
                <label class="layui-form-label">接口密匙</label>
                <div class="layui-input-block">
                    <input type="password" required lay-verify="required" name="alipay[secret]" value="{{ $setting['payment']['alipay']['secret'] }}" placeholder="请输入您的支付宝接口密匙，MD5格式的密匙串" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">保存</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('common.footer')

<script type="text/javascript">
    layer.ready(function (){
        var form = layui.form;
        form.on('switch(js-switch)', function (data){
            let cfg = data.elem.name;
            let value = data.elem.checked ? 1 : 0;
            Core.post('console.account.setting',function (res){
                Core.report(res);
            },{
                op:'js-switch',
                name:cfg,
                value:value,
                uniacid:{{ $uniacid }}
            })
        });
    });
    function setRandom(Elem){
        let code = Wrandom(32);
        $(Elem).prev().find('input').val(code);
    }
    function showSet(Elem){
        let title = Elem.title;
        let id = $(Elem).attr('data-id');
        layer.open({
            title:title,
            shade:0.3,
            shadeClose:true,
            type:1,
            content:$(id).html(),
            area: '720px',
            skin:'fui-layer'
        });
    }
</script>
