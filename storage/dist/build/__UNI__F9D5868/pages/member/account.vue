<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="cu-list menu">
				<view class="cu-item arrow" @click="ChooseAvatar()">
					<view class="content">
						<text>头像</text>
					</view>
					<view class="action">
						<view class="cu-avatar-group">
							<view class="cu-avatar radius lg" :style="'background-image:url('+data.userinfo.avatar+')'"></view>
						</view>
					</view>
				</view>
				<view class="cu-item arrow" @click="EditInfo('nickname','昵称')">
					<view class="content">
						<text>昵称</text>
					</view>
					<view class="action">
						<text class="text-grey">{{data.userinfo.nickname}}</text>
					</view>
				</view>
				<view class="cu-item" @click="EditUserid()" :class="data.userid=='ID'?'':'arrow'" v-if="data.userid!=''">
					<view class="content">
						<text>{{data.userid}}</text>
					</view>
					<view class="action">
						<text :class="data.cutenumber?'text-yellow':'text-grey'">{{data.userid=='ID'?data.userinfo.uid:data.userinfo.userid}}</text>
					</view>
				</view>
				<block v-if="data.qrcode!=''">
					<view class="cu-item arrow" @click="showqrcode=!showqrcode" :class="showqrcode?'arrow-up':''">
						<view class="content">
							<text>二维码名片</text>
						</view>
						<view class="action text-xl">
							<text class="cuIcon-qr_code"></text>
						</view>
					</view>
					<view class="padding-xs text-center bg-white account-qrcode" @click="previewimg(data.qrcode)" :class="showqrcode?'':'hidden'">
						<image mode="widthFix" :src="data.qrcode" class="png qrcode"></image>
					</view>
				</block>
				<view class="cu-item arrow" v-if="data.creditswitch" @click="naviTo('member/vip')">
					<view class="content">
						<text>用户等级</text>
					</view>
					<view class="action">
						<view class="cu-tag round bg-orange light">{{data.userinfo.group.title}}</view>
					</view>
				</view>
				<view class="cu-item" v-else>
					<view class="content">
						<text>用户等级</text>
					</view>
					<view class="action">
						<view class="cu-tag round bg-orange light">{{data.userinfo.group.title}}</view>
					</view>
				</view>
			</view>
			<view class="cu-list menu" v-if="!auditmode">
				<view class="cu-item arrow" @click="EditMobile()">
					<view class="content">
						<text>手机号</text>
					</view>
					<view class="action">
						<text class="text-grey">{{data.userinfo.mobile || '未绑定'}}</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="EditInfo('email','邮箱号')">
					<view class="content">
						<text>邮箱号</text>
					</view>
					<view class="action">
						<text class="text-grey">{{data.userinfo.email || '未绑定'}}</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="EditInfo('msn','微信号')">
					<view class="content">
						<text>微信号</text>
					</view>
					<view class="action">
						<text class="text-grey">{{data.userinfo.msn}}</text>
					</view>
				</view>
			</view>
			<view class="cu-list menu">
				<view class="cu-item arrow" @click="EditInfo('bio','个性签名')">
					<view class="content">
						<text>个性签名</text>
					</view>
					<view class="action" style="max-width: 70%;">
						<text class="text-grey">{{data.userinfo.bio}}</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="showmodal='citypicker'">
					<view class="content">
						<text class="text-black">地区</text>
					</view>
					<view class="action">
						<view class="cu-tag round light" :class="'bg-'+theme.actcolor">{{data.userinfo.resideprovince}}</view>
						<view class="cu-tag round light" :class="'bg-'+theme.actcolor" v-if="data.userinfo.residecity!=''">{{data.userinfo.residecity}}</view>
					</view>
				</view>
			</view>
			<picker :value="data.userinfo.gender" :range="genders" @change="doPickGender">
				<view class="cu-list menu solid-top">
					<view class="cu-item arrow">
						<view class="content">
							<text class="text-black">性别</text>
						</view>
						<view class="action">
							<view class="cu-tag round light" :class="'bg-'+theme.actcolor">{{genders[data.userinfo.gender]}}</view>
						</view>
					</view>
				</view>
			</picker>
			<view class="cu-list menu solid-top" v-if="data.profiles.length>0">
				<view class="cu-item" :class="item.url==''?'':'arrow'" @click="naviTo(item.url)" v-for="(item, index) in data.profiles" :key="index">
					<view class="content">
						<text>{{item.title}}</text>
					</view>
					<view class="action">
						<text :class="item.extra=='text-grey'?'':item.extra">{{item.value||''}}</text>
					</view>
				</view>
			</view>
			<citypicker :ShowPicker="showmodal=='citypicker'" @onResult="doChangeCity" @onCancel="showmodal=''" :city="postdata.residecity" :province="postdata.resideprovince"></citypicker>
		</view>
		<!-- #ifdef H5 || MP-WEIXIN -->
		<ImageCrop :cropImage="scropImage" resFrom="profiles" :cropTempFile="scropTempFile" :isSquare="scropSquare" @cropRes="CropCover"></ImageCrop>
		<!-- #endif -->
		<view class="cu-modal" :class="showmodal=='defaulteditor'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">修改{{editor.title}}</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view>
					<view class="cu-form-group text-left">
						<input :placeholder="'输入您的'+editor.title" v-model="postdata[editor.colunm]" name="input"></input>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-gray" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @click="UpdateInfo(editor.colunm,postdata[editor.colunm])">确定</button>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	import store from '@/store/index.js'
	import swaload from "@/components/util/swaload.vue"
	import citypicker from "@/components/util/citypicker.vue"
	import ImageCrop from "@/components/util/ImageCrop.vue"
	
	export default {
		components: {swaload, citypicker, ImageCrop},
		data() {
			return {
				postdata:{
					avatar:"",
					nickname:"",
					userid:"",
					resideprovince:"未知",
					residecity:"未知"
				},
				scropImage:"",
				scropWidth:192,
				scropSquare:false,
				scropTempFile:"",
				genders:['保密','男','女'],
				showqrcode:false,
				loaded:false,
				theme:core.style,
				auditmode:true,
				editor:{
					title:"",
					colunm:""
				},
				showmodal:'',
				data:{
					title:"个人信息",
					userinfo:{
						uid:0,
						nickname:"",
						userid:"",
						avatar:"",
						group:{
							title:""
						},
						mobile:"",
						gender:0,
						email:"",
						msn:"",
						resideprovince:"",
						residecity:""
					},
					qrcode:"",
					userid:"",
					cutenumber:'',
					profiles:[],
					verifycode:true,
					creditswitch:false
				}
			}
		},
		onLoad(options) {
			// #ifdef MP
			if(typeof(core.system.auditmode)!='undefined'){
				this.auditmode = core.system.auditmode.status;
			}else{
				this.auditmode = false;
			}
			// #endif
			// #ifndef MP
			this.auditmode = false;
			// #endif
			if(typeof(options.showcode)!='undefined'){
				this.showqrcode = true;
			}
		},
		onShow() {
			let that = this;
			this.initData('member/account',{},function(res){
				if(res.userinfo.resideprovince!=""){
					that.postdata.resideprovince = res.userinfo.resideprovince;
					if(res.userinfo.residecity!=""){
						that.postdata.residecity = res.userinfo.residecity;
					}
				}else if(res.userinfo.residecity==""){
					that.data.userinfo.resideprovince = '未知';
				}
				that.loaded = true;
			});
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
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
			EditUserid(){
				if(this.data.userid=='' || this.data.userid=='ID') return false;
				if(this.data.cutenumber!=''){
					return this.naviTo(this.data.cutenumber);
				}
				this.EditInfo('userid',this.data.userid);
			},
			EditMobile(){
				if(this.data.verifycode) return this.naviTo('bind/mobile');
				this.EditInfo('mobile','手机号');
			},
			doPickGender(e){
				let setvalue = parseInt(e.detail.value);
				this.UpdateInfo('gender',setvalue);
			},
			UpdateInfo(colunm='',value=''){
				if(value==='') return false;
				let that = this;
				return core.post("member/account",function(res){
					if(res.type=='success'){
						that.showmodal = '';
						if(colunm!='avatar'){
							that.data.userinfo[colunm] = value;
							that.showmodal = '';
						}
						if(colunm=='nickname'){
							core.userinfo.nickname = value;
							core.cacheclear();
							store.commit('login',core.userinfo);
						}
						if(colunm=='avatar'){
							core.userinfo.avatar = that.data.userinfo.avatar;
							core.cacheclear();
						}
					}
					core.toast(res.message,res.redirect,res.type);
				},{setkey:colunm,setvalue:value,savespset:"true"});
			},
			EditInfo(colunm='',title=''){
				this.editor = {
					title:title,
					colunm:colunm
				};
				this.postdata[colunm] = this.data.userinfo[colunm];
				this.showmodal = 'defaulteditor';
			},
			doChangeCity(res){
				this.UpdateInfo('resideprovince',res.province);
				this.UpdateInfo('residecity',res.city);
			},
			ChooseAvatar(){
				let that = this;
				let options = {
					count: 1, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					success: (res) => {
						// #ifdef H5 || MP-WEIXIN
						that.scropTempFile = res.tempFilePaths[0];
						that.scropImage = res.tempFiles[0].path;
						that.scropSquare = false;
						that.showmodal = 'ImageCroper';
						return uni.getImageInfo({
							src:res.tempFiles[0].path,
							success(img) {
								let size = Math.abs(img.width-img.height);
								if(size<10){
									that.scropSquare = true;
								}
							}
						});
						// #endif
						core.upload(res.tempFilePaths[0],function(res){
							that.data.userinfo.avatar = res.url;
							that.UpdateInfo('avatar',res.path);
						}, 'image', 'attach/upload', {resfrom:"profiles"});
					}
				};
				// #ifdef APP-PLUS
				options.crop = {
					quality:100,
					width:this.scropWidth,
					height:this.scropWidth
				};
				// #endif
				uni.chooseImage(options);
			},
			CropCover(res){
				this.data.userinfo.avatar = res.url;
				this.UpdateInfo('avatar',res.path);
			},
			previewimg(image){
				return uni.previewImage({
					urls:[image]
				})
			}
		}
	}
</script>

<style>
	.cu-list.menu>.cu-item.arrow.arrow-up:before{
		content: "\e6de";
	}
	.account-qrcode{position: relative;}
	.account-qrcode .qrcode{width: 480upx;}
	
	.crop-container{height: 100vh; width: 100vw;}
</style>
