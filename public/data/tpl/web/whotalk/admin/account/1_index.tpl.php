<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">今日统计</div>
                        <div class="layui-card-body">

                            <div class="layui-carousel layadmin-backlog">
                                <div>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>今日充值</h3>
                                                <p><cite>¥&nbsp;<?php  echo $today['recharge'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>今日消费</h3>
                                                <p><cite>¥&nbsp;<?php  echo $today['consume'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>今日订单</h3>
                                                <p><cite><?php  echo $today['orders'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>今日提现</h3>
                                                <p><cite>¥&nbsp;<?php  echo $today['cash'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('group/list')?>" class="layadmin-backlog-body">
                                                <h3>新增积分</h3>
                                                <p><cite><?php  echo $today['creditin'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('content/message')?>" class="layadmin-backlog-body">
                                                <h3>消费积分</h3>
                                                <p><cite><?php  echo $today['creditout'];?></cite></p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">整站统计</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-backlog">
                                <div>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>充值总额</h3>
                                                <p><cite>¥&nbsp;<?php  echo $total['recharge'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>消费总额</h3>
                                                <p><cite>¥&nbsp;<?php  echo $total['consume'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('group/list')?>" class="layadmin-backlog-body">
                                                <h3>订单总数</h3>
                                                <p><cite><?php  echo $total['orders'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>提现总额</h3>
                                                <p><cite>¥&nbsp;<?php  echo $total['cash'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('content/message')?>" class="layadmin-backlog-body">
                                                <h3>用户总余额</h3>
                                                <p><cite>¥&nbsp;<?php  echo $total['credit2'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs4">
                                            <a href="<?php  echo weburl('content/album')?>" class="layadmin-backlog-body">
                                                <h3>用户总积分</h3>
                                                <p><cite><?php  echo $total['credit1'];?></cite></p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">数据概览</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview">
                                <div carousel-item>
                                    <div class="layui-this" id="LAY-index-dataview"><div class="text-center"><i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></i></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var Controller = "<?php  echo $_W['controller'];?>",Action = "<?php  echo $_W['action'];?>";
var layer;
layui.use(['carousel','element'], function(){
    var carousel = layui.carousel,element=layui.element;
    layer = layui.layer
    carousel.render({
        elem: '.layadmin-news'
        ,width:'100%'
        ,anim:'fade'
    });
});
$(function() {
    require(['echarts'], function(ec){
        var option = <?php  echo json_encode($echarts)?>;
        var myChart = ec.init(document.getElementById('LAY-index-dataview'));
        myChart.setOption(option);
    });
});
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>