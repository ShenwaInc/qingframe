<template>
	<view class="dialogpage">
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="dialogcontent" :style="'height: '+heightincress">
				<scroll-view class="dialogchat solid-top" scroll-y="true" :scroll-into-view="intoviewid">
					<view class="cu-chat" @touchstart="InputBule(false)">
						<block v-for="(msg,index) in data.messages" :key="index">
							<view class="cu-info round" v-if="msg.uid==0||msg.highmode==8" :id="'message'+msg.id">
								<rich-text :nodes="msg.message" @click="naviTo(msg.url)"></rich-text>
							</view>
							<view :id="'message'+msg.id" v-else-if="msg.highmode==10">
								<view class="cu-item dialogitem">
									<view class="cu-avatar radius" :style="'background-image:url('+msg.avatar+');'"></view>
									<view class="highmode11 special-candidate main">
										<view class="content">
											<view class="cu-list menu card-menu">
												<view class="cu-item">
													<view class="content noaf nobf"><text>您可能想了解以下问题：</text></view>
												</view>
												<view class="cu-item" v-for="(candi,canii) in msg.special.value" :key="canii">
													<view class="content noaf nobf" @click="postchat(candi)"><text class="text-blue">{{candi}}</text></view>
												</view>
												<view class="cu-item" @click="doLogin()">
													<view class="content noaf nobf"><text class="text-blue">联系人工客服</text></view>
												</view>
											</view>
										</view>
									</view>
									<view class="date">{{msg.datetime}}</view>
								</view>
							</view>
							<view :id="'message'+msg.id" v-else>
								<msglist :msgindex="index" :chatevel="true" @genimages="getChatimage" @lotap="doLongPress" :isManager="false" :userId="data.uid" :chatimages="chatimages" dialogtype="normal" :chatid="data.member.uid" :message="msg"></msglist>
							</view>
							<view class="cu-info round" style="max-width: 80%;" v-if="index==0 && !hasLogin">
								<view class="padding-xs padding-lr-sm" @click="doLogin()">
									<text class="text-blue">登录</text>
									<text>后可发送更多类型消息并查看咨询记录</text>
								</view>
							</view>
						</block>
					</view>
				</scroll-view>
				<view class="cu-bar input noshadow dialogoperation" :class="animationstyle">
					<view class="action" @click="doLogin()">
						<text class="text-grey cuIcon-sound" style="margin-right: 0;"></text>
					</view>
					<input class="solid-bottom" adjust-position auto-blur hold-keyboard :focus="chatfocus" maxlength="-1" cursor-spacing="10" :class="InputStyle" @touchend.prevent="InputBule(true);" confirm-type="send" @confirm="postchatbybtn(1)" v-model="postmessage" @blur="InputBule()" @input="InputInput"></input> 
					<view class="action" @click="faceShow(true)">
						<text class="text-grey" :class="'cuIcon-'+(face.showing?'keyboard':'emojifill')"></text>
					</view>
					<view class="action" @click="toolShow()" v-if="postmessage==''">
						<text class="text-grey" :class="'cuIcon-roundadd'+(toolshowing?'':'fill')"></text>
					</view>
					<view class="action" @touchend.prevent="postchatbybtn(1)" v-else>
						<button class="cu-btn radius" :class="'bg-'+theme.actcolor">发送</button>
					</view>
				</view>
				<view class="face-contianer bg-gray" :class="[(face.showing?'':'hidden'),animationstyle]">
					<view class="nav bg-white">
						<view class="cu-item" :class="face.currface=='normal'?'text-green cur':''" @click="face.currface='normal'">经典</view>
						<view class="cu-item" :class="face.currface=='super'?'text-green cur':''" @click="face.currface='super'">超级</view>
						<view class="cu-item" :class="face.currface=='myface'?'text-green cur':''" @click="face.currface='myface'">我的</view>
					</view>
					<scroll-view scroll-y class="face-faces">
						<view class="face-normal face-items" v-if="face.currface=='normal'">
							<view class="grid col-8 text-center">
								<view class="face-item" @click="faceTochat('face_'+item.path+'_'+item.name,false)" v-for="(item,index) in face.faces.normal" :key="index">
									<image mode="aspectFill" :src="item.pic"></image>
								</view>
							</view>
						</view>
						<view class="face-super face-items" v-else-if="face.currface=='super'">
							<view class="grid col-4 padding-top">
								<view class="face-item" @click="faceTochat('superface_'+item,true)" v-for="(item,index) in face.faces.super" :key="index">
									<superface :face="item"></superface>
								</view>
							</view>
						</view>
						<view class="face-mine face-items" v-else>
							<view class="grid col-5 grid-square flex-sub padding-sm">
								<view class="bg-white radius" @click="doLogin()">
									<text class="cuIcon-cameraadd"></text>
								</view>
							</view>
						</view>
					</scroll-view>
					<view class="face-bar flex justify-end" v-if="face.currface=='normal'">
						<button class="cu-btn radius shadow margin-right-sm text-lg" @click="facecancel()" :class="postmessage==''?'bg-white':'bg-green'">
							<text class="cuIcon-backdelete"></text>
						</button>
						<button class="cu-btn radius shadow text-lg" @click="postchatbybtn(0)" :class="postmessage==''?'bg-white':'bg-green'">
							<text>发送</text>
						</button>
					</view>
				</view>
				<view class="tool-contianer bg-gray" :class="[(toolshowing?'':'hidden'),animationstyle]">
					<view class="padding-tb grid col-4 padding-lr-sm">
						<view class="text-grey text-center padding-lr-sm">
							<view class="radius bg-white padding-tb-sm" @click="doLogin(true)">
								<view class="text-xxxl">
									<text class="cuIcon-picfill"></text>
								</view>
								<view>图片</view>
							</view>
						</view>
						<view class="text-grey text-center padding-lr-sm">
							<view class="radius bg-white padding-tb-sm" @click="doLogin(true)">
								<view class="text-xxxl">
									<text class="cuIcon-videofill"></text>
								</view>
								<view>视频</view>
							</view>
						</view>
						<view class="text-grey text-center padding-lr-sm">
							<view class="radius bg-white padding-tb-sm" @click="doLogin(true)">
								<view class="text-xxxl">
									<text class="cuIcon-locationfill"></text>
								</view>
								<view>位置</view>
							</view>
						</view>
						<view class="text-grey text-center padding-lr-sm">
							<view class="radius bg-white padding-tb-sm" @click="doLogin(true)">
								<view class="text-xxxl">
									<text class="cuIcon-favorfill"></text>
								</view>
								<view>收藏</view>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import superface from "@/components/util/superface.vue"
	import msglist from "@/components/util/msglist.vue"
	var RobotAudioContext = uni.createInnerAudioContext();
	
	export default {
		components: {superface,msglist},
		computed: mapState(['userId', 'hasLogin']),
		data() {
			return {
				animationstyle:"",
				chatfocus:false,
				chatimages:[],
				chatpopup:{
					index:-1,
					bottom:0,
					left:0,
					colspan:4,
					copy:false,
					cancel:0
				},
				face:{
					inited:false,
					faces:{
						normal:[],
						myface:[],
						super:[]
					},
					showing:false,
					currface:'normal'
				},
				heightincress:'100vh',
				InputBottom:0,
				InputStyle:'',
				intoviewid:"",
				langs:core.langs,
				loaded:false,
				navigation:{
					dialog:[]
				},
				platform:core.platform,
				postmessage:"",
				session_id:"",
				theme:core.style,
				toolshowing:false,
				userset:core.userset,
				data:{
					"member": {
						"uid": 0,
						"nickname": "",
						"avatar": core.system.logo
					},
					"uid": this.userId,
					"nickname": "",
					"title": "在线客服",
					"messages": [],
					"avatar": core.system.logo,
					"redirect": "robot/index",
					"lastmid": "",
					"shareinfo": {
						"title": core.system.name,
						"desc": "",
						"cover": core.system.logo,
						"url": ""
					}
				}
			}
		},
		onShareAppMessage(e){
			let options = {};
			if(this.hasLogin){
				options.fromuid = this.userId;
			}
			return {
				title:this.data.shareinfo.title,
				path:core.page('robot/index',options),
			}
		},
		onShareTimeline(e){
			let Query = '';
			if(this.hasLogin){
				Query = 'fromuid='+this.userId;
			}
			return {
				title:this.data.shareinfo.title,
				query:Query
			}
		},
		onLoad() {
			this.initData('robot');
			this.heightincress = "calc(100vh - "+this.CustomBar+"px)";
			RobotAudioContext.autoplay = false;
			RobotAudioContext.src = core.system.siteroot + '/addons/xfy_whotalk/static/notice.mp3';
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
					that.intoviewid = 'message'+res.lastmid;
					core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
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
			chatRemind(){
				if(this.userset.remind_vibrate==1){
					uni.vibrateLong();
				}
				if(this.userset.remind_voice==1){
					RobotAudioContext.play();
				}
			},
			toolShow(frombtn){
				this.intoviewid = '';
				if(this.toolshowing){
					//关闭工具栏
					this.toolshowing = false;
					this.animationstyle = '';
					if(frombtn) this.chatfocus = true;
				}else{
					//打开工具栏
					this.chatfocus = false;
					this.toolshowing = true;
					this.face.showing = false;
					let self = this;
					setTimeout(function(){
						self.intoviewid = 'message' + self.data.lastid;
					},30);
				}
			},
			faceShow(frombtn=false){
				this.intoviewid = '';
				if(this.face.showing){
					//关闭表情选择
					this.face.showing = false;
					this.face.temp = '';
					this.animationstyle = '';
					if(frombtn) this.chatfocus = true;
				}else{
					//打开表情选择
					if(!this.face.inited) return this.faceInit(true);
					this.chatfocus = false;
					this.face.showing = true;
					this.toolshowing = false;
					let self = this;
					setTimeout(function(){
						self.intoviewid = 'message' + self.data.lastmid;
					},50);
				}
				return true;
			},
			faceInit(autoshow=false){
				if(!this.face.inited){
					let cachekey = '_xfy_whotalk_face_' + this.userId;
					let self = this;
					return core.cacheread(cachekey,function(res){
						self.face.inited = true;
						self.face.faces = res.data;
						if(autoshow){
							self.faceShow();
						}
					},function(){
						core.get('face.getface',function(res){
							if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined') return core.report(res);
							self.face.inited = true;
							self.face.faces = res;
							core.cacheset(cachekey,res);
							if(autoshow){
								self.faceShow();
							}
						});
					});
				}else if(autoshow){
					return this.faceShow();
				}
			},
			faceTochat(facename,autopost=false){
				let face = '['+facename+']';
				if(autopost){
					return this.postchat(face);
				}else{
					this.postmessage += face;
				}
			},
			doLogin(toast=false){
				if(this.hasLogin){
					return core.navito(this.data.redirect);
				}
				core.navito('auth/index',{rd:this.data.redirect});
			},
			posttemp(postdata,tempmsg=''){
				let message = tempmsg=='' ? postdata.message : tempmsg;
				let messageid = 'temp' + this.data.messages.length;
				let special = {
					type:'normal',
					value:''
				};
				if(typeof(message)=='string'){
					if(message.search(/\[superface_([a-z]+)\]/)>-1){
						special = {
							type:'superface',
							value:message.replace(/\[superface_([a-z]+)\]/,"$1")
						};
					}
				}
				var messageslength = this.data.messages.push({
					addtime:0,
					at:0,
					avatar:this.data.avatar,
					cancel:1,
					dateline:0,
					datetime:'刚刚',
					gid:0,
					highmode:postdata.highmode,
					id:messageid,
					mediaid:'',
					message:message,
					nickname:'我',
					openid:this.data.uid,
					poster:'@/static/images/poster.jpg',
					special:special,
					status:0,
					touid:postdata.touid,
					uid:this.data.uid,
					uniacid:0,
					url:"",
					vid:0,					
					warned:0,
					yuyintime:0
				});
				let self = this;
				setTimeout(function(){
					self.intoviewid = 'message' + messageid;
				},30);
				return messageslength - 1;
			},
			postchat(msg='',tempmsg=''){
				let message = msg=='' ? this.postmessage : msg;
				if(message=='') return core.toast(core.langs.msg_send_empty);
				let postdata = {
					message:message,
					highmode:0,
					touid:this.data.member.uid
				}
				let chatpushtemp = this.posttemp(postdata,tempmsg);
				let self = this;
				postdata.tempuid = this.data.uid;
				postdata.session_id = this.session_id;
				core.post("robot/postchat",function(res){
					if(res.type!='success'){
						self.data.messages[chatpushtemp].status = -1;
						return core.toast(res.message)
					};
					self.postresult(res,chatpushtemp);
				},postdata);
				//清空输入内容
				this.postmessage = '';
				this.InputStyle = '';
				return true;
			},
			postchatbybtn(fromtext){
				if(fromtext==1 && !this.face.showing) this.chatfocus = true;
				this.postchat(this.postmessage);
			},
			postresult(res,chatpushtemp=0){
				//新消息提醒
				this.chatRemind();
				//将发送内容插入列表
				this.data.messages.splice(chatpushtemp,1,res.message);
				this.data.messages.push(res.reply);
				this.session_id = res.session_id;
				let self = this;
				setTimeout(function(){
					self.intoviewid = 'message'+res.reply.id;
				},50);
			},
			InputBule(autofocus=false) {
				if(this.toolshowing) return this.toolShow(autofocus);
				if(this.face.showing) return this.faceShow(autofocus);
				this.chatfocus = autofocus;
			},
			InputInput(e){
				if(this.postmessage==''){
					this.InputStyle = '';
				}else{
					let hasenter = e.detail.value.indexOf("\n");
					if(hasenter==-1){
						this.InputStyle = '';
					}else{
						this.InputStyle = 'areainput';
					}
				}
			}
		}
	}
</script>

<style>
	.dialogcontent{
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		box-sizing: border-box;
	}
	.dialogchat{
		flex: 1;
		overflow: auto;
		display: flex;
		width: 100%;
		flex-direction: column;
	}
	.dialogoperation{
		display: flex;
		height: 80rpx;
		flex-shrink: 0;
		width: 100vw;
	}
	.cu-chat{overflow: hidden;}
	.cu-chat.hasbg{background: no-repeat center; background-attachment: fixed; background-size: 30%; min-height: calc(100vh-190upx);}
	
	.group-notice{max-height: 40vh; font-size: 32upx; line-height: 50upx; text-indent: 64upx; text-align: left; white-space: pre-wrap;}
	
	.cu-bar.foot, .face-contianer, .tool-contianer{animation-duration: 0.3s;}
	.cu-bar .action.record{flex: 1;}
	.cu-bar .action.record .cu-btn{width: 100%;}
	.face-contianer{height: 260px; width: 100%; z-index: 100; overflow: hidden;}
	.face-faces{height: 430upx; position: relative;}
	.face-items{padding: 8upx;}
	.face-normal{position: relative; overflow: hidden; padding-bottom: 66upx;}
	.face-normal .face-item{padding: 10upx; overflow: hidden;}
	.face-normal .face-item image{max-width: 100%; width: 62upx; height: 62upx;}
	.face-super .face-item .superface{transform: scale(0.6); margin: -30upx;}
	.face-bar{position: absolute; right: 8upx; bottom: 8upx;}
	.tool-contianer{height: 130px; width: 100%; z-index: 100;}
	.tool-contianer .grid>view{margin-bottom: 20upx;}
	.recordbar{position: fixed; top: 50%; left: 50%; width: 460upx; margin-left: -230upx; height: 124upx; margin-top: -62upx; line-height: 62upx; z-index: 999;}
	.bottom0{bottom: 0;}
	.cu-bar.input{padding-right: 0;}
	.cu-bar.input .areainput{max-height: 148upx; line-height: 50upx; padding: 6upx 0; overflow: hidden;}
	.special-candidate > .content{padding: 10upx 20upx !important;}
	.special-candidate .cu-list{margin: 0 !important;}
	.special-candidate .cu-list .cu-item,.special-candidate .cu-item .content{min-height: 68upx; padding: 0;}
	.special-candidate .cu-item .content{padding: 5px 11px;}
</style>
