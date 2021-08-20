<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header"><?php  echo $title;?></div>
                <div class="layui-card-body">
                    <form class="layui-form" method="post" action="<?php  echo $this->createWebUrl('saveset')?>" lay-filter="component-form-element">
                        <input type="hidden" name="_token" value="<?php  echo $_W['token'];?>" />
                        <input type="hidden" name="showtab" id="showtab" value="" />
                        <div class="layui-tab" lay-filter="systemset">
                            <ul class="layui-tab-title">
                                <li class="layui-this" lay-id="main"><a href="#main">客户端设置</a></li>
                                <li lay-id="h5"><a href="#h5">H5/公众号</a></li>
                                <li lay-id="wxapp"><a href="#wxapp">微信小程序</a></li>
                                <li lay-id="android"><a href="#android">安卓APP</a></li>
                                <li lay-id="ios"><a href="#ios">IOS-APP</a></li>
                                <li lay-id="aliapp"><a href="#aliapp">支付宝小程序</a></li>
                                <li lay-id="bdapp"><a href="#bdapp">百度小程序</a></li>
                                <li lay-id="ttapp"><a href="#ttapp">字节跳动小程序</a></li>
                                <li lay-id="quickapp"><a href="#quickapp">快应用（华为）</a></li>
                            </ul>
                            <div class="layui-tab-content">
                                <div class="layui-tab-item layui-show">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">客户端接口令牌</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%">
                                                <input type="text" id="platformapitoken" name="data[platform][apitoken]" value="<?php  echo $_S['platform']['apitoken'];?>" placeholder="客户端敏感操作（如登录/注册/支付/修改密码等）将启用接口令牌验证" autocomplete="off" class="layui-input">
                                            </div>
                                            <span class="layui-input-block-btn">
                                                <a class="layui-btn" onclick="makeapitoken('#platformapitoken')" href="javascript:;">生成新令牌</a>
                                                <?php  if($_S['platform']['apitoken'] && $_W['isfounder']) { ?>
                                                <a class="layui-btn layui-btn-normal" lay-tips="接口文件用于制作小程序、APP时接入安装包内" href="<?php  echo weburl('system/platform',array('op'=>'buildsiteinfo'))?>" target="_blank">下载接口文件</a>
                                                <?php  } ?>
                                            </span>
                                            <p class="layui-word-aux">输入32位字符串（大小写字母+数字）<strong class="text-danger">保存后请勿随意改动，否则客户端将无法正常使用。令牌最好在发布客户端之前就生成好且不能随意改动</strong></p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">首屏启动图片</label>
                                        <?php  echo $this->tpl_form_field_image('data[platform][splash]', $_S['platform']['splash'],'最佳尺寸1080x1882像素，请尽量压缩到最小体积');?>
                                        <div class="layui-input-block">
                                            <p class="layui-word-aux">最佳尺寸1080x1882像素，<strong class="color-danger">请尽量压缩到最小体积</strong></p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">首页轮播图片</label>
                                        <?php  echo $this->tpl_form_field_multi_image('data[platform][apprunshow]',$_S['platform']['apprunshow']);?>
                                        <div class="layui-input-block">
                                            <p class="layui-word-aux">最佳尺寸1080x1882像素，<strong class="color-danger">，请尽量压缩到最小体积</strong></p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">下载广告图</label>
                                        <?php  echo $this->tpl_form_field_image('data[platform][startupimg]', $_S['platform']['startupimg'],'长方形图片，最佳尺寸750x360像素');?>
                                        <div class="layui-input-block">
                                            <p class="layui-word-aux">当用户通过其它客户端访问（如H5、小程序）且已制作APP时，将显示在首页，用户点击即可下载或启动客户端</p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">审核模式</label>
                                        <div class="layui-input-block">
                                            <span lay-tips="该功能仅小程序端支持">
                                                <input type="checkbox" lay-filter="ctrls" data-ctrl=".platformaudit" value="1" name="data[platform][auditmode]"<?php  if($_S['platform']['auditmode']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                            </span>
                                        </div>
                                    </div>
                                    <div class="platformaudit<?php  if(!$_S['platform']['auditmode']) { ?> layui-hide<?php  } ?>">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">按钮文字</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[platform][audittext]" placeholder="请输入首页按钮文字" value="<?php  echo $_S['platform']['audittext'];?>" autocomplete="off" class="layui-input" />
                                                <p class="layui-word-aux">将替换掉首页的“立即体验”按钮的文字</p>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">按钮链接</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[platform][auditurl]" placeholder="请输入首页按钮链接" value="<?php  echo $_S['platform']['auditurl'];?>" autocomplete="off" class="layui-input" />
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">登录文字</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[platform][auditlogin]" placeholder="请输入登录入口链接的文字" value="<?php  echo $_S['platform']['auditlogin'];?>" autocomplete="off" class="layui-input" />
                                                <p class="layui-word-aux">如果填写该项，将在首页“立即体验”按钮底部添加登录入口</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <?php  if(is_error($buildh5)) { ?>
                                    <div class="layui-bg-gray" style="padding: 15px; margin-bottom: 15px">
                                        <p><strong><?php  echo $buildh5['message'];?></strong></p>
                                    </div>
                                    <?php  } else { ?>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">H5前端入口</label>
                                        <div class="layui-input-block">
                                            <a href="<?php  echo $buildh5;?>" target="_blank" class="layui-btn">进入H5前端</a>
                                            <a href="javascript:;" data-url="<?php  echo $buildh5;?>" class="layui-btn js-clip layui-btn-normal">复制H5链接</a>
                                            <a href="<?php  echo weburl('system/platform',array('op'=>'rebuildh5'))?>" lay-tips="如已进行更换域名、站点转移等操作请重新生成H5" class="layui-btn layui-btn-warm">重新生成H5</a>
                                        </div>
                                    </div>
                                    <?php  } ?>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">编译模板</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][template]" value="<?php  echo $_S['platform']['template'];?>" required lay-verify="required" placeholder="默认是index.html，如您不清楚该设置的作用请不要随意修改" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">编译引擎</label>
                                        <div class="layui-input-block">
                                            <select name="data[platform][complie]" lay-search>
                                                <option value=""<?php  if($_S['platform']['complie']=='') { ?> selected<?php  } ?>>默认引擎</option>
                                                <?php  if(is_array($plugins)) { foreach($plugins as $plugin) { ?>
                                                <?php  $pluginobj = p($plugin['identity']);?>
                                                <?php  if(method_exists($pluginobj,'buildh5')) { ?>
                                                <option value="<?php  echo $plugin['identity'];?>"<?php  if($_S['platform']['complie']==$plugin['identity']) { ?> selected<?php  } ?>><?php  echo $plugin['name'];?></option>
                                                <?php  } ?>
                                                <?php  } } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <?php  if($_W['isfounder']) { ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <a href="http://wpa.qq.com/msgrd?v=3&uin=390557442&site=qq&menu=yes" target="_blank" class="layui-btn">制作微信小程序</a>
                                        </div>
                                    </div>
                                    <?php  } ?>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">小程序APPID</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][wxappid]" value="<?php  echo $_S['platform']['wxappid'];?>" placeholder="请输入微信小程序的APPID" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">小程序Secret</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][wxsecret]" value="<?php  echo $_S['platform']['wxsecret'];?>" placeholder="请输入微信小程序的Secret密匙串，用于小程序登录" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <?php  if($_W['isfounder']) { ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <a href="<?php  if(MODULE_VERSON=='S') { ?><?php  echo weburl('app/post')?><?php  } else { ?>https://chat.gxit.org/app/index.php?i=4&c=entry&m=swa_supersale&do=app&r=whotalkcloud.post<?php  } ?>" target="_blank" class="layui-btn">制作安卓APP</a>
                                        </div>
                                    </div>
                                    <?php  } ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">客户端版本号</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][version]" value="<?php  echo $_S['platform']['version'];?>" placeholder="请输入最新的安卓APP版本号，当客户端的版本号低于此处设置的值时，将提醒用户升级，如不填写则不通知" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-hide">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">UrlSchemes</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[platform][adurlschemes]" value="<?php  echo $_S['platform']['adurlschemes'];?>" placeholder="请输入安卓客户端的UrlSchemes" autocomplete="off" class="layui-input">
                                                <p class="layui-word-aux">用于在其它客户端打开APP，如不了解如何使用请勿填写</p>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">自动唤醒APP</label>
                                            <div class="layui-input-block">
                                                <input type="checkbox" value="1" name="data[platform][adautolaunch]"<?php  if($_S['platform']['adautolaunch']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                                <p class="layui-word-aux">在用户进入其它客户端（h5、小程序）时自动打开唤醒APP，仅部分设备支持。<strong class="text-danger">如开启自动唤醒则必须填写UrlSchemes才会生效</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">下载地址</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][adrelease]" value="<?php  echo $_S['platform']['adrelease'];?>" placeholder="请输入安卓客户端下载地址，以http://或https://开头" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">输入客户端的APK文件地址，或应用上架到市场的链接</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <?php  if($_W['isfounder']) { ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <a href="<?php  if(MODULE_VERSON=='S') { ?><?php  echo weburl('app/post')?><?php  } else { ?>https://chat.gxit.org/app/index.php?i=4&c=entry&m=swa_supersale&do=app&r=whotalkcloud.post<?php  } ?>" target="_blank" class="layui-btn">制作IOS客户端</a>
                                        </div>
                                    </div>
                                    <?php  } ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">客户端版本号</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][iosversion]" value="<?php  echo $_S['platform']['iosversion'];?>" placeholder="请输入最新的苹果APP版本号，当客户端的版本号低于此处设置的值时，将提醒用户升级，如不填写则不通知" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item layui-hide">
                                        <label class="layui-form-label">UrlSchemes</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][iosurlschemes]" value="<?php  echo $_S['platform']['iosurlschemes'];?>" placeholder="请输入iOS客户端的UrlSchemes" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">用于在其它客户端打开APP，如不了解如何使用请勿填写</p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item layui-hide">
                                        <label class="layui-form-label">自动唤醒APP</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[platform][iosautolaunch]"<?php  if($_S['platform']['iosautolaunch']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                            <p class="layui-word-aux">在用户进入其它客户端（h5、小程序）时自动打开唤醒APP，仅部分设备支持<strong class="text-danger">如开启自动唤醒则必须填写UrlSchemes才会生效</strong></p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">下载地址</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][iosrelease]" value="<?php  echo $_S['platform']['iosrelease'];?>" placeholder="请输入iOS客户端下载地址，以http://或https://开头" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">输入iOS客户端的PLIST文件地址，或应用上架到应用市场的链接</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <p class="layui-bg-red" style="padding: 10px">敬请期待</p>
                                </div>
                                <div class="layui-tab-item">
                                    <p class="layui-bg-red" style="padding: 10px">敬请期待</p>
                                </div>
                                <div class="layui-tab-item">
                                    <?php  if($_W['isfounder']) { ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label"></label>
                                        <div class="layui-input-block">
                                            <a href="http://wpa.qq.com/msgrd?v=3&uin=390557442&site=qq&menu=yes" target="_blank" class="layui-btn">制作字节跳动小程序</a>
                                        </div>
                                    </div>
                                    <?php  } ?>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">小程序APPID</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][ttappid]" value="<?php  echo $_S['platform']['ttappid'];?>" placeholder="请输入今日头条小程序的APPID" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">小程序Secret</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[platform][ttsecret]" value="<?php  echo $_S['platform']['ttsecret'];?>" placeholder="请输入今日头条小程序的Secret密匙串，用于小程序登录" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-tab-item">
                                    <p class="layui-bg-red" style="padding: 10px">敬请期待</p>
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
    var laypage,layer,laytips;
    var showtab = '<?php  echo $_GPC["showtab"];?>';
    layui.use(['form','element'], function(){
        var admin = layui.admin,form = layui.form,element=layui.element;
        layer = layui.layer
        laypage = layui.laypage;
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
    function makeapitoken(target) {
        let code = Wrandom(32);
        jQuery(target).val(code);
    }
</script>
<style type="text/css">
    .layui-form-label {
        width: 102px !important;
    }
    .layui-input-block {
        margin-left: 136px !important;
    }
</style>
<?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/footer', 2)) : (include template('admin/common/footer', TEMPLATE_INCLUDEPATH));?>
