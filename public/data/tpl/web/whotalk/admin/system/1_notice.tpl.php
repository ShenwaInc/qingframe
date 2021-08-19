<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<style type="text/css">
    .layui-form-label{width:92px;}
    .layui-input-block{margin-left:122px;}
</style>
<div class="layui-card layadmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a><cite>系统设置</cite></a>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <?php  if((!\App\Services\CloudService::ComExists('aliyun') || !!\App\Services\CloudService::ComExists('getui')) && $_W['isfounder']) { ?>
            <div class="layui-card">
                <div class="layui-card-header">缺少组件</div>
                <div class="layui-card-body">
                    <p style="padding: 10px">
                        <strong class="text-danger">您尚未安装阿里云短信组件或APP推送组件，以下功能可能无法使用</strong>
                        <a href="<?php  echo url('console/setting/component')?>" class="layui-btn">立即加载</a>
                    </p>
                </div>
            </div>
            <?php  } ?>
            <div class="layui-card">
                <div class="layui-card-header">消息通知</div>
                <div class="layui-card-body">
                    <form class="layui-form" id="system-notice-form" method="post" action="<?php  echo $this->createWebUrl('saveset')?>" lay-filter="component-form-element">
                        <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                        <input type="hidden" name="showtab" id="showtab" value="" />
                        <div class="layui-tab">
                            <ul class="layui-tab-title">
                                <li class="layui-this" lay-id="sms"><a href="#sms">短信通知</a></li>
                                <li lay-id="account"><a href="#account">公众号模板通知</a></li>
                                <li lay-id="unipush"><a href="#unipush">APP推送</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show">
                                    <div style="padding:2px 10px; margin-bottom:20px;">目前采用&nbsp;&nbsp;<a href="https://promotion.aliyun.com/ntms/yunparter/invite.html?userCode=v99u8qju" target="_blank" class="layui-btn layui-btn-xs">阿里云短信</a>&nbsp;&nbsp;通道，更多通道请联系技术人员定制</div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">Key ID</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%;">
                                                <input type="text" name="data[sms][appid]" value="<?php  echo $_S['sms']['appid'];?>" placeholder="在控制台获得的Access Key ID" autocomplete="off" class="layui-input">
                                            </div>
                                            <a class="layui-btn" href="https://promotion.aliyun.com/ntms/yunparter/invite.html?userCode=v99u8qju" target="_blank">立即申请</a>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">Key Secret</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[sms][secret]" value="<?php  echo $_S['sms']['secret'];?>" placeholder="在控制台获得的Access Key Secret" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">短信签名</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[sms][sign]" value="<?php  echo $_S['sms']['sign'];?>" placeholder="短信签名" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">模板CODE</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%;">
                                                <input type="text" name="data[sms][templaid]" value="<?php  echo $_S['sms']['templaid'];?>" placeholder="在阿里云申请的短信模板CODE，一般以SMS_开头" autocomplete="off" class="layui-input">
                                            </div>
                                            <a class="layui-btn" href="https://dysms.console.aliyun.com/dysms.htm#/domestic/text/template/add" target="_blank">添加模板</a>
                                            <p class="layui-word-aux">申请短信模板CODE时模板内容参考“您的验证码：${code}，请不要把验证码泄露给任何人。如非本人操作请忽略”<a href="javascript:;" class="js-clip color-default" data-url="您的验证码：${code}，请不要把验证码泄露给任何人。如非本人操作请忽略">复制</a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <?php  if($_W['account']['level']!=4) { ?><div style="padding:5px 10px; margin-bottom:20px;" class="layui-bg-red">以下功能仅认证服务号支持</div><?php  } ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">通知时间间隔</label>
                                        <div class="layui-input-block">
                                            <div class="input-group" style="width: 50%;">
                                                <input type="text" name="data[notice][accountrate]" value="<?php  echo $_S['notice']['accountrate'];?>" placeholder="单位为秒，建议设置间隔5分钟以上，否则公众号有封号风险" autocomplete="off" class="layui-input">
                                                <span class="input-group-addon">秒</span>
                                            </div>
                                            <p class="layui-word-aux"><strong class="text-danger">建议设置间隔5分钟以上，对于聊天量大的平台使用此功能可能会因滥用模板消息等而被封</strong></p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">新消息通知</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%;">
                                                <input type="text" name="data[notice][wx_newmsg]" id="wx_newmsg" value="<?php  echo $_S['notice']['wx_newmsg'];?>" placeholder="请输入模板ID" autocomplete="off" class="layui-input">
                                            </div>
                                            <?php  if(!$_S['notice']['wx_newmsg'] && $_W['account']['level']==4) { ?>
                                            <a class="layui-btn js-autotplid" href="javascript:;" data-field="#wx_newmsg" data-tid="OPENTM411665252">自动获取</a>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">等待审核通知</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%;">
                                                <input type="text" name="data[notice][wx_verify]" id="wx_verify" value="<?php  echo $_S['notice']['wx_verify'];?>" placeholder="请输入模板ID" autocomplete="off" class="layui-input">
                                            </div>
                                            <?php  if(!$_S['notice']['wx_newmsg'] && $_W['account']['level']==4) { ?>
                                            <a class="layui-btn js-autotplid" href="javascript:;" data-field="#wx_verify" data-tid="OPENTM408471635">自动获取</a>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">原生推送<span class="layui-icon layui-icon-about" lay-tips="该功能仅在原生APP上生效"></span></label>
                                        <div class="layui-input-block">
                                            <input type="radio" lay-filter="ctrls" data-target=".unipush" value="0" name="data[unipush][switch]" title="关闭"<?php  if(!$_S['unipush']['switch']) { ?> checked="checked"<?php  } ?> />
                                            <input type="radio" lay-filter="ctrls" data-target=".unipush" value="1" name="data[unipush][switch]" title="uniPUSH"<?php  if($_S['unipush']['switch']==1) { ?> checked="checked"<?php  } ?> />
                                            <input type="radio" lay-filter="ctrls" data-target=".unipush" value="2" name="data[unipush][switch]" title="第三方推送"<?php  if($_S['unipush']['switch']==2) { ?> checked="checked"<?php  } ?> />
                                        </div>
                                    </div>
                                    <div class="unipush form-item2<?php  if($_S['unipush']['switch']!=2) { ?> layui-hide<?php  } ?>">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">推送渠道</label>
                                            <div class="layui-input-block">
                                                <select name="data[unipush][channel]" lay-search>
                                                    <option value=""<?php  if($_S['unipush']['channel']=='') { ?> selected<?php  } ?>>请选择第三方推送渠道</option>
                                                    <?php  if(is_array($plugins)) { foreach($plugins as $plugin) { ?>
                                                    <?php  $pluginobj = p($plugin['identity']);?>
                                                    <?php  if(method_exists($pluginobj,'unipush')) { ?>
                                                    <option value="<?php  echo $plugin['identity'];?>"<?php  if($_S['unipush']['channel']==$plugin['identity']) { ?> selected<?php  } ?>><?php  echo $plugin['name'];?></option>
                                                    <?php  } ?>
                                                    <?php  } } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="unipush form-item1<?php  if($_S['unipush']['switch']!=1) { ?> layui-hide<?php  } ?>">
                                        <div class="layui-form-item must">
                                            <label class="layui-form-label">AppID</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[unipush][appid]" value="<?php  echo $_S['unipush']['appid'];?>" autocomplete="off" class="layui-input" />
                                            </div>
                                        </div>
                                        <div class="layui-form-item must">
                                            <label class="layui-form-label">AppKey</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[unipush][appkey]" value="<?php  echo $_S['unipush']['appkey'];?>" autocomplete="off" class="layui-input" />
                                            </div>
                                        </div>
                                        <div class="layui-form-item must">
                                            <label class="layui-form-label">MasterSecret</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[unipush][mastersecret]" value="<?php  echo $_S['unipush']['mastersecret'];?>" autocomplete="off" class="layui-input" />
                                            </div>
                                        </div>
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
<script>
    var Controller = "<?php  echo $_W['controller'];?>",Action = "<?php  echo $_W['action'];?>";
    var laypicker,laypage,layupload,layer,layecharts,laytips;
    var tempopenid = '';
    var showtab = '<?php  echo $_GPC["showtab"];?>';
    layui.use(['form','element','colorpicker'], function(){
        var admin = layui.admin,form = layui.form,element=layui.element,colorpicker = layui.colorpicker;
        layer = layui.layer
        laypage = layui.laypage;
        colorpicker.render({elem:"#themecolorpicker",color:"<?php  echo $_S['theme']['color'];?>",done: function(color){$('#themecolor').val(color)}});
        colorpicker.render({elem:"#themeactivepicker",color:"<?php  echo $_S['theme']['active'];?>",done: function(color){$('#themeactive').val(color)}});
        colorpicker.render({elem:"#themelightpicker",color:"<?php  echo $_S['theme']['light'];?>",done: function(color){$('#themelight').val(color)}});
        colorpicker.render({elem:"#themelinkpicker",color:"<?php  echo $_S['theme']['link'];?>",done: function(color){$('#themelink').val(color)}});
        colorpicker.render({elem:"#themechatbgpicker",color:"<?php  echo $_S['theme']['chatbg'];?>",done: function(color){$('#themechatbg').val(color)}});
        colorpicker.render({elem:"#themechatfontpicker",color:"<?php  echo $_S['theme']['chatfont'];?>",done: function(color){$('#themechatfont').val(color)}});
        layer.ready(function(){
            if(showtab==''){
                var tabs = location.href.split('#');
                showtab = typeof(tabs[1])!='undefined' ? tabs[1] : false;
            }
            if(showtab){
                element.tabChange('systemset', showtab);
            }
            $('.js-autotplid').click(function () {
                let inputfield = $(this).data('field');
                let tid = $(this).data('tid');
                let tplid = $(inputfield).val();
                if (tplid!='') return layer.msg('模板ID已存在，请勿重复获取',{icon:2});
                jQuery.ajax({
                    url:'<?php  echo weburl("system/notice")?>',
                    data:{
                        autotplid:"true",
                        tid:tid,
                        submit:1,
                        token:"<?php  echo $_W['token'];?>"
                    },
                    type:"POST",
                    dataType:"json",
                    success(res){
                        if(res.type!=='success'){
                            return layer.msg(res.message,{icon:2});
                        }
                        $(inputfield).val(res.message);
                        jQuery('#system-notice-form').submit();
                    }
                });
            });
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
        form.on('radio(ctrls)', function(data){
            var target = $(data.elem).data('target');
            $(target).addClass('layui-hide');;
            $(target+'.form-item'+data.value).removeClass('layui-hide');
        });
    });
</script>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>
