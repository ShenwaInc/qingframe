<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="text-center" v-else>
			<!-- #ifdef H5 -->
			<view class="text-empty">
				<text>即将离开{{appName}}进入{{data.title}}</text>
			</view>
			<wx-open-launch-weapp id="launch-btn" :username="originalId" :path="data.path">
				<script type="text/wxtag-template">
					<style>
						.btn-open-weapp {
							background: #05c160;
							border: 0;
							color: #ffffff;
							border-radius: 50px;
							text-align: center;
							height: 40px;
							line-height: 40px;
							outline:none;
							padding: 0px 40px;
							font-size: 16px;
						}
					</style>
					<button class="btn-open-weapp">继续前往</button>
				</script>
			</wx-open-launch-weapp>
			<!-- #endif -->
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	var sweixin = null;
	
	export default {
		data() {
			return {
				loaded:false,
				data:{
					title:"小程序",
					appid:"",
					path:"",
					weburl:""
				},
				appName:core.system.name,
				originalId:""
			}
		},
		onLoad(option) {
			this.data.appid = option.appid;
			if(typeof(option.path)!='undefined'){
				this.data.path = option.path;
			}
			if(typeof(option.weburl)!='undefined'){
				this.data.weburl = option.weburl;
			}
			if(typeof(option.original)!='undefined'){
				this.originalId = option.original;
			}
			if(typeof(option.title)!='undefined'){
				this.data.title = option.title;
			}
			let self = this;
			// #ifdef APP-PLUS
			return plus.share.getServices(function(s){
				let sWeixin = null;
				for(let i in s){
					if(s[i].id=='weixin'){
						sWeixin = s[i];
						break;
					}
				}
				if(sWeixin==null) return core.toast("暂不支持打开小程序");
				sWeixin.launchMiniProgram({
					id:self.originalId,
					path:self.data.path,
					webUrl:self.data.weburl
				}, function(res){
					//console.log("打开成功...")
				}, function(e){
					core.toast("打开到小程序失败");
				});
			}, function(e){
				core.toast("暂不支持打开小程序");
			}), core.back();
			// #endif
			if(!core.inwechat){
				return core.toast("暂不支持跳转小程序", "back");
			}
			this.loaded = true;
			if(this.originalId==""){
				this.originalId = this.data.weburl;
			}
			if(this.data.path.indexOf('?')>=0){
				this.data.path = this.data.path.replace("?", ".html?")
			}else if(this.data.path!=""){
				this.data.path += ".html";
			}
			core.initjwx(function(wx){
				
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