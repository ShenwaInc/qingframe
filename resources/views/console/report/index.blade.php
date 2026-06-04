@include('common.header')
<div class="layui-fluid">
    <div class="main-content fui-content">
        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="{{ wurl('report/post') }}" class="fr layui-btn layui-btn-normal ajaxshow">{{ __('newData', array('data'=>__('workOrder'))) }}</a>
                <span class="title">{{ $title }}</span>
            </div>
            <div class="layui-card-body">
                <div style="min-height: 400px;">
                    <table class="layui-table" lay-skin="nob" lay-even>
                        <thead>
                        <tr>
                            <th class="layui-hide-xs">@lang('WorkOrderID')</th>
                            <th>@lang('content')</th>
                            <th class="layui-hide-xs">@lang('state')</th>
                            <th>@lang('releaseTime')</th>
                            <th class="text-right">@lang('action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $key=>$value)
                            <tr>
                                <td class="layui-hide-xs">
                                    {{ $value['ordersn'] }}&nbsp;<i class="fa fa-copy text-blue js-clip" data-url="{{ $value['ordersn'] }}"></i>
                                </td>
                                <td>
                                    <a href="{{ wurl('report/detail', array('id'=>$value['id'])) }}" class="text-blue ajaxshow">{{ $value['title'] }}</a>
                                </td>
                                <td class="layui-hide-xs"><span class="layui-badge layui-bg-{{ $value['status']>7?'gray':$badges[$value['status']] }}">{{ $value['status']==4?'验证中':$value['statusName'] }}</span></td>
                                <td>{{ $value['created_at'] }}</td>
                                <td class="text-right">
                                    @if($value['status']<6)
                                        <a class="text-blue ajaxshow" href="{{ wurl('report/feedback', array('id'=>$value['id'])) }}">@lang('feedback')</a>&nbsp;
                                        <a class="text-green confirm ajaxshow" data-text="@lang('workOrderComplete')" href="{{ wurl('report/Complete', array('id'=>$value['id'])) }}">@lang('done')</a>&nbsp;
                                        <a class="text-red confirm layui-hide ajaxshow" data-text="@lang('workOrderClose')" href="{{ wurl('report/closeOrder', array('id'=>$value['id'])) }}">@lang('close')</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($list))
                            <tr><td colspan="5" class="text-muted text-center">@lang('empty')</td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="row" style="padding-bottom: 15px;">
                    <div class="layui-col-md6">{{ __('recordsFound', array('row'=>$total)) }}</div>
                    <div class="layui-col-md6 text-right">{!! $pager !!}</div>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    function delAttach(Elem){
        let file = $(Elem).prev().prev().val();
        $('.upload-btn').removeClass('layui-hide');
        $(Elem).parent().parent().remove();
        Core.get('{{ wurl("report/rmAttach") }}', function (res) {
            console.log(res);
        }, {file:file});
    }
</script>
@include('common.footer')
