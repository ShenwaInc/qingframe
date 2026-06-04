<template>
	<view>
		<web-view name="whotalkwebview" :webview-styles="webviewStyles" :src="weburl" v-if="platform!='app'"></web-view>
	</view>
</template>

<script>
	import core from "@/core.js"
	var WebView = null;
	
	export default {
		data() {
			return {
				weburl:'',
				title:'',
				nativeurl:'',
				platform:core.platform,
				data:{
					title:"加载中..."
				},
				webviewStyles:{
					progress : {
						color:core.style.active
					}
				}
			}
		},
		onShareAppMessage(e){
			return {
				title:this.title
			}
		},
		onShareTimeline(e){
			let QueryString = 'url='+this.options.url;
			if(typeof(this.options.title)!='undefined'){
				QueryString += '&title=' + this.options.title;
			}
			if(core.userinfo.uid>0){
				QueryString += '&fromuid=' + core.userinfo.uid;
			}
			return {
				title:this.data.shareinfo.title,
				query:QueryString
			}
		},
		onLoad(options) {
			if(typeof(options.url)=='undefined' || !options.url) return core.toast('参数错误：无效的地址','back');
			if(typeof(options.title)!='undefined' && options.title!=''){
				this.title = options.title;
				uni.setNavigationBarTitle({
					title:options.title
				});
			}
			let weburl = options.url;
			if(weburl.indexOf('%')==-1){
				weburl = encodeURIComponent(weburl);
			}
			this.nativeurl = decodeURIComponent(weburl);
			let url = core.url('web','mobile') + '&url=' + weburl + (core.userinfo.uid==0?'':('&authkey='+core.userinfo.token));
			this.weburl = url;//.replace('swaapi.php','index.php');
			let self = this;
			if(core.platform=='app'){
				let SystemInfoSync = uni.getSystemInfoSync();
				let statusBarHeight = SystemInfoSync.statusBarHeight;
				let WindowHeight = SystemInfoSync.screenHeight - statusBarHeight;
				WebView = plus.webview.create(this.weburl, 'id', {
					plusrequire:"none",
					'uni-app': 'none',
					top: statusBarHeight,
					height:WindowHeight,
					additionalHttpHeaders:{
						"X-AUTH-TOKEN":core.userinfo.token
					}
				}, {
					preload: 'preload webview'
				});
				let currentWebview = this.$mp.page.$getAppWebview();
				currentWebview.append(WebView);
				WebView.setStyle({scalable:true});
				WebView.addEventListener('loaded', function(e){
					self.nativeurl = WebView.getURL();
					let title = WebView.getTitle();
					if(title){
						self.title = title;
						uni.setNavigationBarTitle({
							title:self.title
						});
					}
				}, false);
			}else{
				if(this.platform=='h5'){
					setTimeout(function(){
						let Frames = document.getElementsByTagName('iframe');
						var mainFrame = Frames[0];
						try{
							let title = mainFrame.contentWindow.document.title;
							if(title){
								self.title = title;
								uni.setNavigationBarTitle({
									title:self.title
								});
							}
						}catch(e){
							//TODO handle the exception
							console.log('页面标题获取失败：',e);
							if(self.title=='加载中...'){
								self.title =  '';
								uni.setNavigationBarTitle({
									title:self.title
								});
							}
						}
						console.log(mainFrame.contentWindow);
					},500)
				}
			}
		},
		onShow() {
			
		},
		methods:{
		}
	}
</script>

<style>
</style>
