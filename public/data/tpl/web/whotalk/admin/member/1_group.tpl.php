<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<div class="layui-card layadmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a href="<?php  echo weburl('system')?>"><cite>系统设置</cite></a>
        <?php  if($_W['action']!='list') { ?><a href="<?php  echo weburl('member/list')?>"><cite><?php  echo $title;?></cite></a><?php  } ?>
        <a><cite><?php  echo $operation;?></cite></a>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    <?php  echo $operation;?>&nbsp;&nbsp;
                    <a href="<?php  echo weburl('member/group',array('op'=>'post'))?>" class="layui-btn layui-btn-normal layui-btn-sm ajaxshow" data-width="560"><span class="layui-icon layui-icon-add-1"></span>&nbsp;新增会员等级</a>
                    <button type="button" data-toggle="modal" data-target="#modal-change-group" class="layui-btn layui-btn-sm"><span class="layui-icon layui-icon-set-fill"></span>&nbsp;会员等级变更设置</button>
                </div>
                <div class="layui-card-body">
                    <form class="layui-form" method="post" action="<?php  echo $this->createWebUrl('saveset')?>" lay-filter="component-form-element">
                        <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        <?php  if(is_array($_W['account']['groups'])) { foreach($_W['account']['groups'] as $group) { ?>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><strong><?php  echo $group['title'];?></strong>&nbsp;<a href="<?php  echo weburl('member/group',array('groupid'=>$group['groupid'],'op'=>'post'))?>" class="color-default ajaxshow" data-width="560" title="编辑会员等级"><span class="fa fa-edit"></span></a></label>
                            <div class="layui-input-block">
                                <div class="input-group layui-input-inline" style="width: 72%;">
                                    <span class="input-group-addon">可创建</span>
                                    <input type="text" name="data[prem][<?php  echo $group['groupid'];?>][maxcreate]" value="<?php  echo $_S['prem'][$group['groupid']]['maxcreate'];?>" placeholder="0为不可创建" autocomplete="off" class="layui-input">
                                    <span class="input-group-addon">个群</span>
                                    <span class="input-group-addon">可加入</span>
                                    <input type="text" name="data[prem][<?php  echo $group['groupid'];?>][maxjoin]" value="<?php  echo $_S['prem'][$group['groupid']]['maxjoin'];?>" placeholder="0为不可加入" autocomplete="off" class="layui-input">
                                    <span class="input-group-addon">个群</span>
                                    <span class="input-group-addon">群上限</span>
                                    <input type="text" name="data[prem][<?php  echo $group['groupid'];?>][members]" value="<?php  echo $_S['prem'][$group['groupid']]['members'];?>" placeholder="0为不限" autocomplete="off" class="layui-input">
                                    <span class="input-group-addon">人</span>
                                    <span class="input-group-addon">好友上限</span>
                                    <input type="text" name="data[prem][<?php  echo $group['groupid'];?>][friends]" value="<?php  echo $_S['prem'][$group['groupid']]['friends'];?>" placeholder="0为不限" autocomplete="off" class="layui-input">
                                    <span class="input-group-addon">人</span>
                                    <span class="input-group-addon">抽成比例</span>
                                    <input type="text" name="data[prem][<?php  echo $group['groupid'];?>][proportion]" value="<?php  echo intval($_S['prem'][$group['groupid']]['proportion'])?>" autocomplete="off" class="layui-input">
                                    <span class="input-group-addon">%</span>
                                </div>
                                <input type="checkbox" value="1" name="data[prem][<?php  echo $group['groupid'];?>][makefriend]" title="添加好友"<?php  if($_S['prem'][$group['groupid']]['makefriend']) { ?> checked<?php  } ?> />
                                <input type="checkbox" value="1" name="data[prem][<?php  echo $group['groupid'];?>][hideadvs]" title="无广告"<?php  if($_S['prem'][$group['groupid']]['hideadvs']) { ?> checked<?php  } ?> />
                            </div>
                        </div>
                        <?php  } } ?>
                        <div class="layui-form-item">
                            <label class="layui-form-label">&nbsp;</label>
                            <div class="layui-input-block">
                                <div class="layui-word-aux">抽成比例是指用户在平台的收入需要给平台多少分成</div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">前台升级入口</label>
                            <div class="layui-input-block">
                                <input type="checkbox" lay-filter="ctrls" data-ctrl=".premswitch" lay-skin="switch" name="data[prem][switch]" value="1" lay-text="显示|隐藏"<?php  if($_S['prem']['switch']) { ?> checked<?php  } ?> />
                                <div class="layui-word-aux">开启后用户可以通过个人资料页、钱包页进入会员等级购买页面。您也可以将页面入口放在导航栏、菜单栏、广告等地方。<a href="javascript:;" class="js-clip color-default" data-url="<?php  echo uniurl('member/vip',array(),true)?>">复制入口链接</a></div>
                            </div>
                        </div>
                        <div class="group_level <?php  if($group_level>0) { ?> layui-hide<?php  } ?>">
                            <div class="premswitch<?php  if(!$_S['prem']['switch']) { ?> layui-hide<?php  } ?>">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">首页升级提示</label>
                                    <div class="layui-input-block">
                                        <input type="radio" name="data[prem][hometip]" title="不提示" value="0"<?php  if(!$_S['prem']['hometip']) { ?> checked<?php  } ?> />
                                        <input type="radio" name="data[prem][hometip]" title="非会员提示" value="1"<?php  if($_S['prem']['hometip']==1) { ?> checked<?php  } ?> />
                                        <input type="radio" name="data[prem][hometip]" title="可升级提示" value="2"<?php  if($_S['prem']['hometip']==2) { ?> checked<?php  } ?> />
                                        <div class="layui-word-aux">开启后如用户满足设置条件则会在首页的【我的】【钱包】上方添加升级提示</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit lay-filter="formDemo" type="submit" value="true" name="savedata">保存</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                            </div>
                        </div>
                    </form>
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
        form.on('radio(ctrls)', function(data){
            //console.log(data.value); //被点击的radio的value值
            var ctrltarget = jQuery(data.elem).attr('data-ctrl');
            jQuery('.form-'+ctrltarget).addClass('layui-hide');
            jQuery('.form-'+ctrltarget+data.value).removeClass('layui-hide');
            jQuery('.form-'+ctrltarget).each(function(index, e) {
                jQuery(e).find('input').removeAttr('lay-verify');
                jQuery(e).find('select').removeAttr('lay-verify');
                jQuery(e).find('textarea').removeAttr('lay-verify');
            });
            jQuery('.form-'+ctrltarget+data.value).each(function(index, e) {
                if(typeof(jQuery(e).data('required'))!='undefined'){
                    if(jQuery(e).find('textarea').length>0){
                        jQuery(e).find('textarea').attr('lay-verify','required');
                    }else if(jQuery(e).find('select').length>0){
                        jQuery(e).find('select').attr('lay-verify','required');
                    }else{
                        jQuery(e).find('input').attr('lay-verify','required');
                    }
                }
            });
            form.render();
        });
    });
    $(function () {
        $('.js-group_level').click(function (){
            let Modal = $('#modal-change-group');
            let group_level = Modal.find('input[name="grouplevel"]:checked').val();
            $.ajax({
               url:'<?php  echo url("mc/group/change_group_level")?>',
               data:{
                   group_level:group_level,
                   submit:1,
                   token:"<?php  echo $_W['token'];?>"
               },
                dataType:"json",
                type:"POST",
                success:function (res) {
                    if(res.message.errno===0){
                        layer.msg('保存成功！',{icon:1});
                        Modal.modal('hide');
                    }else {
                        layer.msg(res.message.message,{icon:2});
                    }
                }
            });
        });
    });
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>
<div class="modal fade modal-change-group" id="modal-change-group"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog we7-modal-dialog" style="width:800px">
        <div class="modal-content">
            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal form we7-form" id="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">会员组变更设置</h4>
                </div>
                <div class="modal-body we7-padding-horizontal">
                    <div class="form-group">
                        <input type="radio" name="grouplevel" value="0" id="group_level-0"<?php  if($group_level==0) { ?> checked<?php  } ?> />
                        <label for="group_level-0">付费购买等级</label>
                        <span class="help-block">
								会员组的变更只能通过管理员来变更。
							</span>
                    </div>
                    <div class="form-group">
                        <input type="radio" name="grouplevel" value="1" id="group_level-1"<?php  if($group_level==1) { ?> checked<?php  } ?> />
                        <label for="group_level-1">根据积分多少自动升降</label>
                        <span class="help-block">
								系统根据当前会员的总积分，按照每个会员组所需总积分的设置进行变更。可自动升降。
							</span>
                    </div>
                    <div class="form-group">
                        <input type="radio" name="grouplevel" value="2" id="group_level-2"<?php  if($group_level==2) { ?> checked<?php  } ?> />
                        <label for="group_level-2">根据积分多少只升不降</label>
                        <span class="help-block">
								系统根据当前会员的总积分，如果会员的总积分达到更高一级的会员组，则变更会员组，如果积分少于当前所在会员组所需总积分，保持当前会员组不变，不会降级。
							</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button  class="btn btn-primary js-group_level" type="button">保存</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <input type="hidden" name="token" value="c781f0df">
                </div>
            </form>
        </div>
    </div>
</div>
</body></html>