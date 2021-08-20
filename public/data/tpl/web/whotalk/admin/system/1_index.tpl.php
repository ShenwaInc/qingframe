<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/header', 2)) : (include template('admin/common/header', TEMPLATE_INCLUDEPATH));?>
<div class="layui-card layadmin-header">
    <div class="layui-breadcrumb" lay-filter="breadcrumb">
        <a><cite>系统设置</cite></a>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
            	<div class="layui-card-header">系统设置</div>
                <div class="layui-card-body">
                <form class="layui-form" method="post" action="<?php  echo weburl('system')?>" lay-filter="component-form-element">
                    <input type="hidden" name="_token" value="<?php  echo $_W['token'];?>" />
                    <input type="hidden" name="showtab" id="showtab" value="" />
                    <div class="layui-tab" lay-filter="systemset">
                        <ul class="layui-tab-title">
                            <li class="layui-this" lay-id="basic"><a href="#basic">应用信息</a></li>
                            <li lay-id="register"><a href="#register">注册设置</a></li>
                            <li lay-id="group"><a href="#group">聊天设置</a></li>
                            <li lay-id="credit"><a href="#credit">财务设置</a></li>
                            <li lay-id="attach"><a href="#attach">附件设置</a></li>
                            <li lay-id="domain"><a href="#domain">通讯设置</a></li>
                            <li lay-id="album"><a href="#album">广场朋友圈</a></li>
                            <li lay-id="share"><a href="#share">分享与SEO</a></li>
                            <li lay-id="style"><a href="#style">界面与风格</a></li>
                        </ul>
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">应用名称</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[basic][name]" value="<?php  echo $_S['basic']['name'];?>" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" />
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">应用说明</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[basic][description]" value="<?php  echo $_S['basic']['description'];?>" placeholder="用一句话描述这个应用" autocomplete="off" class="layui-input" />
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">应用标志(favicon)</label>
                                    <?php  echo $this->tpl_form_field_image('data[basic][icon]', $_S['basic']['icon'],'正方形图片（60*60）');?>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">应用LOGO</label>
                                    <?php  echo $this->tpl_form_field_image('data[basic][logo]', $_S['basic']['logo'],'正方形图片（200*200）');?>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">默认图片</label>
                                    <?php  echo $this->tpl_form_field_image('data[basic][defaultimg]', $_S['basic']['defaultimg'],'默认缺省图片');?>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">默认语言</label>
                                    <div class="layui-input-inline">
                                        <div>
                                        <select name="data[basic][defaultlanguage]" lay-verify="" lay-search>
                                            <?php  $langs = m()->getlanguages();?>
                                            <?php  if(is_array($langs)) { foreach($langs as $key => $value) { ?>
                                            <option value="<?php  echo $key;?>"<?php  if($key==$_S['basic']['defaultlanguage']) { ?> selected="selected"<?php  } ?>><?php  echo $value;?></option>
                                            <?php  } } ?>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-mid layui-word-aux">
                                        切换语言后需要更新缓存才会生效&nbsp;&nbsp;<a href="javascript:;" layadmin-event="updateCache" class="layui-btn layui-btn-default">更新缓存</a>&nbsp;&nbsp;<a href="<?php  echo weburl('system/language')?>" class="layui-btn layui-btn-warm">管理语言</a>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">默认表情包</label>
                                    <div class="layui-input-inline">
                                        <div>
                                            <select name="data[basic][facepath]">
                                                <?php  $faces = m()->getfaces();?>
                                                <?php  if(is_array($faces)) { foreach($faces as $key) { ?>
                                                <option value="<?php  echo $key;?>"<?php  if($key==$_S['basic']['facepath']) { ?> selected="selected"<?php  } ?>><?php  echo $key;?></option>
                                                <?php  } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-mid layui-word-aux">表情格式</div>
                                    <div class="layui-input-inline" style="width:20%">
                                        <input type="text" name="data[basic][faceformat]" value="<?php  echo $_S['basic']['faceformat'];?>" placeholder="gif/png/jpg/svg" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">软著编号</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[basic][icp]" value="<?php  echo $_S['basic']['icp'];?>" placeholder="应用软件著作权编号或ICP备案号" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">屏蔽敏感词</label>
                                    <div class="layui-input-block">
                                        <textarea name="data[basic][sensitive]" placeholder="用户发布的任何信息包含此处设置的敏感词将用 * 代替，同时系统会自动将信息和用户标为敏感信息/用户。多个敏感词之间请用 | 隔开（不留空格）" class="layui-textarea"><?php  echo $_S['basic']['sensitive'];?></textarea>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">关闭应用</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" lay-filter="ctrls" data-ctrl=".closesystem" value="1" name="data[basic][closesystem]"<?php  if($_S['basic']['closesystem']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="关闭|开放">
                                    </div>
                                </div>
                                <div class="layui-form-item closesystem<?php  if(!$_S['basic']['closesystem']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">关闭原因</label>
                                    <div class="layui-input-block">
                                        <textarea name="data[basic][closetext]" placeholder="输入软件关闭原因提醒用户" class="layui-textarea"><?php  echo $_S['basic']['closetext'];?></textarea>
                                    </div>
                                </div>
                                <div class="layui-form-item closesystem<?php  if(!$_S['basic']['closesystem']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">关闭跳转地址</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[basic][closeredirect]" value="<?php  echo $_S['basic']['closeredirect'];?>" placeholder="战点关闭后的跳转地址，留空则不跳转" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item<?php  if(!$_W['isfounder']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label" lay-tips="在OEM模式下，指定的管理员将只能看到本模块相关的信息，且仅能操作模块相关的功能">OEM模式</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[basic][hidecopyright]"<?php  if($_S['basic']['hidecopyright']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" lay-tips="配置低的服务器运行插件钩子可能导致响应比原来慢，请理解插件钩子的原理后再进行操作">运行插件钩子</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[basic][runhook]"<?php  if($_S['basic']['runhook']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">新用户注册</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" lay-filter="ctrls" data-ctrl=".registers" value="1" name="data[register][switch]"<?php  if($_S['register']['switch']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开放|禁止">
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">注册方式</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" name="data[register][method][]" value="mobile" title="手机号"<?php  if(in_array('mobile',$_S['register']['method'])) { ?> checked<?php  } ?> />
                                        <input type="checkbox" name="data[register][method][]" value="email" title="邮箱"<?php  if(in_array('email',$_S['register']['method'])) { ?> checked<?php  } ?> />
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">注册验证码</label>
                                    <div class="layui-input-block" lay-tips="启用后请配置好短信和邮件发送设置，关闭后则不验证邮箱和手机号的真实性">
                                        <input type="checkbox" value="1" name="data[register][verifycode]"<?php  if($_S['register']['verifycode']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="启用|关闭">
                                        <?php  if(!\App\Services\CloudService::ComExists('aliyun') && $_W['isfounder']) { ?><p style="margin-top: 8px;" class="layui-word-aux color-red">您还未加载短信发送SDK，短信验证码将无法正常发送。<a href="<?php  echo url('console/setting/component')?>" class="layui-btn layui-btn-sm">立即加载</a></p><?php  } ?>
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">第三方登录</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[register][quicklogin]"<?php  if($_S['register']['quicklogin']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">账户注销功能</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[register][cancellation]"<?php  if($_S['register']['cancellation']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">默认会话权限</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" name="data[register][userset][talk_private]" value="1" title="接收私聊信息"<?php  if($_S['register']['userset']['talk_private']) { ?> checked<?php  } ?> />
                                        <input type="checkbox" name="data[register][userset][validation_apply]" value="1" title="被添加需要验证"<?php  if($_S['register']['userset']['validation_apply']) { ?> checked<?php  } ?> />
                                        <input type="checkbox" name="data[register][userset][allow_search]" value="1" title="允许搜索到我"<?php  if($_S['register']['userset']['allow_search']) { ?> checked<?php  } ?> />
                                        <input type="checkbox" name="data[register][userset][album_open]" value="1" title="开放朋友圈"<?php  if($_S['register']['userset']['album_open']) { ?> checked<?php  } ?> />
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">强制绑定手机</label>
                                    <div class="layui-input-block" lay-tips="通过微信等方式自动登录访问，如未绑定手机号则需绑定才能进行下一步操作">
                                        <input type="checkbox" value="1" name="data[register][bindmobile]"<?php  if($_S['register']['bindmobile']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="启用|关闭">
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">强制关注公号</label>
                                    <div class="layui-input-block" lay-tips="在微信内打开如未关注公众号则需要关注指定公众号才能进行下一步操作">
                                        <input type="checkbox" lay-filter="ctrls" data-ctrl=".bindfollow" value="1" name="data[register][bindfollow]"<?php  if($_S['register']['bindfollow']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="启用|关闭">
                                    </div>
                                </div>
                                <div class="layui-form-item registers bindfollow<?php  if(!$_S['register']['switch'] || !$_S['register']['bindfollow']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">关注说明文字</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[register][followdesc]" value="<?php  echo $_S['register']['followdesc'];?>" placeholder="关注公众号说明文字，将显示在二维码底部" autocomplete="off" class="layui-input" />
                                    </div>
                                </div>
                                <div class="layui-form-item registers<?php  if(!$_S['register']['switch']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">自动机器人</label>
                                    <div class="layui-input-block" lay-tips="启用后新用户注册成功将自动添加一个机器人为好友，后台可指定任一用户为机器人，并可登陆它的账号与其他人交流">
                                        <input type="checkbox" lay-filter="ctrls" data-ctrl=".robotset" value="1" name="data[register][robot]"<?php  if($_S['register']['robot']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="启用|关闭">
                                    </div>
                                </div>
                                <div class="layui-form-item robotset registers<?php  if(!$_S['register']['switch'] || !$_S['register']['robot']) { ?> layui-hide<?php  } ?>">
                                    <?php  echo $this->tpl_form_field_member('指定机器人',$_S['register']['robotuid'],'data[register][robotuid]','请选择一个用户作为机器人，输入昵称或手机号搜索')?>
                                </div>
                                <div class="layui-form-item robotset registers<?php  if(!$_S['register']['switch'] || !$_S['register']['robot']) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">自动发送内容</label>
                                    <div class="layui-input-block">
                                        <textarea name="data[register][robotmsg]" placeholder="用户注册成功后，自动添加机器人并自动发送此处的消息给用户。支持HTML代码" class="layui-textarea"><?php  echo $_S['register']['robotmsg'];?></textarea>
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">用户注册协议</label>
                                    <div class="layui-input-block">
                                        <div class="input-group">
                                            <span class="input-group-addon">注册协议链接</span>
                                            <input type="text" name="data[register][agreement]" maxlength="255" value="<?php  echo $_S['register']['agreement'];?>" placeholder="用户注册协议链接" autocomplete="off" class="layui-input">
                                            <span class="input-group-addon">隐私政策链接</span>
                                            <input type="text" name="data[register][privacy]" maxlength="255" value="<?php  echo $_S['register']['privacy'];?>" placeholder="隐私政策链接" autocomplete="off" class="layui-input">
                                        </div>
                                        <div class="layui-word-aux">
                                            请将用户注册协议内容发布到任意网页，并将其链接粘贴到此处。
                                            <?php  if($_W['isfounder'] || !$_S['basic']['hidecopyright']) { ?>例如：<a href="<?php  echo url('site/article/post')?>" target="_blank" class="color-default">发布文章</a>、<a href="<?php  echo url('platform/material-post',array('new_type'=>'reply'))?>" target="_blank" class="color-default">创建公众号图文</a><?php  } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">消息加载条数</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[group][msglength]" value="<?php  echo $_S['group']['msglength'];?>" placeholder="打开会话时默认加载多少条聊天消息，默认20条" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">自动删除记录</label>
                                    <div class="layui-input-block">
                                        <div class="layui-form-mid layui-word-aux">群聊记录保留</div>
                                        <div class="layui-input-inline" style="width:17.5%">
                                            <input type="text" name="data[group][autoclear]" value="<?php  echo $_S['group']['autoclear'];?>" placeholder="填0为永久保存" autocomplete="off" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">天，私聊记录保留</div>
                                        <div class="layui-input-inline" style="width:17.5%">
                                            <input type="text" name="data[group][autoclear_private]" value="<?php  echo $_S['group']['autoclear_private'];?>" placeholder="填0为永久保存" autocomplete="off" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">天&nbsp;&nbsp;<strong class="text-danger">0或留空为永久保存</strong></div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">聊天图片压缩</label>
                                    <div class="layui-input-block">
                                        <div class="input-group" style="width:360px">
                                            <input type="text" name="data[group][imgcompress]" value="<?php  echo intval($_S['group']['imgcompress'])?>" placeholder="0或100为不压缩" autocomplete="off" class="layui-input">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                        <p class="layui-word-aux">填1到100的压缩率数值，1图片最小100最大。该功能仅在APP、小程序端有效</p>
                                        <p class="layui-word-aux"><strong class="text-danger">注意：如果设置不压缩，系统会根据用户选择图片时选压缩还是原图来自动处理，如果设置了压缩，则用户就算选择原图也仍会进行压缩</strong></p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">开启模糊搜索</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[group][allowsearch]"<?php  if($_S['group']['allowsearch']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                        <p class="layui-word-aux">是否允许通过关键词搜索到群和用户，如不开启则只允许通过群号、用户ID、手机号等全匹配搜索</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">显示用户ID</label>
                                    <div class="layui-input-block">
                                        <input type="radio" lay-filter="ctrls" data-target=".showuids" value="0" name="data[group][showuid]"<?php  if($_S['group']['showuid']==0) { ?> checked<?php  } ?> title="不显示" />
                                        <input type="radio" lay-filter="ctrls" data-target=".showuids" value="1" name="data[group][showuid]"<?php  if($_S['group']['showuid']==1) { ?> checked<?php  } ?> title="显示系统ID" />
                                        <input type="radio" lay-filter="ctrls" data-target=".showuids" value="2" name="data[group][showuid]"<?php  if($_S['group']['showuid']==2) { ?> checked<?php  } ?> title="显示自定义ID" />
                                    </div>
                                </div>
                                <div class="layui-form-item showuids form-item2<?php  if($_S['group']['showuid']!=2) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">自定义ID名</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[group][account]" value="<?php  echo $_S['group']['account'];?>" placeholder="如微信号、微聊号等" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item showuids form-item2<?php  if($_S['group']['showuid']!=2) { ?> layui-hide<?php  } ?>">
                                    <label class="layui-form-label">自定义ID登录</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" lay-filter="ctrls" data-ctrl=".switch-groups" name="data[group][accountlogin]"<?php  if($_S['group']['accountlogin']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">群聊功能</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" lay-filter="ctrls" data-ctrl=".switch-groups" name="data[group][switch]"<?php  if($_S['group']['switch']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                                <div class="switch-groups<?php  if(!$_S['group']['switch']) { ?> layui-hide<?php  } ?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">允许创建群组</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" lay-filter="ctrls" data-ctrl=".allowcreate" value="1" name="data[group][allowcreate]"<?php  if($_S['group']['allowcreate']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="允许|禁止">
                                        </div>
                                    </div>
                                    <div class="layui-form-item allowcreate<?php  if(!$_S['group']['allowcreate']) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">创建需要审核</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[group][needaudit]"<?php  if($_S['group']['needaudit']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="需要|不需要">
                                        </div>
                                    </div>
                                    <div class="layui-form-item allowcreate<?php  if(!$_S['group']['allowcreate']) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">创建需要等级</label>
                                        <div class="layui-input-block">
                                            <div style="width:20%">
                                                <select name="data[group][allowlevel]" lay-verify="" lay-search>
                                                    <option value="0">不限</option>
                                                    <?php  if(is_array($_W['account']['groups'])) { foreach($_W['account']['groups'] as $group) { ?>
                                                    <option value="<?php  echo $group['groupid'];?>"<?php  if($_S['group']['allowlevel']==$group['groupid']) { ?> selected="selected"<?php  } ?>><?php  echo $group['title'];?></option>
                                                    <?php  } } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item allowcreate<?php  if(!$_S['group']['allowcreate']) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">创建需要支付</label>
                                        <div class="layui-input-block">
                                            <div class="layui-col-md3">
                                                <div class="layui-input-inline" style="width:80%">
                                                    <input type="text" name="data[group][paycredit2]" value="<?php  echo $_S['group']['paycredit2'];?>" placeholder="需要支付的金额" class="layui-input">
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">元</div>
                                            </div>
                                            <div class="layui-col-md3">
                                                <div class="layui-input-inline" style="width:80%">
                                                    <input type="text" name="data[group][paycredit1]" value="<?php  echo $_S['group']['paycredit1'];?>" placeholder="需要支付的积分" class="layui-input">
                                                </div>
                                                <div class="layui-form-mid layui-word-aux">积分</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item allowcreate<?php  if(!$_S['group']['allowcreate']) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">创建群组申明</label>
                                        <div class="layui-input-block">
                                            <textarea name="data[group][agreement]" placeholder="创建群组申明（协议）" class="layui-textarea"><?php  echo $_S['group']['agreement'];?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                            	<div class="layui-form-item">
                                    <label class="layui-form-label">开启钱包功能</label>
                                    <div class="layui-input-block">
                                        <div>
                                            <input type="checkbox" lay-filter="ctrls" data-ctrl=".creditset" value="1" name="data[credit][open]"<?php  if($_S['credit']['open']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        </div>
                                        <?php  if($_W['isfounder']) { ?><div class="layui-form-mid layui-word-aux creditset<?php  if(!$_S['credit']['open']) { ?> layui-hide<?php  } ?>"><a href="<?php  echo url('profile/payment')?>" target="_blank" class="layui-btn layui-btn-xs layui-btn-danger">配置支付方式</a></div><?php  } ?>
                                    </div>
                                </div>
                                <div class="creditset<?php  if(!$_S['credit']['open']) { ?> layui-hide<?php  } ?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">启用积分</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" lay-filter="ctrls" data-ctrl=".credit1" value="1" name="data[credit][credit1]"<?php  if($_S['credit']['credit1']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        </div>
                                    </div>
                                    <div class="credit1<?php  if(!$_S['credit']['credit1']) { ?> layui-hide<?php  } ?>">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">积分名称</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[credit][creditname]" value="<?php  echo $_S['credit']['creditname'];?>" placeholder="请输入积分的名称，如积分、金币等" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">积分互转</label>
                                            <div class="layui-input-block">
                                                <span<?php  if(!$_S['credit']['transfer']) { ?> lay-tips="请先开启余额转账"<?php  } ?>>
                                                    <input type="checkbox" value="1"<?php  if(!$_S['credit']['transfer']) { ?> disabled<?php  } ?> name="data[credit][transfer1]"<?php  if($_S['credit']['transfer1'] && $_S['credit']['transfer']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                                </span>
                                                <p class="layui-word-aux">开启后在转账界面可选积分还是余额</p>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">余额转积分</label>
                                            <div class="layui-input-block">
                                                <input type="checkbox" lay-filter="ctrls" data-ctrl=".exchange" value="1" name="data[credit][exchange]"<?php  if($_S['credit']['exchange']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="启用|禁用" />
                                            </div>
                                        </div>
                                        <div class="layui-form-item exchange<?php  if(!$_S['credit']['exchange']) { ?> layui-hide<?php  } ?>">
                                            <label class="layui-form-label">余额积分比例</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[credit][proportion]" value="<?php  echo $_S['credit']['proportion'];?>" placeholder="输入1元余额等于多少积分，仅限整数。0或留空则关闭兑换功能" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">余额支付</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[credit][defray]"<?php  if($_S['credit']['defray']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">余额充值</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[credit][recharge]"<?php  if($_S['credit']['recharge']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">余额转账</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[credit][transfer]"<?php  if($_S['credit']['transfer']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">余额提现</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" lay-filter="ctrls" data-ctrl=".creditcash" value="1" name="data[credit][cash]"<?php  if($_S['credit']['cash']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        </div>
                                    </div>
                                    <div class="creditcash<?php  if(!$_S['credit']['cash']) { ?> layui-hide<?php  } ?>">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">提现方式</label>
                                            <div class="layui-input-block">
                                                <input type="checkbox" name="data[credit][cashtype][]" value="bank" title="银行卡"<?php  if($_S['credit']['cashtype']) { ?><?php  if(in_array('bank',$_S['credit']['cashtype'])) { ?> checked<?php  } ?><?php  } ?> />
                                                <input type="checkbox" name="data[credit][cashtype][]" value="alipay" title="支付宝"<?php  if($_S['credit']['cashtype']) { ?><?php  if(in_array('alipay',$_S['credit']['cashtype'])) { ?> checked<?php  } ?><?php  } ?> />
                                                <input type="checkbox" name="data[credit][cashtype][]" value="wechat_transfer" title="微信转账"<?php  if($_S['credit']['cashtype']) { ?><?php  if(in_array('wechat_transfer',$_S['credit']['cashtype'])) { ?> checked<?php  } ?><?php  } ?> />
                                                <span lay-tips="需企业微信商户支持【企业付款到零钱】">
                                                    <input type="checkbox" name="data[credit][cashtype][]"<?php  if($_W['account']['level']<3) { ?> disabled<?php  } else if(in_array('wechat',$_S['credit']['cashtype'])) { ?> checked<?php  } ?> value="wechat" title="微信钱包" />
                                                </span>
                                                <div class="layui-word-aux" style="margin-top: 8px">
                                                    提现到微信钱包功能需企业微信开通【企业付款到零钱】，<a class="color-default" href="https://pay.weixin.qq.com/index.php/public/product/detail?pid=5" target="_blank">点此开通</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">提现设置</label>
                                            <div class="layui-input-block">
                                                <div class="input-group layui-input-inline" style="width: 50%;">
                                                    <span class="input-group-addon">手续费</span>
                                                    <input type="text" name="data[credit][cashfee]" value="<?php  echo $_S['credit']['cashfee'];?>" placeholder="0或为空则不收取手续费" autocomplete="off" class="layui-input">
                                                    <span class="input-group-addon">%</span>
                                                    <span class="input-group-addon">最低提现</span>
                                                    <input type="text" name="data[credit][mincash]" value="<?php  echo $_S['credit']['mincash'];?>" placeholder="请输入单次提现的最低金额" autocomplete="off" class="layui-input">
                                                    <span class="input-group-addon">元</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">提现说明</label>
                                            <div class="layui-input-block">
                                                <textarea name="data[credit][cashnotice]" placeholder="用户提现时显示的说明" class="layui-textarea"><?php  echo $_S['credit']['cashnotice'];?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">支付宝支付</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="data[credit][alipay][switch]" value="0" lay-filter="ctrls" data-target=".alipay" title="关闭"<?php  if($_S['credit']['alipay']['switch']==0) { ?> checked="checked"<?php  } ?> />
                                            <input type="radio" name="data[credit][alipay][switch]" value="1" lay-filter="ctrls" data-target=".alipay" title="原生支付"<?php  if($_S['credit']['alipay']['switch']==1) { ?> checked="checked"<?php  } ?> />
                                            <input type="radio" name="data[credit][alipay][switch]" value="2" lay-filter="ctrls" data-target=".alipay" title="H5支付"<?php  if($_S['credit']['alipay']['switch']==2) { ?> checked="checked"<?php  } ?> />
                                            <p style="margin-top: 10px" class="layui-word-aux alipay form-item1<?php  if($_S['credit']['alipay']['switch']!=1) { ?> layui-hide<?php  } ?>">注：此处支付配置仅对APP或支付宝小程序端生效。APP开通此支付需要申请签约【APP支付】，<a href="https://b.alipay.com/signing/productDetailV2.htm?productId=I1011000290000001002" target="_blank" class="color-default">点此申请</a> </p>
                                        </div>
                                    </div>
                                    <div class="alipay form-item1<?php  if($_S['credit']['alipay']['switch']!=1) { ?> layui-hide<?php  } ?>">
                                        <div class="layui-form-item must">
                                            <label class="layui-form-label">支付宝账号</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[credit][alipay][account]" value="<?php  echo $_S['credit']['alipay']['account'];?>" placeholder="请输入收款的企业支付宝账号" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item must">
                                            <label class="layui-form-label">合作者PID</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[credit][alipay][partner]" value="<?php  echo $_S['credit']['alipay']['partner'];?>" placeholder="请输入支付宝合作商户号" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">支付宝公钥(RSA2)</label>
                                            <div class="layui-input-block">
                                                <textarea name="data[credit][alipay][publickey]" placeholder="请填写开发者私钥去头去尾去回车，一行字符串" class="layui-textarea"><?php  echo $_S['credit']['alipay']['publickey'];?></textarea>
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">应用私钥(RSA2)</label>
                                            <div class="layui-input-block">
                                                <textarea name="data[credit][alipay][privatekey]" placeholder="请输入生成密钥时获取的私钥字符串，直接使用pem文件的完整字符串" class="layui-textarea"><?php  echo $_S['credit']['alipay']['privatekey'];?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">微信支付</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="data[credit][wxpay][switch]" value="0" lay-filter="ctrls" data-target=".wxpay" title="关闭"<?php  if($_S['credit']['wxpay']['switch']==0) { ?> checked="checked"<?php  } ?> />
                                            <input type="radio" name="data[credit][wxpay][switch]" value="1" lay-filter="ctrls" data-target=".wxpay" title="原生支付"<?php  if($_S['credit']['wxpay']['switch']==1) { ?> checked="checked"<?php  } ?> />
                                            <input type="radio" name="data[credit][wxpay][switch]" value="2" lay-filter="ctrls" data-target=".wxpay" title="H5支付"<?php  if($_S['credit']['wxpay']['switch']==2) { ?> checked="checked"<?php  } ?> />
                                            <p style="margin-top: 10px" class="layui-word-aux wxpay form-item1<?php  if($_S['credit']['wxpay']['switch']!=1) { ?> layui-hide<?php  } ?>">注：此处支付配置仅对APP和微信小程序生效。APP内使用微信支付需要申请签约【APP支付】，<a href="https://pay.weixin.qq.com/index.php/public/product/detail?pid=36&productType=0" target="_blank" class="color-default">点此申请</a> </p>
                                            <p style="margin-top: 10px" class="layui-word-aux wxpay form-item2<?php  if($_S['credit']['wxpay']['switch']!=2) { ?> layui-hide<?php  } ?>">如选择H5支付，则在原生APP内也将调用微信H5支付，微信小程序端除外。（注：请确保您的微信支付商户支持已<a href="https://pay.weixin.qq.com/index.php/public/product/detail?pid=32&productType=0" target="_blank" class="color-default">开通H5支付</a>）</p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item must wxpay form-item2<?php  if($_S['credit']['wxpay']['switch']!=2) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">证书序列号</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%;">
                                                <input type="text" name="data[credit][wxpay][serialno]" value="<?php  echo $_S['credit']['wxpay']['serialno'];?>" placeholder="请输入微信支付的API证书序列号" autocomplete="off" class="layui-input">
                                            </div>
                                            <a class="layui-btn" href="https://pay.weixin.qq.com/index.php/core/cert/api_cert#/" lay-tips="登录微信支付商户后点击【查看证书】按钮" target="_blank">立即查看</a>
                                            <?php  if($_W['isfounder'] || !$_S['basic']['hidecopyright']) { ?><a class="layui-btn layui-btn-normal" href="<?php  echo url('profile/refund/display')?>" lay-tips="编辑微信退款选项，并将从微信支付商户下载的证书文件解压上传" target="_blank">上传证书</a><?php  } ?>
                                        </div>
                                    </div>
                                    <div class="layui-form-item must wxpay form-item1<?php  if($_S['credit']['wxpay']['switch']!=1) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">应用APPID</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%;">
                                                <input type="text" name="data[credit][wxpay][appid]" value="<?php  echo $_S['credit']['wxpay']['appid'];?>" placeholder="请输入在微信开放平台申请的应用的APPID" autocomplete="off" class="layui-input">
                                            </div>
                                            <a class="layui-btn" href="https://open.weixin.qq.com/" target="_blank">立即申请</a>
                                        </div>
                                    </div>
                                    <div class="wxpay form-item1 form-item2<?php  if(!$_S['credit']['wxpay']['switch']) { ?> layui-hide<?php  } ?>">
                                        <div class="layui-form-item must">
                                            <label class="layui-form-label">支付商户号</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[credit][wxpay][mchid]" value="<?php  echo $_S['credit']['wxpay']['mchid'];?>" placeholder="请输入微信支付的商户号" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-form-item must">
                                            <label class="layui-form-label">API密匙</label>
                                            <div class="layui-input-block">
                                                <input type="text" name="data[credit][wxpay][apikey]" value="<?php  echo $_S['credit']['wxpay']['apikey'];?>" placeholder="请输入微信支付的API密匙" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">视频自动播放</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[attach][autoplay]"<?php  if($_S['attach']['autoplay']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">视频自动转码</label>
                                    <div class="layui-input-block<?php  if($_W['setting']['remote']['type']!=2) { ?>" lay-tips="<?php  if($_W['setting']['remote']['type']) { ?>当前仅支持阿里云OSS自动转码<?php  } else { ?>系统未开启上传到云存储<?php  } ?><?php  } ?>">
                                        <input type="checkbox" lay-filter="ctrls"<?php  if($_W['setting']['remote']['type']!=2) { ?> disabled<?php  } ?> data-ctrl=".videoencode" value="1" name="data[attach][encode]"<?php  if($_S['attach']['encode'] && $_W['setting']['remote']['type']==2) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        <?php  if($_W['isfounder']) { ?><p class="layui-word-aux videoencode<?php  if(!$_S['attach']['encode'] || $_W['setting']['remote']['type']!=2) { ?> layui-hide<?php  } ?>" style="margin-top: 10px"><a href="<?php  echo url('profile/remote')?>" target="_blank" class="layui-btn layui-btn-xs">配置云存储</a></p><?php  } ?>
                                    </div>
                                </div>
                                <div class="videoencode<?php  if(!$_S['attach']['encode'] || $_W['setting']['remote']['type']!=2) { ?> layui-hide<?php  } ?>">
                                    <?php  if($_W['setting']['remote']['type']==2) { ?>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">转码链接前缀</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[attach][encodeurl]" value="<?php  echo $_S['attach']['encodeurl'];?>" placeholder="输入转码后的视频URL，以https开头，以/结尾" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">同时转为标清</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[attach][encodesd]"<?php  if($_S['attach']['encodesd']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                            <p class="layui-word-aux">默认转码为高清视频，如开启此选项则同时转存为标清视频（同时保存高清和标清2个视频）</p>
                                        </div>
                                    </div>
                                    <?php  } ?>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">自动截取封面</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" lay-filter="ctrls" data-ctrl=".snapshot" value="1" name="data[attach][snapshot]"<?php  if($_S['attach']['snapshot']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭" />
                                        </div>
                                    </div>
                                    <div class="layui-form-item must snapshot<?php  if(!$_S['attach']['snapshot']) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">封面截取时间</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[attach][snapshottime]" value="<?php  echo max($_S['attach']['snapshottime'],1)?>" placeholder="自动截取第几秒时的画面作为缩略图，默认为1秒" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">默认视频封面</label>
                                    <?php  echo $this->tpl_form_field_image('data[attach][poster]', $_S['attach']['poster'],'视频封面截取失败时显示的图片');?>
                                </div>
                                <p class="layui-word-aux">新版本将支持视频水印等功能</p>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">腾讯地图KEY</label>
                                    <div class="layui-input-block">
                                        <div class="layui-input-inline" style="width: 50%;">
                                            <input type="text" name="data[domain][lbskey]" value="<?php  echo $_S['domain']['lbskey'];?>" placeholder="请输入从腾讯地图云平台申请的KEY，用于获取用户定位等" autocomplete="off" class="layui-input" />
                                        </div>
                                        <a href="https://lbs.qq.com/dev/console/key/add" target="_blank" class="layui-btn">立即申请</a>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">高德地图KEY</label>
                                    <div class="layui-input-block">
                                        <div class="layui-input-inline" style="width: 50%;">
                                            <input type="text" name="data[domain][amapkey]" value="<?php  echo $_S['domain']['amapkey'];?>" placeholder="请输入高德地图控制台申请的KEY，用于发送位置时显示对于位置的图片" autocomplete="off" class="layui-input" />
                                            <p class="layui-word-aux">创建应用后添加KEY时请选择WEB端(JS API)</p>
                                        </div>
                                        <a href="https://console.amap.com/dev/index" target="_blank" class="layui-btn">立即申请</a>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">Socket类型</label>
                                    <div class="layui-input-block">
                                        <span>
                                            <input type="radio" lay-filter="ctrls" data-target=".sockettypes" value="local" name="data[socket][type]"<?php  if($_S['socket']['type']=='local') { ?> checked<?php  } ?> title="本地Socket" />
                                        </span>
                                        <span lay-tips="单个群组人数超过1万人后，超出的人将无法即时收到该群的消息">
                                            <input type="radio" lay-filter="ctrls" data-target=".sockettypes" value="tim" name="data[socket][type]"<?php  if($_S['socket']['type']=='tim') { ?> checked<?php  } ?> title="腾讯云TIM" />
                                        </span>
                                        <input type="radio" lay-filter="ctrls" data-target=".sockettypes" value="" name="data[socket][type]"<?php  if($_S['socket']['type']=='') { ?> checked<?php  } ?> title="其它第三方" />
                                    </div>
                                </div>
                                <div class="sockettypes form-itemlocal<?php  if($_S['socket']['type']!='local') { ?> layui-hide<?php  } ?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">Socket服务器</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[socket][server]" value="<?php  echo $_S['socket']['server'];?>" placeholder="ws://或wws://开头，如wss://socket.whotalk.com.cn:3000" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">即时通讯的依赖地址，以ws://或wws://开头，结尾不要有斜杠</p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">Socket接口</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[socket][api]" value="<?php  echo $_S['socket']['api'];?>" placeholder="http://或https://开头，如http://www.baidu.com" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">即时通讯的依赖地址</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="sockettypes form-itemtim<?php  if($_S['socket']['type']!='tim') { ?> layui-hide<?php  } ?>">
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">SDKAppID</label>
                                        <div class="layui-input-block">
                                            <div class="layui-input-inline" style="width: 50%;">
                                                <input type="text" name="data[socket][timappid]" value="<?php  echo $_S['socket']['timappid'];?>" placeholder="在腾讯云TIM创建应用后自动生成的SDKAppID，一般为10位数字" autocomplete="off" class="layui-input">
                                            </div>
                                            <a href="https://console.cloud.tencent.com/im" target="_blank" class="layui-btn">立即申请</a>
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">SECRETKEY</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[socket][timsecret]" value="<?php  echo $_S['socket']['timsecret'];?>" placeholder="在腾讯云TIM创建应用后自动生成的SECRETKEY" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-form-item must">
                                        <label class="layui-form-label">帐号管理员</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[socket][timidentifier]" value="<?php  echo $_S['socket']['timidentifier'];?>" placeholder="创建应用后需要添加管理员账号，并将账号填写到此处" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                </div>
                                <div class="sockettypes form-item<?php  if($_S['socket']['type']!='') { ?> layui-hide<?php  } ?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">Socket服务器</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[domain][socket]" value="<?php  echo $_S['domain']['socket'];?>" placeholder="http://或https://开头，如http://www.baidu.com" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">即时通讯的依赖地址</p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">群聊Socket</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[domain][groupsocket]" value="<?php  echo $_S['domain']['groupsocket'];?>" placeholder="http://或https://开头，如http://www.baidu.com" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">即时通讯的依赖地址</p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">私聊Socket</label>
                                        <div class="layui-input-block">
                                            <input type="text" name="data[domain][talksocket]" value="<?php  echo $_S['domain']['talksocket'];?>" placeholder="http://或https://开头，如http://www.baidu.com" autocomplete="off" class="layui-input">
                                            <p class="layui-word-aux">即时通讯的依赖地址</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">发现页开关</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[album][square]"<?php  if($_S['album']['square']) { ?> checked="checked"<?php  } ?> title="广场">
                                        <input type="checkbox" value="1" name="data[album][switch]"<?php  if($_S['album']['switch']) { ?> checked="checked"<?php  } ?> title="朋友圈">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">用户发布动态</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" lay-filter="ctrls" data-ctrl=".postalbum" value="1" name="data[album][allowpost]"<?php  if($_S['album']['allowpost']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                                <div class="postalbum<?php  if(!$_S['album']['allowpost']) { ?> layui-hide<?php  } ?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">是否需要审核</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[album][audit]"<?php  if($_S['album']['audit']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="需要|不需要" />
                                            <p class="layui-word-aux">用户发布的动态是否需要后台审核，开启后用户发布的内容需要审核后才会出现在广场和朋友圈</p>
                                        </div>
                                    </div>
                                    <div class="layui-form-item albums<?php  if(!$_S['album']['switch']) { ?> layui-hide<?php  } ?>">
                                        <label class="layui-form-label">动态发布申明</label>
                                        <div class="layui-input-block">
                                            <textarea name="data[album][agreement]" placeholder="朋友圈发布协议" class="layui-textarea"><?php  echo $_S['album']['agreement'];?></textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">动态分类标签</label>
                                        <div class="layui-input-block">
                                            <textarea name="data[album][tags]" placeholder="用户发布动态时最多可选3个标签。多个标签之间请用 | 隔开（不留空格）" class="layui-textarea"><?php  echo $_S['album']['tags'];?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">用户发表评论</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" lay-filter="ctrls" data-ctrl=".commenton" value="1" name="data[album][commenton]"<?php  if($_S['album']['commenton']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="开启|关闭">
                                    </div>
                                </div>
                                <div class="commenton<?php  if(!$_S['album']['commenton']) { ?> layui-hide<?php  } ?>">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">评论需要审核</label>
                                        <div class="layui-input-block">
                                            <input type="checkbox" value="1" name="data[album][commentaudit]"<?php  if($_S['album']['commentaudit']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="需要|不需要">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">默认分享标题</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[share][title]" value="<?php  echo $_S['share']['title'];?>" placeholder="系统分享时默认显示的标题" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">默认分享文字</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[share][desc]" value="<?php  echo $_S['share']['desc'];?>" placeholder="如分享描述为空时引用此处设置" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">默认分享图标</label>
                                    <?php  echo $this->tpl_form_field_image('data[share][thumb]', $_S[share]['thumb']);?>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">关闭外部分享</label>
                                    <div class="layui-input-block" lay-tips="关闭后分享功能将仅限于站内分享">
                                        <input type="checkbox" value="1" name="data[share][closeout]"<?php  if($_S['share']['closeout']) { ?> checked="checked"<?php  } ?> lay-skin="switch" lay-text="关闭|开启">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">SEO关键字</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[seo][keywords]" value="<?php  echo $_S['seo']['keywords'];?>" placeholder="多个用英文逗号分隔，合理设置网页关键字有利于搜索排名" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">SEO描述</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="data[seo][description]" value="<?php  echo $_S['seo']['description'];?>" placeholder="网站SEO描述" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-tab-item">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">发现页入口</label>
                                    <div class="layui-input-block">
                                        <span class="switch-groups<?php  if(!$_S['group']['switch']) { ?> layui-hide<?php  } ?>">
                                            <input type="checkbox" value="1" name="data[group][entry]"<?php  if($_S['group']['entry']) { ?> checked="checked"<?php  } ?> title="全部群组">
                                        </span>
                                        <input type="checkbox" value="1" name="data[album][scan]"<?php  if($_S['album']['scan']) { ?> checked="checked"<?php  } ?> title="扫一扫">
                                        <input type="checkbox" value="1" name="data[album][search]"<?php  if($_S['album']['search']) { ?> checked="checked"<?php  } ?> title="搜一搜">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">下拉菜单入口</label>
                                    <div class="layui-input-block">
                                        <input type="checkbox" value="1" name="data[album][popupscan]"<?php  if($_S['album']['popupscan']) { ?> checked="checked"<?php  } ?> title="扫一扫">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">整体色调</label>
                                    <div class="layui-input-block">
                                        <select name="data[theme][actcolor]" class="layui-select" lay-search>
                                            <option value="">请选择一种前景颜色</option>
                                            <option value="red"<?php  if($_S['theme']['actcolor']=='red') { ?> selected<?php  } ?>>嫣红</option>
                                            <option value="orange"<?php  if($_S['theme']['actcolor']=='orange') { ?> selected<?php  } ?>>桔橙</option>
                                            <option value="yellow"<?php  if($_S['theme']['actcolor']=='yellow') { ?> selected<?php  } ?>>明黄</option>
                                            <option value="olive"<?php  if($_S['theme']['actcolor']=='olive') { ?> selected<?php  } ?>>橄榄</option>
                                            <option value="green"<?php  if($_S['theme']['actcolor']=='green') { ?> selected<?php  } ?>>森绿</option>
                                            <option value="cyan"<?php  if($_S['theme']['actcolor']=='cyan') { ?> selected<?php  } ?>>天青</option>
                                            <option value="blue"<?php  if($_S['theme']['actcolor']=='blue') { ?> selected<?php  } ?>>海蓝</option>
                                            <option value="purple"<?php  if($_S['theme']['actcolor']=='purple') { ?> selected<?php  } ?>>姹紫</option>
                                            <option value="mauve"<?php  if($_S['theme']['actcolor']=='mauve') { ?> selected<?php  } ?>>木槿</option>
                                            <option value="pink"<?php  if($_S['theme']['actcolor']=='pink') { ?> selected<?php  } ?>>桃粉</option>
                                            <option value="brown"<?php  if($_S['theme']['actcolor']=='brown') { ?> selected<?php  } ?>>棕褐</option>
                                            <option value="black"<?php  if($_S['theme']['actcolor']=='black') { ?> selected<?php  } ?>>墨黑</option>
                                            <option value="dazzledark"<?php  if($_S['theme']['actcolor']=='dazzledark') { ?> selected<?php  } ?>>酷黑</option>
                                            <option value="limegreen"<?php  if($_S['theme']['actcolor']=='limegreen') { ?> selected<?php  } ?>>柠檬绿</option>
                                        </select>
                                        <p class="layui-word-aux">按钮、选中项等文字和图标等颜色</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">导航栏背景色</label>
                                    <div class="layui-input-block">
                                        <select name="data[theme][navbg]" class="layui-select" lay-search>
                                            <option value="">请选择一种背景颜色</option>
                                            <option value="bg-white"<?php  if($_S['theme']['navbg']=='bg-white') { ?> selected<?php  } ?>>雅白</option>
                                            <option value="bg-red"<?php  if($_S['theme']['navbg']=='bg-red') { ?> selected<?php  } ?>>嫣红</option>
                                            <option value="bg-orange"<?php  if($_S['theme']['navbg']=='bg-orange') { ?> selected<?php  } ?>>桔橙</option>
                                            <option value="bg-yellow"<?php  if($_S['theme']['navbg']=='bg-yellow') { ?> selected<?php  } ?>>明黄</option>
                                            <option value="bg-olive"<?php  if($_S['theme']['navbg']=='bg-olive') { ?> selected<?php  } ?>>橄榄</option>
                                            <option value="bg-green"<?php  if($_S['theme']['navbg']=='bg-green') { ?> selected<?php  } ?>>森绿</option>
                                            <option value="bg-cyan"<?php  if($_S['theme']['navbg']=='bg-cyan') { ?> selected<?php  } ?>>天青</option>
                                            <option value="bg-blue"<?php  if($_S['theme']['navbg']=='bg-blue') { ?> selected<?php  } ?>>海蓝</option>
                                            <option value="bg-purple"<?php  if($_S['theme']['navbg']=='bg-purple') { ?> selected<?php  } ?>>姹紫</option>
                                            <option value="bg-mauve"<?php  if($_S['theme']['navbg']=='bg-mauve') { ?> selected<?php  } ?>>木槿</option>
                                            <option value="bg-pink"<?php  if($_S['theme']['navbg']=='bg-pink') { ?> selected<?php  } ?>>桃粉</option>
                                            <option value="bg-brown"<?php  if($_S['theme']['navbg']=='bg-brown') { ?> selected<?php  } ?>>棕褐</option>
                                            <option value="bg-grey"<?php  if($_S['theme']['navbg']=='bg-grey') { ?> selected<?php  } ?>>玄灰</option>
                                            <option value="bg-gray"<?php  if($_S['theme']['navbg']=='bg-gray') { ?> selected<?php  } ?>>草灰</option>
                                            <option value="bg-black"<?php  if($_S['theme']['navbg']=='bg-black') { ?> selected<?php  } ?>>墨黑</option>
                                            <option value="bg-dazzledark"<?php  if($_S['theme']['navbg']=='bg-dazzledark') { ?> selected<?php  } ?>>酷黑</option>
                                            <option value="bg-limegreen"<?php  if($_S['theme']['navbg']=='bg-limegreen') { ?> selected<?php  } ?>>柠檬绿</option>
                                            <option value="bg-gradual-red"<?php  if($_S['theme']['navbg']=='bg-gradual-red') { ?> selected<?php  } ?>>渐变魅红</option>
                                            <option value="bg-gradual-orange"<?php  if($_S['theme']['navbg']=='bg-gradual-orange') { ?> selected<?php  } ?>>渐变鎏金</option>
                                            <option value="bg-gradual-green"<?php  if($_S['theme']['navbg']=='bg-gradual-green') { ?> selected<?php  } ?>>渐变翠柳</option>
                                            <option value="bg-gradual-blue"<?php  if($_S['theme']['navbg']=='bg-gradual-blue') { ?> selected<?php  } ?>>渐变靛青</option>
                                            <option value="bg-gradual-purple"<?php  if($_S['theme']['navbg']=='bg-gradual-purple') { ?> selected<?php  } ?>>渐变惑紫</option>
                                            <option value="bg-gradual-pink"<?php  if($_S['theme']['navbg']=='bg-gradual-pink') { ?> selected<?php  } ?>>渐变霞彩</option>
                                        </select>
                                        <p class="layui-word-aux">顶部导航栏背景颜色</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">选中颜色</label>
                                    <div class="layui-input-block">
                                        <div class="layui-input-inline" style="width: 300px;">
                                            <input type="text" name="data[theme][active]" value="<?php  echo $_S['theme']['active'];?>" placeholder="点击右侧按钮选择颜色" class="layui-input" id="themeactive" />
                                        </div>
                                        <div id="themeactivepicker"></div>
                                        <p class="layui-word-aux">按钮、选中项等文字和图标等颜色</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">高亮颜色</label>
                                    <div class="layui-input-block">
                                        <div class="layui-input-inline" style="width: 300px;">
                                            <input type="text" name="data[theme][light]" value="<?php  echo $_S['theme']['light'];?>" placeholder="点击右侧按钮选择颜色" class="layui-input" id="themelight" />
                                        </div>
                                        <div id="themelightpicker"></div>
                                        <p class="layui-word-aux">按钮、选中项等文字和图标等颜色</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">普通链接</label>
                                    <div class="layui-input-block">
                                        <div class="layui-input-inline" style="width: 300px;">
                                            <input type="text" name="data[theme][link]" value="<?php  echo $_S['theme']['link'];?>" placeholder="点击右侧按钮选择颜色" class="layui-input" id="themelink" />
                                        </div>
                                        <div id="themelinkpicker"></div>
                                        <p class="layui-word-aux">普通按钮的颜色</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">聊天背景颜色</label>
                                    <div class="layui-input-block">
                                        <div class="layui-input-inline" style="width: 300px;">
                                            <input type="text" name="data[theme][chatbg]" value="<?php  echo $_S['theme']['chatbg'];?>" placeholder="点击右侧按钮选择颜色" class="layui-input" id="themechatbg" />
                                        </div>
                                        <div id="themechatbgpicker"></div>
                                        <p class="layui-word-aux">自己发送的消息背景颜色</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">聊天文字颜色</label>
                                    <div class="layui-input-block">
                                        <div class="layui-input-inline" style="width: 300px;">
                                            <input type="text" name="data[theme][chatfont]" value="<?php  echo $_S['theme']['chatfont'];?>" placeholder="点击右侧按钮选择颜色" class="layui-input" id="themechatfont" />
                                        </div>
                                        <div id="themechatfontpicker"></div>
                                        <p class="layui-word-aux">自己发送的消息文字颜色</p>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">界面风格</label>
                                    <div class="layui-input-block">
                                        <select name="data[theme][style]" class="layui-select">
                                            <?php  if(is_array($templetes)) { foreach($templetes as $key => $value) { ?>
                                            <option value="<?php  echo $value['name'];?>"<?php  if($value['name']==$_S['theme']['style']) { ?> selected<?php  } ?>><?php  echo $value['title'];?></option>
                                            <?php  } } ?>
                                        </select>
                                        <p class="layui-word-aux">如需要修改前端界面建议参照官方默认的模板新建一个模板并在上面进行修改，这样就不会担心升级后覆盖</p>
                                        <p class="layui-word-aux">注：如需新建模板并不需要复制整个官方模板文件夹，需要修改哪个文件就只需要复制对应的文件即可（注意目录结构要一致）</p>
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
        layer.ready(function () {
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
