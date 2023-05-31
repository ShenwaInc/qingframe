@include('common.header')
<div class="layui-fluid">
    <div class="@if(empty($_GPC['inajax'])) main-content @endif">
        <div class="fui-card layui-card unpadding">
            @if(!$_W['isajax'])
            <div class="layui-card-header nobd">
                <a href="" class="fr text-blue ajaxshow" title="编辑站点信息"><i class="fa fa-edit"></i></a>
                <span class="title">{{ $title }}</span>
            </div>
            @endif
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-skin="nob">
                        <colgroup>
                            <col width="120"/>
                            <col/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td><span class="fui-table-lable">工单号</span></td>
                            <td class="soild-after">{{ $orderInfo['ordersn'] }}&nbsp;<span class="text-blue js-clip" data-url="{{ $orderInfo['ordersn'] }}" style="cursor: pointer;">复制</span></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">工单内容</span></td>
                            <td class="soild-after" style="white-space: pre-wrap">{{ $orderInfo['content'] }}</td>
                        </tr>
                        @if(!empty($orderInfo['secret']))
                        <tr>
                            <td><span class="fui-table-lable">机密信息</span></td>
                            <td class="soild-after" id="orderSecret">
                                <div class="order-secret">{{ $orderInfo['secret'] }}</div>
                                <span class="text-blue js-secret" style="cursor: pointer;">展开</span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td><span class="fui-table-lable">相关附件</span></td>
                            <td class="soild-after">
                                @if(!empty($orderInfo['fileList']))
                                    @foreach($orderInfo['fileList'] as $value)
                                        <p><a href="{{ $value['url'] }}" class="text-blue" target="_blank">{{ $value['name'] }}</a></p>
                                    @endforeach
                                @else
                                    暂无
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">提交时间</span></td>
                            <td class="soild-after">{{ $orderInfo['created_at'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">工单状态</span></td>
                            <td class="soild-after">{{ $orderInfo['statusName'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable"></span></td>
                            <td class="soild-after text-right">
                                @if($orderInfo['status']<6)
                                    <a class="layui-btn layui-btn-normal ajaxshow" href="{{ wurl('report/feedback', array('id'=>$orderInfo['id'])) }}">补充反馈</a>
                                    <a class="layui-btn confirm ajaxshow" data-text="确定此工单已完成验收吗？" href="{{ wurl('report/Complete', array('id'=>$orderInfo['id'])) }}">完成工单</a>
                                    <a class="layui-btn layui-btn-warm confirm ajaxshow" data-text="确定要关闭该工单吗？" href="{{ wurl('report/closeOrder', array('id'=>$orderInfo['id'])) }}">关闭工单</a>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .order-secret{display: none;}
    .layui-show .order-secret{display: block; white-space: pre-wrap}
</style>
<script type="text/javascript">
    $(function () {
        $('.js-secret').click(function () {
            let Elem = $('#orderSecret');
            if(Elem.hasClass('layui-show')){
                Elem.removeClass('layui-show');
                $(this).text('展开');
            }else{
                Elem.addClass('layui-show');
                $(this).text('收起');
            }
        })
    });
</script>
@include('common.footer')
