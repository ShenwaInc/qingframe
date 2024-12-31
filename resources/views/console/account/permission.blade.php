@include('common.header')
<style>
    .title{display: flex;justify-content: space-between}
    .title h2{font-size: 26px;font-weight: 400;line-height: 1;margin-bottom: 20px;}
    .title a{font-size: 16px;cursor:pointer;color: #898989}
    .permissions{min-height: 220px}
    .per-title{background: #f1eeee;padding: 10px;color: #8b8989;display: flex;justify-content: space-between;border-radius:3px}
    .per-title a{color: #1E9FFF}
    .per-list-block{display: flex;margin: 10px 0}
    .per-list-block:first-child{padding-top: 10px}
    .per-list-block .block-head{width: 20%;border: 1px solid #e2e2e2;border-right:none;min-width: 200px }
    .per-list-block .block-head .block-head-title{position: relative;top: 50%;margin-top: -25px;left: 12%}
    .block-cont{min-height: 80px;width: 85%;display: flex;}
    table{margin: 0!important;}
</style>

@if(!$_W['isajax'])
    <div class="main-content">
        <div class='title'>
            <h2>@lang('操作权限')</h2>
            <a href='javascript:history.back()'>@lang('back')</a>
        </div>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                <div class="un-padding">
                    @endif
                    <form class="layui-form" method="POST" action="{{ wurl('account/permission',array('uniacid'=>$uniacid,'uid'=>$uid)) }}">
                        @csrf
                        <input type="hidden" name="redirect" value="{{ referer() }}">
                        <div class="layui-form-item permissions">
                            <div class="per-title">
                                <div>{{ __('permissions', array('operation'=>__('application'))) }}</div>
                            </div>
                            @foreach($modulesList as $value)
                            <div class="per-list-block">
                                <div class="block-head">
                                    <div class="block-head-title text-cut">
                                        <img width="50" alt="{{ $value['title'] }}" class="radius" src="{{ tomedia($value['logo']) }}">
                                        <span>{{ $value['title'] }}</span>
                                    </div>
                                </div>
                                <div class="block-cont">
                                    <table class="layui-table">
                                        <colgroup>
                                            <col width="120">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <input type="checkbox" name="perms[modules][{{$value['name']}}]" lay-filter="modulePerm" data-id="{{$value['name']}}" title="@lang('开启权限')|@lang('关闭权限')" @if(!empty($value['hasPerm'])) checked @endif lay-skin="switch">
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody id="modulePerms_{{ $value['name'] }}" @if(empty($value['hasPerm'])) class="layui-hide" @endif>
                                        @foreach($value['permissions'] as $val)
                                            <tr>
                                                <td>
                                                 <input type="checkbox" class="fj @if($val['indeterminate'])indeterminate @endif" name="routes[modules][{{$value['name']}}][]" value="{{$val['route']}}" title="{{$val['name']}}" lay-skin="primary"  lay-filter="parent" @if($val['exist']) checked @endif>
                                                </td>
                                                <td class="son-perm">
                                                    @foreach($val['subPerm'] as $va)
                                                    <input type="checkbox"  onclick="myOne(this)" name="routes[modules][{{$value['name']}}][]" value='{{$val['route']}}.{{$va['route']}}' title="{{$va['name']}}" lay-skin="primary" lay-filter="son"  @if($va['exist']) checked @endif>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="layui-form-item permissions">
                            <div class='per-title'>
                                <div>{{ __('permissions', array('operation'=>__('service'))) }}</div>
                            </div>
                            @foreach($serversList as $value)
                            <div class='per-list-block'>
                                <div class='block-head'>
                                    <div class='block-head-title'>
                                        <img width='50' alt="{{ $value['title'] }}" class="radius" src="{{ globalMedia($value['cover']) }}">
                                        <span>{{ $value['title'] }}</span>
                                    </div>
                                </div>
                                <div class='block-cont'>
                                    <table class="layui-table">
                                        <colgroup>
                                            <col width="120">
                                            <col>
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <input type="checkbox" name="perms[servers][{{$value['name']}}]" lay-filter="serverPerm" data-id="{{$value['name']}}" title="@lang('开启权限')|@lang('关闭权限')" @if(!empty($value['hasPerm'])) checked @endif lay-skin="switch">
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody id="serverPerms_{{ $value['name'] }}" @if(empty($value['hasPerm'])) class="layui-hide" @endif>
                                        @foreach($value['perms'] as $val)
                                            <tr>
                                                <td>
                                                 <input type="checkbox" class='fj' name="routes[servers][{{$value['name']}}][]" value='{{$val['route']}}' title="{{$val['name']}}" lay-skin="primary"  lay-filter='parent' @if($val['exist']) checked @endif>
                                                </td>
                                                <td>
                                                    @foreach($val['subPerm'] as $va)
                                                        <input type="checkbox"  onclick='myOne(this)' name="routes[servers][{{$value['name']}}][]" value='{{$va['route']}}' title="{{$va['name']}}" lay-skin="primary" lay-filter='son'  @if($va['exist']) checked @endif>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="layui-form-item">
                            <div class="text-center">
                                <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">@lang('save')</button>
                            </div>
                        </div>
                    </form>
                    @if(!$_W['isajax'])
                </div>
            </div>
        </div>
    </div>
@endif
<script type="text/javascript">
    function FormRender(form) {
        form.on('checkbox(parent)', function(data){
            if(data.elem.checked===true){
                data.othis.parent().next().find('input').prop('checked',true)
            }else{
                data.othis.parent().next().find('input').prop('checked',false)
            }
            form.render();
        });
        form.on('checkbox(son)', function(data){
            let Elem = $(data.elem);
            let totals = Elem.parent('.son-perm').find('input[type="checkbox"]').length;
            let checks = Elem.parent('.son-perm').find('input[type="checkbox"]:checked').length;
            if(checks === 0){
                //全部取消
                Elem.parent().prev().find('input[type="checkbox"]').prop('checked', false);
                Elem.parent().prev().find('input[type="checkbox"]').prop('indeterminate', false);
            }else if(totals === checks){
                //全选
                Elem.parent().prev().find('input[type="checkbox"]').prop('indeterminate', false);
                Elem.parent().prev().find('input[type="checkbox"]').prop('checked', true)
            }else{
                //半选
                Elem.parent().prev().find('input[type="checkbox"]').prop('checked', false)
                Elem.parent().prev().find('input[type="checkbox"]').prop('indeterminate', true);
            }
            form.render('checkbox');
        });
        form.on('switch(modulePerm)', function (data) {
            let Elem = $(data.elem);
            let id = "#modulePerms_" + Elem.data('id');
            if(data.elem.checked){
                $(id).removeClass('layui-hide').find('input[type="checkbox"]').prop('disabled', false);
            }else{
                $(id).addClass('layui-hide').find('input[type="checkbox"]').prop('disabled', true);
            }
            form.render('checkbox');
        });
        form.on('switch(serverPerm)', function (data) {
            let Elem = $(data.elem);
            let id = "#serverPerms_" + Elem.data('id');
            if(data.elem.checked){
                $(id).removeClass('layui-hide').find('input[type="checkbox"]').prop('disabled', false);
            }else{
                $(id).addClass('layui-hide').find('input[type="checkbox"]').prop('disabled', true);
            }
            form.render('checkbox');
        });
        let indeterminate = $('input.indeterminate');
        if(indeterminate.length > 0){
            indeterminate.each(function (i, Elem) {
                $(Elem).prop('indeterminate', true);
            });
            form.render('checkbox');
        }

    }
</script>
@include('common.footer')
