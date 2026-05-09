@include('common.header')

<div class="main-content unpadding">

    <h2 class="layui-hide-layer">
        <a href="javascript:window.history.go(-1);" class="pull-right fr layui-btn layui-btn-primary">@lang('back')</a>
        {{ $title }}
    </h2>

    <div class="fui-card layui-card @if(!$_W['isajax']) margin-top-xl @endif">
        <div class="layui-card-header nobd layui-hide-layer">
            <a href="{{ wurl('account/role',array('uniacid'=>$uniacid,'op'=>'add'),true) }}" data-width="750" title="{{ __('newData', array('data'=>__('platformOperator'))) }}" class="fr layui-btn layui-btn-sm layui-btn-normal ajaxshow">{{ __('newData', array('data'=>__('operator'))) }}</a>
            <span class="title">@lang('操作权限')</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="250" />
                        <col class="layui-hide-xs" />
                        <col width="200" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th>@lang('operator')</th>
                        <th class="layui-hide-xs">@lang('操作权限')</th>
                        <th style="text-align: right">@lang('action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $key=>$value)
                        <tr>
                            <td><span{{ $value['expired'] ? ' class=text-gray' : '' }}>{{ $value['username'] }}</span>&nbsp;<span class="layui-badge layui-bg-{{ $colors[$value['role']] }}">{{ $value['roler'] }}</span></td>
                            <td class="layui-hide-xs">{{  __(empty($value['permission'])?'allPermissions':'partialPermissions') }}</td>
                            <td class="text-right">
                                @if($value['role']=='owner')
                                    @php
                                    $ownerUid = $value['uid'];
                                    @endphp
                                    @if($_W['isfounder'])
                                        <a href="javascript:;" onclick="showWindow(this)" data-id="#role-setowner" title="@lang('switchOwner')" class="text-blue">@lang('modify')</a>
                                    @endif
                                @else
                                    <a href="{{ wurl('account/permission',array('uniacid'=>$uniacid,'uid'=>$value['uid']),true) }}" class="text-blue margin-right-sm">@lang('modify')</a>
                                    <a href="{{ wurl('account/role',array('uniacid'=>$uniacid,'uid'=>$value['uid'],'op'=>'remove'),true) }}" class="text-red confirm" data-text="@lang('removeOperatorConfirm')">@lang('delete')</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if(empty($users))
                        <tr>
                            <td colspan="3" class="text-center">@lang('empty')</td>
                        </tr>
                    @elseif($_W['isajax'])
                        <tr>
                            <td colspan="3" class="text-center">
                                <a href="{{ wurl('account/role',array('uniacid'=>$uniacid,'op'=>'add'),true) }}" data-width="750" title="{{ __('newData', array('data'=>__('platformOperator'))) }}" class="layui-btn layui-btn-normal ajaxshow">{{ __('newData', array('data'=>__('operator'))) }}</a>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="layer-content">
    <div class="layui-hide" id="role-setowner">
        <form class="layui-form" style="min-height: 360px" method="POST" action="{{ wurl('account/role',array('uniacid'=>$uniacid,'op'=>'setowner')) }}">
            @csrf
            <div class="layui-form-item">
                <label class="layui-form-label">{{ __('chooseData', array('data'=>__('user'))) }}</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline" style="width: 70%">
                        <select name="uid" lay-search required lay-verify="required" data-uid="{{ $ownerUid }}">
                            <option value="">{{ __('type&search', array('data'=>__('username'))) }}</option>
                            <option value="{{ $_W['uid'] }}" @if($ownerUid==$_W['uid']) disabled @endif>{{ $_W['user']['username'] }}</option>
                            @foreach($subusers as $sub)
                                <option value="{{ $sub['uid'] }}" @if($ownerUid==$sub['uid']) disabled @endif>{{ $sub['username'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ wurl('user/create',array('uid'=>0),true) }}" class="layui-btn ajaxshow">{{ __('newData', array('data'=>__('user'))) }}</a>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">@lang('save')</button>
                    <button type="reset" class="layui-btn layui-btn-primary">@lang('reset')</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('common.footer')
<script type="text/javascript">
    function showWindow(Elem){
        let title = Elem.title;
        let id = $(Elem).attr('data-id');
        layer.open({
            title:title,
            shade:0.3,
            shadeClose:true,
            type:1,
            content:$(id).html(),
            area: '720px',
            skin:'fui-layer',
            success: function(layero, index){
                let filter = 'layform-' + Wrandom(8);
                let Obj = $(layero);
                Obj.find('form.layui-form').attr('lay-filter',filter);
                FormInit(filter);
                EventInit(Obj);
            }
        });
    }
</script>

