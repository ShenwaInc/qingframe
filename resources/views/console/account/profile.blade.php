@include('common.header')

<div class="main-content fui-content">

    <h2>{{ __('manageData', array('data'=>__('platform'))) }}</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li class="layui-this">
                <a href="{{ wurl('account/profile',array('uniacid'=>$uniacid)) }}">@lang('basicInformation')</a>
            </li>
            <li>
                <a href="{{ wurl('account/functions',array('uniacid'=>$uniacid)) }}">@lang('Applications&Services')</a>
            </li>
            @if(in_array($role,['founder','owner']) || $_W['isfounder'])
            <li>
                <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}">@lang('operatingAuthority')</a>
            </li>
            @endif
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            @if(in_array($role,['founder','owner', 'manager']) || $_W['isfounder'])
            <a href="{{ wurl('account/edit',array('uniacid'=>$uniacid), true) }}" class="fr text-blue ajaxshow" title="@lang('EditPlatformInformation')"><i class="fa fa-edit"></i></a>
            @endif
            <span class="title">@lang('basicInformation')</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="200" />
                    </colgroup>
                    <tbody>
                    <tr>
                        <td><span class="fui-table-lable">{{ __('IDofData', array('data'=>__('platform'))) }}</span></td>
                        <td class="soild-after">{{ $uniacid }}&nbsp;&nbsp;<a href="javascript:;" data-url="{{ $uniacid }}" class="text-blue js-clip"><i class="fa fa-copy"></i></a></td>
                        <td class="text-right soild-after">
                            <a href="javascript:;" data-url="{{ url("login/".$account['uniacid']) }}" class="text-blue js-clip">@lang('copyPlatformEntry')</a>
                        </td>
                    </tr>
                        <tr>
                            <td><span class="fui-table-lable">{{ __('nameOfData', array('data'=>__('platform'))) }}</span></td>
                            <td class="soild-after">{{ $account['name'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('platformLOGO')</span></td>
                            <td class="soild-after">
                                <img class="radius" src="{{ tomedia($account['logo']) }}" width="120" />
                            </td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('platformIntroduction')</span></td>
                            <td class="soild-after">{{ $account['description'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('interfaceFile')</span></td>
                            <td class="soild-after"><span class="text-gray">@lang('interfaceFileRemain')</span></td>
                            <td class="text-right soild-after">
                                <a href="javascript:" class="text-blue js-api-verify">@lang('upload')</a>
                            </td>
                        </tr>
                        @if($_W['isfounder'])
                        <tr>
                            <td><span class="fui-table-lable">@lang('expireDate')</span></td>
                            <td class="soild-after">
                                <span id="expiretext">{{ $account['expirdate'] }}</span>
                            </td>
                            <td class="text-right soild-after">
                                <span style="position: relative;">
                                    <a href="javascript:;" class="text-blue">@lang('chooseDate')</a>
                                    <input type="text" id="expirdate" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" name="expire" value="" />
                                </span>
                                @if($account['endtime']>0)
                                <a href="javascript:setForever();" class="text-red margin-left-sm">@lang('longtime')</a>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="fui-table-lable">@lang('defaultEntry')</span></td>
                            <td class="soild-after">
                                <span id="expiretext">{!! $entrance !!}</span>
                            </td>
                            <td class="text-right soild-after">
                                <a href="{{ wurl('account/entry',array('uniacid'=>$uniacid)) }}" title="{{ __('modifyData', array('data'=>__('defaultEntry'))) }}" class="ajaxshow text-blue">@lang('modify')</a>
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
        var laydate = layui.laydate, upload = layui.upload;
        @if($_W['isfounder'])
        laydate.render({
            elem:"#expirdate",
            format:"yyyy-MM-dd",
            value:"{{ $account['endtime']>0 ? date('Y-m-d',$account['endtime']) : date('Y-m-d') }}",
            done:function (value, date, endDate){
                $('#expiretext').text(value);
                setExpire(value);
            }
        });
        @endif
        @if(in_array($role,['founder','owner', 'manager']) || $_W['isfounder'])
        upload.render({
            elem: '.js-api-verify'
            ,url: '{{ url("console/account/apiverify") }}'
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
    @if($_W['isfounder'])
    function setExpire(expiredata=''){
        Core.post('console.account.profile',function (res){
            Core.report(res);
        },{expire:expiredata,op:"setexpire",uniacid:{{ $uniacid }}},'json',true)
    }
    function setForever(){
        Core.confirm('@lang("modifyExpireDateConfirm")',function (){
            $('#expiretext').text('@lang("longtime")');
            setExpire('');
        },false,{title:'{{ __("modifyData", array("data"=>__("expireDate"))) }}'})
    }
    @endif
</script>
