<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="padding bg-white" v-else>
			<view class="padding">
				<view class="avatar shadow text-center" @click="ChooseCover()">
					<view class="cu-avatar xl round shadow" :style="'background-image:url('+data.group.pic+');'"></view>
					<view class="icon text-yellow"><text class="cuIcon-cameraadd"></text></view>
				</view>
			</view>
			<form>
				<view class="cu-form-group margin-top">
					<view class="title">群名称</view>
					<input placeholder="请输入群聊的名称" v-model="data.postdata.name" name="name"></input>
				</view>
				<view class="cu-form-group align-start">
					<view class="title">群介绍</view>
					<textarea maxlength="-1" v-model="data.postdata.description" placeholder="请简要说明这个群组"></textarea>
				</view>
				<view class="cu-form-group align-start">
					<view class="title">欢迎语</view>
					<textarea maxlength="-1" v-model="data.postdata.welcome" placeholder="请输入进群欢迎语(选填)"></textarea>
				</view>
				<view class="cu-form-group align-start" v-if="updatenotice==1">
					<view class="title">群公告</view>
					<textarea maxlength="-1" v-model="data.postdata.notice" placeholder="请输入要发布的群公告"></textarea>
				</view>
				<view class="cu-list menu solid-top">
					<view class="cu-item">
						<view class="content">
							编辑公告
						</view>
						<view class="action">
							<switch class="switch" @change="doUpdateNotice" :class="updatenotice==1?'checked':''" :checked="updatenotice==0?false:true"></switch>
						</view>
					</view>
				</view>
				<view class="flex flex-direction padding-top-xl">
					<button class="cu-btn round text-boldm lg" :class="'bg-'+theme.actcolor" @click="doSave()">保存</button>
				</view>
			</form>
		</view>
		<!-- #ifdef H5 || MP-WEIXIN -->
		<ImageCrop :cropImage="scrop.image" resFrom="profiles" :cropTempFile="scrop.tempfile" :isSquare="scrop.square" @cropRes="CropCover"></ImageCrop>
		<!-- #endif -->
	</view>
</template>

<script>
	import core from "@/core.js"
	import swaload from "@/components/util/swaload.vue"
	import ImageCrop from "@/components/util/ImageCrop.vue"
	
	export default {
		components: {swaload, ImageCrop},
		data() {
			return {
				gid:0,
				theme:core.style,
				updatenotice:0,
				loaded:false,
				showmodal:"",
				data:{
					title:"群资料",
					postdata:{
						name:"",
						pic:"",
						description:"",
						notice:"",
						welcome:""
					},
					group:{
						id:0,
						name:"",
						pic:"",
						datetime:"",
						ismanager:true,
						members:0,
						description:"",
						uid:0,
						nickname:"",
						fee:0,
						allowpost:1,
						protective:0,
						allowjoin:1,
						joinaudit:0,
						ishide:0
					}
				},
				scrop:{
					image:"",
					width:192,
					square:false,
					tempfile:""
				}
			}
		},
		onLoad(options) {
			if(typeof(options.gid)=='undefined' || options.gid=='') return core.toast('无效的群组信息','back');
			this.gid = parseInt(options.gid);
			this.initData('group/profile',{gid:this.gid},function(res){
				if(!res.group.ismanager) return core.toast('您不是群管理员','back');
				
			});
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
					that.data = res;
					that.loaded = true;
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
			doUpdateNotice(e){
				this.updatenotice = e.detail.value ? 1 : 0;
			},
			ChooseCover(){
				var that = this;
				uni.chooseImage({
					count: 1, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (res) => {
						// #ifdef H5 || MP-WEIXIN
						that.scrop.tempfile = res.tempFilePaths[0];
						that.scrop.image = res.tempFiles[0].path;
						that.scrop.square = false;
						that.showmodal = 'ImageCroper';
						return uni.getImageInfo({
							src:res.tempFiles[0].path,
							success(img) {
								let size = Math.abs(img.width-img.height);
								if(size<10){
									that.scrop.square = true;
								}
							}
						});
						// #endif
						core.upload(res.tempFilePaths[0],function(res){
							that.data.group.pic = res.url;
							that.data.postdata.pic = res.path;
						}, 'image', 'attach/upload', {resfrom:"profiles"});
					}
				});
			},
			doSave(){
				if(this.data.postdata.name=='') return core.toast('群名称不能为空');
				if(this.data.postdata.pic=='') return core.toast('请先上传群头像');
				core.post('group/profile',function(res){
					let redirect = res.type=='success' ? 'back' : res.redirect;
					core.toast(res.message,redirect,res.type);
				},{gid:this.data.group.id,saveprofile:"true",data:this.data.postdata,updatenotice:this.updatenotice});
			},
			CropCover(res){
				this.data.postdata.pic = res.path;
				this.data.group.pic = res.url;
			}
		}
	}
</script>

<style>
	.avatar{border-radius: 50%; padding: 8upx; background: #fff; width: 5.33rem; height: 5.33rem; margin: 0 auto; position: relative;}
	.avatar.shadow{box-shadow: 0px 24upx 92upx 0px #E4DCBC;}
	.avatar .cu-avatar{width: 100%; height: 100%;}
	.avatar .icon{position: absolute; right: 0; top: 0; font-size: 48upx;}
	.cu-form-group .title {width: 5em; text-align-last:justify;}
</style>
