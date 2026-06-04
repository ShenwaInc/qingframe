<template>
	<view :class="'page-text-'+textSize">
		<cu-custom :isBack="true" :Barborder="'solid-bottom'">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="cu-bar">
			<view class="action text-grey">
				<text v-if="!auditmode">聊天与</text>通知
			</view>
		</view>
		<view class="cu-list menu">
			<view class="cu-item">
				<view class="content">
					<text class="text-black">新消息声音提醒</text>
				</view>
				<view class="action">
					<switch class="switch" @change="SwitchSet" data-set="remind_voice" :class="[data.userset.remind_voice==1?'checked':'',theme.actcolor]" :checked="data.userset.remind_voice==1?true:false"></switch>
				</view>
			</view>
			<view class="cu-item">
				<view class="content">
					<text class="text-black">新消息震动</text>
				</view>
				<view class="action">
					<switch class="switch" @change="SwitchSet" data-set="remind_vibrate" :class="[data.userset.remind_vibrate==1?'checked':'',theme.actcolor]" :checked="data.userset.remind_vibrate==1?true:false"></switch>
				</view>
			</view>
			<view class="cu-item">
				<view class="content">
					<text class="text-black">回车键发送</text>
				</view>
				<view class="action">
					<switch class="switch" @change="SwitchSet" data-set="post_center" :class="[data.userset.post_center==1?'checked':'',theme.actcolor]" :checked="data.userset.post_center==1?true:false"></switch>
				</view>
			</view>
		</view>
		<block v-if="!auditmode">
			<view class="cu-bar margin-top-sm">
				<view class="action text-grey">
					权限与会话
				</view>
			</view>
			<view class="cu-list menu">
				<view class="cu-item">
					<view class="content">
						<text class="text-black">接收私聊消息</text>
					</view>
					<view class="action">
						<switch class="switch" @change="SwitchSet" data-set="talk_private" :class="[data.userset.talk_private==1?'checked':'',theme.actcolor]" :checked="data.userset.talk_private==1?true:false"></switch>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						<text class="text-black">被添加需要验证</text>
					</view>
					<view class="action">
						<switch class="switch" @change="SwitchSet" data-set="validation_apply" :class="[data.userset.validation_apply==1?'checked':'',theme.actcolor]" :checked="data.userset.validation_apply==1?true:false"></switch>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						<text class="text-black">允许搜索到我</text>
					</view>
					<view class="action">
						<switch class="switch" @change="SwitchSet" data-set="allow_search" :class="[data.userset.allow_search==1?'checked':'',theme.actcolor]" :checked="data.userset.allow_search==1?true:false"></switch>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						<text class="text-black">开放朋友圈</text>
					</view>
					<view class="action">
						<switch class="switch" @change="SwitchSet" data-set="album_open" :class="[data.userset.album_open==1?'checked':'',theme.actcolor]" :checked="data.userset.album_open==1?true:false"></switch>
					</view>
				</view>
			</view>
		</block>
		<view class="cu-bar margin-top-sm">
			<view class="action text-grey">
				账户与绑定
			</view>
		</view>
		<view class="cu-list menu">
			<view class="cu-item arrow" @click="naviTo('bind/mobile')">
				<view class="content">
					<text class="text-black">手机号</text>
				</view>
				<view class="action">
					<view class="cu-tag round light" :class="data.account.mobile==''?'bg-orange':'bg-'+theme.actcolor">{{data.account.mobile==''?'未绑定':data.account.mobile}}</view>
				</view>
			</view>
			<block v-if="data.quicklogin">
				<!--  #ifndef MP-TOUTIAO -->
				<view class="cu-item arrow" @click="naviTo('bind/account')">
					<view class="content">
						<text class="text-black">微信</text>
					</view>
					<view class="action">
						<view class="cu-tag round light" :class="data.account.weixin==''?'bg-orange':'bg-'+theme.actcolor">{{data.account.weixin==''?'未绑定':data.account.weixin}}</view>
					</view>
				</view>
				<!--  #endif -->
				<view class="cu-item arrow" v-if="!auditmode && (data.account.qq||platform=='app')" @click="unSupport()">
					<view class="content">
						<text class="text-black">QQ</text>
					</view>
					<view class="action">
						<view class="cu-tag round light" :class="data.account.qq==''?'bg-orange':'bg-'+theme.actcolor">{{data.account.qq==''?'未绑定':data.account.qq}}</view>
					</view>
				</view>
			</block>
		</view>
		<block v-if="!auditmode">
			<view class="cu-bar margin-top-sm">
				<view class="action text-grey">
					主题风格
				</view>
			</view>
			<picker :value="langkeys.indexOf(data.userset.language)" :range="languages" @change="doPickLang">
				<view class="cu-list menu hidden">
					<view class="cu-item arrow">
						<view class="content">
							<text class="text-black">选择语言</text>
						</view>
						<view class="action">
							<view class="cu-tag round light" :class="'bg-'+theme.actcolor">{{data.langs[data.userset.language]}}</view>
						</view>
					</view>
				</view>
			</picker>
			<view class="cu-list menu solid-top">
				<view class="cu-item arrow" @click="showTheme()">
					<view class="content">
						<text class="text-black">界面风格</text>
					</view>
					<view class="action">
						<view class="cu-tag round light" :class="'bg-'+theme.actcolor">{{ColorList[theme.actcolor]}}主题</view>
						<view class="cu-tag round" :class="theme.navbg">{{BgColorList[theme.navbg]}}导航</view>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						<text class="text-black">字体大小</text>
					</view>
					<view class="action text-size">
						<button class="cu-tag radius" @click="doTextSize('sm')" :class="[textSize=='sm'?'bg-'+theme.actcolor:'']"><text>默认</text></button>
						<button class="cu-tag radius" @click="doTextSize('lg')" :class="[textSize=='lg'?'bg-'+theme.actcolor:'']"><text class="text-lg">中</text></button>
						<button class="cu-tag radius" @click="doTextSize('xl')" :class="[textSize=='xl'?'bg-'+theme.actcolor:'']"><text class="text-xl">大</text></button>
					</view>
				</view>
			</view>
		</block>
		<view class="cu-list menu margin-top">
			<view class="cu-item arrow" @click="naviTo('member/account')">
				<view class="content">
					<text class="text-black">修改个人资料</text>
				</view>
			</view>
			<view class="cu-item arrow" @click="showmodal='passport'">
				<view class="content">
					<text class="text-black">修改登录密码</text>
				</view>
			</view>
			<view class="cu-item arrow" @click="doClearCache()">
				<view class="content">
					<text class="text-black">清理数据缓存</text>
				</view>
			</view>
			<view class="cu-item arrow" @click="doCancellation()" v-if="data.cancellation">
				<view class="content">
					<text class="text-black">注销当前账户</text>
				</view>
			</view>
			<view class="cu-item arrow hidden" @click="unSupport()">
				<view class="content">
					<text class="text-black">设置手势密码</text>
				</view>
			</view>
		</view>
		<view class="cu-list menu text-center margin-bottom-xl">
			<view class="cu-item arrow" @click="doCheckout()">
				<view class="content">
					<text class="text-red">退出当前账号</text>
				</view>
			</view>
			<view class="cu-item arrow" @click="doQuitApp()" v-if="platform=='app' || inplusapp">
				<view class="content">
					<text class="text-gray">关闭{{sitename}}</text>
				</view>
			</view>
			<navigator class="cu-item arrow" target="miniProgram" open-type="exit" v-else-if="platform=='wxapp'">
				<view class="content">
					<text class="text-gray">关闭{{sitename}}</text>
				</view>
			</navigator>
			<view class="cu-item arrow" @click="doQuitH5()" v-if="inwechat && platform=='h5'">
				<view class="content">
					<text class="text-gray">关闭{{sitename}}</text>
				</view>
			</view>
		</view>
		<view class="cu-modal" :class="showmodal=='cancellation'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">注销账户</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding-tb text-left bg-white">
					<view class="padding-sm">
						<text>我们向您的{{cancellation.sendtype=='mobile'?'手机号':'邮箱'}}</text>
						<text class="margin-lr-xs" :class="'text-'+theme.actcolor">{{cancellation.account}}</text>
						<text>发送了一条验证码信息，请将验证码填写到下方确认本次操作</text>
					</view>
					<view class="cu-form-group">
						<view class="solid-bottom padding-bottom-sm">
							<input type="number" v-model="cancellation.verifycode" placeholder="请输入您收到的验证码" name="input"></input>
						</view>
						<button class="cu-btn shadow" :disabled="verifycodetext=='重新获取'?false:true" @click="doCancellation(true)" :class="'bg-'+theme.actcolor">{{verifycodetext}}</button>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn bg-red" @tap="confirmCancellation(cancellation.verifycode)">确定</button>
						<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @tap="showmodal=''">取消</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal" :class="showmodal=='passport'?'show':''">
			<view class="cu-modal-bg" @click="showmodal=''"></view>
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">{{data.repassport?'重置登录密码':'设置登录密码'}}</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding bg-white text-left">
					<form>
						<view class="cu-form-group" v-if="data.repassport && postdata.verifytype=='pass'">
							<view class="title">原密码</view>
							<input placeholder="请输入登录密码" v-model="postdata.oldpassword" type="text" :password="viewpassword=='oldpassword'?false:true" name="oldpassword"></input>
							<text @click="viewpassword=(viewpassword=='oldpassword'?'':'oldpassword')" :class="viewpassword=='oldpassword'?'cuIcon-attentionfill text-'+theme.actcolor:'cuIcon-attentionforbid text-gray'"></text>
						</view>
						<block v-if="postdata.verifytype=='code'">
							<view class="padding-sm">
								<text>我们向您的{{verifycodetype=='mobile'?'手机号':'邮箱'}}</text>
								<text class="margin-lr-xs" :class="'text-'+theme.actcolor">{{postdata.verifyaccount}}</text>
								<text>发送了一条验证码信息，请将验证码填写到下方确认本次操作</text>
							</view>
							<view class="cu-form-group">
								<view class="title">验证码</view>
								<input type="text" v-model="postdata.verifycode" placeholder="输入您收到的验证码" name="verifycode"></input>
								<button class="cu-btn shadow" :disabled="verifycodetext=='重新获取'?false:true" @click="doPassSendcode()" :class="'bg-'+theme.actcolor">{{verifycodetext}}</button>
							</view>
						</block>
						<view class="cu-form-group">
							<view class="title">新密码</view>
							<input placeholder="请设置一个登录密码" v-model="postdata.newpassword" type="text" :password="viewpassword=='newpassword'?false:true" name="newpassword"></input>
							<text @click="viewpassword=(viewpassword=='newpassword'?'':'newpassword')" :class="viewpassword=='newpassword'?'cuIcon-attentionfill text-'+theme.actcolor:'cuIcon-attentionforbid text-gray'"></text>
						</view>
						<view class="cu-form-group">
							<view class="title">确认密码</view>
							<input placeholder="请再次输入新密码" v-model="postdata.confirmpassword" type="text" :password="viewpassword=='confirmpassword'?false:true" name="confirmpassword"></input>
							<text @click="viewpassword=(viewpassword=='confirmpassword'?'':'confirmpassword')" :class="viewpassword=='confirmpassword'?'cuIcon-attentionfill text-'+theme.actcolor:'cuIcon-attentionforbid text-gray'"></text>
						</view>
						<view class="padding-tb-sm text-right" @click="doPassSendcode()" v-if="data.repassport && postdata.verifytype=='pass'">
							<text class="text-blue">忘记密码？</text>
						</view>
					</form>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn" :class="'line-'+theme.actcolor" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @tap="doSavePassword()">确定</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal" :class="showmodal=='showthemes'?'show':''">
			<view class="cu-modal-bg" @click="showmodal=''"></view>
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">更换主题</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="bg-white">
					<picker :value="themepicker.actcolor" :range="themepicker.colornames" @change="ChooseActcolor">
						<view class="cu-list menu text-left">
							<view class="cu-item arrow">
								<view class="content">
									<text class="text-black">整体色调</text>
								</view>
								<view class="action">
									<view class="cu-tag round light" :class="'bg-'+themepicker.colors[themepicker.actcolor]">{{themepicker.colornames[themepicker.actcolor]}}</view>
								</view>
							</view>
						</view>
					</picker>
					<picker :value="themepicker.navbg" :range="themepicker.bgcolornames" @change="ChooseNavbg">
						<view class="cu-list menu text-left">
							<view class="cu-item arrow">
								<view class="content">
									<text class="text-black">导航栏背景</text>
								</view>
								<view class="action">
									<view class="cu-tag round" :class="themepicker.bgcolors[themepicker.navbg]">{{themepicker.bgcolornames[themepicker.navbg]}}</view>
								</view>
							</view>
						</view>
					</picker>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-gray text-gray" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @tap="doSaveTheme()">确定</button>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import store from '@/store/index.js'
	import core from "@/core.js"
	var codeInterval = null;
	
	export default {
		computed: mapState(['textSize']),
		data() {
			return {
				auditmode:true,
				platform:core.platform,
				sitename:core.system.name,
				theme:core.style,
				cancellation:{
					verify:true,
					verifycode:'',
					account:'',
					sendtype:'mobile'
				},
				ColorList:{
					"red":"红色",
					"orange":"橘橙",
					"yellow":"明黄",
					"olive":"橄榄",
					"green":"绿色",
					"cyan":"天青",
					"blue":"海蓝",
					"purple":"紫色",
					"mauve":"姹紫",
					"pink":"粉桃",
					"brown":"棕褐",
					"black":"墨黑",
					"dazzledark":"酷黑",
					"limegreen":"柠檬绿"
				},
				BgColorList:{
					"bg-red":"嫣红",
					"bg-orange":"橘橙",
					"bg-yellow":"明黄",
					"bg-olive":"橄榄",
					"bg-green":"绿色",
					"bg-cyan":"天青",
					"bg-blue":"海蓝",
					"bg-purple":"紫色",
					"bg-mauve":"姹紫",
					"bg-pink":"粉桃",
					"bg-brown":"棕褐",
					"bg-grey":"玄灰",
					"bg-gray":"草灰",
					"bg-black":"墨黑",
					"bg-dazzledark":"酷黑",
					"bg-white":"雅白",
					"bg-limegreen":"柠檬绿",
					"bg-gradual-red":"渐变魅红",
					"bg-gradual-orange":"渐变鎏金",
					"bg-gradual-green":"渐变翠柳",
					"bg-gradual-blue":"渐变靛青",
					"bg-gradual-purple":"渐变惑紫",
					"bg-gradual-pink":"渐变霞彩"
				},
				themepicker:{
					inited:false,
					colornames:[],
					colors:['red','orange','yellow','olive','green','cyan','blue','purple','mauve','pink','brown','black', 'dazzledark','limegreen'],
					bgcolornames:[],
					bgcolors:[],
					actcolor:-1,
					navbg:-1
				},
				verifycodetext:'重新获取',
				verifycodetype:'mobile',
				showmodal:"",
				inplusapp:false,
				inwechat:core.inwechat,
				langkeys:['zh'],
				languages:['简体中文'],
				postdata:{
					savepassword:"true",
					oldpassword:"",
					newpassword:"",
					confirmpassword:"",
					verifycode:"",
					verifytype:"pass",
					verifyaccount:""
				},
				viewpassword:"",
				data:{
					langs:[],
					repassport:false,
					title:"设置",
					account:{
						mobile:"",
						weixin:"",
						qq:""
					},
					cancellation:false,
					quicklogin:false,
					userset:{
						'remind_voice':1,
						'remind_vibrate':1,
						'post_center':1,
						'talk_private':1,
						'validation_apply':0,
						'allow_search':1,
						'album_open':1,
						'language':'zh'
					}
				}
			}
		},
		onLoad() {
			let self = this;
			if(typeof(inWhotalkPlus)!='undefined') this.inplusapp = true;
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
			this.initData('member/setting',{},function(res){
				self.langkeys = [];
				self.languages = [];
				for(let i in res.langs){
					self.langkeys.push(i);
					self.languages.push(res.langs[i]);
				}
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
			showTheme(){
				if(!this.themepicker.inited){
					this.themepicker.actcolor = this.themepicker.colors.indexOf(this.theme.actcolor);
					for(let color in this.ColorList){
						this.themepicker.colornames.push(this.ColorList[color]);
					}
					for(let bg in this.BgColorList){
						this.themepicker.bgcolors.push(bg);
						this.themepicker.bgcolornames.push(this.BgColorList[bg]);
					}
					this.themepicker.navbg = this.themepicker.bgcolors.indexOf(this.theme.navbg);
					this.themepicker.inited = true;
					return this.showmodal = 'showthemes';
				}
				this.showmodal = 'showthemes';
			},
			ChooseNavbg(e){
				this.themepicker.navbg = e.detail.value;
			},
			ChooseActcolor(e){
				this.themepicker.actcolor = e.detail.value;
			},
			doTextSize(Size){
				let clientSet = core.clientSet;
				clientSet.textSize = Size;
				let self = this;
				core.cacheset("_system_client_session_", clientSet, function(e){
					core.clientSet = clientSet
					store.commit('clientTextSize', Size);
				});
			},
			doSaveTheme(){
				core.style.actcolor = this.themepicker.colors[this.themepicker.actcolor];
				core.style.navbg = this.themepicker.bgcolors[this.themepicker.navbg];
				this.theme = core.style;
				return this.doClearCache('设置成功！');
			},
			doClearCache(toast='清理完成！'){
				core.cacheclear();
				return core.toast(toast,'home','success');
			},
			doSavePassword(){
				if(this.postdata.verifytype=='pass'){
					if(this.data.repassport && this.postdata.oldpassword=='') return core.toast('请输入您的原密码');
				}else{
					if(this.postdata.verifycode==''){
						return core.toast('请输入您收到的验证码');
					}
				}
				if(this.postdata.newpassword=='') return core.toast('请设置一个新密码');
				if(this.postdata.confirmpassword=='') return core.toast('请输入确认密码');
				if(this.postdata.newpassword!=this.postdata.confirmpassword) return core.toast('两次输入的密码不一致');
				let postdata = this.postdata;
				postdata.sendtype = this.verifycodetype;
				core.post('member/password',function(res){
					if(res.type!='success') return core.report(res);
					if(core.logout()){
						return core.toast('修改成功，即将重新登录','login','success');
					}
				},postdata);
			},
			doPassSendcode(){
				let self = this;
				return core.post("member/autosendcode",function(res){
					if(res.type!='success') return core.report(res);
					if(res.message.sendtype=='none'){
						return core.toast('无可用验证方式，请联系管理员重置密码');
					}
					self.postdata.verifytype = 'code';
					self.postdata.verifyaccount = res.message.account;
					self.verifycodetype = res.message.sendtype;
					self.showmodal = 'passport';
					self.verifycodetext = '120s';
					var n = 119;
					if(codeInterval!=null){
						clearInterval(codeInterval);
						codeInterval = null;
					}
					codeInterval = setInterval(function(){
						if(n==0){
							clearInterval(codeInterval);
							codeInterval = null;
							self.verifycodetext = '重新获取';
							return;
						}else{
							self.verifycodetext = n+'s';
						}
						n--
					},1e3);
					core.toast('验证码已发送','','success');
				});
			},
			SwitchSet(e){
				let setkey = e.currentTarget.dataset.set;
				if(!setkey) return false;
				let setvalue = e.detail.value ? 1 : 0;
				let self = this;
				core.post("member/setting",function(res){
					let redirect = res.type=='success' ? '' : res.redirect;
					if(res.type=='success'){
						self.data.userset[setkey] = setvalue;
						core.userset[setkey] = setvalue;
					}
					core.toast(res.message,redirect,res.type);
				},{setkey:setkey,setvalue:setvalue,savespset:"true"});
			},
			doPickLang(e){
				let setvalue = this.langkeys[e.detail.value];
				if(!setvalue) return core.toast('请选择要切换的语言');
				if(setvalue==this.data.userset.language) return core.toast('操作成功','','success');;
				let self = this;
				core.post("member/setting",function(res){
					let redirect = res.type=='success' ? 'home' : res.redirect;
					if(res.type=='success'){
						self.data.userset.language = setvalue;
						core.userset.language = setvalue;
						core.initsys();
					}
					core.toast(res.message,redirect,res.type);
				},{setkey:'language',setvalue:setvalue,savespset:"true"});
			},
			doCheckout(){
				return core.confirm('退出后需要重新登录','切换账号',function(){
					if(core.logout()){
						core.navito('auth/index',{},2);
					}
				});
			},
			doQuitApp(){
				core.confirm('确定要退出'+this.sitename+'？','退出'+this.sitename,function(){
					plus.runtime.quit(); 
				});
			},
			doQuitH5(){
				core.initjwx(function(wx){
					wx.closeWindow();
				});
			},
			confirmCancellation(verifycode){
				let code = verifycode;
				if(this.cancellation.verify && !code) return core.toast('请输入正确的验证码');
				core.post('member/cancellation',function(res){
					if(res.type=='success'){
						core.logout();
					}
					core.report(res);
				},{code:verifycode,sendtype:this.cancellation.sendtype});
			},
			doCancellation(unconfirm=false){
				let self = this;
				let sentcode = function(){
					core.post('member/autosendcode',function(res){
						if(res.type!='success') return core.report(res);
						if(res.message.sendtype=='none'){
							self.cancellation.verify = false;
							self.cancellation.sendtype = 'none';
							console.log('未发送验证码，直接注销');
							return self.confirmCancellation(0);
						}else{
							self.cancellation = {
								verify:true,
								verifycode:'',
								account:res.message.account,
								sendtype:res.message.sendtype
							}
							self.showmodal = 'cancellation';
							self.verifycodetext = '120s';
							var n = 119;
							if(codeInterval!=null){
								clearInterval(codeInterval);
								codeInterval = null;
							}
							codeInterval = setInterval(function(){
								if(n==0){
									clearInterval(codeInterval);
									codeInterval = null;
									self.verifycodetext = '重新获取';
									return;
								}else{
									self.verifycodetext = n+'s';
								}
								n--
							},1e3);
						}
					},{},'json',true);
				};
				if(unconfirm) return sentcode();
				this.showmodal = '';
				core.confirm('注销后该账户在系统上的所有信息都将被清空且无法恢复，是否确定要注销？','注销账户',sentcode)
			},
			unSupport(){
				return core.toast('敬请期待');
			}
		}
	}
</script>

<style>
	.cu-form-group .title {min-width: calc(4em + 15px); text-align-last:justify;}
	.action.text-size .cu-tag + .cu-tag{margin-left: 30upx;}
	.page-text-xl .cu-list.menu>.cu-item .content{font-size: 38upx;}
	.page-text-lg .cu-list.menu>.cu-item .content{font-size: 34upx;}
</style>
