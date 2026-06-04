<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else class="margin bg-white radius shadow">
			<view class="nav solid-bottom">
				<view class="flex text-center">
					<view class="cu-item flex-sub" @click="currtab='account'" :class="currtab=='account'?'cur text-'+theme.actcolor:''" v-if="data.level==4">公众号</view>
					<view class="cu-item flex-sub" @click="currtab='weixin'" :class="currtab=='weixin'?'cur text-'+theme.actcolor:''" v-if="appweixin || data.weixin.id>0">APP</view>
					<view class="cu-item flex-sub" @click="currtab='wxapp'" :class="currtab=='wxapp'?'cur text-'+theme.actcolor:''" v-if="platform=='wxapp' || data.wxapp.id>0">小程序</view>
				</view>
			</view>
			<view v-if="currtab=='account'">
				<view v-if="data.account.fanid">
					<view class="padding text-center margin-top-xl">
						<view class="cu-avatar round xl" :style="[{ backgroundImage:'url(' + data.account.avatar + ')' }]"></view>
					</view>
					<view class="text-center padding-lr padding-bottom">
						<view class="align-center flex justify-center">
							<text class="text-xl">{{data.account.nickname}}</text>
							<text class="cu-tag round sm margin-left-xs" :class="'bg-'+(data.hasfocus?theme.actcolor:'orange')">{{data.hasfocus?'已关注':'未关注'}}</text>
						</view>
						<view class="text-lg padding-sm margin-top-sm" @click="doUnbind('account')" :class="'text-'+theme.actcolor">解除绑定</view>
					</view>
				</view>
				<view class="qrcode text-center padding" v-if="!data.hasfocus">
					<block v-if="data.qrcode">
						<image :src="data.qrcode" mode="heightFix"></image>
						<view class="text-gray padding-bottom">
							<text>{{data.focustext}}</text>
						</view>
						<view class="flex flex-direction">
							<button class="cu-btn lg" @click="doSaveQrcode()" :class="'bg-'+theme.actcolor">保存到相册</button>
						</view>
					</block>
					<view class="text-empty" v-else>关注二维码获取失败</view>
				</view>
			</view>
			<view v-if="currtab=='weixin'">
				<block v-if="data.weixin.id>0">
					<view class="padding text-center margin-top-xl">
						<view class="cu-avatar round xl" :style="[{ backgroundImage:'url(' + data.weixin.userinfo.avatar + ')' }]">
							<view class="cu-tag badge" :class="'bg-'+theme.actcolor">APP</view>
						</view>
					</view>
					<view class="text-center padding-lr padding-bottom">
						<text class="text-xl">{{data.weixin.userinfo.nickname}}</text>
						<view class="text-lg padding-sm margin-top-sm" @click="doUnbind('weixin')" :class="'text-'+theme.actcolor">解除绑定</view>
					</view>
				</block>
				<view class="text-empty" v-else>
					<text>暂未绑定微信登录</text>
					<view class="padding-lr-xl padding-bottom-xl flex flex-direction">
						<view class="cu-btn lg radius" :class="'bg-'+theme.actcolor" @click="doBinding('weixin')">立即绑定</view>
					</view>
				</view>
			</view>
			<view v-if="currtab=='wxapp'">
				<block v-if="data.wxapp.id>0">
					<view class="padding text-center margin-top-xl">
						<view class="cu-avatar round xl" :style="[{ backgroundImage:'url(' + data.wxapp.userinfo.avatar + ')' }]">
							<view class="cu-tag badge" :class="'bg-'+theme.actcolor">小程序</view>
						</view>
					</view>
					<view class="text-center padding-lr padding-bottom">
						<text class="text-xl">{{data.wxapp.userinfo.nickname}}</text>
						<view class="text-lg padding-sm margin-top-sm" @click="doUnbind('wxapp')" :class="'text-'+theme.actcolor">解除绑定</view>
					</view>
				</block>
				<!-- #ifdef MP-WEIXIN -->
				<view class="text-empty" v-if="data.wxapp.id==0">
					<text>暂未绑定微信小程序</text>
					<view class="padding-lr-xl padding-bottom-xl flex flex-direction">
						<button class="cu-btn lg radius" :class="'bg-'+theme.actcolor" open-type="getUserInfo" @getuserinfo="WxappLogin" withCredentials="true">
							<text>立即绑定</text>
						</button>
					</view>
				</view>
				<!-- #endif -->
			</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				loaded:false,
				currtab:"account",
				appweixin:false,
				platform:core.platform,
				theme:core.style,
				data:{
					"title":"绑定微信",
					"hasfocus":false,
					"focustext":"长按或截图使用微信识别二维码关注",
					"level":0,
					"qrcode":"",
					"unionid":"",
					"account":{"fanid":0,"openid":""},
					"wxapp":{"id":0,"openid":""},
					"weixin":{"id":0,"openid":""},
				}
			}
		},
		onLoad() {
			// #ifdef APP-PLUS
			let self = this;
			uni.getProvider({
			    service: 'oauth',
			    success: function (res) {
			        for(var p in res.provider){
						if(res.provider[p]=='weixin'){
							self.appweixin = true;
							break;
						}
					}
			    }
			});
			// #endif
		},
		onPullDownRefresh() {
			this.initData('bind/account');
			uni.stopPullDownRefresh();
		},
		onShow() {
			this.initData('bind/account',{},function(res){
				//console.log(res);
			});
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.loaded = true;
					that.data = res;
					uni.setNavigationBarTitle({
						title:res.title
					});
					if(callback){
						callback(res);
					}
				},data);
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			WxappLogin(){
				let self = this;
				wx.getUserProfile({
					desc:'用于完善会员资料',
					success(infoRes) {
						uni.login({
							provider: 'weixin',
							success: (res) => {
								let postdata = {provider:'wxapp',userinfo:infoRes.userInfo,bindwx:"true",code:res.code}
								return core.post('bind/account',function(ret){
									if(ret.type!='success') return core.report(ret);
									core.toast('绑定成功！','','success');
									self.initData('bind/account');
								},postdata);
							}
						});
					},
					fail(e) {
						console.log(e);
					}
				});
			},
			doSaveQrcode(){
				// #ifdef H5
				if(typeof(doPlusSaveImage)=='undefined' || typeof(plus)=='undefined'){
					return core.toast('请长按图片保存');
				}
				// #endif
				let self = this;
				let SaveQrcode = function(res){
					// #ifdef H5
					if(typeof(doPlusSaveImage)=='function'){
						return doPlusSaveImage(res.tempFilePath);
					}
					if(typeof(plus)!='undefined'){
						return plus.gallery.save(res.tempFilePath,function(){
							core.toast('保存成功！','','success');
						},function(e){
							core.toast('保存失败，请重试');
							console.log('保存图片失败：',e)
						});
					}
					return core.toast('请长按图片保存');
					// #endif
					uni.saveImageToPhotosAlbum({
						filePath:res.tempFilePath,
						success:function(){
							core.toast('保存成功！','','success');
						},
						fail(e) {
							core.toast('保存失败，请重试');
							console.log('保存图片失败：',e)
						}
					});
				}
				return uni.downloadFile({
					url:this.data.qrcode,
					success:SaveQrcode,
					fail:function(e){
						core.toast('图片下载失败');
						console.log('图片下载失败：',e)
					}
				})
			},
			doBinding(provider='weixin'){
				let self = this;
				uni.login({
					provider: 'weixin',
					success: (res) => {
						let useroptions = {
							provider: 'weixin',
							success: (infoRes) => {
								let postdata = {provider:provider,userinfo:infoRes.userInfo,bindwx:"true"};
								core.post('bind/account',function(res){
									if(res.type!='success') return core.report(res);
									core.toast('绑定成功！','','success');
									self.initData('bind/account');
								},postdata);
							},
							fail(e) {
								console.error(e);
								return core.toast('授权失败');
							}
						}
						uni.getUserInfo(useroptions);
					},
					fail: (err) => {
						core.toast('授权失败');
						console.error(err);
					}
				});
			},
			doUnbind(provider='account'){
				let cftext = provider=='account' ? '解除后将无法接收公众号通知，使用公众号登录将创建新的账户' : '解除后在'+(provider=='wxapp'?'小程序':'APP')+'上使用该微信登录将绑定新账户';
				let self = this;
				core.confirm(cftext,'解除绑定',function(){
					core.post('bind/unbind',function(res){
						if(res.type!='success') return core.report(res);
						self.initData('bind/account');
					},{provider:provider});
				});
			}
		}
	}
</script>

<style>
</style>
