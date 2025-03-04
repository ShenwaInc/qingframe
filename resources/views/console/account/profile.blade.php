@include('common.header')

<div class="main-content fui-content">

    <h2 class="layui-hide">{{ __('manageData', array('data'=>__('platform'))) }}</h2>

    <div class="layui-tab fui-tab margin-bottom-xl layui-hide">
        <ul class="layui-tab-title title_tab">
            <li class="layui-this">
                <a href="{{ wurl('account/profile',array('uniacid'=>$uniacid)) }}">@lang('基础信息')</a>
            </li>
            <li>
                <a href="{{ wurl('account/functions',array('uniacid'=>$uniacid)) }}">@lang('应用与服务')</a>
            </li>
            @if(in_array($role,['founder','owner']) || $_W['isfounder'])
            <li>
                <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}">@lang('操作权限')</a>
            </li>
            @endif
        </ul>
    </div>

    <div class="layui-row layui-col-space20 fui-flex fui-flex-stretch">
        <div class="layui-col-md6 layui-col-sm12">
            <div class="fui-card layui-card" style="height: 100%;">
                <div class="layui-card-header nobd">
                    @if($_W['isfounder'] || $role=='founder')
                        <a href="{{ wurl('account/modules',array('uniacid'=>$uniacid), true) }}" class="fr text-blue ajaxshow" title="{{ __('manageData', array('data'=>__('application'))) }}">@lang('manage')</a>
                    @endif
                    <span class="title">@lang('application')</span>
                </div>
                <div class="layui-card-body">
                    @if(empty($components))
                        <div class="fui-empty text-center" style="line-height: 150px;">
                            <span class="text-gray" style="font-size: 16px;">@lang('NoAppsAvailable')</span>
                        </div>
                    @else
                        <div class="layui-row layui-col-space15 fui-list card">
                            @foreach($components as $item)
                                <div class="layui-col-lg6 layui-col-sm12 fui-item arrow">
                                    <a target="_blank" href="{{ wurl("m/".$item['identity']) }}" class="fui-content margin-0">
                                        <div class="fui-info">
                                            <img alt="{{ $item['name'] }}" class="radius" src="{{ $item['logo'] }}" />
                                            <strong class="card-name">@lang($item['name'])</strong>
                                        </div>
                                    </a>
                                    @if($_W['isfounder'])
                                        <a class="js-dropdown" target="_blank" href="{{ wurl("m/".$item['identity']."/system_setting") }}">
                                            <span class="layui-icon layui-icon-set text-blue"></span>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="layui-col-md6 layui-col-sm12">
            <div class="fui-card layui-card">
                <div class="layui-card-header nobd">
                    @if(in_array($role,['founder','owner', 'manager']) || $_W['isfounder'])
                        <a href="{{ wurl('account/edit',array('uniacid'=>$uniacid), true) }}" class="fr text-blue ajaxshow" title="@lang('EditPlatformInformation')">@lang('edit')</a>
                    @endif
                    <span class="title">@lang('平台信息')</span>
                </div>
                <div class="layui-card-body">
                    <div class="un-padding">
                        <table class="layui-table fui-table lines" lay-skin="nob">
                            <colgroup>
                                <col width="100" />
                                <col />
                                <col width="150" />
                            </colgroup>
                            <tbody>
                            <tr>
                                <td><span class="fui-table-lable">{{ __('IDofData', array('data'=>__('platform'))) }}</span></td>
                                <td class="soild-after">{{ $uniacid }}&nbsp;&nbsp;<a href="javascript:;" data-url="{{ $uniacid }}" class="text-gray js-clip"><i class="fa fa-copy"></i></a></td>
                                <td class="text-right soild-after">
                                    <a href="javascript:;" data-url="{{ $_W['siteroot']."login/".$account['uniacid'] }}" class="text-blue js-clip">@lang('copyPlatformEntry')</a>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="fui-table-lable">{{ __('nameOfData', array('data'=>__('platform'))) }}</span></td>
                                <td class="soild-after">{{ __($account['name']) }}</td>
                                <td class="text-right soild-after">
                                    <span id="expiretext" class="text-gray">{{ $account['expirdate'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="fui-table-lable">@lang('platformLOGO')</span></td>
                                <td class="soild-after">
                                    <img class="radius" src="{{ globalMedia($account['logo']) }}" width="120" />
                                </td>
                                <td class="text-right soild-after"></td>
                            </tr>
                            @if($account['description'])
                            <tr>
                                <td><span class="fui-table-lable">@lang('platformIntroduction')</span></td>
                                <td class="soild-after">{{ $account['description'] }}</td>
                                <td class="text-right soild-after"></td>
                            </tr>
                            @endif
                            <tr>
                                <td>
                                    <span class="fui-table-lable">@lang('平台工具')</span>
                                </td>
                                <td colspan="2">
                                    <div class="layui-row layui-col-space20">
                                        <div class="fui-icon-list">
                                            <a href="{{ wurl('account/entry',array('uniacid'=>$uniacid)) }}" title="{{ __('modifyData', array('data'=>__('默认入口'))) }}" class="ajaxshow">
                                                <div class="fui-icon-item">
                                                    <span class="layui-icon-console layui-icon"></span>
                                                </div>
                                                @lang('默认入口')
                                            </a>
                                        </div>
                                        <div class="fui-icon-list" lay-tips="@lang('interfaceFileRemain')">
                                            <a href="javascript:" class="js-api-verify" title="@lang('接口文件')">
                                                <div class="fui-icon-item">
                                                    <span class="layui-icon-upload layui-icon"></span>
                                                </div>
                                                @lang('接口文件')
                                            </a>
                                        </div>
                                        @if(in_array($role,['founder','owner']) || $_W['isfounder'])
                                        <div class="fui-icon-list">
                                            <a href="javascript:setDomain('{{$settings['bind_domain']}}');" title="@lang('绑定域名')">
                                                <div class="fui-icon-item js-domain @if(!empty($settings['bind_domain'])) selected @endif">
                                                    <span class="layui-icon-website layui-icon"></span>
                                                </div>
                                                @lang('绑定域名')
                                            </a>
                                        </div>
                                        <div class="fui-icon-list">
                                            <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}" class="ajaxshow" title="@lang('操作权限')">
                                                <div class="fui-icon-item">
                                                    <span class="layui-icon-group layui-icon"></span>
                                                </div>
                                                @lang('操作权限')
                                            </a>
                                        </div>
                                        @endif
                                        @if($_W['isfounder'])
                                            <div class="fui-icon-list" lay-tips="@lang('清空表示设为长期有效')">
                                                <a href="javascript:;">
                                                    <div class="fui-icon-item">
                                                        <span class="layui-icon-date layui-icon"></span>
                                                    </div>
                                                    @lang('expireDate')
                                                </a>
                                                <input type="text" id="expirdate" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" name="expire" title="@lang('expireDate')" value="" />
                                            </div>
                                            <div class="fui-icon-list">
                                                <a href="https://www.yuque.com/shenwa/qingru/wq6gs0omqb3gb82h" target="_blank" title="@lang('使用指南')">
                                                    <div class="fui-icon-item">
                                                        <span class="layui-icon-about text-orange layui-icon"></span>
                                                    </div>
                                                    @lang('使用指南')
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fui-card layui-card margin-top-lg">
        <div class="layui-card-header nobd">
            <span class="title">@lang('功能与服务')</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15 fui-list card">
                @foreach($servers as $value)
                    <div class="layui-col-lg3 layui-col-md4 layui-col-sm6 layui-col-xs12 fui-item fui-item-sm arrow">
                        <a target="_blank" href="{{ $value['entrance'] }}" title="@lang('manage')" class="fui-content">
                            <div class="fui-info">
                                <img alt="@lang($value['title'])" class="radius" src="{{ assets($value['cover']) }}" />
                                <strong class="card-name">@lang($value['title'])</strong>
                                <p class="text-cut">@lang($value['summary'])</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

<style>
    .fui-icon-list{float: left; padding: 10px; position: relative; max-width: 120px; box-sizing: border-box;}
    .fui-icon-list a{display: block; width: 100%; height: 100%; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
    .fui-icon-item{border: 1px solid #EEEEEE; border-radius: 15px; width: 45px; height: 45px; line-height: 45px; padding: 5px; margin: 0 auto 8px; overflow: hidden; position: relative;}
    .fui-icon-item.selected:before{content: ''; width: 0; height: 0; border-bottom: 34px solid #2ABA8E; border-left: 34px solid transparent; position: absolute; display: block; right: -1px; bottom: -1px}
    .fui-icon-item.selected:after{position: absolute; font-family: layui-icon!important; content: "\e605"; right: 0; bottom: 0; font-size: 16px; font-weight: bold; color: #fff; line-height: 24px;}
    .fui-icon-item .layui-icon{font-size: 32px;}
</style>

@include('common.footer')


<script type="text/javascript">
    layer.ready(function (){
        var laydate = layui.laydate, upload = layui.upload;
        @if($_W['isfounder'])
        laydate.render({
            elem:"#expirdate",
            format:"yyyy-MM-dd",
            value:"{{ $account['endtime']>0 ? date('Y-m-d',$account['endtime']) : date('Y-m-d') }}",
            done:function (value, date, endDate){
                $('#expiretext').text(value);
                setExpire(value);
            },
            lang:"{{ $_W['locale']=='zh' ? 'cn' : 'en' }}"
        });
        @endif
        @if(in_array($role,['founder','owner', 'manager']) || $_W['isfounder'])
        upload.render({
            elem: '.js-api-verify'
            ,url: '{{ wurl("account/apiverify") }}'
            ,accept:'file'
            ,acceptMime:'text/plain'
            ,exts:"txt"
            ,data:{_token:"{{ csrf_token() }}"}
            ,done:function (res, index, upload){
                Core.report(res);
            }
        });
        @endif
    });
    @if(in_array($role,['founder','owner']) || $_W['isfounder'])
        function setDomain(domain='') {
            let options = {title: '{{ __('请输入要绑定的域名') }}', value: domain, maxlength:50, btn:['@lang("确定")', '@lang("取消")'], placeholder:"{{ __('仅支持单个域名，只填写host部分') }}"};
            if (domain && domain!==""){
                options.btn = ['@lang("确定")', '@lang("解除绑定")', '@lang("取消")'];
                options.btn2 = function (index) {
                    layer.confirm('@lang("确定要解除绑定吗？")', {icon: 3, title:'@lang("解除域名绑定")'}, function (e) {
                        Core.post('console.account.profile',function (res){
                            Core.report(res);
                            if(res.type==='success'){
                                $('.js-domain').removeClass('selected');
                                window.location.reload();
                            }else{
                                layer.close(e);
                            }
                        },{domain:"",op:"setDomain",uniacid:{{ $uniacid }}},'json',true)
                    }, function () {
                        layer.close(index);
                    });
                    return false;
                };
            }
            layer.prompt(options, function(value, index, elem){
                if(value === '') return elem.focus();
                let regex = /^(?:[a-zA-Z0-9_-]+\.)*[a-z]{2,6}$/;
                if(!regex.test(value)){
                    layer.msg('{{ __('请输入正确格式的域名') }}', {icon:2});
                    return elem.focus();
                }
                Core.post('console.account.profile',function (res){
                    Core.report(res);
                    if(res.type==='success'){
                        $('.js-domain').addClass('selected');
                        layer.close(index);
                        window.location.reload();
                    }else{
                        elem.focus();
                    }
                },{domain:value,op:"setDomain",uniacid:{{ $uniacid }}},'json',true)
            });
        }
    @endif
    @if($_W['isfounder'])
    function setExpire(expired){
        if(expired===''){
            return setForever();
        }
        Core.post('console.account.profile',function (res){
            Core.report(res);
        },{expire:expired,op:"setExpire",uniacid:{{ $uniacid }}},'json',true)
    }
    function setForever(){
        Core.confirm('@lang("modifyExpireDateConfirm")',function (){
            $('#expiretext').text('@lang("长期")');
            Core.post('console.account.profile',function (res){
                Core.report(res);
            },{expire:'',op:"setExpire",uniacid:{{ $uniacid }}},'json',true)
        },false,{title:'{{ __("modifyData", array("data"=>__("expireDate"))) }}'})
    }
    @endif
</script>
