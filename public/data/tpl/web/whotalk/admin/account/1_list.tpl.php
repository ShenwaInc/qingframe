<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .layui-form-label{width:202px !important;}
    .layui-input-block{margin-left:232px !important;}
</style>
<div class="layui-card layadmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a href="<?php  echo weburl('account')?>"><cite>财务管理</cite></a>
        <a><cite><?php  echo $ops[$op];?></cite></a>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header"><?php  echo $ops[$op];?></div>
                <div class="layui-card-body">
                    <div class="layui-tab">
                        <ul class="layui-tab-title">
                            <?php  if(is_array($ops)) { foreach($ops as $key => $value) { ?>
                            <li<?php  if($key==$op) { ?> class="layui-this"<?php  } ?>><a href="<?php  echo weburl('account/list',array('op'=>$key))?>"><?php  echo $value;?></a></li>
                            <?php  } } ?>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <div class="layui-table-tool" style="width: auto; border-bottom: 0;">
                                    <form method="post" class="layui-form" action="<?php  echo weburl('account/list',array('op'=>$op))?>">
                                        <div class="layui-table-tool-temp">
                                            <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                                            <div class="layui-inline">
                                                <div class="layui-input-inline">
                                                    <?php  echo tpl_form_field_daterange('time', array('starttime' => date('Y-m-d', $starttime), 'endtime' => date('Y-m-d', $endtime)));?>
                                                </div>
                                            </div>
                                            <div class="layui-btn-container layui-inline">
                                                <button type="submit" value="true" name="searchsubmit" class="layui-btn layui-btn-normal">搜索</button>
                                            </div>
                                        </div>
                                        <div class="layui-table-tool-self">
                                            <button type="submit" value="true" name="exportnow" class="layui-btn layui-btn-xs layui-btn-warm" lay-tips="导出当前"><i class="layui-icon layui-icon-export"></i></button>
                                            <button type="submit" value="true" name="exportall" class="layui-btn layui-btn-xs layui-btn-danger" lay-tips="导出全部"><i class="layui-icon layui-icon-export"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <table class="layui-table">
                                    <colgroup>
                                        <col width="60">
                                        <col width="200">
                                        <col width="150">
                                        <col width="100">
                                        <col width="150">
                                        <col width="180">
                                        <col />
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>UID</th>
                                        <th>昵称</th>
                                        <th>手机号</th>
                                        <th class="text-center">类型</th>
                                        <th>金额</th>
                                        <th class="text-center">时间</th>
                                        <th>备注</th>
                                    </tr>
                                    </thead>
                                    <tbody class="navigations">
                                    <?php  if(is_array($data)) { foreach($data as $key => $row) { ?>
                                    <tr>
                                        <td><?php  echo $row['uid'];?></td>
                                        <td><?php  echo $users[$row['uid']]['nickname'];?></td>
                                        <td><?php  echo $users[$row['uid']]['mobile'];?></td>
                                        <td class="text-center">
                                            <?php  if($row['num'] > 0) { ?>
                                            <span class="label label-success">充值</span>
                                            <?php  } else { ?>
                                            <span class="label label-danger">消费</span>
                                            <?php  } ?>
                                        </td>
                                        <td><?php  echo abs($row['num']);?><?php  if($op=='credit2') { ?>元<?php  } else { ?>积分<?php  } ?></td>
                                        <td class="text-center"><?php  echo date('Y-m-d H:i', $row['createtime'])?></td>
                                        <td style="cursor: pointer"><span data-toggle="popover" data-placement="top" data-trigger="hover" data-content="<?php  echo $row['remark'];?>"><?php  echo cutstr($row['remark'], 30, '...');?></span></td>
                                    </tr>
                                    <?php  } } ?>
                                    <?php  if(!$data) { ?>
                                    <tr><td colspan="7" class="text-muted text-center">暂无数据</td></tr>
                                    <?php  } ?>
                                    <tr><td colspan="7" class="text-right"><span class="pull-left">共找到<?php  echo $total;?>条记录</span> <?php  echo $pager;?></td></tr>
                                    </tbody>
                                </table>
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
var laypicker,laypage,layupload,layer,layecharts,layform;
layui.use(['form','element'], function(){
    var form = layui.form,element=layui.element;
    layer = layui.layer;
    layform = layui.form;
    laypage = layui.laypage;
    form.on('switch(ctrls)',function(data){
        var ctrltarget = jQuery(data.elem).attr('data-ctrl');
        if(data.elem.checked){
            jQuery(ctrltarget).removeClass('layui-hide');
        }else{
            jQuery(ctrltarget).addClass('layui-hide');
        }
    });
    form.on('switch(actives)',function(data){
        if(data.elem.checked){
            jQuery('.filter-actives').attr('checked',false);
            data.elem.checked = true;
            form.render();
        }else{

        }
    });
});
$(function () {
    $('.upbtn').click(function () {
        var obj = $(this).parent().parent();
        if(obj.index()!=0){
            obj.prev().before(obj);
        }
    });
    $('.downbtn').click(function () {
        var obj = $(this).parent().parent();
        var counts = $('.navigations').find('tr').length;
        if(counts-obj.index()>1){
            obj.next().after(obj);
        }
    });
    $('.deletebtn').click(function () {
        var obj = $(this).parent().parent();
        layer.confirm('确定要删除？',{icon:3,title: '删除导航'},function (index) {
            obj.remove();
            layer.close(index);
        });
    });
    $('.removebtn').click(function () {
        var hrel = $(this).attr('href');
        layer.confirm('删除后不可恢复，是否确定要删除？',{icon:3,title: '删除导航'},function (index) {
            window.location.href = hrel;
        });
        return false;
    });
    $('.addnav').click(function () {
        $('.navappend').removeClass('layui-hide').find('input.required').attr('required',true).attr('lay-','required');
        layform.render();
    });
    $('.hidebtn').click(function () {
        $('.navappend').addClass('layui-hide').find('input.required').attr('required',false).removeAttr('lay-verify');
        layform.render();
    });
});
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>