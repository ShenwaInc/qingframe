<script>
	import Vue from 'vue'
	import core from "@/core.js"
	import swasocket from "@/swasocket.js"
	
	export default {
		onLaunch: function(options) {
			console.log('欢迎使用Whotalk即时通讯系统');
			console.log('Whotalk官网：https://www.whotalk.com.cn/');
			if(typeof(options.query.debug)!='undefined') core.debug = options.query.debug=='yes' ? true : false;
			if(typeof(options.query.fromuid)!='undefined') core.agentid = parseInt(options.query.fromuid);
			let userstate = typeof(options.query.state)=='undefined' ? '' : options.query.state;
			core.init(userstate);
			uni.getSystemInfo({
				success: function(e) {
					let custom, CustomBar;
					// #ifndef MP
					Vue.prototype.StatusBar = e.statusBarHeight;
					if (e.osName == 'android') {
						CustomBar = e.statusBarHeight + 50;
					} else {
						CustomBar = e.statusBarHeight + 45;
						core.device = 'ios';
					};
					// #endif
					// #ifdef MP-WEIXIN
					custom = wx.getMenuButtonBoundingClientRect();
					// #endif
					// #ifdef MP-TOUTIAO
					custom = tt.getMenuButtonBoundingClientRect();
					// #endif
					// #ifdef MP-TOUTIAO || MP-WEIXIN
					Vue.prototype.StatusBar = e.statusBarHeight;
					Vue.prototype.Custom = custom;
					CustomBar = custom.bottom + custom.top - e.statusBarHeight;
					// #endif
					// #ifdef MP-ALIPAY
					CustomBar = e.statusBarHeight + e.titleBarHeight;
					// #endif
					Vue.prototype.CustomBar = e.CustomBar = CustomBar;
					core.Client = e;
				}
			})
			// #ifdef APP-PLUS  
			if(typeof(plus.push)!='undefined'){
				const _handlePush = function(message) {
					// TODO
					let payload = message.payload;
					if(core.isJsonString(payload)){
						payload = JSON.parse(payload);
					}
					if(typeof(payload.pagePath)!='undefined'){
						let ws = plus.webview.currentWebview();
						let weburl = ws.getURL();
						let redt = weburl.indexOf('dialog/index')>-1 ? 1 : 0;
						return core.navito('index/index',{rd:payload.pagePath});
					}
				};  
				plus.push.addEventListener('click', _handlePush);  
				//plus.push.addEventListener('receive', _handlePush);
			}
			// #endif
			uni.$on('SwaSocketConnect', function(res){
				uni.$on('SRredirect', function(socketdata){
					core.navito(socketdata.page,socketdata.data,socketdata.mode);
				});
				uni.$on('SRmessage', function(socketdata){
					core.toast(socketdata.message,socketdata.redirect,socketdata.mode);
				});
				uni.$on("UserLogout",function(e){
					uni.$off('SRredirect');
					uni.$off('SRmessage');
				});
			});
			uni.onNetworkStatusChange(function (res) {
				uni.$emit("NetworkStatusChange", res);
				if(res.isConnected){
					if(!core.connected){
						core.initsys(userstate);
					}else if(!swasocket.Connected){
						core.initsocket(core.userinfo.usersign, core.system.socket);
					}
				}
			});
		},
		onShow: function() {
			if(!core.appshowing && core.userinfo.uid>0){
				if(core.system.socket.type=='local'){
					core.SocketHeart(core.userinfo.usersign);
				}
			}
			core.appshowing = true;
			console.log('App Show');
		},
		onHide: function() {
			core.appshowing = false;
			console.log('App Hide');
		}
	}
</script>

<style>
	/*每个页面公共css */
	/* #ifndef APP-PLUS-NVUE */
	@import "colorui/main.css";
	@import "colorui/icon.css";
	@import "@/colorui/ifont.css";
	@import "style.css";
	
	.status_bar{width: 100%; height: var(--status-bar-height);}
	/* #endif */
</style>
