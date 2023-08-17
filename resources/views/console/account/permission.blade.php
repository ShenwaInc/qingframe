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
            <h2>@lang('operatingAuthority')</h2>
            <a href='javascript:;' onClick="javascript:history.back()">@lang('back')</a>
        </div>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                <div class="un-padding">
                    @endif
                    <form class="layui-form" method="POST" action="{{ wurl('account/permission',array('uniacid'=>$uniacid,'uid'=>$uid)) }}">
                        @csrf
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
                                                <td>
                                                    @foreach($val['subPerm'] as $va)
                                                    <input type="checkbox"  onclick='myOne(this)' name="routes[modules][{{$value['name']}}][]" value='{{$va['route']}}' title="{{$va['name']}}" lay-skin="primary" lay-filter='son'  @if($va['exist']) checked @endif>
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
                            <div style='float: right'>
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
    layui.use(['form'],function (){
        var form = layui.form;
        form.on('checkbox(parent)', function(data){
            if(data.elem.checked===true){
                data.othis.parent().next().find('input').prop('checked',true)
            }else{
                data.othis.parent().next().find('input').prop('checked',false)
            }
            form.render();
        });
        form.on('checkbox(son)', function(data){
            if(data.elem.checked===true){
                data.othis.parent().prev().find('input').prop('checked',true)
            }else{
                let make=false;
                data.othis.parent('td').find('input').each(function(){
                    if(($(this).prop('checked')) === true){
                        make=true;
                    }
                })
                if (make===false){
                    data.othis.parent().prev().find('input').prop('checked',false)
                }
            }
            form.render();
        });
    });
</script>
@include('common.footer')
