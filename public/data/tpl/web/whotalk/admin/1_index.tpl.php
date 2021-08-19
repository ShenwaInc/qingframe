<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    p cite sub{font-size: 50%;}
</style>
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
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>今日登录</h3>
                                                <p><cite><?php  echo $today['login'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>APP登录</h3>
                                                <p><cite><?php  echo $today['clients'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>新增用户</h3>
                                                <p><cite><?php  echo $today['members'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3 lay-tips="系统采用前后端分离的机制，前端所有功能均使用接口方式进行交互，因此除H5端外不再统计页面访问量">今日访问(H5)<i class="layui-icon layui-icon-help"></i></h3>
                                                <p><cite><?php  echo $today['visit'];?><sub>次</sub>&nbsp;/&nbsp;<?php  echo $today['visiter'];?><sub>人</sub></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('group/list')?>" class="layadmin-backlog-body">
                                                <h3>新增群组</h3>
                                                <p><cite><?php  echo $today['groups'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('content/message')?>" class="layadmin-backlog-body">
                                                <h3>新增发言</h3>
                                                <p><cite><?php  echo $today['message'];?></cite></p>
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
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>用户总数</h3>
                                                <p><cite><?php  echo $total['members'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('member/list')?>" class="layadmin-backlog-body">
                                                <h3>APP用户</h3>
                                                <p><cite><?php  echo $total['clients'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('group/list')?>" class="layadmin-backlog-body">
                                                <h3>群组总数</h3>
                                                <p><cite><?php  echo $total['groups'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>总访问量</h3>
                                                <p><cite><?php  echo $total['visit'];?><sub>次</sub> / <?php  echo $total['visiter'];?><sub>人</sub></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('content/message')?>" class="layadmin-backlog-body">
                                                <h3>发言总数</h3>
                                                <p><cite><?php  echo $total['message'];?></cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6 layui-col-md4">
                                            <a href="<?php  echo weburl('content/album')?>" class="layadmin-backlog-body">
                                                <h3>动态总数</h3>
                                                <p><cite><?php  echo $total['albums'];?></cite></p>
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

        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">常用入口地址</div>
                <div class="layui-card-body layui-text">
                    <table class="layui-table" style="width: 100%">
                        <colgroup>
                            <col width="100">
                            <col>
                        </colgroup>
                        <tbody>
                            <tr>
                                <td>后台入口</td>
                                <td class="layui-row">
                                    <div class="layui-col-md12" lay-tips="为方便使用可以将某个单独域名的隐性URL或显性URL解析到该地址">
                                        <a href="<?php echo MODULE_URL;?>console.php?i=<?php  echo $_W['uniacid'];?>" class="pull-left" target="_blank"><?php echo MODULE_URL;?>console.php?i=<?php  echo $_W['uniacid'];?></a>&nbsp;&nbsp;
                                        <a href="javascript:;" data-url="<?php echo MODULE_URL;?>console.php?i=<?php  echo $_W['uniacid'];?>" class="color-default js-clip"><i class="fa fa-copy"></i> 复制</a>&nbsp;&nbsp;
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="layui-table" style="width: 100%">
                        <colgroup>
                            <col width="100">
                            <col>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td>首页入口</td>
                            <td class="layui-row">
                                <div class="layui-col-md12">
                                    <a href="<?php  echo wmurl('','',true)?>" class="pull-left" target="_blank"><?php  echo wmurl('','',true)?></a>&nbsp;&nbsp;
                                    <a href="javascript:;" data-url="<?php  echo wmurl('','',true)?>" class="color-default js-clip"><i class="fa fa-copy"></i> 复制</a>&nbsp;&nbsp;
                                    <div class="btn-group">
                                        <a href="javascript:;" onmouseover="$(this).click()" style="margin-right:0px;" class="color-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-qrcode"></i> 二维码</a>
                                        <div class="dropdown-menu js-url-qrcode" data-size="200" data-url="<?php  echo wmurl('','',true)?>">
                                            <div class="qrcode-block" style="padding: 10px;"><canvas></canvas></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>广场入口</td>
                            <td class="layui-row">
                                <div class="layui-col-md12">
                                    <a href="<?php  echo uniurl('album/index',array(),true)?>" class="pull-left" target="_blank"><?php  echo uniurl('album/index',array(),true)?></a>&nbsp;&nbsp;
                                    <a href="javascript:;" data-url="<?php  echo uniurl('album/index',array(),true)?>" class="color-default js-clip"><i class="fa fa-copy"></i> 复制</a>&nbsp;&nbsp;
                                    <div class="btn-group">
                                        <a href="javascript:;" onmouseover="$(this).click()" style="margin-right:0px;" class="color-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-qrcode"></i> 二维码</a>
                                        <div class="dropdown-menu js-url-qrcode" data-size="200" data-url="<?php  echo uniurl('album/index',array(),true)?>">
                                            <div class="qrcode-block" style="padding: 10px;"><canvas></canvas></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>钱包入口</td>
                            <td class="layui-row">
                                <div class="layui-col-md12">
                                    <a href="<?php  echo uniurl('credit/index',array(),true)?>" class="pull-left" target="_blank"><?php  echo uniurl('credit/index',array(),true)?></a>&nbsp;&nbsp;
                                    <a href="javascript:;" data-url="<?php  echo uniurl('credit/index',array(),true)?>" class="color-default js-clip"><i class="fa fa-copy"></i> 复制</a>&nbsp;&nbsp;
                                    <div class="btn-group">
                                        <a href="javascript:;" onmouseover="$(this).click()" style="margin-right:0px;" class="color-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-qrcode"></i> 二维码</a>
                                        <div class="dropdown-menu js-url-qrcode" data-size="200" data-url="<?php  echo uniurl('credit/index',array(),true)?>">
                                            <div class="qrcode-block" style="padding: 10px;"><canvas></canvas></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">版本信息</div>
                <div class="layui-card-body layui-text">
                    <table class="layui-table">
                        <colgroup>
                            <col width="100">
                            <col>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td>当前版本</td>
                            <td>
                                <?php  if(MODULE_VERSON=='S') { ?>插件版<?php  } else if(MODULE_VERSON=='V') { ?>正式版<?php  } else { ?>尝鲜版<?php  } ?> <?php echo MODULE_RELEASE;?>&nbsp;Release<?php echo MODULE_RELEASE_DATE;?>
                                <?php  if($_W['isfounder']) { ?><a href="https://www.yuque.com/docs/share/84abf7ef-7d11-44f1-a510-ed70ef14ef3d?#" target="_blank" style="padding-left: 15px;">更新日志</a><?php  } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>底层框架</td>
                            <td>WeEngine-Laster (V2.5+)</td>
                        </tr>
                        <tr>
                            <td>主要特色</td>
                            <td>SaaS IM / 高度自定义 / 多语言 / 清爽极简 / 有效防屏</td>
                        </tr>
                        <?php  if($_W['isfounder']) { ?>
                        <tr>
                            <td>维护渠道</td>
                            <td style="padding-bottom: 0;">
                                <div class="layui-btn-container">
                                    <a href="https://www.whotalk.com.cn/" target="_blank" class="layui-btn layui-btn-danger">提交工单</a>
                                    <a href="https://www.gxit.org/" target="_blank" class="layui-btn">联系售后</a>
                                </div>
                            </td>
                        </tr>
                        <?php  } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
var Controller = "<?php  echo $_W['controller'];?>",Action = "<?php  echo $_W['action'];?>";
var layer;
layui.use(['carousel','element','layer'], function(){
    var carousel = layui.carousel;//,echarts = layui.echarts;
    var element = layui.element;
    layer = layui.layer
    carousel.render({
        elem: '.layadmin-news'
        ,width:'100%'
        ,anim:'fade'
    });
});
$(function() {
    require(['jquery.qrcode','echarts'], function(n,ec){
        $('.js-url-qrcode').each(function(){
            url = $(this).data('url');
            $(this).find('.qrcode-block').html('').qrcode({
                render: 'canvas',
                width: $(this).data('size'),
                height: $(this).data('size'),
                text: url
            });
        });
        var option = <?php  echo json_encode($echarts)?>;
        var myChart = ec.init(document.getElementById('LAY-index-dataview'));
        myChart.setOption(option);
    });
});
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>
