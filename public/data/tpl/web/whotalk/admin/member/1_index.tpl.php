<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
.layui-form-label{width:202px !important;}
.layui-input-block{margin-left:232px !important;}
</style>
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
                <div class="layui-card-header"><?php  echo $operation;?><?php  if($_W['action']=='delete') { ?> - <?php  echo $member['nickname'];?><?php  } ?></div>
                <div class="layui-card-body">
                    <?php  if($_W['action']=='list') { ?>
                    <div class="layui-table-tool" style="width: auto; border-bottom: 0;">
                        <form method="post" class="layui-form" action="<?php  echo weburl('member/list')?>">
                            <div class="layui-table-tool-temp">
                                <div class="layui-btn-container layui-inline">
                                    <a href="<?php  echo url('mc/member/add')?>" target="_blank" class="layui-btn layui-btn-normal" lay-tips="后台添加用户后，需要登录一次才会在此列表中出现">添加用户</a>
                                </div>
                                <input type="hidden" name="_token" value="<?php  echo $_W['token'];?>" />
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width: 320px;">
                                        <input type="text" name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键字搜索，如昵称/手机号/邮箱/UID" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width: 100px;">
                                        <select name="status">
                                            <option value="-1">状态</option>
                                            <option value="1"<?php  if($_GPC['status']==1) { ?> selected<?php  } ?>>正常</option>
                                            <option value="0"<?php  if(isset($_GPC['status']) && $_GPC['status']==0) { ?> selected<?php  } ?>>禁止</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width: 100px;">
                                        <select name="follow">
                                            <option value="-1">所有用户</option>
                                            <option value="1"<?php  if($_GPC['follow']==1) { ?> selected<?php  } ?>>已关注</option>
                                            <option value="0"<?php  if(isset($_GPC['follow']) && $_GPC['follow']==0) { ?> selected<?php  } ?>>未关注</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline" style="width: 150px;">
                                        <select name="groupid">
                                            <option value="0">所有等级</option>
                                            <?php  if(is_array($_W['account']['groups'])) { foreach($_W['account']['groups'] as $group) { ?>
                                            <option value="<?php  echo $group['groupid'];?>"<?php  if($group['groupid']==$_GPC['groupid']) { ?> selected<?php  } ?>><?php  echo $group['title'];?></option>
                                            <?php  } } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-btn-container layui-inline">
                                    <button type="submit" value="true" name="searchsubmit" class="layui-btn layui-btn-normal">搜索</button>
                                    <?php  if($_GPC['warned']==1) { ?>
                                    <a href="<?php  echo weburl('member/list')?>" class="layui-btn layui-btn-normal">所有用户</a>
                                    <?php  } else { ?>
                                    <a href="<?php  echo weburl('member/list',array('warned'=>1))?>" lay-tips="因操作原因被系统标记为敏感的用户，如发送含有敏感词的消息、动态、评论等。可视情况封禁账号" class="layui-btn layui-btn-danger">敏感用户</a>
                                    <?php  } ?>
                                    <a href="javascript:;" lay-tips="认证公众号对已关注的用户进行批量标签。操作前最好先同步粉丝数据，避免出现遗漏。" class="batchtagging layui-btn">批量标签</a>
                                    <a href="javascript:;" lay-tips="可以设置新用户注册时的起始UID，设置后UID将从该数值递增" class="uidinit layui-btn layui-btn-warm">初始化UID</a>
                                </div>
                            </div>
                            <div class="layui-table-tool-self">
                                <button type="submit" value="true" name="exportnow" class="layui-btn layui-btn-xs layui-btn-warm" lay-tips="导出当前"><i class="layui-icon layui-icon-export"></i></button>
                                <button type="submit" value="true" name="exportall" class="layui-btn layui-btn-xs layui-btn-danger" lay-tips="导出全部"><i class="layui-icon layui-icon-export"></i></button>
                            </div>
                        </form>
                    </div>
                    <?php  } ?>
                    <form class="layui-form" method="post" action="<?php  echo weburl('member/'.$_W['action'])?>" lay-filter="component-form-element">
                        <input type="hidden" name="_token" value="<?php  echo $_W['token'];?>" />
                        <?php  if($_W['action']=='list') { ?>
                        <table class="layui-table" style="margin-top: 0;" id="table-checkbox" lay-filter="advstable" lay-even>
                            <colgroup>
                                <col width="60" />
                                <col width="260" />
                                <col width="180" />
                                <col width="150" />
                                <col width="180" />
                                <col width="100" />
                                <col />
                            </colgroup>
                            <thead>
                            <tr>
                                <th class="text-center">UID</th>
                                <th>昵称</th>
                                <th>手机号/邮箱</th>
                                <th class="text-center">余额/<?php  echo $_S['credit']['creditname'];?></th>
                                <th class="text-center">最后在线/注册时间</th>
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
                                            <strong style="font-size: 14px; line-height: 26px;"><?php  echo $value['nickname'];?></strong>&nbsp;&nbsp;<?php  if($value['follow']) { ?><span class="layui-badge layui-bg-blue">已关注</span><?php  } else { ?><span class="layui-badge layui-bg-gray">未关注</span><?php  } ?><?php  if($value['warned']) { ?>&nbsp;&nbsp;<span class="layui-badge layui-bg-red">敏感</span><?php  } ?>
                                            <br/><span class="text-muted"><?php  echo $value['title'];?></span><?php  if($value['clientid']) { ?>&nbsp;&nbsp;<span class="js-clip" data-url="<?php  echo $value['clientid'];?>" lay-tips="最后登录IP：<?php  echo $value['clientid'];?>"><i class="layui-icon layui-icon-location"></i></span><?php  } ?>
                                        </div>
                                    </a>
                                </td>
                                <td><i class="layui-icon layui-icon-cellphone"></i>&nbsp;<?php  echo $value['mobile'];?><br/><i class="fa fa-envelope-o"></i>&nbsp;<?php  if(strpos($value['email'],'@we7.cc')!==false || !$value['email']) { ?>暂无<?php  } else { ?><?php  echo $value['email'];?><?php  } ?></td>
                                <td class="text-center"><i class="layui-icon layui-icon-rmb"></i>&nbsp;<?php  echo $value['credit2'];?><br/><i class="layui-icon layui-icon-diamond"></i>&nbsp;<?php  echo $value['credit1'];?></td>
                                <td class="text-center"><?php  echo date('Y-m-d H:i',$value['dateline'])?><br/><?php  echo date('Y-m-d H:i',$value['addtime'])?></td>
                                <td class="text-center"><input type="checkbox" lay-filter="userstatus" data-uid="<?php  echo $value['uid'];?>" name="status[<?php  echo $value['id'];?>]"<?php  if($value['status']) { ?> checked<?php  } ?> value="1" lay-skin="switch" lay-text="正常|禁止" /></td>
                                <td>
                                    <div class="layui-btn-group">
                                        <a href="javascript:;" data-url="<?php  echo wmurl('dialog',array('cid'=>$value['uid'],'type'=>'normal'),true)?>" class="layui-btn layui-btn-normal layui-btn-sm js-clip"><i class="fa fa-copy"></i>&nbsp;复制链接</a>
                                        <a href="javascript:;" class="layui-btn layui-btn-sm modal-trade-credit2" data-type="credit2" data-uid="<?php  echo $value['uid'];?>" data-title="余额"><i class="layui-icon layui-icon-rmb" style="vertical-align: middle"></i> 余额充值</a>
                                        <a href="javascript:;" class="layui-btn layui-btn-sm modal-trade-credit1" data-type="credit1" data-uid="<?php  echo $value['uid'];?>" data-title="<?php  echo $_S['credit']['creditname'];?>"><i class="layui-icon layui-icon-diamond" style="vertical-align: middle"></i> <?php  echo $_S['credit']['creditname'];?>充值</a>
                                        <a href="<?php  echo weburl('member/delete',array('uid'=>$value['uid']));?>" class="layui-btn layui-btn-danger layui-btn-sm"><i class="layui-icon layui-icon-delete"></i>清理</a>
                                        <a href="<?php  echo weburl('content/message',array('uid'=>$value['uid']));?>" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon layui-icon-chat"></i>消息</a>
                                        <a href="<?php  echo weburl('content/album',array('uid'=>$value['uid']));?>" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon layui-icon-picture"></i>动态</a>
                                    </div>
                                </td>
                            </tr>
                            <?php  } } ?>
                            <?php  if(!$list) { ?>
                            <tr><td colspan="7" class="text-muted text-center">暂无数据</td></tr>
                            <?php  } ?>
                            <tr><td colspan="7" class="text-right"><span class="pull-left">共找到<?php  echo $total;?>个用户</span> <?php  echo $pager;?></td></tr>
                            </tbody>
                        </table>
                        <?php  } else if($_W['action']=='delete') { ?>
                        <input type="hidden" name="uid" value="<?php  echo $member['uid'];?>" />
                        <div class="layui-form-item">
                            <label class="layui-form-label">清除内容</label>
                            <div class="layui-input-block">
                                <input type="checkbox" name="clear[chatfriend]" value="1" title="私聊信息">
                                <input type="checkbox" name="clear[friendship]" value="1" title="好友关系">
                                <input type="checkbox" name="clear[chatgroup]" value="1" title="群聊信息">
                                <input type="checkbox" name="clear[groupjoin]" value="1" title="<?php  wlang('text_groupname')?>数据">
                                <input type="checkbox" name="clear[collectinfo]" value="1" title="收藏信息">
                                <input type="checkbox" name="clear[moment]" value="1" title="个人相册">
                                <input type="checkbox" name="clear[face]" value="1" title="自定义表情">
                                <input type="checkbox" name="clear[comment]" value="1" title="评论数据">
                                <input type="checkbox" name="clear[cashlog]" value="1" title="提现记录">
                                <input type="checkbox" name="clear[redpacket]" value="1" title="红包记录">
                                <span lay-tips="删除用户上传视频会连同视频文件一起删除，转码的视频及封面需要手动删除">
                                    <input type="checkbox" name="clear[videos]" value="1" title="上传视频">
                                </span>
                                <span lay-tips="该用户在其它模块可能也使用到快捷登录，清理后用户的所有模块的快捷登录数据都将被清理">
                                    <input type="checkbox" name="clear[quicklogin]" value="1" title="快捷登录">
                                </span>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-warm" lay-tips="清理数据将仅清理以上勾选的数据，保留账号及关键信息" onclick="if(!confirm('清理后不可恢复，是否确定要清理？')){return false;}" lay-submit lay-filter="formDemo" type="submit" value="true" name="savedata">清理数据</button>
                                <button class="layui-btn layui-btn-danger" lay-tips="直接删除将删除该用户账户信息及其产生的所有数据" onclick="if(!confirm('删除后不可恢复，是否确定要删除？')){return false;}" lay-submit lay-filter="formDemo" type="submit" value="true" name="deletebtn">直接删除</button>
                                <a class="layui-btn" href="<?php  echo weburl('member/list')?>">返回</a>
                            </div>
                        </div>
                        <?php  } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
require(['trade'], function(trade){
    trade.init();
});
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
        var uid = jQuery(data.elem).attr('data-uid');
        var postdata = {uid:uid,_token:'<?php  echo $_W["token"];?>',updatestatus:'yes',inajax:1,status:(data.elem.checked?1:0)};
        jQuery.ajax({url:'<?php  echo weburl("member/list")?>',type:"POST",data:postdata,dataType: 'json',success:function (data) {
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
    $('#select_all').click(function(){
        $('.navs-item :checkbox').prop('checked', $(this).prop('checked'));
    });
    $('.uidinit').click(function () {
        var index = layer.prompt({
            formType: 0,
            value: '',
            title: '请输入起始UID（不能小于当前最大UID）',
            maxlength:20
        }, function(value, index){
            $.ajax({url:'<?php  echo weburl("member/uidinit")?>',type:"POST",data: {_token:'<?php  echo $_W["token"];?>',uidinit:value,submit:1},dataType: 'json',success:function (ret) {
                    layer.close(index);
                    var icon = ret.type=='success' ? 1 : 2;
                    layer.msg(ret.message,{icon:icon});
                    if (ret.redirect!=''){
                        setTimeout(function () {
                            window.location.href = ret.redirect;
                        },1000);
                    }
                }});
        });
        return false;
    });
    $('.batchtagging').click(function () {
        var index = layer.prompt({
            formType: 0,
            value: 'whotalk',
            title: '请输入标签名（如标签不存在则自动创建）',
            maxlength:20
        }, function(value, index){
            $.ajax({url:'<?php  echo weburl("member/batchtagging")?>',type:"POST",data: {_token:'<?php  echo $_W["token"];?>',tagname:value,submit:1},dataType: 'json',success:function (ret) {
                layer.close(index);
                var icon = ret.type=='success' ? 1 : 2;
                layer.msg(ret.message,{icon:icon});
                if (ret.redirect!=''){
                    setTimeout(function () {
                        window.location.href = ret.redirect;
                    },1000);
                }
            }});
        });
        return false;
    });
});
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>
