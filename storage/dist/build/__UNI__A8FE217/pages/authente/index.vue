<template>
	<view>
		<cu-custom :bgColor="'bg-'+bgcolor" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="head padding-xl" :class="'bg-'+bgcolor">
				<view class="align-center flex justify-center">
					<view class="round-square">
						<view class="round-square">
							<view class="round-square text-xxxl">
								<text class="cuIcon-safe"></text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="mode0" v-if="mode==0">
				<view class="bg-white padding-top-sm">
					<view class="cu-form-group must" v-if="data.requires.indexOf('realname')>=0">
						<view class="title">真实姓名</view>
						<input v-model="postdata.name" type="text" placeholder="请输入您的真实姓名" />
					</view>
					<view class="cu-form-group margin-top-xs must" v-if="data.requires.indexOf('idcard')>=0">
						<view class="title">证件号码</view>
						<input v-model="postdata.idcard" type="text" placeholder="请输入您的身份证号码" />
					</view>
					<block v-if="data.requires.indexOf('idcardz')>=0">
						<view class="cu-bar bg-white margin-top-xs">
							<view class="action">身份证正面照</view>
						</view>
						<view class="cu-form-group">
							<view class="grid col-4 grid-square flex-sub">
								<view class="solids" @click="ChooseImg('idcardz')" v-if="postdata.idcardz==''">
									<text class="cuIcon-cameraadd"></text>
								</view>
								<view class="bg-img" @click="ViewImg(thumbs.idcardz_url)" v-else>
									<image :src="thumbs.idcardz_url" mode="aspectFill"></image>
									<view class="cu-tag bg-red" @tap.stop="DelImg('idcardz')">
										<text class='cuIcon-close'></text>
									</view>
								</view>
							</view>
						</view>
					</block>
					<block v-if="data.requires.indexOf('idcardf')>=0">
						<view class="cu-bar bg-white margin-top-xs">
							<view class="action">身份证反面照</view>
						</view>
						<view class="cu-form-group">
							<view class="grid col-4 grid-square flex-sub">
								<view class="solids" @click="ChooseImg('idcardf')" v-if="postdata.idcardf==''">
									<text class="cuIcon-cameraadd"></text>
								</view>
								<view class="bg-img" @click="ViewImg(thumbs.idcardf_url)" v-else>
									<image :src="thumbs.idcardf_url" mode="aspectFill"></image>
									<view class="cu-tag bg-red" @tap.stop="DelImg('idcardf')">
										<text class='cuIcon-close'></text>
									</view>
								</view>
							</view>
						</view>
					</block>
					<block v-if="data.requires.indexOf('idcards')>=0">
						<view class="cu-bar bg-white margin-top-xs">
							<view class="action">手持身份证照</view>
						</view>
						<view class="cu-form-group">
							<view class="grid col-4 grid-square flex-sub">
								<view class="solids" @click="ChooseImg('idcards')" v-if="postdata.idcards==''">
									<text class="cuIcon-cameraadd"></text>
								</view>
								<view class="bg-img" @click="ViewImg(thumbs.idcards_url)" v-else>
									<image :src="thumbs.idcards_url" mode="aspectFill"></image>
									<view class="cu-tag bg-red" @tap.stop="DelImg('idcards')">
										<text class='cuIcon-close'></text>
									</view>
								</view>
							</view>
						</view>
					</block>
				</view>
				<view class="flex flex-direction padding-lr margin-top">
					<button @click="doSubmit()" class="cu-btn bg-orange lg round">提交审核</button>
				</view>
			</view>
			<view v-else>
				<view v-if="data.verify.status==1" class="padding-sm light bg-green">
					<text>恭喜您，实名认证已通过！</text>
				</view>
				<view class="padding-sm light bg-yellow flex justify-between" v-else>
					<text class="text-lg text-orange" v-if="data.verify.status==0">认证信息审核中，请耐心等待</text>
					<text class="text-lg text-red" v-else>审核未通过：{{data.verify.checknote}}</text>
				</view>
				<view class="cu-list menu">
					<view class="cu-item" v-if="data.verify.checktime!=''">
						<view class="content">审核时间</view>
						<view class="action">
							<text class="text-grey">{{data.verify.checktime}}</text>
						</view>
					</view>
					<view class="cu-item">
						<view class="content">真实姓名</view>
						<view class="action text-lg text-grey">{{data.verify.name}}</view>
					</view>
					<view class="cu-item">
						<view class="content">身份证号</view>
						<view class="action text-lg text-grey">{{data.verify.idcard_hide}}</view>
					</view>
					<view class="cu-item" v-if="data.verify.idcardz!=''" @click="ViewImg(thumbs.idcardz_url)">
						<view class="content">身份证正面</view>
						<view class="action text-xxl text-blue">
							<text class="cuIcon-picfill"></text>
						</view>
					</view>
					<view class="cu-item" v-if="data.verify.idcardf!=''" @click="ViewImg(thumbs.idcardf_url)">
						<view class="content">身份证反面</view>
						<view class="action text-xxl text-blue">
							<text class="cuIcon-picfill"></text>
						</view>
					</view>
					<view class="cu-item" v-if="data.verify.idcards!=''" @click="ViewImg(thumbs.idcards_url)">
						<view class="content">手持身份证</view>
						<view class="action text-xxl text-blue">
							<text class="cuIcon-picfill"></text>
						</view>
					</view>
					<view class="cu-item">
						<view class="content">提交时间</view>
						<view class="action">
							<text class="text-grey">{{data.verify.addtime}}</text>
						</view>
					</view>
				</view>
				<view @click="mode=0" class="padding text-center" v-if="data.verify.status==0 || data.reverify">
					<text class="text-blue text-lg">{{data.verify.status==0?'编辑资料':'重新认证'}}</text>
				</view>
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
				bgcolor:"orange",
				mode:0,
				postdata:{
					name:"",
					idcard:"",
					idcardz:"",
					idcardf:"",
					idcards:""
				},
				thumbs:{
					idcardz_url:"",
					idcardf_url:"",
					idcards_url:""
				},
				data:{
					title:"实名认证",
					requires:['realname','idcard'],
					reverify:false,
					verify:{
						id:0
					}
				}
			}
		},
		onLoad() {
			this.initData("authentication");
		},
		onShow() {
			
		},
		onPullDownRefresh() {
			this.initData("authentication");
			uni.stopPullDownRefresh();
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					if(res.verify.id>0){
						for(let i in that.postdata){
							that.postdata[i] = res.verify[i];
						}
						for(let i in that.thumbs){
							that.thumbs[i] = res.verify[i];
						}
						let idcardtext = res.verify.idcard + "";
						res.verify.idcard_hide = "************" + idcardtext.slice(14);
						that.mode = 1;
						that.bgcolor = res.verify.status==1 ? 'green' : 'orange';
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
			OcrImg(img){
				let self = this;
				core.get("authentication/ocr",function(res){
					if(res.type=='success'){
						self.postdata.name = res.message.name;
						self.postdata.idcard = res.message.idcard;
					}
					console.log("OCR识别结果：",res);
				},{path:img});
			},
			ChooseImg(id){
				let self = this;
				let Callback = function(res){
					self.postdata[id] = res.path;
					self.thumbs[id+'_url'] = res.url;
					if(id=='idcardz'){
						self.OcrImg(res.path);
					}
				}
				// #ifdef H5
				if(core.inwechat){
					return core.initjwx(function(wx){
						wx.chooseImage({
							count: 1, // 默认9
							sizeType:['original'],
							success: function (ret) {
								wx.uploadImage({
									localId: ret.localIds[0], // 需要上传的图片的本地ID，由chooseImage接口获得
									isShowProgressTips: 1, // 默认为1，显示进度提示
									success: function (result) {
										core.get('attach/wxmedia',function(res){
											if (res.type=='success'){
												Callback(res.message);
											}else {
												core.toast(res.message,res.redirect,res.type);
											}
										},{media_id:result.serverId});
									},fail:function (res) {
										util.toast("上传失败，请重试",'','error');
										console.log(res);
									}
								});
							},fail:function (res) {
								util.toast("操作失败，请重试",'','error');
								console.log(res);
							}
						});
					});
				}
				// #endif
				uni.chooseImage({
					count: 1, //默认9
					sizeType: ['original'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (ret) => {
						core.upload(ret.tempFilePaths[0],function(res){
							Callback(res);
						});
					}
				});
			},
			DelImg(id){
				let self = this;
				core.confirm("删除后不可恢复","删除图片",function(){
					self.postdata[id] = "";
					self.thumbs[id+"_url"] = "";
				});
			},
			ViewImg(src){
				console.log(src);
				uni.previewImage({
					urls:[src],
					current:0
				})
			},
			doSubmit(){
				let data = this.postdata;
				if(data.name=="") return core.toast("请输入您的真实姓名");
				if(data.idcard=="") return core.toast("请输入您的真实姓名");
				if(data.idcardz=="" && this.data.requires.indexOf('idcardz')>=0) return core.toast("请上传身份证正面照");
				if(data.idcardf=="" && this.data.requires.indexOf('idcardf')>=0) return core.toast("请上传身份证反面照");
				if(data.idcards=="" && this.data.requires.indexOf('idcards')>=0) return core.toast("请上传手持身份证照");
				data.submit = 1;
				data.saveauthente = "true";
				let self = this;
				core.post("authentication/post",function(res){
					if(res.type!='success') return core.report(res);
					core.toast(res.message, res.redirect, 'success');
					self.initData("authentication");
				}, data);
			}
		}
	}
</script>

<style>
	.head{width: 100%; min-height: 320upx; overflow: hidden;}
	.mode0{padding-bottom: 60upx;}
	.round-square{background-color: rgba(55,55,55,0.1); padding: 40upx; border-radius: 50%;}
	.round-square .cuIcon-safe{font-size: 72upx;}
	.cu-form-group .title{min-width: calc(4em + 15px);}
	.cu-form-group ~ .cu-bar:not(.no-border){border-top: 0.5px solid #eee;}
</style>
