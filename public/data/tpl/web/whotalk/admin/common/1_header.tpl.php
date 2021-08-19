<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!$_GPC['infloat'] && !$_GPC['inajax']) { ?>
<!DOCTYPE html>
<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php  if(isset($title)) $_W['page']['title'] = $title?><?php  if(!empty($_W['page']['title'])) { ?><?php  echo $_W['page']['title'];?> - <?php  } ?><?php  if($_S['basic']['name']) { ?><?php  echo $_S['basic']['name'];?><?php  } else { ?><?php  echo $_W['account']['name'];?><?php  } ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="<?php echo MODULE_URL;?>icon.jpg" />
    <link rel="stylesheet" href="/static/layui/css/layui.css?v=<?php echo MODULE_RELEASE_DATE;?>">
    <link rel="stylesheet" href="<?php echo MODULE_STATIC;?>layui/css/admin.css?v=<?php echo MODULE_RELEASE_DATE;?>">
    <link href="/web/resource/css/bootstrap.min.css?v=<?php echo MODULE_RELEASE_DATE;?>" rel="stylesheet">
    <link href="/web/resource/css/common.css?v=<?php echo MODULE_RELEASE_DATE;?>" rel="stylesheet">
	<script type="text/javascript">var BaseApiUrl = '<?php  echo $this->createweburl("web",array("infloat"=>"yes"))?>&r=',BaseController="<?php  echo $_W['controller'];?>",BaseAction="<?php  echo $_W['action'];?>",BaseRoute="<?php  echo $_GPC['r'];?>",require = { urlArgs: 'v=<?php echo MODULE_RELEASE_DATE;?>' };;</script>
	<script type="text/javascript">
	if(navigator.appName == 'Microsoft Internet Explorer'){
		if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
			alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
		}
	}

	window.sysinfo = {
		<?php  if(!empty($_W['uniacid'])) { ?>'uniacid': '<?php  echo $_W['uniacid'];?>',<?php  } ?>
		<?php  if(!empty($_W['acid'])) { ?>'acid': '<?php  echo $_W['acid'];?>',<?php  } ?>
		<?php  if(!empty($_W['openid'])) { ?>'openid': '<?php  echo $_W['openid'];?>',<?php  } ?>
		<?php  if(!empty($_W['uid'])) { ?>'uid': '<?php  echo $_W['uid'];?>',<?php  } ?>
		'isfounder': <?php  if(!empty($_W['isfounder'])) { ?>1<?php  } else { ?>0<?php  } ?>,
		'family': '<?php echo IMS_FAMILY;?>',
		'siteroot': '<?php  echo $_W['siteroot'];?>',
		'siteurl': '<?php  echo $_W['siteurl'];?>',
		'attachurl': '<?php  echo $_W['attachurl'];?>',
		'attachurl_local': '<?php  echo $_W['attachurl_local'];?>',
		'attachurl_remote': '<?php  echo $_W['attachurl_remote'];?>',
		'module' : {'url' : '<?php  if(defined('MODULE_URL')) { ?><?php echo MODULE_URL;?><?php  } ?>', 'name' : '<?php  if(defined('IN_MODULE')) { ?><?php echo IN_MODULE;?><?php  } ?>'},
		'cookie' : {'pre': '<?php  echo $_W['config']['cookie']['pre'];?>'},
		'account' : <?php  echo json_encode($_W['account'])?>,
		'server' : {'php' : '<?php  echo phpversion()?>'},
	};
	var require = { urlArgs: 'v=<?php echo MODULE_RELEASE_DATE;?>' };
	</script>
	<script type="text/javascript" src="/static/layui/layui.js?v=<?php echo MODULE_RELEASE_DATE;?>"></script>
    <script type="text/javascript" src="/web/resource/js/lib/jquery-1.11.1.min.js?v=<?php echo MODULE_RELEASE_DATE;?>"></script>
    <script type="text/javascript" src="/web/resource/js/lib/bootstrap.min.js?v=<?php echo MODULE_RELEASE_DATE;?>"></script>
    <script type="text/javascript" src="/web/resource/js/app/util.js?v=<?php echo MODULE_RELEASE_DATE;?>"></script>
    <script type="text/javascript" src="/web/resource/js/app/common.min.js?v=<?php echo MODULE_RELEASE_DATE;?>"></script>
    <script type="text/javascript" src="/web/resource/js/require.js?v=<?php echo MODULE_RELEASE_DATE;?>"></script>
</head>
<body layadmin-themealias="ocean-header" class="layui-layout-body" style="position:inherit !important;">
    <div class="layui-layout layui-layout-admin">
        <?php  $menu = m('prem')->getmenu($_W['uid'])?>
        <?php (!empty($this) && $this instanceof WeModule || 1) ? (include $this->template('admin/common/menu', 2)) : (include template('admin/common/menu', TEMPLATE_INCLUDEPATH));?>
        <!-- 主体内容 -->
        <div class="layui-body">
<?php  } ?>
