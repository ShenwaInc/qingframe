<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .layui-form-label{width:202px !important;}
    .layui-input-block{margin-left:232px !important;}
</style>
<div class="layui-card layadmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a href="<?php  echo weburl('account')?>"><cite>财务管理</cite></a>
        <a><cite>提现管理</cite></a>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">提现管理</div>
                <div class="layui-card-body">
                    <div class="layui-table-tool" style="width: auto; border-bottom: 0;">
                        <form method="post" class="layui-form" action="<?php  echo weburl('account/cashlog')?>">
                            <div class="layui-table-tool-temp">
                                <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <?php  echo tpl_form_field_daterange('time', array('starttime' => date('Y-m-d', $starttime), 'endtime' => date('Y-m-d', $endtime)));?>
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width: 360px;">
                                        <input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键字搜索，如用户昵称/备注" autocomplete="off" class="layui-input">
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
                            <col width="80">
                            <col width="230">
                            <col width="120">
                            <col width="100">
                            <col width="150">
                            <col width="150">
                            <col width="100">
                            <col />
                        </colgroup>
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>昵称</th>
                            <th class="text-center">提现方式</th>
                            <th>金额</th>
                            <th>实际到账</th>
                            <th class="text-center">申请时间</th>
                            <th class="text-center">当前状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="navigations">
                        <?php  if(is_array($list)) { foreach($list as $key => $row) { ?>
                        <tr>
                            <td><?php  echo $row['id'];?></td>
                            <td><a href="<?php  echo url('mc/member/base_information',array('uid'=>$value['uid']))?>" target="_blank"><img src="<?php  echo avatar($row['uid'])?>" height="36" /> <?php  echo $row['nickname'];?></a></td>
                            <td class="text-center"><?php  echo $cashtype[$row['method']];?></td>
                            <td><?php  echo $row['amount'];?>元</td>
                            <td><?php  echo $row['actual'];?>元</td>
                            <td class="text-center"><?php  echo date('Y-m-d H:i', $row['addtime'])?></td>
                            <td class="text-center">
                                <?php  if($row['status']==1) { ?><?php  if($row['paid']) { ?><span class="layui-badge layui-bg-green" lay-tips="<?php  echo $row['remark'];?>">已打款</span><?php  } else { ?><span class="layui-badge layui-bg-orange">待审核</span><?php  } ?><?php  } else if($row['status']==2) { ?><span class="layui-badge layui-bg-red" lay-tips="<?php  echo $row['remark'];?>">已拒绝</span><?php  } else { ?><span class="layui-badge layui-bg-red">已撤销</span><?php  } ?>
                            </td>
                            <td>
                                <div class="layui-hide" id="detail_<?php  echo $row['id'];?>">
                                    <?php  $content = unserialize($row['content']);?>
                                    <?php  if($row['method']=='bank') { ?>
                                    <p><strong>开户银行：</strong>&nbsp;&nbsp;<?php  echo $content['name'];?></p>
                                    <p><strong>开户支行：</strong>&nbsp;&nbsp;<?php  echo $content['branch'];?></p>
                                    <p><strong>银行卡号：</strong>&nbsp;&nbsp;<?php  echo $content['account'];?></p>
                                    <p><strong>开户人姓名：</strong>&nbsp;&nbsp;<?php  echo $content['realname'];?></p>
                                    <?php  } else if($row['method']=='alipay') { ?>
                                    <p><strong>支付宝账号：</strong>&nbsp;&nbsp;<?php  echo $content['account'];?></p>
                                    <p><strong>真实姓名：</strong>&nbsp;&nbsp;<?php  echo $content['realname'];?></p>
                                    <?php  } else if($row['method']=='wechat_transfer') { ?>
                                    <p><strong>微信号：</strong>&nbsp;&nbsp;<?php  echo $content['account'];?></p>
                                    <?php  } else if($row['method']=='wechat') { ?>
                                    <p class="layui-bg-red" style="padding: 10px;">微信商户直接转账到用户的钱包，该操作不可退回</p>
                                    <p class="text-center">
                                        <a href="<?php  echo url('mc/chats',array('openid'=>$row['openid']))?>" target="_blank">
                                            <img src="<?php  echo $content['avatar'];?>" height="56" style="border-radius: 50%" /><br/>
                                            <span><?php  echo $content['nickname'];?></span>
                                        </a>
                                    </p>
                                    <?php  } ?>
                                    <p><strong>提现备注：</strong>&nbsp;&nbsp;<?php  echo $content['remark'];?></p>
                                </div>
                                <?php  if($row['status']==1 && $row['paid']==0) { ?>
                                <?php  if($row['method']=='wechat') { ?>
                                <a href="javascript:;" data-cid="<?php  echo $row['id'];?>" lay-tips="请确保微信商户号有足够的资金" class="layui-btn agreebtn">同意转账</a>
                                <?php  } else { ?>
                                <a href="javascript:;" data-cid="<?php  echo $row['id'];?>" data-result="1" class="layui-btn refusebtn">同意并已打款</a>
                                <?php  } ?>
                                <?php  } ?>
                                <a href="javascript:;" class="layui-btn layui-btn-normal" onclick="layer.open({title:'对方账号信息',content:$('#detail_<?php  echo $row['id'];?>').html()})">账号信息</a>
                                <?php  if($row['status']==1 && $row['paid']==0) { ?>
                                <a href="javascript:;" data-cid="<?php  echo $row['id'];?>" data-result="0" class="layui-btn layui-btn-warm refusebtn">拒绝</a>
                                <?php  } ?>
                            </td>
                        </tr>
                        <?php  } } ?>
                        <?php  if(!$list) { ?>
                        <tr><td colspan="8" class="text-muted text-center">暂无数据</td></tr>
                        <?php  } ?>
                        <tr><td colspan="8" class="text-right"><span class="pull-left">共找到<?php  echo $total;?>条记录</span> <?php  echo $pager;?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Controller = "<?php  echo $_W['controller'];?>",Action = "<?php  echo $_W['action'];?>";
    var laypicker,laypage,layupload,layer,layecharts,layform;
    var tempopenid = '';
    var showtab = '<?php  echo $_GPC["showtab"];?>';
    layui.use(['form','element','colorpicker'], function(){
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
        $('.agreebtn').click(function () {
            let cid = $(this).data('cid');
            layer.confirm('同意后，将自动通过微信商户转账对应金额到用户的微信钱包且无法撤回，是否确定要操作？', {icon: 3, title:'同意提现到微信钱包'}, function(index){
                $.ajax({
                    url:'<?php  echo weburl("account/cashlog",array("auditcash"=>"yes"))?>',
                    type:"POST",
                    dataType:"json",
                    data:{
                        token:"<?php  echo $_W['token'];?>",
                        result:1,
                        remark:'同意提现到微信钱包',
                        cid:cid
                    },
                    success:function (ret) {
                        layer.msg(ret.message,{icon:(ret.type=='success'?1:2)});
                        setTimeout(function () {
                            window.location.reload();
                        },1500)
                    }});
                layer.close(index);
            });
        });
        $('.refusebtn').click(function () {
            var cid = $(this).data('cid');
            var result = $(this).data('result');
            layer.prompt({title:'请输入审核备注',formType:2,area:['360px','100px']},function (value,index,elem) {
                if(value==''){
                    elem.focus();
                    return false;
                }
                $.ajax({url:'<?php  echo weburl("account/cashlog",array("auditcash"=>"yes"))?>',type:"POST",dataType:"json",data:{token:"<?php  echo $_W['token'];?>",result:result,remark:value,cid:cid},success:function (ret) {
                    layer.msg(ret.message,{icon:(ret.type=='success'?1:2)});
                    setTimeout(function () {
                        window.location.reload();
                    },1500)
                }});
                layer.close(index);
            });
        });
    });
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>