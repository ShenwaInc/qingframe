<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
.layui-form-label{width:202px !important;}
.layui-input-block{margin-left:232px !important;}
</style>
<div class="layui-card layadmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a href="<?php  echo weburl('system')?>"><cite>系统设置</cite></a>
        <?php  if($op!='index') { ?><a href="<?php  echo weburl($_GPC['r'])?>"><cite><?php  echo $title;?></cite></a><?php  } ?>
        <a><cite><?php  echo $operation;?></cite></a>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
            	<div class="layui-card-header"><?php  echo $operation;?></div>
                <div class="layui-card-body">
                    <?php  if($op=='edit') { ?>
                    <form class="layui-form" method="post" action="<?php  echo weburl('system/language')?>" lay-filter="component-form-element">
                        <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        <input type="hidden" name="op" value="edit" />
                        <input type="hidden" name="lang" value="<?php  echo $lang;?>" />
                        <?php  if(!$lang) { ?>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">语言包标识</label>
                            <div class="layui-input-block">
                                <input type="text" name="identifie" value="" lay-verify="required" placeholder="字母/数字/下划线，必须以字母开头" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">语言包名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="languagename" value="" lay-verify="required" placeholder="如简体中文、繁体中文、英文等" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <?php  } ?>
                        <?php  if(is_array($languages)) { foreach($languages as $key => $value) { ?>
                        <div class="layui-form-item must">
                            <label class="layui-form-label"><?php  echo $key;?></label>
                            <div class="layui-input-block">
                                <input type="text" name="langs[<?php  echo $key;?>]" value="<?php  echo $value;?>" lay-verify="required" placeholder="请根据原有内容修改文字" autocomplete="off" class="layui-input">
                                <?php  if(strpos($value,'{')!==false) { ?><p class="text-danger">里面的&nbsp;<b>{{<?php echo 数字;?>}}</b>&nbsp;代表变量，请不要改动，避免显示异常</p><?php  } ?>
                            </div>
                        </div>
                        <?php  } } ?>
                        <div class="layui-form-item appendrow">
                            <a href="javascript:;" onclick="AddLang()" class="layui-btn layui-btn-xs layui-btn-fluid">+新增一个词条</a>
                            <label class="layui-form-label">&nbsp;</label>
                            <div class="layui-input-block"><p class="layui-word-aux"><strong class="text-danger">注：新增的词条仅对当前语言有效，且还原语言包后将丢失</strong> </p></div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                              <button class="layui-btn" lay-submit lay-filter="formDemo" type="submit" value="true" name="savedata">保存</button>
                              <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                            </div>
                        </div>
                    </form>
                    <?php  } else { ?>
                    <div class="layui-word-aux">
                        新增/编辑或还原语言包后需要更新缓存才会生效&nbsp;&nbsp;<a href="javascript:;" layadmin-event="updateCache" class="layui-btn layui-btn-sm layui-btn-warm">更新缓存</a><a href="<?php  echo weburl('system/language',array('op'=>'edit'))?>" class="layui-btn layui-btn-sm layui-btn-normal">新增语言包</a>
                    </div>
                    <table class="layui-table">
                        <colgroup>
                            <col width="200">
                            <col>
                            <col width="320">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>标识</th>
                                <th>名称</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  if(is_array($langs)) { foreach($langs as $key => $lang) { ?>
                            <tr>
                                <td><?php  echo $key;?></td>
                                <td><?php  echo $lang;?></td>
                                <td>
                                    <div class="layui-btn-group">
                                        <a href="<?php  echo weburl('system/language',array('op'=>'edit','lang'=>$key))?>" class="layui-btn layui-btn-sm layui-btn-default"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                                        <?php  if(file_exists($temppath.$_W['uniacid'].'_'.$key.'.temp.php')) { ?>
                                        <a href="<?php  echo weburl('system/language',array('op'=>'upgrade','lang'=>$key))?>" lay-tips="当升级新版本涉及到语言包更新时请点此更新语言包" class="layui-btn layui-btn-sm layui-btn-normal"><i class="layui-icon layui-icon-upload"></i>更新</a>
                                        <a href="<?php  echo weburl('system/language',array('op'=>'restore','lang'=>$key))?>" class="layui-btn layui-btn-sm layui-btn-danger" onclick="if(!confirm('是否确认将语言包还原为官方提供的语言包？')){return false;}"><i class="layui-icon layui-icon-refresh-1"></i>还原</a>
                                        <?php  } ?>
                                    </div>
                                </td>
                            </tr>
                            <?php  } } ?>
                        </tbody>
                    </table>
                    <?php  } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var Controller = "<?php  echo $_W['controller'];?>",Action = "<?php  echo $_W['action'];?>";
var laypicker,laypage,layupload,layer,layecharts;
var tempopenid = '';
var showtab = '<?php  echo $_GPC["showtab"];?>';
layui.use(['form','element','colorpicker'], function(){
	var admin = layui.admin,form = layui.form,element=layui.element,colorpicker = layui.colorpicker;
	layer = layui.layer
	laypage = layui.laypage;
        colorpicker.render({elem:"#themecolorpicker",color:"<?php  echo $_S['theme']['color'];?>",done: function(color){$('#themecolor').val(color)}});
	layer.ready(function(){
		if(showtab==''){
			var tabs = location.href.split('#');
			showtab = typeof(tabs[1])!='undefined' ? tabs[1] : false;
		}
		if(showtab){
			element.tabChange('systemset', showtab);
		}
	});
	element.on('tab(systemset)', function(data){
		jQuery('#showtab').val(jQuery(this).attr('lay-id'));
	});
	form.on('switch(ctrls)',function(data){
		var ctrltarget = jQuery(data.elem).attr('data-ctrl');
		if(data.elem.checked){
			jQuery(ctrltarget).removeClass('layui-hide');
		}else{
			jQuery(ctrltarget).addClass('layui-hide');
		}
	});
});
function AddLang(){
	var html = '<div class="layui-form-item"><div class="layui-form-label" style="padding: 0 0 0 15px;"><input type="text" name="appendkey[]" value="" required placeholder="字母开头，字母/数字/下划线" autocomplete="off" class="layui-input"></div><div class="layui-input-block"><input type="text" name="appendvalue[]" value="" required placeholder="词条内容" autocomplete="off" class="layui-input"></div></div>';
	jQuery('.appendrow').before(html);
}
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>
