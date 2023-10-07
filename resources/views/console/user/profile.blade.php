@include('common.header')
<script type="text/javascript">
    function setAvatar(attach, layIndex){
        $('.user-avatar').attr('src',attach.url);
        layer.close(layIndex);
        Core.post('{{ wurl("user/setAvatar") }}', function (res) {
            if(res.type!=='success') return Core.report(res);
            layer.msg('@lang("successful")',{icon:1});
        }, {path:attach.path});
    }
</script>

<div class="main-content">

    <h2 class="weui-desktop-page__title">@lang('accountManagement')</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li class="layui-this">
                <a href="javascript:;">@lang('personalInformation')</a>
            </li>
            <li>
                <a href="{{ wurl('user/subuser') }}">@lang('subAccount')</a>
            </li>
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">@lang('accountManagement')</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="100" />
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><span class="fui-table-lable">@lang('username')</span></td>
                            <td class="soild-after">{{ $_W['user']['username'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('avatar')</span></td>
                            <td class="soild-after">
                                <img class="radius user-avatar" src="{{ tomedia($profile['avatar']) }}" width="72" />
                            </td>
                            <td class="text-right soild-after">
                                <a href="javascript:" class="text-blue" onclick="Core.StoragePicker(this, false, setAvatar)" title="{{ __('modifyData', array('data'=>__('avatar'))) }}">@lang('modify')</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('password')</span></td>
                            <td class="soild-after"> ******** @if($_W['user']['register_type']==1)<span class="layui-badge">@lang('初始密码很不安全')</span>@endif</td>
                            <td class="text-right soild-after">
                                <a href="{{ url('console/user/passport') }}" class="text-blue ajaxshow" title="{{ __('modifyData', array('data'=>__('password'))) }}">@lang('modify')</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@include('common.footer')
