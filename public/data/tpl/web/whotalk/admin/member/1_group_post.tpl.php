<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<div class="layui-fluid">
    <form class="layui-form" method="post" action="<?php  echo weburl('member/group',array('op'=>'post'))?>">
        <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
        <?php  if($group) { ?>
        <input type="hidden" name="groupid" value="<?php  echo $group['groupid'];?>" />
        <?php  } ?>
        <div class="layui-form-item">
            <label class="layui-form-label">等级名称</label>
            <div class="layui-input-block">
                <input type="text" name="data[title]" required lay-verify="required" placeholder="请输入该等级名称" value="<?php  echo $group['title'];?>" autocomplete="off" class="layui-input" />
            </div>
        </div>
        <div class="layui-form-item<?php  if($group_level==0) { ?> layui-hide<?php  } ?>">
            <label class="layui-form-label">升级条件</label>
            <div class="layui-input-block">
                <div class="input-group">
                    <input type="text" name="data[credit]" placeholder="请输入该升级到该等级所需总积分" value="<?php  echo $group['credit'];?>" autocomplete="off" class="layui-input" />
                    <span class="input-group-addon">积分</span>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">购买方式</label>
            <div class="layui-input-block">
                <input type="radio" lay-filter="ctrls" data-ctrl="paytype" name="data[paytype]" title="月付" value="0"<?php  if(!$group['paytype']) { ?> checked<?php  } ?> />
                <input type="radio" lay-filter="ctrls" data-ctrl="paytype" name="data[paytype]" title="季付" value="1"<?php  if($group['paytype']==1) { ?> checked<?php  } ?> />
                <input type="radio" lay-filter="ctrls" data-ctrl="paytype" name="data[paytype]" title="年付" value="2"<?php  if($group['paytype']==2) { ?> checked<?php  } ?> />
            </div>
        </div>
        <div class="layui-form-item<?php  if($group_level!=0) { ?> layui-hide<?php  } ?>">
            <label class="layui-form-label">续费价格</label>
            <div class="layui-input-block">
                <div class="input-group">
                    <input type="text" name="data[price]" placeholder="请输入该等级续费价格" value="<?php  echo $group['price'];?>" autocomplete="off" class="layui-input" />
                    <span class="input-group-addon form-paytype form-paytype0<?php  if($group['paytype']) { ?> layui-hide<?php  } ?>">元/月</span>
                    <span class="input-group-addon form-paytype form-paytype1<?php  if($group['paytype']!=1) { ?> layui-hide<?php  } ?>">元/季</span>
                    <span class="input-group-addon form-paytype form-paytype2<?php  if($group['paytype']!=2) { ?> layui-hide<?php  } ?>">元/年</span>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">更多设置</label>
            <div class="layui-input-block">
                <input type="checkbox" name="data[isdefault]" value="1" title="默认"<?php  if($group['isdefault']) { ?> checked<?php  } ?> />
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo" type="submit" value="true" name="savedata">保存</button>
                <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                <?php  if($group) { ?>
                <a href="<?php  echo weburl('member/group',array('op'=>'remove','groupid'=>$group['groupid']))?>" class="layui-btn layui-btn-danger" onclick="if(!confirm('删除后不可恢复，是否确定要删除？')){return false}">删除</a>
                <?php  } ?>
            </div>
        </div>
    </form>
</div>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>