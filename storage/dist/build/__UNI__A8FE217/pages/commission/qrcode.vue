<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<image :src="data.qrcode" style="width: 100%;" mode="widthFix"></image>
			<view class="cu-bar tabbar foot noshadow">
				<view class="action">
					<button @click="initData('commission/qrcode',{op:'remake'})" class="cu-btn lg margin-bottom-xl round-only-left" :class="['bg-'+theme.actcolor]">重新生成</button>
					<button @click="doSaveQrcode()" class="cu-btn lg margin-bottom-xl bg-orange no-radius">保存</button>
					<button @click="showshare=true" class="cu-btn lg margin-bottom-xl bg-red round-only-right">分享</button>
				</view>
			</view>
		</view>
		<share :shareinfo="shareinfo" :shareinner="false" @closeshare="doShareClose" :showshare="showshare"></share>
	</view>
</template>

<script>
	import core from "@/core.js"
	import share from "@/components/util/share.vue"
	
	export default {
		components: {share},
		data() {
			return {
				inh5:true,
				loaded:false,
				platform:core.platform,
				theme:core.style,
				redirecturl:"",
				showshare:false,
				shareinfo:{
					url:'',
					providers:4,
					provider:{}
				},
				data:{
					title:"我的推广码",
					qrcode:"../../static/images/img_vip.png",
					inviteurl:"",
					force:0,
					shareinfo:{
						title:"",
						desc:"",
						cover:"",
						url:""
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.rd)!='undefined'){
				this.redirecturl = options.rd;
				if(this.redirecturl.indexOf('%')>-1){
					this.redirecturl = decodeURIComponent(this.redirecturl);
				}
			}
			this.initData('commission/qrcode');
			if(core.platform!='h5' || typeof(plus)!='undefined'){
				this.inh5 = false;
			}
		},
		onShareAppMessage(e){
			let self = this;
			return {
				title:this.data.shareinfo.title,
				imageUrl:this.data.shareinfo.cover,
				desc:this.data.shareinfo.desc,
				success:function(){
					self.doShareClose('success');
				},
				fail:function(){
					self.doShareClose('fail');
				}
			}
		},
		onShareTimeline(){
			this.doShareClose('opening');
			return {
				title:this.data.shareinfo.title,
				query:"fromuid="+core.userinfo.uid,
				imageUrl:this.data.shareinfo.cover
			}
		},
		onShow() {
			
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
					if(typeof(res.shareinfo)!='undefined'){
						that.shareinfo = core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
					}
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
			doShareClose(res){
				this.showshare = false;
				if(this.data.force==2){
					//强制分享
					if(res=='fail'){
						if(core.inwechat && this.platform=='h5'){
							res = 'inwechat';
						}else{
							return core.toast('分享失败，请重试');
						}
					}else if(res=='inner'){
						return core.toast('必须分享到外部才生效');
					}
					let self = this;
					core.post('commission/qrcode',function(ret){
						console.log(ret);
						if(ret.type=='success'){
							if(res=='success' && self.redirecturl!=""){
								core.toast('分享成功！',self.redirecturl,'success')
							}
							if(res=='copy'){
								core.toast('请将复制的链接分享给好友');
							}
							if(res=='opening'){
								core.toast('分享成功后可正常使用');
							}
						}
					},{shareres:res});
				}
			},
			doSaveQrcode(){
				if(this.inh5) return core.toast('请长按图片保存');
				let self = this;
				// #ifdef H5
				if(typeof(doPlusSaveImage)=='undefined' || typeof(plus)=='undefined'){
					return core.toast('请长按图片保存');
				}
				// #endif
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
			}
		}
	}
</script>

<style>
</style>
