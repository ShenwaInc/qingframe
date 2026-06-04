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
				options:{},
				platform:core.platform,
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
		onNavigationBarButtonTap(e){
			if(e.type=='back') return core.back();
			if(e.type=='share'){
				let self = this;
				return core.post('dialog/shareweb',function(res){
					if(res.type!='success') return core.report(res);
					core.navito('dialog/share',{mid:res.message.id,fp:'web'});
				},{title:this.title,url:this.nativeurl});
			}
		},
		onLoad(options) {
			this.options = options;
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
			this.weburl = url;
			let self = this;
			// #ifdef APP-PLUS
			let SystemInfoSync = uni.getSystemInfoSync();
			let statusBarHeight = SystemInfoSync.statusBarHeight + (core.device=='ios'?50:45);
			let WindowHeight = SystemInfoSync.windowHeight;
			WebView = plus.webview.create(this.weburl, 'id', {
				plusrequire:"none",
				"uni-app":"none",
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
			if(this.title==""){
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
			}
			if(typeof(options.nos)!='undefined'){
				currentWebview.setTitleNViewButtonStyle(0, {
					type:"none",
					text:""
				});
			}
			// #endif
			// #ifdef H5
			if(this.title==""){
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
					}
					//console.log(mainFrame.contentWindow);
				},500);
			}
			// #endif
			let bgcolor = core.style.navbg.replace('gradual-','');
			bgcolor = bgcolor.replace('bg-','');
			let fontcolor = '#ffffff';
			if(bgcolor=='white' || bgcolor=='yellow' || bgcolor=='gray') fontcolor = '#333333';
			uni.setNavigationBarColor({
				frontColor:fontcolor,
				backgroundColor:this.ColorList[bgcolor]
			});
		},
		onShow() {
			
		},
		methods:{
		}
	}
</script>

<style>
</style>