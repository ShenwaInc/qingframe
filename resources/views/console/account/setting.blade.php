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
                    <a href="{{ wurl('account/component',array('uniacid'=>$uniacid)) }}">应用权限</a>
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
                                <a href="javascript:;" class="text-blue">接口配置</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">微信支付</span></td>
                            <td class="soild-after">
                                <input type="checkbox" lay-filter="js-switch" name="payment.wechat.pay_switch" lay-skin="switch" lay-text="开启|关闭"{{ $setting['payment']['wechat']['pay_switch']==1 ? ' checked' : '' }} />
                            </td>
                            <td class="text-right soild-after">
                                <a href="javascript:;" class="text-blue">接口配置</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
</script>
