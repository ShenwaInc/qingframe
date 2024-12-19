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
    .per-list-block .block-head{width: 15%;border: 1px solid #e2e2e2;border-right:none;min-width: 200px }
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
                            <div class='per-title'>
                                <div>{{ __('permissions', array('operation'=>__('application'))) }}</div>
                            </div>
                            @foreach($modulesList as $value)
                            <div class='per-list-block'>
                                <div class='block-head'>
                                    <div class='block-head-title'>
                                        <img width='50' alt="{{ $value['title'] }}" class="radius" src="{{ tomedia($value['logo']) }}">
                                        <span>{{ $value['title'] }}</span>
                                    </div>
                                </div>
                                <div class='block-cont'>
                                    <table class="layui-table">
                                        <tbody>
                                        @foreach($value['permissions'] as $val)
                                            <tr>
                                                <td style='width: 120px'>
                                                 <input type="checkbox" class='fj' name="routes[modules][{{$value['name']}}][]" value='{{$val['route']}}' title="{{$val['name']}}" lay-skin="primary"  lay-filter='parent' @if($val['exist']) checked @endif>
                                                </td>
                                                <td class="son-perm">
                                                    @foreach($val['subPerm'] as $va)
                                                    <input type="checkbox"  onclick='myOne(this)' name="routes[modules][{{$value['name']}}][]" value='{{$val['route']}}.{{$va['route']}}' title="{{$va['name']}}" lay-skin="primary" lay-filter='son'  @if($va['exist']) checked @endif>
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
                                        <img width='50' alt="{{ $value['title'] }}" class="radius" src="{{ tomedia($value['cover']) }}">
                                        <span>{{ $value['title'] }}</span>
                                    </div>
                                </div>
                                <div class='block-cont'>
                                    <table class="layui-table">
                                        <tbody>
                                        @foreach($value['perms'] as $val)
                                            <tr>
                                                <td style='width: 120px'>
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
            console.log(totals, checks);
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
    }
</script>
@include('common.footer')
