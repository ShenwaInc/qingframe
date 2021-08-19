<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .layui-form-label{width:202px !important;}
    .layui-input-block{margin-left:232px !important;}
</style>
<div class="layui-card layadmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a href="<?php  echo weburl('system')?>"><cite>系统设置</cite></a>
        <?php  if($_W['action']!='list') { ?><a href="<?php  echo weburl('group/list')?>"><cite><?php  echo $title;?></cite></a><?php  } ?>
        <a><cite><?php  echo $operation;?></cite></a>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header"><?php  echo $operation;?><?php  if($group) { ?> - <?php  echo $group['name'];?><?php  } ?></div>
                <div class="layui-card-body">
                    <?php  if($_W['action']=='list') { ?>
                    <div class="layui-table-tool" style="width: auto; border-bottom: 0;">
                        <form method="post" class="layui-form" action="<?php  echo weburl('group/list')?>">
                            <div class="layui-table-tool-temp">
                                <div class="layui-btn-container layui-inline">
                                    <a href="<?php  echo weburl('group/post')?>" class="layui-btn layui-btn-normal">新增群组</a>
                                </div>
                                <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width: 420px;">
                                        <input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键字搜索，如<?php  wlang('text_groupname')?>名称/群主/群号/群介绍" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="status">
                                            <option value="-1">状态</option>
                                            <option value="1"<?php  if($_GPC['status']==1) { ?> selected<?php  } ?>>正常</option>
                                            <option value="0"<?php  if(isset($_GPC['status']) && $_GPC['status']==0) { ?> selected<?php  } ?>>禁止</option>
                                        </select>
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
                    <?php  } else if($_W['action']=='member') { ?>
                    <div class="layui-table-tool" style="width: auto; border-bottom: 0;">
                        <form method="post" class="layui-form" action="<?php  echo weburl('group/member')?>">
                            <div class="layui-table-tool-temp">
                                <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width: 420px;">
                                        <input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键字搜索，如成员的昵称/手机号/邮箱" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="status">
                                            <option value="-1">状态</option>
                                            <option value="1"<?php  if($_GPC['status']==1) { ?> selected<?php  } ?>>已加入</option>
                                            <option value="0"<?php  if(isset($_GPC['status']) && $_GPC['status']==0) { ?> selected<?php  } ?>>待审核</option>
                                            <option value="2"<?php  if($_GPC['status']==2) { ?> selected<?php  } ?>>已拒绝</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="gid" lay-verify="required" lay-search>
                                            <option value="">请选择群组</option>
                                            <?php  if(is_array($groups)) { foreach($groups as $key => $value) { ?>
                                            <option value="<?php  echo $value['id'];?>"<?php  if($group['id']==$value['id']) { ?> selected<?php  } ?>><?php  echo $value['name'];?></option>
                                            <?php  } } ?>
                                        </select>
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
                    <?php  } ?>
                    <form class="layui-form" method="post" action="<?php  echo weburl('group/'.$_W['action'])?>" lay-filter="component-form-element">
                        <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        <?php  if($_W['action']=='list') { ?>
                        <table class="layui-table" style="margin-top: 0;" id="table-checkbox" lay-filter="advstable" lay-even>
                            <colgroup>
                                <col width="60" />
                                <col width="320" />
                                <col width="250" />
                                <col width="180" />
                                <col width="180" />
                                <col width="100" />
                                <col />
                            </colgroup>
                            <thead>
                            <tr>
                                <th class="text-center">群号</th>
                                <th>群名称</th>
                                <th>群主</th>
                                <th class="text-center">积分/热度</th>
                                <th class="text-center">最后更新/创建时间</th>
                                <th class="text-center">状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="navigations">
                            <?php  if(is_array($list)) { foreach($list as $key => $value) { ?>
                            <tr>
                                <td class="text-center"><?php  echo $value['id'];?></td>
                                <td>
                                    <a href="<?php  echo wmurl('group/view',array('cid'=>$value['id']),true,true)?>" target="_blank">
                                        <img src="<?php  echo defaultimg($value['pic'])?>" class="avatar pull-left" height="48" style="vertical-align: middle; border-radius: 4px; margin-right: 10px;" />
                                        <div>
                                            <strong style="font-size: 14px; line-height: 26px;"><?php  echo $value['name'];?></strong>
                                            <br/><span class="text-muted">共<?php  echo $value['members'];?>人，累计<?php  echo $value['posts'];?>次发言</span>
                                        </div>
                                    </a>
                                </td>
                                <td><a href="<?php  echo url('mc/member/base_information',array('uid'=>$value['uid']))?>" target="_blank"><?php  echo $value['nickname'];?></a></td>
                                <td class="text-center"><i class="layui-icon layui-icon-diamond"></i>&nbsp;<?php  echo $value['credits'];?><br/><i class="layui-icon layui-icon-fire"></i>&nbsp;<?php  echo $value['heats'];?></td>
                                <td class="text-center"><?php  echo date('Y-m-d H:i',$value['dateline'])?><br/><?php  echo date('Y-m-d H:i',$value['addtime'])?></td>
                                <td class="text-center"><input type="checkbox" lay-filter="userstatus" data-gid="<?php  echo $value['id'];?>" name="status[<?php  echo $value['id'];?>]"<?php  if($value['status']) { ?> checked<?php  } ?> value="1" lay-skin="switch" lay-text="正常|关闭" /></td>
                                <td>
                                    <div class="layui-btn-group">
                                        <a href="<?php  echo weburl('group/post',array('gid'=>$value['id']))?>" class="layui-btn layui-btn-normal layui-btn-sm">编辑</a>
                                        <a href="javascript:;" data-url="<?php  echo uniurl('group/detail',array('gid'=>$value['id']),true)?>" class="layui-btn layui-btn-sm js-clip"><i class="fa fa-copy"></i>&nbsp;复制链接</a>
                                        <a href="<?php  echo weburl('group/member',array('gid'=>$value['id']))?>" class="layui-btn layui-btn-warm layui-btn-sm"><i class="layui-icon layui-icon-group"></i> 群成员</a>
                                        <a href="<?php  echo weburl('group/delete',array('gid'=>$value['id']));?>" class="layui-btn layui-btn-danger layui-btn-sm removebtn"><i class="layui-icon layui-icon-delete"></i>删除</a>
                                    </div>
                                </td>
                            </tr>
                            <?php  } } ?>
                            <?php  if(!$list) { ?>
                            <tr><td colspan="7" class="text-muted text-center">暂无数据</td></tr>
                            <?php  } ?>
                            <tr><td colspan="7" class="text-right"><span class="pull-left">共找到<?php  echo $total;?>个群组</span> <?php  echo $pager;?></td></tr>
                            </tbody>
                        </table>
                        <?php  } else if($_W['action']=='post') { ?>
                        <?php  if($group) { ?><input type="hidden" name="gid" value="<?php  echo $group['id'];?>" /><?php  } ?>
                        <div class="layui-form-item must">
                            <label class="layui-form-label"><?php  wlang('text_groupname')?>名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="data[name]" value="<?php  echo $group['name'];?>" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label"><?php  wlang('text_groupname')?>头像</label>
                            <?php  echo $this->tpl_form_field_image('data[pic]', $group['pic'],'正方形图片（128*128）');?>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><?php  wlang('text_groupname')?>简介</label>
                            <div class="layui-input-block">
                                <textarea name="data[description]" placeholder="请输入简介" class="layui-textarea"><?php  echo $group['description'];?></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label"><?php  wlang('text_groupname')?>公告</label>
                            <div class="layui-input-block">
                                <textarea name="data[notice]" placeholder="请输入群公告，支持HTML代码" class="layui-textarea"><?php  echo $group['notice'];?></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <?php  echo $this->tpl_form_field_member('指定群主',$group['uid'],'data[uid]','请选择一个用户作为群主，输入用户名/姓名/手机号搜索');?>
                        </div>
                        <div class="layui-form-item layui-hide">
                            <label class="layui-form-label">公共<?php  wlang('text_groupname')?><span class="layui-icon layui-icon-help" lay-tips="公共群组允许所有人自由发言，无需申请也无需审核。如设定为公共群组则群主设置将失效"></span></label>
                            <div class="layui-input-block">
                                <input type="checkbox" value="1" name="data[iscommon]"<?php  if($group['iscommon']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="公共群组|私有群组">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">进群收费</label>
                            <div class="layui-input-block">
                                <input type="number" name="data[fee]" value="<?php  echo $group['fee'];?>" placeholder="请输入用户进群需要收取的费用，单位为元，0为免费" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">状态</label>
                            <div class="layui-input-block">
                                <input type="checkbox" value="1" name="data[status]"<?php  if($group['status'] || !$group) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">更多设置</label>
                            <div class="layui-input-block">
                                <input type="checkbox" name="data[allowpost]" value="1" title="允许发言"<?php  if($group['allowpost'] || !$group) { ?> checked<?php  } ?> />
                                <input type="checkbox" name="data[protective]" value="1" title="群成员保护"<?php  if($group['protective']) { ?> checked<?php  } ?> />
                                <input type="checkbox" name="data[allowjoin]" value="1" title="允许加入"<?php  if($group['allowjoin'] || !$group) { ?> checked<?php  } ?> />
                                <input type="checkbox" name="data[joinaudit]" value="1" title="加入需要审核"<?php  if($group['joinaudit']) { ?> checked<?php  } ?> />
                                <input type="checkbox" name="data[ishide]" value="1" title="在列表中隐藏"<?php  if($group['ishide']) { ?> checked<?php  } ?> />
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formDemo" type="submit" value="true" name="savedata">保存</button>
                                <button class="layui-btn layui-btn-primary" type="reset">重填</button>
                            </div>
                        </div>
                        <?php  } else if($_W['action']=='apply') { ?>
                        <table class="layui-table" style="margin-top: 0;" id="table-checkbox" lay-filter="advstable" lay-even>
                            <colgroup>
                                <col width="60" />
                                <col width="320" />
                                <col width="180" />
                                <col width="180" />
                                <col />
                                <col width="100" />
                                <col width="180" />
                            </colgroup>
                            <thead>
                            <tr>
                                <th class="text-center">UID</th>
                                <th>用户</th>
                                <th>所在群组</th>
                                <th class="text-center">申请时间</th>
                                <th>申请说明</th>
                                <th class="text-center">状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="navigations">
                            <?php  if(is_array($list)) { foreach($list as $key => $value) { ?>
                            <tr>
                                <td class="text-center"><?php  echo $value['uid'];?></td>
                                <td>
                                    <a href="<?php  echo url('mc/member/base_information',array('uid'=>$value['uid']))?>" target="_blank">
                                        <img src="<?php  echo avatar($value['uid'])?>" class="avatar pull-left" height="48" style="vertical-align: middle; border-radius: 4px; margin-right: 10px;" />
                                        <div>
                                            <strong style="font-size: 14px; line-height: 26px;"><?php  echo $value['nickname'];?></strong>
                                        </div>
                                    </a>
                                </td>
                                <td><a href="<?php  echo wmurl('group/view',array('cid'=>$value['cid']),true,true)?>" target="_blank"><?php  echo $value['name'];?></a></td>
                                <td class="text-center"><?php  echo date('Y-m-d H:i',$value['dateline'])?></td>
                                <td><?php  echo $value['content'];?></td>
                                <td class="text-center"><span class="layui-badge layui-bg-orange">待审核</span></td>
                                <td>
                                    <a href="<?php  echo weburl('group/member',array('gid'=>$value['cid'],'uid'=>$value['uid'],'agreet'=>'yes','from'=>'apply'))?>" class="layui-btn layui-btn-blue layui-btn-sm">同意</a>
                                    <a href="<?php  echo weburl('group/member',array('gid'=>$value['cid'],'uid'=>$value['uid'],'refuse'=>'yes','from'=>'apply'));?>" class="layui-btn layui-btn-warm layui-btn-sm refusebtn">拒绝</a>
                                </td>
                            </tr>
                            <?php  } } ?>
                            <?php  if(!$list) { ?>
                            <tr><td colspan="7" class="text-muted text-center">暂无数据</td></tr>
                            <?php  } else { ?>
                            <tr><td colspan="7" class="text-right"><span class="pull-left">共找到<?php  echo $total;?>个申请记录</span> <?php  echo $pager;?></td></tr>
                            <?php  } ?>
                            </tbody>
                        </table>
                        <?php  } else if($_W['action']=='member') { ?>
                        <table class="layui-table" style="margin-top: 0;" id="table-checkbox" lay-filter="advstable" lay-even>
                            <colgroup>
                                <col width="60" />
                                <col width="320" />
                                <col width="180" />
                                <col width="180" />
                                <col width="100" />
                                <col width="100" />
                                <col />
                            </colgroup>
                            <thead>
                            <tr>
                                <th class="text-center">UID</th>
                                <th>用户</th>
                                <th>所在群组</th>
                                <th class="text-center">最后操作/加入时间</th>
                                <th>发言</th>
                                <th class="text-center">状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="navigations">
                            <?php  if(is_array($list)) { foreach($list as $key => $value) { ?>
                            <tr>
                                <td class="text-center"><?php  echo $value['uid'];?></td>
                                <td>
                                    <a href="<?php  echo url('mc/member/base_information',array('uid'=>$value['uid']))?>" target="_blank">
                                        <img src="<?php  echo avatar($value['uid'])?>" class="avatar pull-left" height="48" style="vertical-align: middle; border-radius: 4px; margin-right: 10px;" />
                                        <div>
                                            <strong style="font-size: 14px; line-height: 26px;"><?php  echo $value['nickname'];?></strong>
                                            <br/><?php  if($value['hide']) { ?><span class="text-muted">关闭会话</span><?php  } else { ?><span class="text-muted">开启会话</span><?php  } ?>
                                        </div>
                                    </a>
                                </td>
                                <td><a href="<?php  echo wmurl('group/view',array('cid'=>$value['cid']),true,true)?>" target="_blank"><?php  echo $group['name'];?></a></td>
                                <td class="text-center"><?php  echo date('Y-m-d H:i',$value['dateline'])?><br/><?php  echo date('Y-m-d H:i',$value['addtime'])?></td>
                                <td><?php  echo $value['posts'];?>次</td>
                                <td class="text-center"><span class="layui-badge layui-bg-<?php  if($value['status']==1) { ?>blue<?php  } else if($value['status']==2) { ?>gray<?php  } else { ?>orange<?php  } ?>"><?php  if($value['status']==1) { ?>已加入<?php  } else if($value['status']==2) { ?>已拒绝<?php  } else { ?>待审核<?php  } ?></span></td>
                                <td>
                                    <?php  if($value['status']==1) { ?>
                                    <a href="<?php  echo weburl('group/member',array('gid'=>$value['cid'],'uid'=>$value['uid'],'remove'=>'yes'))?>" class="layui-btn layui-btn-danger layui-btn-sm removebtn">移除</a>
                                    <?php  } else if($value['status']==0) { ?>
                                    <a href="<?php  echo weburl('group/member',array('gid'=>$value['cid'],'uid'=>$value['uid'],'agreet'=>'yes'))?>" class="layui-btn layui-btn-blue layui-btn-sm">同意</a>
                                    <a href="<?php  echo weburl('group/member',array('gid'=>$value['cid'],'uid'=>$value['uid'],'refuse'=>'yes'));?>" class="layui-btn layui-btn-warm layui-btn-sm refusebtn">拒绝</a>
                                    <?php  } ?>
                                </td>
                            </tr>
                            <?php  } } ?>
                            <?php  if(!$list) { ?>
                            <tr><td colspan="7" class="text-muted text-center"><?php  if($group) { ?>暂无数据<?php  } else { ?><strong class="text-danger">请先选择群组查看成员列表</strong><?php  } ?></td></tr>
                            <?php  } else { ?>
                            <tr><td colspan="7" class="text-right"><span class="pull-left">共找到<?php  echo $total;?>个成员</span> <?php  echo $pager;?></td></tr>
                            <?php  } ?>
                            </tbody>
                        </table>
                        <?php  } ?>
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
        form.on('switch(userstatus)',function(data){
            var gid = jQuery(data.elem).attr('data-gid');
            var postdata = {gid:gid,token:'<?php  echo $_W["token"];?>',updatestatus:'yes',inajax:1,status:(data.elem.checked?1:0)};
            jQuery.ajax({url:'<?php  echo weburl("group/list")?>',type:"POST",data:postdata,dataType: 'json',success:function (data) {
                    layer.msg(data.message,{icon:(data.type=='success'?1:2)});
                    if(data.type=='success'){
                        setTimeout(function () {
                            //window.location.reload();
                        },500);
                    }
                }})
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
        $('.hidebtn').click(function () {
            $('.navappend').addClass('layui-hide').find('input.required').attr('required',false).removeAttr('lay-verify');
            layform.render();
        });
        $('#select_all').click(function(){
            $('.navs-item :checkbox').prop('checked', $(this).prop('checked'));
        });
    });
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>