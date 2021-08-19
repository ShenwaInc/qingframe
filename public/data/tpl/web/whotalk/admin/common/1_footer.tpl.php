<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!$_GPC['infloat'] && !$_GPC['inajax']) { ?>
		<?php  if($_W['isfounder'] || !$_S['basic']['hidecopyright']) { ?>
		<div class="container-fluid footer text-center" role="footer">
			<div class="copyright"><?php  echo $_W['setting']['page']['copyright'];?></div>

			<div>
				<?php  if(!empty($_W['setting']['copyright']['icp'])) { ?>
				备案号：<a href="http://beian.miit.gov.cn/" target="_blank"><?php  echo $_W['setting']['copyright']['icp'];?></a>
				<?php  } ?>
				<?php  if(!empty($_W['setting']['copyright']['policeicp']['policeicp_location']) && !empty($_W['setting']['copyright']['policeicp']['policeicp_code'])) { ?>
				<a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=<?php  echo $_W['setting']['copyright']['policeicp']['policeicp_code']?>" >
					&nbsp;&nbsp;<img src="./resource/images/icon-police.png" >
					<?php  echo $_W['setting']['copyright']['policeicp']['policeicp_location']?> <?php  echo $_W['setting']['copyright']['policeicp']['policeicp_code']?>号
				</a>
				<?php  } ?>
			</div>
		</div>
		<?php  } ?>
	</div>
	<!-- 辅助元素，一般用于移动设备下遮罩 -->
	<div class="layadmin-body-shade" layadmin-event="shade"></div>
</div>
<script type="text/javascript">
$(function(){
	$('.ajaxshow').on('click',this,function(){
		let geturl = $(this).attr('href');
		let title = typeof($(this).attr('title'))!='undefined' ? $(this).attr('title') : $(this).text();
		let width = typeof($(this).attr('data-width'))!='undefined' ? $(this).attr('data-width') + 'px' : '990px';
		$.ajax({url:geturl,type:'GET',data:{inajax:1},dataType:"html",success: function(Html){
			if(isJsonString(Html)){
				var obj = jQuery.parseJSON(Html);
				if(typeof(obj.message)!='undefined'){
					layer.msg(obj.message,{icon:0});
				}
				setTimeout(function(){
					if(typeof(obj.url)!='undefined'){
						window.location.href = obj.url;
					}
					if(typeof(obj.redirect)!='undefined'){
						window.location.href = obj.redirect;
					}
				},1500);
			}else{
				let WindowId = 'ajaxwindow' + Wrandom(6);
				layer.open({type:1,content:Html,id:WindowId,title:title,shade:0.3,area:width,shadeClose:true});
				let Ajaxwindow = $('#'+WindowId);
				if(Ajaxwindow.find('form.layui-form').length>0){
					var filter = Ajaxwindow.find('form.layui-form').attr('lay-filter');
					let initdate = Ajaxwindow.find('.layui-input-laydate').length>0;
					FormInit(filter,initdate);
				}
			}
		}});
		return false;
	});
	$('.showmenu').on('click',this,function(){
		$(this).dropdown();
	});
	$('a.confirm').on('click',this,function(){
		var comfirmText = $(this).data('text');
		var redirect = $(this).attr('href');
		layer.confirm(comfirmText, {icon: 3, title:'提示'}, function(index){
			window.location.href = redirect;
			layer.close(index);
		});
		return false;
	});
	$('.layui-fluid [lay-tips]').each(function (index,element) {
		$(element).on({
			mouseenter:function () {
				var tipstr = jQuery(this).attr('lay-tips');
				laytips = layer.tips(tipstr,this,{tips:1,time:0});
			},
			mouseleave:function () {
				layer.close(laytips);
			}
		});
	});
	$('[layadmin-event]').click(function () {
		let layevent = $(this).attr('layadmin-event');
		let WinBody = $('body');
		switch (layevent) {
			case 'flexible' :
				WinBody.addClass('layadmin-side-spread-sm');
				break;
			case 'shade' :
				WinBody.removeClass('layadmin-side-spread-sm');
				break;
			case 'closedp' :
				var datapicker = $(this).data('dp');
				$(datapicker).val(''),$(datapicker+'-i').val('').dropdown('toggle').focus();
				break;
			case 'updateCache' :
				layui.use('layer', function(){
					var index = layer.load(0,{shade: false,time: 3000});
					$.post('./index.php?c=system&a=updatecache&do=updatecache', {}, function(data) {
						console.log(data);
						layer.close(index);
						layer.msg('缓存更新成功',{icon:1})
					})
				});
				break;
			case 'showqrcode' :
				var qrcode = $(this).data('url');
				let title = typeof($(this).data('title'))=='undefined' ? '使用微信扫描二维码' : $(this).data('title');
				layer.open({
					title:title,
					content: '<div style="width: 200px; height: 200px; margin: 0 auto;"><img src="'+qrcode+'" height="200" width="200" /></div>',
					shade:0.5,
					shadeClose:true
				});
				break;
			case 'fullscreen' :
				let doc=document.documentElement
				// if(doc.requestFullscreen){
				//     doc.requestFullscreen()
				//     document.exitFullscreen()
				// }
				if(doc.webkitRequestFullScreen){
					doc.webkitRequestFullScreen();
					document.webkitCancelFullScreen()
				}
				if(doc.mozRequestFullScreen){
					doc.mozRequestFullScreen()
					document.mozCancelFullScreen()
				}
				if(doc.msRequestFullscreen){
					doc.msRequestFullscreen()
					document.msExitFullscreen()
				}
				break;
			default :
				return true;
		}
	});
});
function Wrandom(len=8){
	let codes = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
	let maxPos = codes.length;
	let code = '';
	for (let i = 0; i < len; i++) {
		code += codes.charAt(Math.floor(Math.random() * maxPos));
	}
	return code;
}
function FormInit(filter,initdate=false){
	let FormRender = function (){
		layui.form.render(null, filter);
		if(initdate){
			jQuery('.layui-input-laydate').each(function(index, element) {
				layui.laydate.render({
					elem: element //指定元素
					,format:'yyyy-MM-dd'
				});
			});
		}
	}
	if(typeof(layui.form)=='undefined' || (initdate && typeof(layui.laydate)=='undefined')){
		let user = [];
		if(typeof(layui.form)=='undefined') user.push('form');
		if(initdate && typeof(layui.laydate)=='undefined') user.push('laydate');
		return layui.use(user, function(){
			layer.ready(FormRender)
		});
	}
	FormRender();
}
function isJsonString(str){
	try {
		if(typeof(jQuery.parseJSON(str)) == "object") {
			return true;
		}
	} catch(e) {
	}
	return false;
}
</script>
<style type="text/css">
.w7-window-side, .layui-table-tool-self{display:none !important; visibility:hidden !important;}
.layui-input-block em.close{right:auto !important;}
* {-webkit-box-sizing: content-box; -moz-box-sizing: content-box; box-sizing: content-box;}
.modal, .modal * {-webkit-box-sizing: border-box !important; -moz-box-sizing: border-box !important; box-sizing: border-box !important;}
a:hover,a:visited,a:active,a:focus{text-decoration:none;}
.layui-nav .list{border:none;}
h1, .h1, h2, .h2, h3, .h3{margin-top:0; margin-bottom:0;}
.layui-btn:focus{color:#fff !important;}
.layui-form-label{width:92px;}
.layui-input-block{margin-left:126px;}
.layui-detail .layui-form-item{margin-bottom:0;}
.pagination{margin:0 !important;}
.layui-badge .layui-icon {font-size: 10px;}
.must .layui-form-label:after{content:'*'; margin-left:5px; color:#f00; line-height:9px;}
a.layui-badge:hover{color:#fff; cursor:pointer;}
.layui-input-text{line-height:38px;}
.layui-bg-1{background-color: #FFB800!important}
.layui-bg-2{background-color: #1E9FFF !important}
.layui-bg-3{background-color: #FFB800 !important}
.layui-bg-4{background-color: #009688 !important}
.layui-bg-4{background-color: #2F4056 !important}
.layui-bg--1{background-color: #eee !important; color:#666 !important;}
.layui-form-select dl{z-index:9999;}
.layui-input-inline ~ .layui-form-mid{padding: 0 !important; line-height: 38px;}
.layui-side-menu .layui-nav .layui-nav-item .fa {position: absolute;top: 50%;left: 20px;margin-top: -19px;line-height: 40px;}
.layui-nav .layui-nav-child a{line-height: 36px;}
.layui-layout-admin .layui-header a, .layui-layout-admin .layui-header a cite{color: #fff;}
.layui-layout-admin .layui-body > .layui-fluid{min-height: 98%;}
.dropdown .layui-icon-close{display: none; position: absolute; top: 50%; margin-top: -15px; height: 30px; line-height: 30px; right: 30px; font-size: 20px;}
.dropdown.open .layui-icon-close{display: block;}
.dropdown.open .layui-edge {margin-top: -9px; -webkit-transform: rotate(180deg); transform: rotate(180deg); margin-top: -3px\9;}
</style>
<?php  if(!$noclosetag) { ?></body></html><?php  } ?>
<?php  } ?>
