<template>
	<view class="dialogpage" :class="'page-text-'+textSize">
		<view class="dialogcontent" :style="'height: '+heightincress">
			<scroll-view class="dialogchat solid-top" @scrolltoupper="onScrolltoUpper" @scrolltolower="onscrolltolower" @scroll="onScroll" scroll-y="true" :scroll-into-view="intoviewid">
				<view class="cu-chat" @touchstart="InputBule(false)">
					<!-- #ifdef APP-PLUS -->
					<view class="cu-chat-keyboard" :style="'height:'+heightKeyBoard+'px'"></view>
					<!-- #endif -->
					<block v-for="(msg,index) in data.messages" :key="index">
						<view class="cu-info round" :class="msg.special.type" v-if="msg.uid==0||msg.highmode==8" :id="'message'+msg.id">
							<rich-text :nodes="msg.message" @click="naviTo(msg.url)"></rich-text>
							<text v-if="msg.special.type=='revoked'" class="margin-left-xs text-blue" @click="doReedit(index)">重新编辑</text>
						</view>
						<view :id="'message'+msg.id" v-else>
							<msglist :msgindex="index" :chatevel="true" @genimages="getChatimage" @playaudio="doAudioPlay" @dospecial="doSpecialAction" @atuser="doAtUser" @lotap="doLongPress" :showRead="data.showread" :isManager="data.ismanager" :userId="userId" :chatimages="chatimages" :dialogtype="type" :chatid="chatid" :message="msg"></msglist>
						</view>
					</block>
				</view>
			</scroll-view>
			<view class="flex noshadow flex-wrap">
				<view class="bg-gray solid-top padding-sm flex justify-between basis-full" v-if="postdata.quote!=''">
					<view class="flex-sub">
						<text class="chat-quote"><text class="text-grey margin-right-xs">回复</text>{{postdata.quote}}</text>
					</view>
					<view class="text-black text-lg" @click="postdata.quote=''">
						<text class="cuIcon cuIcon-close"></text>
					</view>
				</view>
				<view class="dialogoperation cu-bar input">
					<view class="action" @click="recordShow()">
						<text class="text-grey" style="margin-right: 0;" :class="'cuIcon-'+(record.showing?'keyboard':'sound')"></text>
					</view>
					<view class="action record flex padding-right-sm" @touchmove="recordCancel" @longpress="recordStart" @touchend="recordStop" :class="record.showing?'':'hidden'">
						<button class="cu-btn radius bg-grey light flex-direction">{{record.status?record.text:'请使用APP发送语音'}}</button>
					</view>
					<!-- #ifdef H5 || MP-WEIXIN -->
					<editor id="chathigheditor" class="chateditor" :class="[InputStyle,record.showing?'hidden':'']" auto-height="true" :show-confirm-bar="false" hold-keyboard :adjust-position="false" :focus="chatfocus" @focus="InputFocus" @input="InputInput" @blur="InputBule()" @ready="onEditorReady"></editor>
					<!-- #endif -->
					<!-- #ifdef APP-PLUS -->
					<editor id="chathigheditor" class="chateditor" :class="[InputStyle,record.showing?'hidden':'']" auto-height="true" :show-confirm-bar="false" hold-keyboard :adjust-position="true" cursor-spacing="10" :focus="chatfocus" @focus="InputFocus" @input="InputInput" @blur="InputBule()" @ready="onEditorReady"></editor>
					<!-- #endif -->
					<!-- #ifdef MP-ALIPAY || MP-BAIDU || MP-TOUTIAO-->
					<input class="solid-bottom" hold-keyboard :focus="chatfocus" maxlength="-1" cursor-spacing="10" :class="[InputStyle,record.showing?'hidden':'']" :confirm-type="chatpostcenter?'send':''" @confirm="postchatbybtn(1)" v-model="postdata.message" @blur="InputBule()"></input>
					<!-- #endif -->
					<view class="action" @click="faceShow(true)">
						<text class="text-grey" :class="'cuIcon-'+(face.showing?'keyboard':'emojifill')"></text>
					</view>
					<view class="action" @click="toolShow()" v-if="postdata.message==''">
						<text class="text-grey" :class="'cuIcon-roundadd'+(toolshowing?'':'fill')"></text>
					</view>
					<block v-else>
						<view class="action" @touchend.prevent="postchatbybtn(1)" v-if="chattouchend">
							<button class="cu-btn radius" :class="'bg-'+theme.actcolor">发送</button>
						</view>
						<view class="action" @click="postchatbybtn(1)" v-else>
							<button class="cu-btn radius" :class="'bg-'+theme.actcolor">发送</button>
						</view>
					</block>
				</view>
			</view>
			<view class="recordbar padding radius" :class="'bg-'+theme.actcolor" v-if="record.recorder">
				<view class="flex text-center">
					<view class="padding-right-sm">
						<text class="cuIcon-voicefill" :class="record.cancel?'':'playing'"></text>
						<text v-if="record.cancel">松开手取消录音</text>
						<text v-else>您正在说话...</text>
					</view>
					<view class="flex-sub">
						<button class="cu-tag light round" :class="'bg-'+theme.actcolor">上滑取消</button>
					</view>
				</view>
			</view>
			<view class="face-contianer bg-gray" :class="face.showing?'':'hidden'">
				<view class="nav bg-white">
					<view class="cu-item" :class="face.currface=='normal'?'cur text-'+theme.actcolor:''" @click="face.currface='normal'">经典</view>
					<view class="cu-item" :class="face.currface=='super'?'cur text-'+theme.actcolor:''" @click="face.currface='super'">超级</view>
					<view class="cu-item" :class="face.currface=='myface'?'cur text-'+theme.actcolor:''" @click="face.currface='myface'">我的</view>
				</view>
				<scroll-view scroll-y class="face-faces">
					<view class="face-normal face-items" v-if="face.currface=='normal'">
						<view class="grid col-8 text-center">
							<view class="face-item" @click="faceTochat(index,false)" v-for="(item,index) in face.faces.normal" :key="index">
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
							<view class="bg-white radius" @click="myfaceadd()">
								<text class="cuIcon-cameraadd"></text>
							</view>
							<view @tap="myfaceTochat(index)" @longpress.stop="myfacerm(index)" class="bg-img" v-for="(item,index) in face.faces.myface" :key="index">
								<image :src="item.resource" mode="aspectFill"></image>
							</view>
						</view>
					</view>
				</scroll-view>
				<view class="face-bar flex justify-end" v-if="face.currface=='normal'">
					<button class="cu-btn radius shadow margin-right-sm text-lg" @click="facecancel()" :class="postdata.message==''?'bg-white':'bg-'+theme.actcolor">
						<text class="cuIcon-backdelete"></text>
					</button>
					<button class="cu-btn radius shadow text-lg" @click="postchatbybtn(0)" :class="postdata.message==''?'bg-white':'bg-'+theme.actcolor">
						<text>发送</text>
					</button>
				</view>
			</view>
			<view class="tool-contianer bg-gray" :class="toolshowing?'':'hidden'">
				<scroll-view scroll-y="true" show-scrollbar="true" style="height: 200px;">
					<view class="padding-tb grid col-4 padding-lr-sm">
						<block v-for="(item, index) in navigation.dialog" :key="index">
							<view class="text-grey text-center padding-lr-sm" v-if="!item.dialog || item.dialog==dialogId">
								<view class="radius bg-white padding-tb-sm" @click="toolTap(item.url)" :class="item.extra">
									<view class="text-xxxl">
										<text :class="item.icon==''?'cuIcon-link':item.icon"></text>
									</view>
									<view>{{item.name}}</view>
								</view>
							</view>
						</block>
					</view>
				</scroll-view>
			</view>
			<!-- #ifdef MP-WEIXIN || H5 -->
			<view class="cu-chat-keyboard" v-if="!toolshowing && !face.showing" :style="'height:'+heightKeyBoard+'px'"></view>
			<!-- #endif -->
		</view>
		<view class="chatnoread bg-white shadow-blur solid" @click="GotoLastmsg(true)" :class="'text-'+theme.actcolor" v-if="viewHistory">
			<text class="cuIcon-unfold margin-right-xs"></text><text class="text-sm">查看最新消息</text>
		</view>
		<advs :isPopup="true" :advs="advs.dialog"></advs>
		<view class="cu-modal" :class="showmodal=='groupnotice'?'show':''" v-if="data.group.id>0">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">群公告</view>
				</view>
				<view class="padding-xl">
					<scroll-view scroll-y class="group-notice">
						<rich-text :nodes="data.group.notice" :selectable="true"></rich-text>
					</scroll-view>
				</view>
				<view class="cu-bar bg-white justify-center">
					<view class="action">
						<button class="cu-btn" :class="'bg-'+theme.actcolor" @tap="showmodal=''">我知道了</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal redpacket-show" v-if="redpacket.id>0" :class="showmodal=='redpacket'?'show':''">
			<view class="cu-modal-bg" @tap.stop="showmodal=''"></view>
			<view class="cu-dialog">
				<view class="cu-bar justify-end">
					<view class="content"></view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-white"></text>
					</view>
				</view>
				<view class="padding-xl">
					<view class="padding-lr">
						<view class="cu-list menu">
							<view class="cu-item nobg">
								<view class="content">
									<image :src="redpacket.avatar" class="png radius" mode="aspectFit"></image>
									<text class="text-cut margin-left-xs">{{redpacket.nickname}}的红包</text>
								</view>
							</view>
						</view>
					</view>
					<view class="text-center text-xxl">
						<text v-if="redpacket.remain>0 && redpacket.status==1">{{redpacket.remark}}</text>
						<text v-else-if="redpacket.status==3">红包已过期</text>
						<text v-else>手慢了，红包派完了</text>
					</view>
					<view class="padding-lr text-center padding-top-xl" v-if="redpacket.remain>0 && redpacket.status==1">
						<button class="redpacket-grab" @click="doGrabRedpacket(redpacket.id)">
							<text>開</text>
						</button>
					</view>
					<view class="padding text-center margin-top-xl" v-if="redpacket.gid>0">
						<view @click="naviTo('redpacket/detail',{id:redpacket.id})">
							<text>看看大家的手气</text>
							<text class="cuIcon-right"></text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="popupover" :class="[showmodal=='popupover'?'active':'',chatpopup.extra]">
			<view class="popupover-bg" @touchstart="showmodal='';chatpopup.index=-1"></view>
			<view class="radius popcontainer shadow-blur" :style="'bottom:'+chatpopup.bottom+'px;left:'+chatpopup.left+'px'">
				<view class="cu-list grid text-white" :class="'col-'+chatpopup.colspan">
					<view class="cu-item" :class="chatpopup.copy?'':'hidden'" @click="doCopy()">
						<view class="cuIcon-copy"></view>
						<text>复制</text>
					</view>
					<view class="cu-item" @click="doCancel()" v-if="chatpopup.cancel>0">
						<view class="cuIcon-delete"></view>
						<text>{{chatpopup.cancel==1?'撤回':'删除'}}</text>
					</view>
					<view class="cu-item" @click="doShare()" v-if="chatpopup.share">
						<view class="cuIcon-share"></view>
						<text>转发</text>
					</view>
					<view class="cu-item" @click="doFavor()">
						<view class="cuIcon-favor"></view>
						<text>收藏</text>
					</view>
					<view class="cu-item" @click="doReply()">
						<view class="cuIcon-forward rotate"></view>
						<text>回复</text>
					</view>
					<view class="cu-item" :class="chatpopup.report?'':'hidden'"  @click="doReport()">
						<view class="cuIcon-warn"></view>
						<text>投诉</text>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal bottom-modal" :class="showmodal=='groupat'?'show':''" v-if="type=='group'">
			<view class="cu-modal-bg" @tap.stop="showmodal=''"></view>
			<view class="cu-dialog" @tap.stop="">
				<view class="cu-bar bg-white">
					<view class="action text-blue" @tap="showmodal=''">取消</view>
				</view>
				<userpicker :Users="groupmembers" pStyle="userscorller" @onResult="doAtGroup"></userpicker>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import store from '@/store/index.js'
	import core from "@/core.js"
	import swasocket from "@/swasocket.js"
	import superface from "@/components/util/superface.vue"
	import advs from "@/components/util/advs.vue"
	import msglist from "@/components/util/msglist.vue"
	import userpicker from "@/components/util/userpicker.vue"
	// #ifdef APP-PLUS
	import permision from "@/TrtcCloud/permission.js"
	// #endif
	
	//#ifndef H5	
	const recorderManager = uni.getRecorderManager();
	//#endif
	var recordStart = 0, recordStop = 0;
	var AudioPlayer = null;
	
	export default {
		components: {superface,advs,msglist,userpicker},
		computed: mapState(['userId', 'userName','newMsg','Chatid', 'Chattype', 'textSize']),
		data() {
			return {
				chatid:0,
				dialogId:1,
				inwechat:core.inwechat,
				viewHistory:false,
				chatscrolly:0,
				platform:core.platform,
				clipboardtext:'',
				chatDevice:core.device,
				chatHasRemain:false,
				chatfocus:false,
				chatforback:0,
				chattouchend:true,
				chatTyping:false,
				chatpostcenter:core.userset.post_center==1?true:false,
				chatpopup:{
					index:-1,
					bottom:0,
					left:0,
					colspan:5,
					copy:false,
					share:false,
					report:false,
					cancel:0,
					extra:""
				},
				chatimages:[],
				higheditor:{
					status:false,
					content:"",
					editorCtx:null,
					tempMsg:"",
					inputLen:0,
					faceTyping:false,
					textTyping:false
				},
				genchating:false,
				heightincress:'100vh',
				heightKeyBoard:0,
				inited:false,
				intoviewid:"",
				wxmediaid:"",
				audioplayer:{
					playing:false,
					pauseitem:-1,
					curritem:-1,
					src:""
				},
				sitename:core.system.name,
				showmodal:'',
				InputBottom:0,
				InputStyle:'',
				locations:{
					status:false,
					name:'',
					address:'',
					latitude:0.00,
					longitude:0.00,
					pushtemp:-1
				},
				toolshowing:false,
				type:'normal',
				theme:core.style,
				navigation:{
					dialog:[]
				},
				advs:{
					dialog:[]
				},
				postdata:{
					message:"",
					at:'',
					vid:0,
					highmode:0,
					yuyintime:0,
					dialogtype:'normal',
					exkey:'',
					quote:""
				},
				posting:false,
				groupmembers:[],
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
				record:{
					cancel:true,
					recorder:false,
					movestart:0,
					uploadurl:"",
					showing:false,
					starttime:0,
					status:false,
					stoptime:0,
					duration:0,
					text:'长按  说话'
				},
				langs:core.langs,
				redpacket:{
					id:0,
					msgindex:0,
					gid:0,
					touid:0,
					uid:0,
					nickname:0,
					avatar:"",
					status:0,
					remain:0,
					hasgrabed:false
				},
				data:{
					title:"会话",
					messages:[],
					firstid:0,
					imgcompress:0,
					lastid:0,
					location:false,
					avatar:'',
					newapplys:0,
					ismanager:false,
					isreport:false,
					redpack:false,
					transfer:false,
					group:{
						id:0,
						notice:"",
						noticedate:0
					},
					shareinfo:{
						title:"",
						cover:"",
						url:"",
						desc:""
					},
					showread:false
				}
			}
		},
		onLoad(options) {
			if(typeof(options.cid)=='undefined' || !options.cid) return core.toast('无效的会话ID','back');
			this.chatid = parseInt(options.cid);
			if(typeof(options.type)!='undefined' && options.type!='') this.type = options.type;
			if(typeof(options.exkey)!='undefined') this.postdata.exkey = options.exkey;
			this.dialogId = this.type=='normal' ? 1 : 2;
			this.postdata.cid = this.chatid;
			this.postdata.dialogtype = this.type;
			store.commit('chatinit',{
				cid:this.chatid,
				type:this.type
			});
			if(!core.inited){
				uni.$on('CoreInited',this.CoreInited);
			}else{
				this.CoreInited(core);
			}
			//#ifndef APP-PLUS
			let SystemInfoSync = uni.getSystemInfoSync();
			this.chatDevice = SystemInfoSync.osName;
			if(typeof(SystemInfoSync.windowTop)!='undefined'){
				this.heightincress = "calc(100vh - "+SystemInfoSync.windowTop+"px)";
			}else{
				this.heightincress = SystemInfoSync.windowHeight+"px";
			}
			//#endif
			swasocket.ChatId = this.type + "" + this.chatid;
			let self = this;
			//#ifndef H5
			this.record.status = true;
			recorderManager.onStop(function (res) {
				if(self.record.cancel) return self.postdata.yuyintime = 0;
				self.record.stoptime = (new Date()).getTime();
				self.audioplayer.curritem = -1;
				//上传录音文件
				core.upload(res.tempFilePath,function(ret){
					self.postdata.highmode = 5;
					self.postchat(ret.path,res.tempFilePath);
				},'audio');
			});
			//#endif
			//#ifdef H5
			if(!("ontouchstart" in window)){
				this.chattouchend = false;
			}
			//#endif
			let bgcolor = core.style.navbg.replace('gradual-','');
			bgcolor = bgcolor.replace('bg-','');
			let fontcolor = '#ffffff';
			if(bgcolor=='white' || bgcolor=='yellow' || bgcolor=='gray') fontcolor = '#333333';
			uni.setNavigationBarColor({
				frontColor:fontcolor,
				backgroundColor:this.ColorList[bgcolor]
			});
			// #ifdef APP-PLUS || MP-WEIXIN
			uni.onKeyboardHeightChange(res => {
				self.heightKeyBoard = res.height;
				if(res.height>0){
					self.GotoLastmsg(true, 200);
				}
			})
			// #endif
			this.AudioPlayerinit();
		},
		onShow(){
			if(!this.inited || this.genchating || this.locations.pushtemp>=0) return;
			let that = this;
			core.get("dialog/redialog",function(res){
				if(res.type=='success'){
					let lastid = parseInt(that.data.lastid);
					that.data.lastid = res.message.lastmid;
					if(res.message.messages.length>0){
						that.data.messages = that.data.messages.concat(res.message.messages);
						that.GotoLastmsg(false,50);
						that.doSetReader(res.message.lastmid, lastid);
						that.DialogCache();
					}
				}
				if(res.redirect!=''){
					core.toast(res.message,res.redirect,res.type);
				}
			},{dialogtype:this.type,cid:this.chatid,lastmid:this.data.lastid});
		},
		onUnload() {
			store.commit('chatinit',{
				cid:0,
				type:'normal'
			});
			if(AudioPlayer!=null){
				AudioPlayer.destroy();
				AudioPlayer = null;
			}
			swasocket.ChatId = "";
			swasocket.Playing = false;
			// #ifdef APP-PLUS || MP-WEIXIN
			uni.offKeyboardHeightChange(function(){});
			// #endif
			let cacheKey = "MessageTemp"+this.type+this.chatid;
			if(this.postdata.message!=""){
				core.cacheset(cacheKey, this.postdata.message);
				uni.$emit("HaveDraft", {id:this.type+""+this.chatid, isDraft: true});
			}else{
				core.cacheremove(cacheKey);
			}
			uni.$off('SRdialog',this.SocketRecive);
			uni.$off('SRrmdialog',this.SocketRecive);
			uni.$off('SRredialog',this.SocketRecive);
			uni.$off('SRcanclechat',this.SocketRecive);
			uni.$off('SRsetread',this.SocketRecive);
			uni.$off('SRTyping',this.SocketRecive);
			uni.$off('CoreInited',this.CoreInited);
		},
		onShareAppMessage(e){
			let pageurl = this.type=='group' ? core.page('group/detail',{gid:this.chatid}) : core.page('member/index',{uid:this.chatid});
			pageurl += '&fromuid='+this.userId;
			return {
				title:this.data.shareinfo.title,
				path:pageurl,
			}
		},
		onNavigationBarButtonTap(e){
			if(e.type=='back') return core.back();
			if(this.type=='group'){
				if(e.type=='share'){
					return core.navito('friend/recommend',{gid:this.chatid});
				}
				core.navito('group/detail',{gid:this.chatid,fp:'dialog'});
			}else{
				core.navito('member/index',{uid:this.chatid,fp:'dialog'});
			}
		},
		methods:{
			initData(route, data={}, callback=false){
				data.lastid = 0;
				let that = this;
				let CacheKey = this.type + '_' + this.chatid;
				core.cacheread(CacheKey,function(cache){
					that.data.messages = cache.data.message;
					that.data.title = cache.data.title;
					data.lastid = cache.data.lastid;
					that.data.lastid = data.lastid;
					that.data.firstid = cache.data.firstid;
					that.chatHasRemain = cache.data.hasRemain;
					that.GotoLastmsg(true,100);
					that.DialogLoad(data, route, callback=false);
				}, function(){
					that.DialogLoad(data, route, callback=false);
				});
				let cacheKey = "MessageTemp"+this.type+this.chatid;
				core.cacheread(cacheKey, function(Cache){
					that.postdata.message = Cache.data;
					that.higheditor.tempMsg = Cache.data;
					if(that.higheditor.editorCtx!=null && Cache.data!=''){
						that.higheditor.editorCtx.setContents({
							html:Cache.data
						});
					}
				});
				uni.$on('ClearMessages', function(res){
					if(res.type==that.type && res.chatid==that.chatid){
						that.data.messages = [];
					}
				})
			},
			DialogLoad(params, route='dialog', callback=false){
				let that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					if(typeof(params.lastid)!='undefined' && params.lastid>0){
						if(res.lastid==null || res.lastid==0){
							res.lastid = params.lastid;
						}
						res.firstid = that.data.firstid;
						res.messages = that.data.messages.concat(res.messages);
					}
					that.data = res;
					if(res.messages.length>0){
						that.GotoLastmsg(true,200);
					}
					if(that.inited) return true;
					that.inited = true;
					uni.$emit('DialogLaunch',{type:that.type, cid:that.chatid});
					let pagetitle = res.title;
					// #ifdef H5
					//初始化语音
					if(that.inwechat){
						core.initjwx(function(){
							that.record.status = true;
						}); 
					}else if(typeof(plus)!='undefined'){
						that.record.status = true;
					}
					// #endif
					//初始化表情
					that.faceInit();
					if(res.group.id>0){
						//群公告
						that.GroupNoticeInit();
					}
					if(typeof(res.safeRemind)!='undefined'){
						that.safeRemind(res.safeRemind);
					}
					core.cachecloud('navigation',function(res){
						that.navigation = res;
					});
					core.cachecloud('advs',function(advs){
						that.advs.dialog = advs.dialog;
					});
					uni.setNavigationBarTitle({
						title:pagetitle
					});
					//设置消息为已读
					that.doSetReader(res.lastid,res.firstid);
					that.DialogCache();
					core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
					if(callback){
						callback(res);
					}
				},params);
			},
			DialogCache(readBack=false){
				let CacheKey = this.type + '_' + this.chatid;
				let MsgLength = this.data.messages.length;
				let firstId = this.data.firstid;
				if(MsgLength>50 && !readBack && !this.posting){
					//如果消息记录大于50，则截取最后50条
					//浏览消息记录或正在发送消息时不截取
					this.data.messages = this.data.messages.splice(MsgLength-50,50);
					firstId = this.data.messages[0].id;
					this.data.firstid = firstId;
				}
				if(this.data.messages.length<=0) return true;
				let lastmsg = this.data.messages[this.data.messages.length-1];
				return core.cacheset(CacheKey, {
					lastid:typeof(lastmsg)=='undefined' ? this.data.lastid : lastmsg.id,
					firstid:firstId,
					message:this.data.messages,
					title:this.data.title,
					hasRemain:this.chatHasRemain
				});
			},
			GroupNoticeInit(){
				if(this.data.group.notice=='') return false;
				let noticedate = this.data.group.noticedate;
				let cachekey = 'groupnoticeview'+this.data.group.id;
				let self = this;
				let shownotice = function(viewdate=0){
					if(viewdate>0 && viewdate==noticedate) return false;
					core.cacheset(cachekey,noticedate);
					self.showmodal = 'groupnotice';
				}
				return core.cacheread(cachekey,function(res){
					shownotice(parseInt(res.data));
				},function(e){
					shownotice(0);
				});
			},
			safeRemind(RemindText){
				if(this.chatHasRemain || RemindText=="") return true;
				this.posttemp({
					highmode:8,
					message:'<span class="cuIcon-warnfill text-orange margin-right-xs"></span>' + RemindText,
					special:{
						type:"notice",
						value:""
					}
				});
				this.chatHasRemain = true;
				this.DialogCache();
			},
			AudioPlayerinit(callback=false){
				var self = this;
				AudioPlayer = uni.createInnerAudioContext();
				AudioPlayer.autoplay = true;
				AudioPlayer.startTime = 0
				AudioPlayer.onCanplay(function(res){
					let currindex = self.audioplayer.curritem;
					if(AudioPlayer==null || currindex<0) return;
					let yuyintime = Math.round(AudioPlayer.duration + 0);
					if(yuyintime>0) self.data.messages[currindex].yuyintime = yuyintime;
				});
				AudioPlayer.onPlay(function(){
					swasocket.Playing = true;
					self.audioplayer.playing = true;
					self.audioplayer.pauseitem = -1;
					let currindex = self.audioplayer.curritem;
					self.data.messages[currindex].special.value = 1;
					uni.hideLoading();
				});
				AudioPlayer.onEnded(function(){
					self.audioplayer.pauseitem = -1;
					let currindex = self.audioplayer.curritem;
					self.data.messages[currindex].special.value = 2;
					self.onAudioPause('Ended');
					if(currindex>-1){
						core.get('dialog/setreader',function(res){},{cid:self.chatid,dialogtype:self.type,mid:self.data.messages[currindex].id});
						self.audioplayer.curritem = -1;
					}
				});
				AudioPlayer.onStop(function(){
					self.onAudioPause('Stop');
				});
				AudioPlayer.onPause(function(){
					self.audioplayer.pauseitem = self.audioplayer.curritem;
					if(self.audioplayer.curritem==-1) return true;
					let currindex = self.audioplayer.curritem;
					self.data.messages[currindex].special.value = 3;
					self.onAudioPause('Pause');
				});
				AudioPlayer.onError(function(e){
					if(self.audioplayer.curritem<0) return;
					core.toast('播放失败，请重试');
					self.onAudioPause('Error');
					console.log('播放过程出现异常：',e);
				});
				if(typeof(callback)=='function'){
					callback();
				}
			},
			onAudioPause(state){
				this.audioplayer.playing = false;
				swasocket.Playing = false;
			},
			doAudioPlay(index){
				if(this.audioplayer.playing){
					//如果正在播放,则先暂停正在播放的音频
					let currindex = this.audioplayer.curritem;
					//如果是同一个音频则暂停
					if(index==currindex){
						this.data.messages[currindex].special.value = 3;
						return AudioPlayer.pause();
					}
					this.data.messages[currindex].special.value = 2;
					//如果不是同一个则停止后播放接下来的
					AudioPlayer.stop();
				}
				let src = this.data.messages[index].message + "";
				this.audioplayer.curritem = index;
				if(this.audioplayer.pauseitem==index || this.audioplayer.src==src){
					return AudioPlayer.play();
				}else if(this.audioplayer.pauseitem>=0){
					let pauseitem = this.audioplayer.pauseitem;
					this.data.messages[pauseitem].special.value = 2;
				}
				this.DialogCache();
				//直接播放
				AudioPlayer.startTime = 0;
				AudioPlayer.autoplay = true;
				AudioPlayer.src = src;
				this.audioplayer.src = src;
				core.toast("读取中...", "", "loading");
			},
			doSetReader(lastid,firstid=0){
				core.get('dialog/setreader',function(res){
					//console.log(res);
				},{cid:this.chatid,dialogtype:this.type,lastmid:lastid,firstmid:firstid});
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			getChatimage(pic){
				if(this.chatimages.indexOf(pic)<0){
					this.chatimages.push(pic);
				}
			},
			doChooseLocation(){
				this.locations.pushtemp = 0;
				let self = this;
				let LCallBack = function(pos){
					let messageid = 'temp' + self.data.messages.length;
					let special = {
						type:'location',
						value:{
							name:pos.name,
							address:pos.address,
							latitude:pos.latitude,
							longitude:pos.longitude,
							cover:core.system.siteroot + '/addons/xfy_whotalk/static/images/img_location.jpg'
						}
					};
					let Tempmessage = {
						addtime:0,
						at:0,
						avatar:self.data.avatar,
						cancel:1,
						dateline:0,
						datetime:'刚刚',
						gid:self.type=='group'?self.chatid:0,
						highmode:7,
						id:messageid,
						mediaid:'',
						message:'',
						nickname:self.userName,
						openid:self.userId,
						poster:'',
						special:special,
						status:0,
						touid:self.type=='normal'?self.chatid:0,
						uid:self.userId,
						uniacid:0,
						url:"",
						vid:0,					
						warned:0,
						yuyintime:0
					}
					//添加临时消息
					let messageslength = self.data.messages.push(Tempmessage);
					//下滑到最底部
					setTimeout(function(){
						self.intoviewid = 'message' + messageid;
					},30);
					//发送位置消息
					let chatpushtemp = messageslength - 1;
					self.locations.pushtemp = chatpushtemp;
					core.post("dialog/location",function(res){
						if(res.type!='success'){
							self.data.messages[chatpushtemp].status = -1;
							return core.toast(res.message);
						}
						self.postresult(res,chatpushtemp);
						self.locations.pushtemp = -1;
					},{data:pos,cid:self.chatid,dialogtype:self.type,exkey:self.postdata.exkey});
				};
				if(this.locations.status){
					//return LCallBack(this.locations);
				}
				uni.chooseLocation({
					success:function(res){
						res.status = true;
						self.locations = res;
						LCallBack(res);
					},
					fail:function(e){
						core.toast('获取位置失败');
						console.log('获取地图定位失败：',e);
					}
				});
			},
			onScrolltoUpper(){
				if(this.data.firstid==0 || this.audioplayer.playing) return false;
				let self = this;
				core.get("dialog",function(res){
					if(typeof(res.message)!='undefined') return false;
					if(self.audioplayer.playing) return false;
					if(res.count==0){
						self.data.firstid = 0;
						return;
					}
					self.data.firstid = res.firstid;
					self.data.messages = res.messages.concat(self.data.messages);
					setTimeout(function(){
						self.intoviewid = 'message' + res.lastid;
					},100);
					self.doSetReader(res.lastid, res.firstid);
					self.DialogCache(true);
				},{cid:this.chatid,dialogtype:this.type,getchat:'yes',firstid:this.data.firstid});
			},
			onscrolltolower(){
				this.viewHistory = false;
				this.chatforback = 0;
			},
			onScroll(e){
				if(this.chatscrolly>e.detail.scrollTop){
					let scrollTop = this.chatscrolly-e.detail.scrollTop;
					this.chatforback += scrollTop;
					if(this.chatforback>=500){
						this.viewHistory = true;
						this.intoviewid = '';
					}
				}
				this.chatscrolly = e.detail.scrollTop;
				//console.log(e.detail.scrollTop, this.viewHistory);
			},
			onEditorReady(){
				this.higheditor.status = true;
				let self = this;
				uni.createSelectorQuery().select('#chathigheditor').context((res) => {
					self.higheditor.editorCtx = res.context;
					if(self.higheditor.tempMsg!="" && self.higheditor.tempMsg==self.postdata.message){
						res.context.setContents({
							html:self.higheditor.tempMsg
						});
					}
				}).exec()
			},
			CoreInited(data){
				this.SocketInit(data.system.socket.type);
				this.inwechat = data.inwechat;
				let self = this;
				this.initData('dialog',{cid:this.chatid,dialogtype:this.type}, function(res){
					// #ifdef APP-PLUS
					if(plus.os.name != "iOS"){
						self.requestAndroidPermission('android.permission.RECORD_AUDIO');
					}
					// #endif
					self.AudioPlayerinit();
				});
			},
			chooseImage(){
				let that = this;
				if(this.inwechat){
					let wx = core.jweixin;
					return wx.chooseImage({
						count: 9, // 默认9
						success: function (ret) {
							for (let i in ret.localIds){
								wx.uploadImage({
									localId: ret.localIds[i], // 需要上传的图片的本地ID，由chooseImage接口获得
									isShowProgressTips: 1, // 默认为1，显示进度提示
									success: function (result) {
										core.get('attach/wxmedia',function(res){
											if (res.type=='success'){
												that.postdata.highmode = 4;
												that.postchat(res.message.path,res.message.url);
											}else {
												core.toast(res.message,res.redirect,res.type);
											}
										},{media_id:result.serverId});
									},fail:function (res) {
										util.toast(Luanguages.msg_upload_failed,'','error');
										console.log(res);
									}
								});
							}
						},fail:function (res) {
							util.toast(Luanguages.msg_operation_failed,'','error');
							console.log(res);
						}
					});
				}
				uni.chooseImage({
					count: 9, //默认9
					sizeType: ['compressed','original'], //可以指定是原图还是压缩图，默认二者都有
					success: function(res){
						let CallBack = function(FilePath){
							core.upload(FilePath,function(ret){
								that.postdata.highmode = 4;
								that.postchat(ret.path,ret.url);
							},'image','attach/upload');
						};
						for(let i in res.tempFilePaths){
							if(that.data.imgcompress>0){
								that.compressImage(res.tempFilePaths[i],CallBack);
							}else{
								CallBack(res.tempFilePaths[i]);
							}
						}
					}
				});
			},
			compressImage(tempFilePath,callback){
				let imgcompress = this.data.imgcompress;
				console.log('开始压缩图片(压缩率'+imgcompress+'%):'+tempFilePath);
				if(this.platform=='h5'){
					let Quality = imgcompress/100;
					return uni.getImageInfo({
						src:tempFilePath,
						success:function(res){
							let img = new Image();
							img.src = res.path;
							let canvas = document.createElement('canvas');
							let ctx = canvas.getContext('2d')
							canvas.width     = res.width 
							canvas.height    = res.height
							ctx.drawImage(img, 0, 0, res.width, res.height)
							canvas.toBlob(function(fileSrc) {
								let fileObj = new File([fileSrc],new Date().getTime()+'.jpg',{type:'image/jpeg'});
								callback(fileObj); 
							},'image/jpeg',Quality)
						},
						fail:function(e){
							callback(tempFilePath);
						}
					});
				}else{
					uni.compressImage({
						quality:imgcompress,
						src:tempFilePath,
						success: (res) => {
							callback(res.tempFilePath)
						},
						fail: (e) => {
							console.log(e);
						}
					})
				}
			},
			chooseVideo(){
				var that = this;
				uni.chooseVideo({
					count: 1,
					sourceType: ['camera', 'album'],
					compressed:false,
					success: function (res) {
						let messageid = 'temp_video' + new Date().getTime();
						let messageLenght = that.data.messages.push({
							addtime:0,
							at:that.postdata.at,
							avatar:that.data.avatar,
							cancel:0,
							dateline:0,
							datetime:'刚刚',
							gid:that.type=='group'?that.chatid:0,
							highmode:2,
							id:messageid,
							isread:0,
							mediaid:"",
							message:res.tempFilePath,
							nickname:that.userName,
							openid:that.userId,
							poster:'/static/images/poster.jpg',
							special:{
								type:'normal',
								value:'',
								progress:0
							},
							status:0,
							touid:that.type=='normal'?that.chatid:0,
							uid:that.userId,
							uniacid:0,
							url:"",
							vid:0,					
							warned:0,
							yuyintime:0
						});
						let tempId = messageLenght - 1;
						let uploadTask = core.upload(res.tempFilePath,function(ret){
							that.postdata.highmode = 2;
							that.postdata.vid = ret.vid;
							that.postchat(ret.path,ret.url,tempId);
						},'video','attach/upload',{loading:false});
						uploadTask.onProgressUpdate(function(e){
							that.data.messages[tempId].special.progress = e.progress;
						});
						setTimeout(function(){
							that.intoviewid = 'message' + messageid;
						}, 100);
					}
				});
			},
			recordCancel(e){
				if(!this.record.status || !this.record.recorder) return false;
				if(this.record.movestart==0){
					this.record.movestart = e.touches[0].clientY;
				}else{
					if(e.touches[0].clientY<this.record.movestart){
						if(this.record.movestart-e.touches[0].clientY>100){
							this.record.cancel = true;
						}
					}
				}
			},
			async requestAndroidPermission(permisionID) {
				let self = this;
			    var result = await permision.requestAndroidPermission(permisionID);
			    if(result == -1) {
					self.record.status = false;
			        core.toast("未授予麦克风权限");
			    }
			},
			recordStart(e){
				if(!this.record.status) return false;
				if(this.audioplayer.playing){
					//如正在播放语音则暂停
					AudioPlayer.pause();
				}
				this.record.cancel = false;
				this.record.movestart = 0;
				this.record.text = '松开  结束';
				this.record.starttime = (new Date()).getTime();
				this.record.recorder = true;
				// #ifndef H5
				return recorderManager.start({
					format:'mp3'
				});
				// #endif
				// #ifdef H5
				if(this.inwechat){
					//微信内H5录音开始
					let self = this;
					core.jweixin.onVoiceRecordEnd({
						// 录音时间超过一分钟没有停止的时候会执行 complete 回调
						complete: function (res) {
							self.record.stoptime = (new Date()).getTime();
							self.record.text = '长按  说话';
							if(self.record.stoptime-self.record.starttime<1000){
								self.record.cancel = true;
								return core.toast('录音时间太短');
							}
							self.postdata.yuyintime = Math.round((self.record.stoptime-self.record.starttime)/1000) * 1000;
							self.record.recorder = false;
							self.recordComplete(res.localId);
						}
					});
					setTimeout(function(){
						core.jweixin.startRecord({
							success: function(){
								if(typeof(localStorage)!='undefined'){
									localStorage.rainAllowRecord = 'true';
								}
							},
							fail(e){
								console.log(e);
								core.toast("录音失败");
							},
							cancel: function () {
								core.toast("录音已取消");
							}
						});
					},300);
				}else if(typeof(inWhotalkPlus)!='undefined'){
					return doPlusRecordStart(this);
				}else{
					this.recordStop();
				}
				// #endif
			},
			recordComplete(mediaid){
				if(this.record.cancel) return false;
				let self = this;
				core.jweixin.uploadVoice({
					localId: mediaid, // 需要上传的音频的本地ID，由stopRecord接口获得
					isShowProgressTips: 1, // 默认为1，显示进度提示
					success: function (res) {
						//把录音在微信服务器上的id（res.serverId）发送到自己的服务器供下载。
						self.postdata.highmode = 5;
						self.postchat('WXMEDIA:'+res.serverId,'wxvoice:'+mediaid);
					}
				});
			},
			recordStop(e){
				if(!this.record.status) return false;
				this.record.text = '长按  说话';
				this.record.stoptime = (new Date()).getTime();
				if(this.record.stoptime-this.record.starttime<1000){
					this.record.cancel = true;
					core.toast('录音时间太短');
				}
				this.postdata.yuyintime = this.record.stoptime-this.record.starttime;
				if(this.record.recorder){
					this.record.recorder = false;
					// #ifndef H5
					return recorderManager.stop();
					// #endif
					// #ifdef H5
					if(this.inwechat){
						let self = this;
						//微信内H5录音结束
						core.jweixin.stopRecord({
							success: function (res) {
								self.recordComplete(res.localId);
							},
							fail: function (res) {
								core.toast("停止录音动作发生异常(0)");
							}
						});
					}else if(typeof(doPlusRecordStop)=='function'){
						this.record.uploadurl = core.system.siteroot + core.url("attach/upload") + '&authkey='+core.userinfo.token;
						return doPlusRecordStop(this);
					}else{
						if(!this.record.cancel) core.toast('请使用APP发送语音');
					}
					// #endif
				}else{
					core.toast('录音失败');
				}
			},
			recordShow(){
				this.record.showing = !this.record.showing;
				if(!this.record.showing) this.chatfocus = true;
			},
			toolShow(frombtn){
				if(this.toolshowing){
					//关闭工具栏
					this.toolshowing = false;
					if(frombtn) this.chatfocus = true;
				}else{
					//打开工具栏
					this.face.showing = false;
					this.toolshowing = true;
					if(this.chatfocus) this.chatfocus = false;
					this.GotoLastmsg(true,30);
				}
			},
			toolTap(url){
				if(url=='choose/image'){
					return this.chooseImage();
				}else if(url=='choose/video'){
					return this.chooseVideo();
				}else if(url=='choose/location'){
					return this.doChooseLocation();
				}
				return core.navito(url,{cid:this.chatid,type:this.type});
			},
			facecancel(){
				if(this.postdata.message=='') return false;
				let message = this.postdata.message + '';
				if(this.higheditor.status){
					let matchs = message.match(/\<img src="(.+?)layim-chat-face">/g);
					if(matchs && matchs.length>0){
						let matchFace = matchs[matchs.length-1];
						return this.higheditor.editorCtx.setContents({
							html:message.replace(matchFace, ""),
							success:this.InitEditor
						});
					}
					return false;
				}
				let laststr = message.substr(message.length-1,1);
				if(laststr==']'){
					let regs = /\[face_([a-zA-Z0-9]+)_([a-zA-Z0-9]+)\]$/g;
					let matchresult = message.search(regs);
					if(matchresult>-1){
						this.postdata.message = message.replace(regs,'');
						return true;
					}
				}
				this.postdata.message = message.substr(0,message.length-1);
			},
			faceTochat(face,autopost=false){
				let facename;
				if(autopost){
					facename = '['+face+']';
				}else{
					let item = this.face.faces.normal[face];
					// #ifdef APP-PLUS || MP-WEIXIN || H5
					this.higheditor.faceTyping = true;
					this.postdata.highmode = 1;
					let message = this.postdata.message.replace("<p><br></p>", "");
					let img = '<img src="'+item.pic+'" height="26px" style="vertical-align: middle" class="layim-chat-face" />';
					let html = message.indexOf('</p>') == -1 ? message + img : message.replace(/<\/p>$/, img+'</p>');
					return this.higheditor.editorCtx.setContents({
						html:html,
						success:this.InitEditor
					});
					// #endif
					facename = '[face_'+item.path+'_'+item.name+']';
				}
				if(autopost){
					return this.postchat(facename);
				}else{
					this.postdata.message += facename;
				}
			},
			myfaceTochat(index){
				this.postdata.highmode = 4;
				let myface = this.face.faces.myface[index];
				this.postchat(myface.attach,myface.resource);
			},
			myfaceadd(){
				let that = this;
				let updateface = function(myfaces){
					let cachekey = '_xfy_whotalk_face_' + that.userId;
					core.cacheset(cachekey,myfaces);
					if(myfaces.myface.length==1){
						core.toast('长按可删除表情');
					}
					return true;
				};
				if(this.inwechat){
					let wx = core.jweixin;
					return wx.chooseImage({
						count: 1, // 默认9
						success: function (ret) {
							wx.uploadImage({
								localId: ret.localIds[0], // 需要上传的图片的本地ID，由chooseImage接口获得
								isShowProgressTips: 1, // 默认为1，显示进度提示
								success: function (result) {
									core.get('face/wxmedia',function(res){
										if (res.type=='success'){
											that.face.faces.myface.unshift(res.message);
											updateface(that.face.faces);
										}else {
											core.toast(res.message,res.redirect,res.type);
										}
									},{media_id:result.serverId});
								},fail:function (res) {
									util.toast(Luanguages.msg_upload_failed,'','error');
									console.log(res);
								}
							});
						},fail:function (res) {
							util.toast(Luanguages.msg_operation_failed,'','error');
							console.log(res);
						}
					});
				}
				uni.chooseImage({
					count: 1, //默认9
					sizeType: ['compressed','original'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (ret) => {
						let callback = function(FilePaths){
							core.upload(FilePaths,function(res){
								that.face.faces.myface.unshift(res);
								updateface(that.face.faces);
							},'image','face/upload');
						}
						if(that.data.imgcompress>0){
							that.compressImage(ret.tempFilePaths[0],callback);
						}else{
							callback(ret.tempFilePaths[0]);
						}
					}
				});
			},
			myfacerm(index){
				let that = this;
				let myface = this.face.faces.myface[index];
				return core.confirm('删除后不可恢复，是否确定要删除？','删除表情',function(){
					core.get('face/delete',function(res){
						if(res.type!='success') return core.report(res);
						that.face.faces.myface.splice(index,1);
						let cachekey = '_xfy_whotalk_face_' + that.userId;
						core.cacheset(cachekey,that.face.faces);
					},{fid:myface.id});
				});
			},
			faceShow(frombtn=false){
				if(this.record.showing) this.record.showing = false;
				if(this.face.showing){
					//关闭表情选择
					this.face.showing = false;
					this.face.temp = '';
					if(frombtn) this.chatfocus = true;
				}else{
					//打开表情选择
					if(!this.face.inited) return this.faceInit(true);
					this.toolshowing = false;
					this.face.showing = true;
					if(this.chatfocus) this.chatfocus = false;
					this.GotoLastmsg(true, 30);
				}
				return true;
			},
			doAtUser(e, nomark=false){
				if(this.type!='group') return false;
				let atusers = this.postdata.at=='' ? [] : this.postdata.at.split(',');
				if(atusers.indexOf(e.uid+"")>=0){
					this.showmodal = '';
					return false;
				}
				atusers.push(e.uid);
				this.postdata.at = atusers.join(',');
				let nicktext = (nomark ? "" : " @") + e.nickname  + " ";
				// #ifdef H5 || APP-PLUS || MP-WEIXIN
				if(this.postdata.message==""){
					nicktext = "@" + e.nickname + " ";
				}
				this.higheditor.editorCtx.insertText({
					text: nicktext,
					success:this.InitEditor
				});
				// #endif
				// #ifdef MP-ALIPAY || MP-BAIDU || MP-TOUTIAO
				this.postdata.message = this.postdata.message + nicktext;
				// #endif
				this.showmodal = '';
			},
			doAtGroup(item){
				if(item.uid==-1){
					if(this.data.ismanager){
						this.postdata.at = 'all';
						// #ifdef H5 || APP-PLUS || MP-WEIXIN
						this.higheditor.editorCtx.insertText({
							text:"所有人 ",
							success:this.InitEditor
						});
						// #endif
						// #ifdef MP-ALIPAY || MP-BAIDU || MP-TOUTIAO
						this.postdata.message = this.postdata.message + "所有人 ";
						// #endif
					}
					return this.showmodal = '';
				}
				let user = {
					uid:item.uid,
					nickname:item.nickname
				}
				this.doAtUser(user,true);
			},
			doGrabRedpacket(){
				if(this.redpacket.id<=0) return core.toast('无效的红包信息');
				if(this.redpacket.hasgrabed) return core.toast('您已经抢过该红包');
				let self = this;
				core.get("redpacket/grab",function(res){
					if(res.type!='success') return core.report(res);
					self.data.messages[self.redpacket.msgindex].special.value.grabed = true;
					self.showmodal = '';
					self.DialogCache();
					core.navito('redpacket/detail',{id:self.redpacket.id});
				},{id:this.redpacket.id});
			},
			doSpecialAction(options){
				if(options.type=='showredpacket'){
					let index = options.index;
					if(this.data.messages[index].special.value.grabed){
						return core.navito('redpacket/detail',{id:this.data.messages[index].special.value.id});
					}
					let self = this;
					core.get("redpacket/show",function(res){
						if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
							if(typeof(res.message.received)!='undefined'){
								self.data.messages[index].special.value.grabed = true;
								self.DialogCache();
								return core.navito('redpacket/detail',{id:self.data.messages[index].special.value.id});
							}
							return core.toast(res.message,res.redirect,res.type);
						}
						let redpacket = res.redpacket;
						redpacket.msgindex = index;
						self.redpacket = redpacket;
						if(res.redpacket.status!=1 || res.redpacket.hasgrabed || res.redpacket.remain==0){
							self.data.messages[index].special.value.grabed = true;
							self.DialogCache();
						}
						self.showmodal = 'redpacket';
					},{id:options.value,fp:'popover'})
				}
			},
			doShare(){
				if(this.chatpopup.index<0) return false;
				this.showmodal = '';
				return core.navito('dialog/share',{mid:this.data.messages[this.chatpopup.index].id}),this.chatpopup.index=-1;
			},
			doCancel(){
				if(this.chatpopup.index<0) return false;
				let message = this.data.messages[this.chatpopup.index];
				if(message.uid!=this.userId && !this.data.ismanager) return core.toast('功能暂未开放');
				let confirmtext = message.cancel==1 ? core.langs.text_withdraw : core.langs.text_delete;
				let self = this;
				core.confirm(core.langs.text_delete_confirm,confirmtext,function(){
					self.showmodal = '';
					core.get('dialog/cancelchat',function(res){
						if(res.type=='success' || res.message=='找不到该消息'){
							if(self.chatpopup.cancel==1){
								if(message.highmode<=1){
									message.special.type = "revoked";
									message.special.value = message.message + "";
								}
								message.highmode = 8;
								message.message = "你撤回了一条消息";
								self.data.messages[self.chatpopup.index] = message;
							}else{
								self.data.messages.splice(self.chatpopup.index,1);//删除
							}
							return self.DialogCache();
						}
						self.chatpopup.index = -1;
						core.toast(res.message,res.redirect,res.type);
					},{mid:message.id});
				});
			},
			doReedit(index){
				this.postdata.highmode = 1;
				let html = this.data.messages[index].special.value;
				return this.higheditor.editorCtx.setContents({
					html:html,
					success:this.InitEditor
				});
			},
			doFavor(){
				if(this.chatpopup.index<0) return false;
				let self = this;
				core.post('dialog/collect',function(res){
					self.showmodal = '';
					self.chatpopup.index = -1;
					core.toast(res.message,res.redirect,res.type);
				},{docollect:"true",mid:this.data.messages[this.chatpopup.index].id,name:''})
			},
			doCopy(){
				if(this.chatpopup.index<0) return false;
				return core.copy(this.clipboardtext);
			},
			doReply(){
				if(this.chatpopup.index<0) return false;
				let message = this.data.messages[this.chatpopup.index];
				if(message.status!=1){
					return core.toast("不可回复该消息");
				}
				let msg = message.special.simpleContent + '';
				this.postdata.quote = message.nickname + '：' + msg.replace(/\n/g, '');
				this.chatfocus = true;
				this.doAtUser({
					uid:message.uid,
					nickname:message.nickname
				});
				this.GotoLastmsg(true, 100);
			},
			doReport(){
				if(this.chatpopup.index<0) return false;
				let mid = this.data.messages[this.chatpopup.index].id;
				if(typeof(mid)=='undefined') return false;
				return core.navito("report/index",{id:mid,type:"message"});
			},
			doLongPress(res){
				let index = res.index;
				this.chatpopup.index = index;
				let popcol = 3;
				this.chatpopup.copy = false;
				this.chatpopup.report = false;
				this.chatpopup.share = false;
				this.chatpopup.extra = res.extra;
				if(this.data.messages[index].highmode<=1){
					let text = this.data.messages[index].message + '';
					text = text.replace(/\<\/p\>/g, "</p>\n");
					this.clipboardtext = text.replace(/<[^>]*>|/g,"");
					if(this.clipboardtext!=''){
						popcol += 1;
						this.chatpopup.copy = true;
					}
				}
				let unShares = [3,5,6], highMode = this.data.messages[index].highmode;
				if(unShares.indexOf(highMode)<0){
					this.chatpopup.share = true;
					popcol += 1;
				}
				if(this.data.messages[index].uid!=this.userId && this.data.isreport){
					this.chatpopup.report = true;
					popcol += 1;
				}
				if(this.data.messages[index].uid!=this.userId && !this.data.ismanager){
					popcol -= 1;
					this.chatpopup.cancel = 0;
				}else{
					this.chatpopup.cancel = this.data.messages[index].cancel==1 ? 1 : 2;
				}
				this.chatpopup.colspan = Math.min(popcol, 4);
				this.chatpopup.bottom = res.bottom;
				this.chatpopup.left = res.left;
				this.showmodal = 'popupover';
			},
			faceInit(autoshow=false){
				let Callback = function(){
					return autoshow ? this.faceShow() : true;
				}
				if(this.face.inited) return Callback();
				this.face.inited = true;
				let cachekey = '_xfy_whotalk_face_' + this.userId;
				let self = this;
				return core.cacheread(cachekey,function(res){
					self.face.faces = res.data;
					Callback();
				},function(){
					core.get('face.getface',function(res){
						if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
							self.face.inited = false;
							return core.report(res);
						}
						self.face.faces = res;
						core.cacheset(cachekey,res);
						Callback();
					});
				});
			},
			posttemp(postdata,tempmsg=''){
				let message = tempmsg=='' ? postdata.message : tempmsg;
				let mediaid = '';
				let messageid = 'temp' + this.data.messages.length;
				let special = typeof(postdata.special)!='undefined' ? postdata.special : {type:'normal',value:''};
				if(typeof(message)=='string'){
					if(message.search(/\[superface_([a-z]+)\]/)>-1){
						special = {
							type:'superface',
							value:message.replace(/\[superface_([a-z]+)\]/,"$1")
						};
					}
				}
				if(postdata.highmode==5){
					special.value = 2;
					if(message.indexOf('wxvoice:')===0){
						mediaid = message.replace('wxvoice:','');
						message = '';
					}
				}
				this.posting = true;
				var messageslength = this.data.messages.push({
					addtime:0,
					at:postdata.at,
					avatar:this.data.avatar,
					cancel:1,
					dateline:0,
					datetime:'刚刚',
					gid:this.type=='group'?this.chatid:0,
					highmode:postdata.highmode,
					id:messageid,
					isread:0,
					mediaid:mediaid,
					message:message,
					nickname:this.userName,
					openid:this.userId,
					poster:'/static/images/poster.jpg',
					quote:postdata.quote,
					special:special,
					status:0,
					touid:this.type=='normal'?this.chatid:0,
					uid:this.userId,
					uniacid:0,
					url:"",
					vid:postdata.vid,					
					warned:0,
					yuyintime:parseInt(postdata.yuyintime/1000)
				});
				let self = this;
				setTimeout(function(){
					self.intoviewid = 'message' + messageid;
				},30);
				return messageslength - 1;
			},
			postchat(msg='',tempmsg='', tempId=-1){
				let message = msg=='' ? this.postdata.message : msg;
				if(message=='') return core.toast(core.langs.msg_send_empty);
				let postdata = JSON.parse(JSON.stringify(this.postdata));
				postdata.message = message;
				let chatpushtemp = tempId>=0 ? tempId : this.posttemp(postdata,tempmsg);
				let self = this;
				core.post("dialog/postchat",function(res){
					if(res.status!=1){
						self.data.messages[chatpushtemp].status = -1;
						return core.toast(res.message)
					};
					self.postresult(res,chatpushtemp);
				},postdata);
				//清空输入内容
				if(this.higheditor.status && this.postdata.message!=''){
					this.higheditor.editorCtx.clear();
				}
				this.higheditor.textTyping = false;
				this.postdata.message = '';
				this.postdata.at = '';
				this.postdata.quote = '';
				this.postdata.highmode = 0;
				this.postdata.yuyintime = 0;
				this.InputStyle = '';
				return true;
			},
			postresult(res,chatpushtemp=0){
				//将发送内容插入列表
				res.message.isread = this.data.messages[chatpushtemp].isread;
				this.data.messages.splice(chatpushtemp,1,res.message);
				this.posting = false;
				let newLen = this.data.messages.length;
				this.data.lastid = this.data.messages[newLen-1].id;
				this.GotoLastmsg(true, 100);
				this.DialogCache();
				//发送socket数据
				if(typeof(res.socketdata)!='undefined'){
					let touser = core.system.socket.type=='tim' ? res.touid : res.socketdata.to;
					let socketserver = typeof(res.socketurl)=='undefined' ? '' : res.socketurl;
					this.SocketSend(res.socketdata.content,touser,socketserver);
				}
				//更新首页
				let avatar = this.type=='group' ? this.data.group.pic : this.data.member.avatar;
				uni.$emit('DialogPost',{cid:this.chatid,type:this.type,spct:res.spct,avatar:avatar,title:this.data.title})
				//异步通知对方
				if(this.type=='normal'){
					core.get("dialog/notice",function(rets){},{cid:this.chatid,type:this.type,mid:res.message.id});
				}
				let cacheKey = "MessageTemp"+this.type+this.chatid;
				core.cacheremove(cacheKey);
				uni.$emit("HaveDraft", {id:this.type+""+this.chatid, isDraft: false});
			},
			SocketSend(data,touser,server=''){
				let scktype = core.system.socket.type;
				let socketdata = {};
				if(scktype=='local'){
					socketdata = {
						"Method": "Message/SendToUsers",
						"Type": 0,
						"Message": JSON.stringify(data),
						"data":{
							"userIds":touser
						}
					};
				}else if(scktype=='tim'){
					let conversationtype = this.type=='group' ? swasocket.TIM.TYPES.CONV_GROUP : swasocket.TIM.TYPES.CONV_C2C;
					socketdata = swasocket.Socket.createTextMessage({
						to: touser+"",
						conversationType: conversationtype ,
						payload: {
							text: JSON.stringify(data)
						}
					});
				}else{
					socketdata = {
						"content":JSON.stringify(data),
						"to":touser
					}
				}
				swasocket.Send(socketdata,scktype,server);
			},
			SocketInit(ctype=''){
				uni.$on('SRdialog', this.SocketRecive);
				uni.$on('SRrmdialog', this.SocketRecive);
				uni.$on('SRredialog', this.SocketRecive);
				uni.$on('SRcanclechat', this.SocketRecive);
				uni.$on('SRsetread', this.SocketRecive);
				uni.$on('SRTyping', this.SocketRecive);
			},
			SocketRecive(socketdata){
				if(socketdata.type=='dialog'){
					if(socketdata.dialogid==this.chatid && socketdata.dialogtype==this.type){
						return this.GetnewChat(socketdata.mid);
					}
				}else if(socketdata.type=='rmdialog'){
					//从列表中删除会话
					let ctypeid = this.type + this.chatid;
					if(socketdata.ctypeid==ctypeid){
						let CacheKey = this.type + '_' + this.chatid;
						core.cacheremove(CacheKey);
						core.back();
					}
				}else if(socketdata.type=='redialog'){
					//重新加载会话
					this.DialogLoad({cid:this.chatid,dialogtype:this.type,lastid:0});
				}else if(socketdata.type=='canclechat'){
					if(socketdata.dialogtype==this.type && socketdata.dialogid==this.chatid){
						for(let index in this.data.messages){
							if(this.data.messages[index].id==socketdata.mid){
								this.data.messages[index].highmode = 8;
								this.data.messages[index].message = socketdata.spct;
								break;
							}
						}
						this.DialogCache();
					}
				}else if(socketdata.type=='setread'){
					if(socketdata.chatid==this.chatid && socketdata.dialogtype==this.type){
						let msgid = parseInt(socketdata.mid);
						if(msgid>this.data.lastid){
							this.data.messages[this.data.messages.length-1].isread += 1;
						}else{
							for(let index in this.data.messages){
								if(this.data.messages[index].id==msgid || this.data.messages[index].id==socketdata.mid){
									this.data.messages[index].isread += 1;
									break;
								}
							}
						}
						this.DialogCache();
					}
				}else if(socketdata.type=='Typing'){
					if(this.chatTyping) return false;
					if(socketdata.chatid==this.chatid && socketdata.dialogtype==this.type){
						this.chatTyping = true;
						let self = this;
						uni.setNavigationBarTitle({
							title:"对方正在输入..."
						});
						setTimeout(function() {
							self.chatTyping = false;
							uni.setNavigationBarTitle({
								title:self.data.title
							});
						}, 3000);
					}
				}
			},
			GetnewChat(mid=0){
				let postdata = {mid:mid,cid:this.chatid,setread:1,dialogtype:this.type};
				this.genchating = true;
				let self = this;
				return core.post("dialog/getnewchat",function(res){
					self.genchating = false;
					if(res.type!='success') return core.toast(res.message);
					self.data.messages.push(res.message);
					self.data.lastid = res.message.id;
					self.GotoLastmsg();
					self.DialogCache(self.viewHistory);
				},postdata);
			},
			GotoLastmsg(force=false, timeout=100){
				if(this.viewHistory && !force) return true;
				this.intoviewid = "messageNochoosed";
				let self = this;
				setTimeout(function(){
					self.intoviewid = 'message' + self.data.lastid;
					self.viewHistory = false;
					self.chatforback = 0;
				},timeout)
			},
			postchatbybtn(fromtext){
				if(fromtext==1 && !this.face.showing) this.chatfocus = true;
				this.postchat();
			},
			InitEditor(callback=false, Backspace=false){
				if(!this.higheditor.status) return false;
				let self = this;
				this.higheditor.editorCtx.getContents({
					success(res) {
						//删除@用户
						if(Backspace && res.text.search(/@(\S+)([\n\r])$/)>=0){
							res.html = res.html.replace(/@([^@]+)<\/p>$/, "<p/>");
							self.higheditor.editorCtx.setContents({html:res.html});
							if(self.postdata.at!='all'){
								let atusers = self.postdata.at=='' ? [] : self.postdata.at.split(',');
								atusers.pop();
								self.postdata.at = atusers.join(',');
							}
						}
						if(res.html=="<p><br></p>"){
							self.postdata.highmode = 0;
							res.html = res.text = "";
						}
						self.postdata.message = res.html;
						if(res.html.indexOf("@")==-1){
							self.postdata.at = '';
						}else if(self.type=='group' && self.postdata.at!='all' && res.text.search(/@([\n\r])$/)>-1){
							//群聊@他人
							self.InitGroupAt();
						}
						if(typeof(callback)=='function'){
							callback(res);
						}
					}
				});
			},
			InitGroupAt(){
				// #ifdef APP-PLUS || MP-WEIXIN || H5
				uni.hideKeyboard();
				// #endif
				this.chatfocus = false;
				let self = this;
				if(this.groupmembers.length==0){
					return core.get('group/members',function(res){
						if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
							return core.report(res);
						}
						if(res.group.ismanager){
							res.members.unshift({
								id:0,
								avatar:"",
								uid:-1,
								nickname:"所有人",
								style:"text-orange",
								title:"",
								extra:""
							});
						}
						self.groupmembers = res.members;
						self.showmodal = 'groupat';
					},{gid:this.chatid, fp:'picker'},'json',true);
				}
				this.showmodal = 'groupat';
			},
			InputBule(autofocus=false) {
				this.higheditor.textTyping = false;
				if(this.face.showing && !this.higheditor.status) return this.faceShow(autofocus);
				this.chatfocus = autofocus;
			},
			InputFocus(e){
				if(this.higheditor.faceTyping){
					this.higheditor.faceTyping = false;
					// #ifdef MP-WEIXIN
					return true;
					// #endif
				}
				if(this.toolshowing) this.toolshowing = false;
				if(this.face.showing) this.face.showing = false;
			},
			InputInput(e){
				if(typeof(e.detail.html)=='undefined' || e.detail.text=="\n" || e.detail.html=="<p><br></p>"){
					this.postdata.highmode = 0;
					this.postdata.at = '';
					this.showmodal = "";
					return this.postdata.message = "";
				}
				let isDel = (this.higheditor.inputLen - e.detail.text.length) == 1;
				this.higheditor.inputLen = e.detail.text.length;
				//正在输入
				if(this.type=='normal' && !this.higheditor.textTyping){
					this.SocketSend({type:"Typing", chatid: core.userinfo.uid, dialogtype: this.type, title: "对方正在输入中..."}, this.data.member.usersign);
				}
				this.higheditor.textTyping = true;
				this.postdata.highmode = 1;
				//回车发送
				let hasenter = typeof(e.detail.text)!='undefined' ? e.detail.text.indexOf("\n\n") : -1;
				let self = this;
				let Callback;
				if(hasenter>0 && this.chatpostcenter){
					Callback = function(res){
						let postmsg = res.html.replace('<p><br></p>','');
						postmsg = postmsg.replace('</p><p><span class="ql-cursor">﻿</span></p>', '</p>');
						return self.postchat(postmsg);
					}
				}
				return this.InitEditor(Callback, isDel);
			}
		}
	}
</script>

<style>
	@import "@/colorui/animation.css";
	
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
		max-height: 280upx;
		overflow: hidden;
		flex-shrink: 0;
		width: 100vw;
		min-height: calc(55px + env(safe-area-inset-bottom) / 2);
		padding-bottom: calc(env(safe-area-inset-bottom) / 2);
	}
	.chateditor{
		height: auto;
		max-height: 230upx;
		min-height: 64upx;
		margin: 10upx 0;
		padding:2upx 15upx;
		display: flex;
		background-color: #f4f5f9;
		margin-left: 8px;
		border-radius: 5px;
		line-height: 64upx;
		font-size: 30upx;
	}
	.page-text-lg .chateditor{font-size: 34upx;}
	.page-text-lg .cu-bar.input .action [class*="cuIcon-"]{font-size: 52upx;}
	.page-text-xl .chateditor{font-size: 38upx;}
	.page-text-xl .cu-bar.input .action [class*="cuIcon-"]{font-size: 56upx;}
	.chatnoread{position: fixed; bottom: 150upx; right: 0; padding: 10upx 20upx; border-radius: 2500upx 0 0 2500upx; z-index: 100;}
	.chateditor ~ .action .cu-btn{min-width: 3.5em; padding: 0 15upx;}
	.cu-chat{overflow: hidden;}
	.cu-chat.hasbg{background: no-repeat center; background-attachment: fixed; background-size: 30%; min-height: calc(100vh-190upx);}
	.dialogchat .layim-chat-face{vertical-align: middle; max-height: 48upx;}
	.cu-chat .dialogitem.notime:last-child {padding-bottom: 30upx;}
	.chat-quote{max-width: calc(100vw - 3em); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; display: inline-block; line-height: 100%;}
	.chat-quote br{display: none;}
	.cu-chat .cu-info{max-width: 540upx;}
	.cu-chat .cu-info.notice{color: #f37b1d; background-color: #fde6d2; text-align: justify; padding: 16upx 20upx;}
	
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
	.tool-contianer{height: 200px; width: 100%; z-index: 100; overflow: hidden;}
	.tool-contianer .grid>view{margin-bottom: 20upx;}
	.recordbar{position: fixed; top: 50%; left: 50%; width: 460upx; margin-left: -230upx; height: 124upx; margin-top: -62upx; line-height: 62upx; z-index: 999;}
	.bottom0{bottom: 0;}
	.cu-bar.input{padding-right: 0;}
	.cu-bar.input .areainput{max-height: 148upx; line-height: 50upx; padding: 6upx 0; overflow: hidden;}
	.redpacket-show .cu-dialog{width: 510upx; background-color: #f45540; color: #ebcd99;}
	.redpacket-grab{width: 160upx; height: 160upx; border-radius: 50%; line-height: 160upx; font-size: 72upx; background: #ebcd99; color: #3e3c3f; font-family: serif; box-shadow: 0 0 3px 0px #6b6464; margin-top: 60upx;}
	.popcontainer{position: absolute; left: 100upx; background: #2C405A; padding: 10upx; width: 320upx;}
	.popcontainer .cu-list.grid{background: none;}
	.popcontainer .cu-list.grid>.cu-item{padding: 4upx 10upx; text-align: center;}
	.popcontainer .cu-list.grid>.cu-item [class*=cuIcon]{font-size: 36upx; margin-top: 0;}
	.popcontainer .cu-list.grid>.cu-item text{color: #FFFFFF; font-size: 24upx;}
	.popcontainer::after{width: 20upx; height: 20upx; background: #2C405A; content: ''; left: 50%; margin-left: -10upx; bottom: -8upx; position: absolute; transform: rotate(45deg); border-radius: 4upx;}
	.popupover.popup .popcontainer::after{bottom: auto; top: -8upx;}
	
	.cu-chat-keyboard{display: block; width: 100%;}
</style>
