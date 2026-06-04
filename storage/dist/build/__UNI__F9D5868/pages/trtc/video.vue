<template>
	<view>
		<cu-custom bgColor="nobg text-white" :isBack="true">
			<block slot="content">{{connecting?duratext:""}}</block>
		</cu-custom>
		<view class="padding-xl trtc-user" v-if="!connecting">
			<view class="flex justify-center">
				<view class="cu-avatar xl radius" :style="'background-image: url('+data.userinfo.avatar+');'"></view>
			</view>
			<view class="padding text-white text-center">
				<text class="text-xxl">{{data.userinfo.nickname}}</text>
				<view class="text-lg margin-top text-muted">{{frompage=='call'?'正在等待对方接受邀请':'对方邀请您进行视频通话'}}</view>
			</view>
		</view>
		<!-- #ifdef H5 -->
		<view :class="viewMyself?'remoteStream':'localStream shadow-blur'" @click="SwitchStream(true)">
			<div id="local_stream"></div>
		</view>
		<view :class="[viewMyself?'localStream shadow-blur':'remoteStream', connecting?'':'hidden']" @click="SwitchStream()">
			<div id="remote_stream"></div>
		</view>
		<!--  #endif -->
		<!-- #ifdef MP-WEIXIN -->
		<live-pusher @statechange="_pusherStateChangeHandler" :class="viewMyself?'remoteStream':'localStream'" :url="pusher.url" :enable-camera="client.camera" :enable-mic="client.voice" v-if="pusher.url" @click="SwitchStream(true)" />
		<view v-for="(item, index) in playerList" :key="index" @click="SwitchStream()">
			<live-player :class="viewMyself?'localStream shadow-blur':'remoteStream'" mode="RTC" :sound-mode="item.soundMode" :src="item.src" autoplay :id="item.id" />
		</view>
		<!--  #endif -->
		<block v-if="loaded">
			<view class="foot trtc-bar" :class="showbar?'':'hidden'" v-if="data.frompage=='call'||connecting">
				<view class="grid" :class="connecting?'col-4':'col-2'">
					<view class="padding text-center" @click="RotateCamera()">
						<view class="roundicon" :class="client.cameraId?'line-white':'bg-white'">
							<text class="cuIcon-camerarotate"></text>
						</view>
						<view class="padding-top-sm">
							<text class="text-muted">翻转</text>
						</view>
					</view>
					<view class="padding text-center" @click="SwitchCamera()">
						<view class="roundicon" :class="client.camera?'bg-white':'line-white close'">
							<text class="cuIcon-camerafill"></text>
						</view>
						<view class="padding-top-sm">
							<text class="text-muted">摄像头</text>
						</view>
					</view>
					<block v-if="connecting">
						<view class="padding text-center" @click="SwitchVoice()">
							<view class="roundicon" :class="client.voice?'bg-white':'line-white close'">
								<text class="cuIcon-voicefill"></text>
							</view>
							<view class="padding-top-sm">
								<text class="text-muted">麦克风</text>
							</view>
						</view>
						<view class="padding text-center" @click="SwitchEarpiece()">
							<view class="roundicon" :class="client.earpiece?'bg-white':'line-white'">
								<text :class="'cuIcon-notification'+(client.earpiece?'forbidfill':'fill')"></text>
							</view>
							<view class="padding-top-sm">
								<text class="text-muted">{{client.earpiece?'听筒':'扬声器'}}</text>
							</view>
						</view>
					</block>
				</view>
				<view class="text-center">
					<view class="padding text-center" @click="doHangup()">
						<view class="roundicon bg-red rotate">
							<text class="cuIcon-dianhua"></text>
						</view>
					</view>
				</view>
			</view>
			<view class="foot trtc-bar" v-else>
				<view class="grid col-2">
					<view class="padding text-center" @click="doRefuse()">
						<view class="padding text-center">
							<view class="roundicon bg-red rotate">
								<text class="cuIcon-dianhua"></text>
							</view>
							<view class="padding-top-sm">
								<text class="text-muted">拒绝</text>
							</view>
						</view>
					</view>
					<view class="padding text-center" @click="doAnswer()">
						<view class="padding text-center">
							<view class="roundicon" :class="'bg-'+theme.actcolor">
								<text class="cuIcon-dianhua"></text>
							</view>
							<view class="padding-top-sm">
								<text class="text-muted">接听</text>
							</view>
						</view>
					</view>
				</view>
			</view>
		</block>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import swasocket from "@/swasocket.js"
	// #ifdef H5
	import TRTC from 'trtc-js-sdk';
	// #endif
	// #ifdef MP-WEIXIN
	import TRTC from 'trtc-wx-sdk';
	// #endif
	var TClient,remoteStream;
	var localStream = null;
	var intvalStream = null;
	
	export default {
		data() {
			return {
				chatid:0,
				duratext:"0:01",
				frompage:'call',
				type:'normal',
				connecting:false,	//会话状态
				hasHangup:false,
				joined:false,
				loaded:false,
				viewMyself:true,
				showbar:true,
				joinuids:[],
				cameras:[],
				speakers:[],
				theme:core.style,
				pusher:{
					url:"",
					roomID:"",
					userID:"",
					userSig:""
				},
				playerList:[],
				player:{},
				TrtcClient:null,
				client:{
					voice:true,
					camera:false,
					cameraId:0,
					remoteVoice:true,
					speakerId:1,
					connected:false,	//是否在房间内
					initialized:false,
					duration:1,
					audios:[],
					earpiece:false
				},
				data:{
					frompage:'call',
					releasedate:0,
					roomid:'',
					rtcinfo:{
						mode:'rtc',
						sdkAppId:'',
						userId:'',
						userSig:'',
						type:2
					},
					title:"语音通话",
					userinfo:{
						uid:0,
						avatar:core.system.logo,
						nickname:"加载中...",
						userID:""
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.cid)=='undefined' || !options.cid) return core.toast('无效的会话','back','error');
			this.chatid = parseInt(options.cid);
			if(typeof(options.fp)!='undefined' && options.fp) this.frompage = options.fp;
			if(typeof(options.type)!='undefined' && options.type) this.type = options.type;
			if(this.type!='normal') return core.toast('暂不支持多人通话','back','error');
			swasocket.TRTC.connecting = true;
			let self = this;
			this.initData('trtc/video' ,{uid:this.chatid,fp:this.frompage,mode:'video',dialogtype:this.type}, function(res){
				self.frompage = res.frompage;
				console.log("视频通话场景开始", res);
				uni.$on('SRtrtc', self.SocketRecive);
				if(res.frompage!='call'){
					self.joined = true;
				}
				// #ifdef MP-WEIXIN
				console.log("初始化TRTC");
				self.TrtcClient = new TRTC(self);
				wx.setKeepScreenOn({
					keepScreenOn: true,
				});
				self.pusher = {
					url:"",
					roomID:res.roomid,
					userID:res.rtcinfo.userId,
					userSig:res.rtcinfo.userSig
				};
				self.bindTRTCRoomEvent();
				// #endif
			});
		},
		onShow() {
			
		},
		onUnload() {
			swasocket.TRTC.connecting = false;
			this.connecting = false;
			uni.$off('SRtrtc');
			// #ifdef MP-WEIXIN
			wx.setKeepScreenOn({
				keepScreenOn: false,
			});
			// #endif
			if(intvalStream!=null){
				clearInterval(intvalStream);
				intvalStream = null;
			}
			if(this.data.roomid>0 && !this.hasHangup){
				this.LeaveRoom(true);
			}
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
			SocketRecive(socketdata){
				if(socketdata.roomid!=this.data.roomid && socketdata.uid!=this.chatid) return;
				console.log('实时通话状态更新:', socketdata);
				if(socketdata.mode=='dismiss' || (socketdata.mode=='leave' && this.joined)){
					//挂断或退出
					if(this.client.connected){
						return this.LeaveRoom(true, '通话已结束');
					}
					return core.toast('对方已取消','back');
				}else if(socketdata.mode=='join'){
					//对方接听
					this.JoinRoom();
					this.joinuids.push(socketdata.uid);
					this.joined = true;
					this.data.userinfo = {
						uid:socketdata.uid,
						avatar:socketdata.avatar,
						nickname:socketdata.nickname
					}
				}else if(socketdata.mode=='refuse'){
					return core.toast('对方已拒绝','back');
				}else if(socketdata.mode=='busy'){
					return core.toast('对方正忙','back');
				}else if(socketdata.mode=='enter'){
					//对方进入通话界面
				}
			},
			voiceConnected(state=""){
				this.connecting = true;	//无论是对方还是己方接听都更新状态
				this.viewMyself = false;
				this.startTime();
				console.log(this.playerList, state);
			},
			RotateCamera(){
				if(!this.client.camera) return core.toast("请先打开摄像头");
				let index = 1 - this.client.cameraId;
				let self = this;
				// #ifdef H5
				if(this.cameras.length<=1){
					return core.toast("切换失败，请重试");
				}
				localStream.switchDevice('video', this.cameras[index].deviceId).then(()=>{
					self.client.cameraId = index;
				}).catch((e)=>{
					console.log("摄像头切换失败：", e);
					core.toast("切换失败，请重试");
				});
				// #endif
				// #ifdef MP-WEIXIN
				this.TrtcClient.getPusherInstance().switchCamera({success:function(res){
					self.client.cameraId = index;
				}});
				// #endif
			},
			SwitchStream(myself=false){
				if(this.viewMyself==myself){
					return this.showbar = !this.showbar;
				}
				this.viewMyself = !this.viewMyself;
			},
			SwitchVoice(){
				// #ifdef H5
				if(this.client.voice){
					//关闭麦克风
					localStream.muteAudio();
				}else{
					//打开麦克风
					localStream.unmuteAudio();
				}
				// #endif
				this.client.voice = !this.client.voice;
			},
			SwitchEarpiece(){
				if(this.joinuids.indexOf(this.userId)==-1) return core.toast('未开始连接');
				if(!this.joined) return core.toast('对方尚未接入对话');
				let index = 1 - this.client.speakerId;
				// #ifdef H5
				if(this.speakers.length<=1){
					return core.toast('切换失败');
				}
				remoteStream.setAudioOutput(this.speakers[index].deviceId);
				// #endif
				// #ifdef MP-WEIXIN
				this.playerList = this.TrtcClient.setPlayerAttributes(this.player.streamID, {soundMode:index?'speaker':'ear'});
				// #endif
				this.client.speakerId = index;
				this.client.earpiece = !index;
			},
			SwitchCamera(){
				if(!this.client.camera){
					// #ifdef H5
					if(localStream==null){
						this.initClient();
						return this.createStream();
					}else{
						localStream.unmuteVideo();
					}
					// #endif
					// #ifdef MP-WEIXIN
					if(this.pusher.url==""){
						return this.CreatePusherWxapp();
					}
					// #endif
				}else{
					// #ifdef H5
					localStream.muteVideo();
					// #endif
				}
				this.client.camera = !this.client.camera;
			},
			startTime(){
				let self = this;
				intvalStream = setInterval(function(){
					let duration = self.client.duration + 1;
					let second = duration % 60;
					let minute;
					if(duration>3600){
						let hours = Math.trunc(duration/3600);
						minute = Math.trunc((duration % 3600)/60);
						self.duratext = hours + ":"+(minute>9?"":"0")+minute+":"+(second>9?"":"0")+second;
					}else{
						minute = Math.trunc(duration/60);
						self.duratext = minute+":"+(second>9?"":"0")+second;
					}
					self.client.duration += 1;
				}, 1000);
			},
			doHangup(){
				//取消、挂断
				let text = this.connecting ? '通话结束' : '已取消';
				return this.LeaveRoom(this.connecting, text);
			},
			doRefuse(){
				//拒绝
				let self = this;
				core.post('trtc/refuse',function(res){
					if(res.type=='success'){
						core.toast('已拒绝，通话结束','back');
					}else{
						core.report(res);
					}
				},{roomid:this.data.roomid},'json',true);
			},
			doAnswer(){
				//接听
				let self = this;
				core.post('trtc/answer',function(res){
					if(res.type=='success'){
						try{
							self.JoinRoom();
						}catch(e){
							//TODO handle the exception
							console.log(e);
							self.doHangup();
						}
					}else{
						core.report(res);
					}
				},{roomid:this.data.roomid},'json',true);
			},
			JoinRoom(){
				console.log('即将加入房间...', this.data.frompage+":"+this.data.roomid);
				// #ifdef MP-WEIXIN
				return this.JoinRoomWxApp(!this.client.camera);
				// #endif
				// #ifdef H5
				this.initClient();
				let self = this;
				TClient.join({
					roomId:this.data.roomid
				}).then(() => {
					console.log('进房成功：即将初始化并发布本地流');
					self.client.connected = true;
					self.joinuids.push(self.userId);
					if(self.joined){
						self.voiceConnected("TClient.join");
					}
					if(localStream==null){
						self.createStream(true);
					}else{
						self.publishStream();
					}
					core.toast('请使用听筒接听')
				}).catch(error => {
					console.error('进房失败 ' + error);
					self.LeaveRoom();
				});
				// #endif
			},
			LeaveRoom(autoleave=false,toast=""){
				if(this.data.roomid<=0) return core.toast('未成功接入会话','back','error');
				this.hasHangup = true;
				let self = this;
				core.post('trtc/leave',function(res){
					if(self.client.connected){
						if(self.joinuids.indexOf(self.userId)>-1){
							// #ifdef MP-WEIXIN
							self.LeaveRoomWxApp(toast);
							// #endif
							// #ifdef H5
							//关闭麦克风
							try{
								localStream.close();
								// 取消发布本地流
								TClient.unpublish(localStream);
								localStream = null;
							}catch(e){
								//TODO handle the exception
								console.log(e.message);
							}
							self.LeaveRoomH5(toast);
							// #endif
						}
						self.client.connected = false;
					}
					if(!autoleave){
						return core.back();
					}else if(res.type!='success'){
						core.report(res);
					}
				},{roomid:this.data.roomid});
			}
			// #ifdef H5
			,
			async LeaveRoomH5(toast=""){
				let self = this;
				//离开房间
				await TClient.leave().catch(error => {
					console.error('leaving room failed: ' + error);
					TClient.destroy();
				}).then(function(){
					console.log(self.data.rtcinfo.userId + "已退出房间");
					TClient.destroy();
					if(toast!=""){
						core.toast(toast, 'back');
					}
				});
			},
			initClient(){
				if(!this.data.rtcinfo.sdkAppId) return core.toast('未配置AppId','back','error');
				if(this.client.initialized) return true;
				this.client.initialized = true;
				let rtcInfo = this.data.rtcinfo;
				TClient = TRTC.createClient({
					mode:"rtc",
					sdkAppId:rtcInfo.sdkAppId,
					userId:rtcInfo.userId,
					userSig:rtcInfo.userSig
				});
				let self = this;
				TClient.on('stream-added',event => {
					remoteStream = event.stream;
					self.remoteStreamid = remoteStream.getId();
					self.joined = true;
					console.log('远端流增加: ' + remoteStream.getId());
					if(!self.connecting){
						self.voiceConnected('stream-added');
					}
					//订阅远端流
					TClient.subscribe(remoteStream);
				});
				TClient.on('client-banned',event => {
					console.log('用户被动退出房间: ' , event);
					self.LeaveRoom(false);
					//用户被动退出房间
				});
				TClient.on('stream-subscribed',event => {
					const remoteStream = event.stream;
					console.log('远端流订阅成功：' + remoteStream.getId());
			
					// 播放远端流
					remoteStream.play("remote_stream");
				});
				TClient.on('mute-audio',event => {
					core.toast('对方已关闭麦克风');
				});
				TRTC.getCameras().then(cameraList => {
				    console.log("获取到摄像头:", cameraList);
					self.cameras = cameraList;
				}).catch(error => console.error('getCameras error observed ' + error));
				TRTC.getSpeakers().then(function(devices){
					console.log('获取到扬声器：', devices);
					self.speakers = devices;
				}).catch(e=>{
					core.toast('获取扬声器失败');
				});
			},
			publishStream(){
				TClient.publish(localStream).catch(error => {
					console.error('发布本地流失败：', error);
				}).then(function(e){
					console.log('本地流发布成功!');
				});
			},
			createStream(autoPublish=false){
				let self = this;
				localStream = TRTC.createStream({
					userId:this.data.rtcinfo.userId,
					audio: true,
					video: true,
				});
				localStream.initialize().catch(error => {
					console.error('本地流初始化失败：', error);
				}).then(function(e){
					console.log('本地流初始化成功');
					self.client.camera = true;
					localStream.play('local_stream');
					if(autoPublish){
						self.publishStream();
					}
				});
			}
			// #endif
			// #ifdef MP-WEIXIN
			,
			_pusherStateChangeHandler(event){
				this.TrtcClient.pusherEventHandler(event);
			},
			CreatePusherWxapp(){
				const pusherConfig = {
					mode:"RTC",
					enableCamera:true,
					enableMic:true
				}
				const pushRes = this.TrtcClient.createPusher(pusherConfig);
				if(pushRes.pusherAttributes.url!="" && typeof(pushRes.pusherAttributes.url)=='string'){
					this.pusher.url = pushRes.pusherAttributes.url;
					this.client.camera = true;
				}
				console.log("本地推流创建成功：", pushRes.pusherAttributes);
			},
			async JoinRoomWxApp(autoPush=false){
				if(autoPush){
					this.CreatePusherWxapp();
				}
				try{
					const res = this.TrtcClient.enterRoom({
						roomID: this.pusher.roomID,
						sdkAppID: this.data.rtcinfo.sdkAppId,
						userID: this.pusher.userID,
						userSig: this.pusher.userSig
					});
					if(typeof(res.url)=='string' && res.url!=""){
						this.client.connected = true;
						this.joinuids.push(this.userId);
						this.pusher.url = res.url;
						this.client.camera = true;
					}else{
						return this.LeaveRoom(false, "接入失败");
					}
					if(this.joined && !this.connecting){
						this.voiceConnected();
					}
					this.TrtcClient.getPusherInstance().start();
				}catch(e){
					//TODO handle the exception
					console.log(e);
					return core.toast('音频初始化失败', 'back');
				}
				core.toast('请使用听筒接听')
			},
			LeaveRoomWxApp(toast=""){
				const result = this.TrtcClient.exitRoom();
				this.pusher.url = "";
				this.playerList = result.playerList;
				if(toast!=""){
					core.toast(toast, 'back');
				}
			},
			bindTRTCRoomEvent(){
				const TRTC_EVENT = this.TrtcClient.EVENT;
				let self = this;
				let setPlayerAttributesHandler = function(player, options){
					self.player = player;
					self.playerList= self.TrtcClient.setPlayerAttributes(player.streamID, options);
				}
				// 初始化事件订阅
				this.TrtcClient.on(TRTC_EVENT.LOCAL_JOIN, (event) => {
					console.log('* room LOCAL_JOIN', event);
				})
				this.TrtcClient.on(TRTC_EVENT.LOCAL_LEAVE, (event) => {
					console.log('* room LOCAL_LEAVE', event)
				})
				this.TrtcClient.on(TRTC_EVENT.ERROR, (event) => {
					console.log('* room ERROR', event)
				})
				// 远端用户加入
				this.TrtcClient.on(TRTC_EVENT.REMOTE_USER_JOIN, (event) => {
					console.error('* room REMOTE_USER_JOIN', event);
					self.joined = true;
					if(intvalStream==null){
						self.voiceConnected("REMOTE_USER_JOIN");
					}
					if(event.data.playerList){
						self.playerList = event.data.playerList;
					}
				})
				// 远端用户退出
				this.TrtcClient.on(TRTC_EVENT.REMOTE_USER_LEAVE, (event) => {
					console.error('* room REMOTE_USER_LEAVE', event)
				})
				// 远端用户推送音频
				this.TrtcClient.on(TRTC_EVENT.REMOTE_AUDIO_ADD, (event) => {
					console.log('* room REMOTE_AUDIO_ADD', event);
					setPlayerAttributesHandler(event.data.player, { muteAudio: false })
				})
				// 远端用户取消推送音频
				this.TrtcClient.on(TRTC_EVENT.REMOTE_AUDIO_REMOVE, (event) => {
					console.log('* room REMOTE_AUDIO_REMOVE', event);
					setPlayerAttributesHandler(event.data.player, { muteAudio: true });
					console.log("对方关闭了麦克风");
				})
				//远端的用户有新的视频上行。
				this.TrtcClient.on(TRTC_EVENT.REMOTE_VIDEO_ADD, function(event){
					console.log('* room REMOTE_VIDEO_ADD', event.data);
					setPlayerAttributesHandler(event.data.player, { muteVideo: false })
				});
				//远端的用户有视频上行移除。
				this.TrtcClient.on(TRTC_EVENT.REMOTE_VIDEO_REMOVE, function(event){
					console.log('* room REMOTE_VIDEO_REMOVE', event.data);
					setPlayerAttributesHandler(event.data.player, { muteVideo: true })
					console.log("对方关闭了摄像头");
				});
				//被服务端踢出或房间被解散
				this.TrtcClient.on(TRTC_EVENT.KICKED_OUT, function(event){
					console.log('被服务端踢出或房间被解散', event);
					let toast = '通话结束';
					if(!self.connecting){
						toast = (self.frompage=='call'?'':'对方') + '已取消';
					}
					self.LeaveRoom(false, toast);
				});
			}
			// #endif
		}
	}
</script>

<style>
	page{background-color: #8799a3;}
	.remoteStream{position: fixed; top: 0; left: 0; height: 100vh; width: 100vw; z-index: 10;}
	.remoteStream > *, .localStream > * {position: absolute; top: 0; left: 0; width: 100%; height: 100%;}
	.localStream{position: fixed; right: 30upx; top: 120upx; width: 190upx; height: 356upx; z-index: 15; background-color: #8799a3; border-radius: 10upx;}
	.cu-avatar.xl{width: 186upx; height: 186upx;}
	.trtc-bar, .trtc-user{z-index: 20;}
	.trtc-user{position: relative;}
</style>