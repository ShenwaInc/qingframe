<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{titles[action]}}</block>
		</cu-custom>
		<view>
			<view class="padding-xl text-center text-xxl head" @click="ChooseCover">
				<image class="round avatar margin-top-xl" :src="avatar"></image>
				<text class="cuIcon-camera text-orange" v-if="action=='register'"></text>
			</view>
			<view class="padding-xl">
				<form>
					<view class="cu-form-group round">
						<text class="cuIcon-people margin-right-sm"></text>
						<input type="text" @input="typeusername()" :placeholder="'请输入'+data.registermethod+(action=='login'&&data.accountname!=''?'/'+data.accountname:'')" name="username" v-model="postdata.username"></input>
						<block v-if="(action=='register' && regMobile) || postdata.mode=='code'">
							<picker v-if="data.overseaCode" mode="selector" class="noaf region-picker" :range="region.list" :value="237" @change="doRegion">
								<view class="cu-capsule round">
									<view class="cu-tag" :class="'bg-'+theme.actcolor">+{{region.code}}</view>
									<view class="cu-tag" :class="'line-'+theme.actcolor">{{region.text}}</view>
								</view>
							</picker>
						</block>
					</view>
					<view class="margin-top cu-form-group round" v-if="data.verifycode && (action=='register' || postdata.mode=='code')">
						<text class="cuIcon-mail margin-right-sm"></text>
						<input type="text" placeholder="输入收到的验证码" name="verifycode" v-model="postdata.verifycode"></input>
						<button @click="sendcode()" :disabled="verifycodetext=='获取验证码'?false:true" :class="verifycodetext=='获取验证码'?'bg-'+theme.actcolor:''" class="cu-btn shadow round">{{verifycodetext}}</button>
					</view>
					<view class="margin-top cu-form-group round" v-if="action=='register'">
						<text class="cuIcon-profile margin-right-sm"></text>
						<input type="text" placeholder="设置一个好听的昵称" name="nickname" v-model="postdata.nickname"></input>
					</view>
					<view class="margin-top cu-form-group round" v-if="action=='register' || (step==1 && postdata.mode!='code')">
						<text class="cuIcon-lock margin-right-sm"></text>
						<input type="text" :focus="inputfocus=='password'?true:false" :password="!viewpassword" :placeholder="action=='login'?'请输入您的登录密码':'设置一个登录密码'" name="password" @confirm="dologin()" v-model="postdata.password"></input>
						<text @click="viewpassword=!viewpassword" :class="viewpassword?['text-'+theme.actcolor,'cuIcon-attentionfill']:'cuIcon-attentionforbid'"></text>
					</view>
					<view class="margin-top cu-form-group round" v-if="action=='register' && data.invitaCode!=0">
						<text class="cuIcon-qrcode margin-right-sm"></text>
						<input type="text" :placeholder="'请输入邀请码'+(data.invitaCode==2?'':'(选填)')" name="fromuid" v-model="postdata.invitacode"></input>
					</view>
					<view class="margin-top" v-if="action=='login'">
						<view class="margin-top flex flex-direction">
							<button class="cu-btn lg round" @click="dologin()" :class="'bg-'+theme.actcolor">{{btnarr[step]}}</button>
						</view>
						<view class="flex margin-top-xl text-center text-lg padding-lr-xl" :style="'color:'+theme.link" v-if="data.register || data.verifycode">
							<view class="flex-sub" :class="data.register?'solid-right':''" v-if="data.verifycode">
								<view @click="postdata.mode='code';step=1" v-if="postdata.mode!='code'">验证码登录</view>
								<view v-else @click="postdata.mode='password'">密码登录</view>
							</view>
							<view class="flex-sub" v-if="data.register" @click="action='register'">
								<text>立即注册</text>
							</view>
						</view>
					</view>
					<view class="margin-top" v-else>
						<view class="margin-top flex flex-direction">
							<button class="cu-btn lg round" @click="doregister()" :class="'bg-'+theme.actcolor">立即注册</button>
						</view>
						<view class="flex margin-top-xl text-center text-lg text-blue padding-lr-xl">
							<view class="flex-sub" @click="action='login'">
								<text>已有账号？前往登录</text>
							</view>
						</view>
					</view>
				</form>
			</view>
			<view class="text-center padding-top margin-top padding-left padding-right" v-if="quicklogin && data.quicklogin">
				<view class="text-gray text-lg">使用第三方授权登录</view>
				<view class="flex quicklogin">
					<!-- #ifdef MP-WEIXIN -->
					<view class="flex-sub quick-item" v-if="quicklogins.weixin">
						<button class="quick-weixin nobg noaf padding-0" @tap="WxappLogin()">
							<text class="ifont icon-weixin"></text>
						</button>
					</view>
					<!-- #endif -->
					<!-- #ifdef H5 -->
					<view class="flex-sub quick-item" v-if="inwechat" @click="h5login()">
						<view class="quick-weixin">
							<text class="ifont icon-weixin"></text>
						</view>
					</view>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<view class="flex-sub quick-item" v-if="quicklogins.weixin" @click="login('weixin')">
						<view class="quick-weixin">
							<text class="ifont icon-weixin"></text>
						</view>
					</view>
					<!-- #endif -->
					<!-- #ifdef MP-TOUTIAO -->
					<view class="flex-sub quick-item" v-if="quicklogins.toutiao" @click="login('toutiao')">
						<view class="quick-ttapp">
							<text class="ifont icon-douyin"></text>
						</view>
					</view>
					<!-- #endif -->
					<view class="flex-sub quick-item" v-if="quicklogins.qq" @click="login('qq')">
						<view class="quick-qq">
							<text class="ifont icon-qq"></text>
						</view>
					</view>
					<view class="flex-sub quick-item" v-if="quicklogins.sinaweibo" @click="login('sina')">
						<view class="quick-sina">
							<text class="ifont icon-sina"></text>
						</view>
					</view>
				</view>
			</view>
			<view class="padding-xl text-center text-lg" v-if="data.agreement || data.privacy">
				<text @click="doAgree()">
					<text class="text-xl" :class="postdata.isagree?'cuIcon-roundcheckfill text-'+theme.actcolor:'cuIcon-round'"></text>
					<text class="margin-left-xs">已阅读并同意</text>
				</text>
				<text :style="'color:'+theme.link" @click="doViewAgreement(data.privacy,'隐私政策')" v-if="data.privacy!=''">《隐私政策》</text>
				<text v-if="data.agreement && data.privacy">和</text>
				<text :style="'color:'+theme.link" @click="doViewAgreement(data.agreement,'用户注册协议')" v-if="data.agreement!=''">《用户注册协议》</text>
			</view>
		</view>
		 <tf-verify-img @succeed="verifyRes" @close="sliderVerify.show = false" v-if="sliderVerify.show"></tf-verify-img>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import tfVerifyImg from '@/components/tf-verify-img/tf-verify-img.vue';
	import regCode from "@/regCode.js";
	var regmobile = /^(\+)?(86)?0?1\d{10}$/;
	var regemail = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	var regaccount = /^[_a-zA-Z0-9]{6,15}$/;
	var regOrgMobile = /^\d{7,10}$/;
	var codeInterval = null;
	
	export default {
		computed: mapState(['userId', 'hasLogin']),
		components:{tfVerifyImg},
		data() {
			return {
				action:'login',
				fromuid:core.agentid,
				inwechat:core.inwechat,
				isemail:false,
				isMobile:false,
				regMobile:false,
				theme:core.style,
				checkusername:"",
				redirecturl:"home",
				redirectweb:"",
				region:{
					code:"86",
					text:"中国大陆",
					list:regCode
				},
				avatar:core.system.logo,
				dosubmiting:false,
				systemname:core.system.name,
				platform:core.platform,
				viewpassword:false,
				showmodal:"",
				wxappcode:'',
				quicklogin:false,
				quicklogins:{
					"weixin":false,
					"qq":false,
					"sinaweibo":false,
					"toutiao":false
				},
				inputfocus:'',
				titles:{
					"login":"登录",
					"register":"注册"
				},
				step:0,
				sliderVerify:{
					state:0,
					show:false
				},
				btnarr:['下一步','登录'],
				postdata:{
					username:"",
					clientid:"",
					mode:"password",
					password:"",
					isagree:true,
					verifycode:"",
					avatar:"",
					nickname:"",
					invitacode:""
				},
				verifycodetext:'发送验证码',
				verifycodesending:false,
				data:{
					title:"登录",
					register:false,
					verifycode:false,
					overseaCode:false,
					registermethod:"手机号/邮箱",
					regMethods:{email:"邮箱", mobile:"手机号", account:"用户名"},
					agreement:"",
					authkey:"",
					accountname:"",
					invitaCode:0,	//0隐藏，1选填，2必填
					quicklogin:false,
					privacy:""
				}
			}
		},
		onLoad(options) {
			if(typeof(options.act)!='undefined' && options.act!='') this.action = options.act;
			if(typeof(options.rd)!='undefined'){
				this.redirecturl = options.rd;
				if(this.redirecturl.indexOf('%')!=-1){
					this.redirecturl = decodeURIComponent(this.redirecturl);
				}
				//console.log("登录后跳转：", options.rd);
			}
			if(this.hasLogin) core.logout();
			if(core.platform=='h5' && this.inwechat){
				//let loginurl = core.url('','mobile');
				//return window.location.href = loginurl;
				this.quicklogin = true;
			}
			if(this.fromuid>0){
				this.postdata.invitacode = this.fromuid + "";
			}
			// #ifdef APP-PLUS
			if(typeof(plus.push)!='undefined'){
				let pinf = plus.push.getClientInfo();
				if(typeof(pinf.clientid)!='undefined' && pinf.clientid && pinf.clientid!="null"){
					this.postdata.clientid = pinf.clientid;
					console.log('获取客户端编号：'+pinf.clientid);
				}
			}
			// #endif
			// #ifdef H5
			if(typeof(options.rw)!='undefined' && options.rw){
				this.redirectweb = options.rw;
			}
			// #endif
			this.initData('auth');
			// #ifndef H5
			let self = this;
			uni.getProvider({
			    service: 'oauth',
			    success: function (res) {
					if (res.provider.length>0) self.quicklogin = true;
			        for(var p in res.provider){
						self.quicklogins[res.provider[p]] = true;
					}
					// #ifdef MP-WEIXIN
					if(!wx.getUserProfile){
						self.quicklogins.weixin = false;
					}
					// #endif
			    }
			});
			// #endif
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					//console.log(res);
					if(typeof(res.quicklogin)=='undefined'){
						res.quicklogin = true;
					}
					if(typeof(res.privacy)!='undefined'){
						if(res.privacy!='' && res.privacy!=null){
							that.postdata.isagree = false;
						}
					}
					if(typeof(res.agreement)!='undefined'){
						if(res.agreement!='' && res.agreement!=null){
							that.postdata.isagree = false;
						}
					}
					that.data = res;
					that.regMobile = typeof(res.regMethods.mobile)!='undefined';
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
			doAgree(){
				this.postdata.isagree = !this.postdata.isagree;
			},
			doViewAgreement(url,title='用户注册协议'){
				return core.navito('web/index',{title:title,url:encodeURIComponent(url)});
			},
			doRegion(e){
				let regions = this.region.list[e.detail.value].split('-');
				this.region.code = regions[1];
				this.region.text = regions[0];
				if(this.postdata.username!=''){
					this.typeusername();
				}
			},
			doback(){
				return core.back();
			},
			// #ifdef H5
			h5login(){
				let loginurl = core.url('auth/wxlogin','mobile');
				if(this.redirectweb!=''){
					loginurl += '&rw=' + this.redirectweb;
				}
				if(this.fromuid>0){
					loginurl += '&fromuid=' + this.fromuid;
				}
				window.location.href = loginurl;
			},
			// #endif
			ChooseCover(){
				if(this.action!='register') return false;
				var that = this;
				uni.chooseImage({
					count: 1, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					crop:{
						quality:100,
						width:200,
						height:200
					},
					success: (res) => {
						core.upload(res.tempFilePaths[0],function(res){
							that.avatar = res.url;
							that.postdata.avatar = res.path;
						}, 'image', 'attach/upload', {resfrom:"profiles"});
					}
				});
			},
			// #ifdef MP-WEIXIN
			WxappLogin(){
				if(!this.postdata.isagree) return core.toast('请先同意用户注册协议');
				if(this.dosubmiting) return false;
				let self = this;
				wx.getUserProfile({
					desc:'用于完善会员资料',
					success(infoRes) {
						uni.login({
							provider: 'weixin',
							success: (res) => {
								let postdata = {provider:'wxapp',userinfo:infoRes.userInfo}
								postdata.code = res.code;
								if(self.fromuid>0) postdata.fromuid = self.fromuid;
								postdata.appsecret = core.apitoken;
								postdata.clientid = self.postdata.clientid;
								return core.post('auth/quicklogin',function(ret){
									if(ret.type!='success') return core.report(ret);
									core.login(ret.member);
									core.toast(ret.message, self.redirecturl ,'success');
								},postdata);
							}
						});
					},
					fail(e) {
						console.log(e);
					}
				});
			},
			// #endif
			verifyRes(){
				this.sliderVerify.state = 1;
				this.sliderVerify.show = false;
				this.sendcode();
			},
			sendcode(){
				if(this.verifycodetext!='获取验证码' || this.verifycodesending) return false;
				if(!this.postdata.isagree) return core.toast('请先同意用户注册协议');
				if(this.sliderVerify.state==0){
					return this.sliderVerify.show = true;
				}
				let username = this.postdata.username;
				if(username==''){
					return core.toast('请输入您的'+this.data.registermethod);
				}
				if (!username.match(regmobile) && !username.match(regemail) && this.region.code=="86") {
					return core.toast(this.data.registermethod+'格式不正确');
				}
				this.verifycodesending = true;
				var that = this;
				return core.get("auth/code",function(res){
					that.verifycodesending = false;
					if(res.type!='success') return core.report(res);
					core.toast(res.message,'','success');
					that.verifycodetext = '120s';
					var n = 120;
					codeInterval = setInterval(function(){
						if(n==0){
							clearInterval(codeInterval);
							that.verifycodetext = '获取验证码';
							return;
						}else{
							that.verifycodetext = n+'s';
						}
						n--
					},1e3);
				},{fromreg:this.action=='register'?1:0,mobile:username,sendcode:"true",authkey:this.data.authkey,regcode:this.region.code});
			},
			typeusername(){
				let username = this.postdata.username;
				this.isemail = username.match(regemail);
				this.isMobile = username.match(regmobile) || (this.region.code!="86" && username.match(regOrgMobile));
				if (this.isMobile || this.isemail){
					this.verifycodetext = '获取验证码';
				}else{
					this.verifycodetext = '发送验证码';
				}
				if(this.action!='login') return false;
				if (this.isMobile || username.match(regaccount) || this.isemail){
					this.checkusername = username;
					let forceReg = false;
					if (this.isMobile && this.regMobile){
						forceReg = true;
					}
					this.doAvatar(username, forceReg);
				}else if(this.step==1){
					this.step=0;
					this.avatar = core.system.logo;
				}
			},
			login(provider){
				if(!this.postdata.isagree) return core.toast('请先同意用户注册协议');
				if(this.dosubmiting) return false;
				if(!this.quicklogins[provider]) return core.toast('暂不支持该登录方式');
				this.dosubmiting = true;
				var that = this;
				uni.login({
					provider: provider,
					success: (res) => {
						let useroptions = {
							provider: provider,
							success: (infoRes) => {
								let postdata = {provider:provider,userinfo:infoRes.userInfo}
								// #ifdef MP-TOUTIAO
								postdata.code = res.code;
								// #endif
								if(that.fromuid>0) postdata.fromuid = that.fromuid;
								postdata.appsecret = core.apitoken;
								postdata.clientid = that.postdata.clientid;
								return core.post('auth/quicklogin',function(res){
									if(res.type!='success') return core.report(res);
									core.login(res.member);
									core.toast(res.message, that.redirecturl ,'success');
								},postdata);
							},
							fail(e) {
								console.error(e);
								return core.toast('登陆失败，请重试');
							},
							complete:function(e){
								that.dosubmiting = false;
							}
						}
						// #ifdef MP-TOUTIAO
						useroptions.withCredentials = true;
						// #endif
						uni.getUserInfo(useroptions);
					},
					fail: (err) => {
						core.toast('授权失败');
						that.dosubmiting = false;
						console.error(err);
					}
				});
			},
			doAvatar(username, forceReg=false){
				let self = this;
				core.get("auth/getavatar",function(res){
					if(res.type=='success'){
						self.step = 1;
						self.avatar = res.avatar;
						if(self.postdata.mode!='code') self.inputfocus = 'password';
					}else{
						if(!username.match(regmobile) && !username.match(regemail)) return false;
						self.step = 0;
						self.avatar = core.system.logo;
						if(forceReg){
							self.action='register';
						}
					}
				},{username:username,authkey:this.data.authkey,getavatar:true,regcode:this.region.code})
			},
			dologin(){
				if(!this.postdata.isagree) return core.toast('请先同意用户注册协议');
				if(this.dosubmiting) return false;
				if(this.postdata.username=='') return core.toast('请输入您的用户名');
				let username = this.postdata.username;
				if (!username.match(regmobile) && !username.match(regemail) && !username.match(regaccount)){
					return core.toast('用户名格式不正确');
				}
				if(this.step==0){
					if(this.checkusername!=username){
						return this.doAvatar(username, true);
					}
					return this.action = 'register';
				}
				let data = {username:username,mode:this.postdata.mode,dologin:1,authkey:this.data.authkey,clientid:this.postdata.clientid,fromuid:this.fromuid}
				if(this.postdata.mode=='code'){
					if(this.postdata.verifycode=='') return core.toast('验证码不能为空');
					data.verifycode = this.postdata.verifycode;
				}else{
					if(this.postdata.password=='') return core.toast('登录密码不能为空');
					data.password = this.postdata.password;
				}
				data.regcode = this.region.code;
				this.dosubmiting = true;
				let that = this;
				return core.post('auth/login',function(res){
					that.dosubmiting = false;
					if(res.type!='success') return core.report(res);
					core.login(res.member);
					// #ifdef H5
					if(that.redirectweb!=''){
						let loginurl = that.redirectweb;
						if(loginurl.indexOf('%')>=0){
							loginurl = decodeURIComponent(loginurl);
						}
						core.toast(res.message, '' ,'success');
						return window.location.href = loginurl;
					}
					// #endif
					let redirecturl = res.redirect=='home' ? that.redirecturl : res.redirect;
					core.toast(res.message, redirecturl ,'success');
				},data);
			},
			doregister(){
				if(!this.postdata.isagree) return core.toast('请先同意用户注册协议');
				if(this.dosubmiting) return false;
				let username = this.postdata.username;
				if(username=='') return core.toast('请输入您的用户名');
				if (!username.match(regmobile) && !username.match(regemail)){
					let isRegAccount = typeof(res.regMethods.account)!='undefined' && username.match(regaccount);
					let isRegOrgMobile = username.match(regOrgMobile) && this.region.code!="86"
					if(!isRegAccount && !isRegOrgMobile){
						return core.toast('用户名格式不正确');
					}
				}
				if(this.postdata.password=='') return core.toast('登录密码不能为空');
				if(this.postdata.verifycode=='' && this.data.verifycode) return core.toast('验证码不能为空');
				if(this.postdata.nickname=='') return core.toast('请设置一个昵称');
				if(this.data.invitaCode>0){
					let invitaCode = this.postdata.invitacode=='' ? 0 : parseInt(this.postdata.invitacode);
					if(invitaCode==0 && this.data.invitaCode==2){
						return core.toast('请输入邀请码');
					}
					if(this.fromuid==0 || invitaCode>0){
						this.fromuid = invitaCode;
					}
				}
				this.dosubmiting = true;
				let data = {username:username,doregister:1,authkey:this.data.authkey,fromuid:this.fromuid}
				data.verifycode = this.postdata.verifycode;
				data.password = this.postdata.password;
				data.nickname = this.postdata.nickname;
				data.avatar = this.postdata.avatar;
				data.clientid = this.postdata.clientid;
				data.regcode = this.region.code;
				let that = this;
				return core.post('auth/register',function(res){
					that.dosubmiting = false;
					if(res.type!='success') return core.report(res);
					core.login(res.member);
					// #ifdef H5
					if(that.redirectweb!=''){
						let loginurl = that.redirectweb;
						if(loginurl.indexOf('%')>=0){
							loginurl = decodeURIComponent(loginurl);
						}
						core.toast(res.message, '' ,'success');
						return window.location.href = loginurl;
					}
					// #endif
					let redirecturl = res.redirect=='home' ? that.redirecturl : res.redirect;
					core.toast(res.message, redirecturl ,'success');
				},data);
			}
		}
	}
</script>

<style>	
	.ifont{width: 100upx; height: 100upx; font-size: 100upx; line-height: 100upx;}
	.avatar{height: 180upx; width: 180upx; border: 10upx solid #FFFFFF;}
	.head{position: relative;}
	.head .cuIcon-camera{position: absolute; top: 230upx; left: 50%; margin-left: 56upx;}
	.agreement .cu-bar .content{width: calc(100% - 200upx);}
	.agreement-text{max-height: 40vh; font-size: 32upx; line-height: 50upx; text-indent: 64upx; text-align: left;}
	.quicklogin{padding-left: 100upx; padding-right: 100upx;}
	.quick-item{padding-top: 30upx;}
	.quick-ttapp{background-color: #170B1A; width: 100upx; height: 100upx; border-radius: 50%; color: #FFFFFF; margin: 0 auto; text-align: center;}
	.quick-ttapp .ifont{font-size: 60upx; line-height: 100upx;}
</style>
