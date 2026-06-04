<template>
	<view :class="['page-text-'+textSize]">
		<!-- #ifndef APP-PLUS -->
		<web-view :class="hasBar?'wtweb_tabs':''" name="whotalkwebview" :webview-styles="webviewStyles" :src="weburl" v-if="!inapp"></web-view>
		<!-- #endif -->
		<view class="cu-bar tabbar foot" :class="theme.navbg" v-if="mainnavs.length>0" id="MainNavView">
			<view class="action" :class="[currenturl==item.url?'cur text-'+theme.actcolor:'', item.extra]" @click="checkoutbar(index)" v-for="(item, index) in mainnavs" :key="index">
				<view :class="item.icon+(currenturl==item.url?'fill':'')"></view>
				{{item.name}}
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	var WebView = null;
	
	export default {
		computed: mapState(['textSize']),
		data() {
			return {
				weburl:'',
				title:'加载中...',
				currenturl:'',
				nativeurl:'',
				inapp:true,
				hasBar:false,
				options:{},
				mainnavs:[],
				platform:core.platform,
				theme:core.style,
				ColorList:{
					"red":"#e54d42",
					"orange":"#f37b1d",
					"yellow":"#fbbd08",
					"olive":"#8dc63f",
					"green":"#39b54a",
					"cyan":"#1cbbb4",
					"blue":"#0081ff",
					"purple":"#6739b6",
					"mauve":"#9c26b0",
					"pink":"#e03997",
					"brown":"#a5673f",
					"grey":"#8799a3",
					"gray":"#f0f0f0",
					"black":"#333333",
					"dazzledark":"#2a2833",
					"white":"#ffffff",
					"limegreen":"#05c160",
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
			this.options = options;
			if(typeof(options.url)=='undefined' || !options.url) return core.toast('参数错误：无效的地址','back');
			if(typeof(options.title)!='undefined' && options.title!=''){
				this.title = options.title;
				uni.setNavigationBarTitle({
					title:options.title
				});
			}
			if(typeof(plus)=='undefined'){
				this.inapp = false;
			}
			let weburl = options.url;
			if(weburl.indexOf('%')==-1){
				weburl = encodeURIComponent(weburl);
			}
			this.nativeurl = decodeURIComponent(weburl);
			this.loadnavs();
			let url = core.url('web','mobile') + '&url=' + weburl + (core.userinfo.uid==0?'':('&authkey='+core.userinfo.token));
			this.weburl = url;//.replace('swaapi.php','index.php');
			let bgcolor = core.style.navbg.replace('gradual-','');
			bgcolor = bgcolor.replace('bg-','');
			let fontcolor = '#ffffff';
			if(bgcolor=='white' || bgcolor=='yellow' || bgcolor=='gray') fontcolor = '#000000';
			uni.setNavigationBarColor({
				frontColor:fontcolor,
				backgroundColor:this.ColorList[bgcolor]
			});
			// #ifndef H5 || APP-PLUS
			return this.doSetTitle();
			// #endif
			let self = this;
			// #ifdef APP-PLUS || H5
			if(typeof(plus)=='undefined'){
				if(this.title=='加载中...'){
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
							self.doSetTitle();
						}
						console.log(mainFrame.contentWindow);
					},500);
				}
			}
			// #endif
		},
		onShow() {
		},
		onUnload() {
			if(WebView!=null){
				WebView.close();
			}
		},
		methods:{
			loadnavs(){
				let Pages = getCurrentPages();
				let curpage = Pages[(Pages.length-1)];
				let _options = curpage.options;
				if(_options.url.indexOf('%')==-1){
					_options.url = encodeURIComponent(_options.url);
				}
				this.currenturl = core.page('#/'+curpage.route,_options);
				this.currenturl = this.currenturl.replace('/pages/','');
				let self = this;
				core.cacheread('mainnavs',function(res){
					self.mainnavs = res.data;
					if(res.data.length==0){
						if(self.inapp){
							return self.doPlusCreateWebVie(0);
						}
					}
					self.hasBar = true;
					if(self.inapp){
						setTimeout(function(){
							let elemquery = uni.createSelectorQuery().in(self).select('#MainNavView');
							elemquery.boundingClientRect(function(res){
								self.doPlusCreateWebVie(res.height);
							}).exec();
						},10)
					}
				},function(e){
					if(self.inapp){
						self.doPlusCreateWebVie(0);
					}
				})
			},
			doPlusCreateWebVie(barHeight=0){
				let statusBarHeight = core.device=='ios' ? 50:45;
				// #ifdef APP-PLUS
				statusBarHeight += plus.navigator.getStatusbarHeight();
				// #endif
				let WindowHeight = core.Client.windowHeight - barHeight - statusBarHeight;
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
				let currentWebview;
				// #ifdef APP-PLUS
				currentWebview = this.$mp.page.$getAppWebview();
				// #endif
				// #ifdef H5
				currentWebview = plus.webview.currentWebview();
				// #endif
				currentWebview.append(WebView);
				WebView.setStyle({scalable:true});
				if(this.title=='加载中...'){
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
			},
			checkoutbar(i){
				let nav = this.mainnavs[i];
				if(nav.url==this.currenturl) return false;
				uni.$emit('checkoutBar', {index:i});
				return core.back();
			},
			doSetTitle(){
				if(this.title=='加载中...'){
					this.title =  '';
					uni.setNavigationBarTitle({
						title:this.title
					});
				}
			}
		}
	}
</script>

<style>
	.wtweb_tabs{bottom: calc(55px + env(safe-area-inset-bottom) / 2) !important;}
</style>