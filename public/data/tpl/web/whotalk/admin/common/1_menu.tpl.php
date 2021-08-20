<?php defined('IN_IA') or exit('Access Denied');?><div class="layui-header" style="background: #1E9FFF;">
    <div class="layui-logo layui-hide-xs" style="background-color: #0085E8 !important;color: #fff;"><?php  if($_S['basic']['name']) { ?><?php  echo $_S['basic']['name'];?><?php  } else { ?>后台管理中心<?php  } ?></div>
    <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item layadmin-flexible layui-hide layui-show-xs-block" lay-unselect="">
            <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                <i class="layui-icon layui-icon-spread-left"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs">
            <a href="<?php  echo $_W['siteurl'];?>" title="刷新">
                <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
        </li>
        <li class="layui-nav-item">
            <a href="javascript:;">快捷导航<span class="layui-nav-more"></span></a>
            <dl class="layui-nav-child layui-anim layui-anim-upbit">
                <dd><a href="<?php  echo url('mc/member/add')?>" target="_blank">添加用户</a></dd>
                <dd><a href="<?php  echo weburl('group/post')?>">添加群组</a></dd>
                <dd><a href="<?php  echo weburl('account/cashlog')?>">提现管理</a></dd>
                <hr style="margin: 0;">
                <dd><a href="<?php  echo weburl('system/language')?>">语言管理</a></dd>
                <dd><a href="<?php  echo weburl('system/index')?>">系统设置</a></dd>
            </dl>
        </li>
        <?php  if($_W['isfounder'] || !$_S['basic']['hidecopyright']) { ?>
        <li class="layui-nav-item layui-hide-xs">
            <a href="https://shimo.im/docs/XRkgJOKZ41UrFbqM" target="_blank" title="使用帮助">
                使用帮助
            </a>
        </li>
        <?php  } ?>
    </ul>
    <ul class="layui-nav layui-layout-right">
        <li class="layui-nav-item" lay-unselect>
            <a href="<?php  echo wmurl('','',true)?>" title="访问网页版前台" target="_blank">
                <i class="layui-icon layui-icon-home"></i>
            </a>
        </li>
        <li class="layui-nav-item">
            <a href="javascript:;">
                <i class="layui-icon-username layui-icon"></i>
                <?php  echo $_W['username'];?>
            </a>
            <dl class="layui-nav-child layui-anim layui-anim-upbit" style="padding: 0;">
                <?php  if($_W['isfounder'] || !$_S['basic']['hidecopyright']) { ?><dd><a href="<?php  echo url('user/profile')?>" target="_blank"><span class="layui-icon layui-icon-username"></span>&nbsp;账户信息</a></dd><?php  } ?>
                <dd><a href="<?php  echo weburl('system/passport')?>" data-width="460" class="ajaxshow"><span class="layui-icon layui-icon-password"></span>&nbsp;修改密码</a></dd>
                <hr style="margin: 5px 0;">
                <dd><a href="javascript:;" layadmin-event="updateCache"><span class="layui-icon layui-icon-refresh"></span>&nbsp;更新缓存</a></dd>
                <?php  if($_W['isfounder'] || !$_S['basic']['hidecopyright']) { ?><dd><a href="<?php  echo url('console')?>"><span class="layui-icon layui-icon-website"></span>&nbsp;返回系统</a></dd><?php  } ?>
                <dd><a href="<?php  echo weburl('logout')?>" onclick="if(!confirm('退出后需要重新登录，是否确定要退出？')){return false;}"><span class="layui-icon layui-icon-password"></span>&nbsp;退出登录</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item layui-hide-xs layui-hide"><a href="<?php  echo url('console')?>">返回系统</a></li>
    </ul>
</div>
<?php  if($_S['basic']['icon'] && $nimabi) { ?>
<style type="text/css">
.layadmin-side-shrink .layui-layout-admin .layui-logo{background-size:50px 50px; background-position:center center; background-image:url(<?php  echo tomedia($_S['basic']['icon'])?>);}
</style>
<?php  } ?>
<!-- 侧边菜单 -->
<div class="layui-side layui-side-menu" style="background-color: #344058 !important;">
    <div class="layui-side-scroll">
        <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
        <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu" style="background-color: #344058 !important;">
            <?php  if(is_array($menu)) { foreach($menu as $key => $value) { ?>
            <li class="layui-nav-item<?php  if($value['name']==$_W['controller'] || ($_W['isplugin'] && $value['name']=='plugin')) { ?> layui-nav-itemed<?php  } ?>">
                <a data-name="<?php  echo $value['name'];?>" href="<?php  if(count($value['list'])>0) { ?>javascript:;<?php  } else { ?><?php  echo $value['jump'];?><?php  } ?>" lay-tips="<?php  echo $value['title'];?>" lay-direction="2">
                    <i class="<?php  echo $value['icon'];?>"></i>
                    <cite><?php  echo $value['title'];?></cite>
                </a>
                <?php  if(count($value['list'])>0) { ?>
                <dl class="layui-nav-child">
                    <?php  if(is_array($value['list'])) { foreach($value['list'] as $k => $val) { ?>
                    <dd data-name="<?php  echo $value['name'];?>"<?php  if(($val['name']==$_W['action'] && $value['name']==$_W['controller']) || $_W['siteurl']==$val['jump'] || (!$value['list'][$_W['action']] && $k=='index' && $value['name']==$_W['controller']) || ($_W['isplugin'] && $val['name']==$_W['plugin'])) { ?> class="<?php  echo $_W['action'];?> layui-this"<?php  } ?>>
                    <a layer-href="<?php  echo $value['name'];?>.<?php  echo $val['name'];?>" href="<?php  echo $val['jump'];?>"<?php  if($val['target']) { ?> target="<?php  echo $val['target'];?>"<?php  } ?>><?php  echo $val['title'];?></a>
                    </dd>
                    <?php  } } ?>
                </dl>
                <?php  } ?>
            </li>
            <?php  } } ?>
            <li class="layui-nav-item layui-hide">
                <a href="<?php  echo url('home/welcome')?>" lay-tips="返回系统" lay-direction="2">
                    <i class="layui-icon layui-icon-return"></i>
                    <cite>返回系统</cite>
                </a>
            </li>
        </ul>
    </div>
</div>
