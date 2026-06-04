<template>
	<view>
		<cu-custom bgColor="bg-grey" :isBack="true">
			<block slot="content"></block>
		</cu-custom>
		<view class="padding-xl">
			<view class="flex justify-center">
				<view class="cu-avatar xl radius" :style="'background-image: url('+data.userinfo.avatar+');'"></view>
			</view>
			<view class="padding text-white text-center">
				<text class="text-xxl">{{data.userinfo.nickname}}</text>
				<view class="text-lg margin-top text-muted" v-if="!connecting">{{frompage=='call'?'正在等待对方接受邀请':'对方邀请您进行语音通话'}}</view>
				<view class="text-lg margin-top text-white" v-else><text>{{duratext}}</text></view>
			</view>
		</view>
		<block v-if="loaded">
			<view class="foot trtc-bar" v-if="data.frompage=='call'||connecting">
				<view class="grid col-3">
					<view class="padding text-center" @click="SwitchVoice()">
						<view class="roundicon" :class="client.voice?'bg-white':'line-white close'">
							<text class="cuIcon-voicefill"></text>
						</view>
						<view class="padding-top-sm">
							<text class="text-muted">{{client.voice?'静音':'打开麦克风'}}</text>
						</view>
					</view>
					<view class="padding text-center" @click="doHangup()">
						<view class="roundicon bg-red rotate">
							<text class="cuIcon-dianhua"></text>
						</view>
						<view class="padding-top-sm">
							<text class="text-muted">{{joined?'挂断':'取消'}}</text>
						</view>
					</view>
					<view class="padding text-center" @click="SwitchEarpiece()">
						<view class="roundicon" :class="client.earpiece?'bg-white':'line-white'">
							<text :class="'cuIcon-notification'+(client.earpiece?'fill':'forbidfill')"></text>
						</view>
						<view class="padding-top-sm">
							<text class="text-muted">{{client.earpiece?'听筒':'免提'}}</text>
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
		<!-- #ifdef MP-WEIXIN -->
		<view class="hidden">
			<live-pusher :url="pusher.url" :enable-camera="false" :enable-mic="client.voice" v-if="pusher.url" />
			<view v-for="(item, index) in playerList" :key="index">
				<live-player mode="RTC" :id="item.id" autoplay :src="item.src" autoplay :sound-mode="client.earpiece?'ear':'speaker'" />
			</view>
		</view>
		<!--  #endif -->
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
	// #ifdef APP-PLUS
	import TrtcCloud from '@/TrtcCloud/lib/index';
	import { TRTCAudioQuality, TRTCAppScene, TRTCRoleType } from '@/TrtcCloud/lib/TrtcDefines';
	import permision from "@/TrtcCloud/permission.js"
	// #endif
	var localStream,TClient,remoteStream;
	var intvalStream = null;
	
	export default {
		computed: mapState(['userId', 'hasLogin']),
		data() {
			return {
				chatid:0,
				duratext:"0:01",
				platform:core.platform,
				frompage:'call',
				type:'normal',
				connecting:false,
				hasHangup:false,
				joined:false,
				loaded:false,
				joinuids:[],
				remoteStreamid:'',
				client:{
					voice:true,
					connected:false,
					duration:1,
					audios:[],
					speakers:[],
					initspeaker:false,
					earpiece:true
				},
				pusher:{
					url:"",
					roomID:"",
					userID:"",
					userSig:""
				},
				playerList:[],
				theme:core.style,
				TrtcClient:null,
				trtcCloud:null,
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
			let self = this;
			// #ifdef APP-PLUS
			this.trtcCloud = TrtcCloud.createInstance();
			this.trtcCloud.on('onEnterRoom', this.createAppStream);
			this.trtcCloud.on('onExitRoom', function(res){
				console.log(self.data.rtcinfo.userId + "已退出房间");
			});
			if(plus.os.name == "iOS"){
				let recorder = permision.judgeIosPermission("record");
				if(!recorder){
					core.toast("未授予麦克风权限");
					//return permision.gotoAppPermissionSetting();
				}
			}else{
				this.requestAndroidPermission('android.permission.RECORD_AUDIO');
			}
			// #endif
			swasocket.TRTC.connecting = true;
			this.initData('trtc/voice',{uid:this.chatid,fp:this.frompage,mode:'voice',dialogtype:this.type},function(res){
				self.loaded = true;
				self.frompage = res.frompage;
				console.log("语音通话场景开始", res);
				uni.$on('SRtrtc', self.SocketRecive);
				// #ifdef MP-WEIXIN
				self.initClientWx(res.rtcinfo);
				// #endif
			});
		},
		onShow() {
			
		},
		onUnload() {
			uni.$off('SRtrtc');
			if(intvalStream!=null){
				clearInterval(intvalStream);
				intvalStream = null;
			}
			swasocket.TRTC.connecting = false;
			if(this.data.roomid>0 && !this.hasHangup){
				this.connecting = false;
				return this.LeaveRoom(true);
			}
			// #ifdef MP-WEIXIN
			wx.setKeepScreenOn({
				keepScreenOn: false,
			});
			// #endif
			// #ifdef APP-PLUS
			this.trtcCloud.stopLocalAudio();
			this.trtcCloud.off('*');
			// #endif
		},
		methods:{
			initData(route, data={}, callback=false){
				var self = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					self.data = res;
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
			SwitchVoice(){
				if(!this.connecting) return core.toast('未开始连接');
				// #ifdef H5
				if(this.client.voice){
					//关闭麦克风
					localStream.muteAudio();
				}else{
					//打开麦克风
					localStream.unmuteAudio();
				}
				// #endif
				// #ifdef APP-PLUS
				this.trtcCloud.muteLocalAudio(this.client.voice?true:false);
				// #endif
				this.client.voice = !this.client.voice;
			},
			SwitchEarpiece(){
				if(this.joinuids.indexOf(this.userId)==-1) return core.toast('未开始连接');
				if(!this.joined) return core.toast('对方尚未接入对话');
				// #ifdef APP-PLUS
				this.trtcCloud.setAudioRoute(this.client.earpiece?1:0);
				// #endif
				// #ifdef H5
				if(!this.client.initspeaker){
					let self = this;
					//获取扬声器列表
					return TRTC.getSpeakers().then(function(devices){
						self.client.initspeaker = true;
						self.client.speakers = devices;
						self.SwitchEarpiece();
					}).catch(e=>{
						core.toast('获取扬声器失败');
						console.log(e);
					});
				}
				console.log("扬声器列表：",this.client.speakers);
				if(this.client.speakers.length<=1){
					return core.toast('切换失败');
				}
				if(this.client.earpiece){
					//关闭扬声器
					remoteStream.setAudioOutput(this.client.speakers[1].deviceId);
				}else{
					//打开扬声器
					remoteStream.setAudioOutput(this.client.speakers[0].deviceId);
				}
				// #endif
				this.client.earpiece = !this.client.earpiece;
			},
			SocketRecive(socketdata){
				if(socketdata.roomid!=this.data.roomid && socketdata.uid!=this.chatid) return;
				console.log('实时通话状态更新:', socketdata);
				if(socketdata.mode=='dismiss' || (socketdata.mode=='leave' && this.joined)){
					if(this.client.connected){
						return this.LeaveRoom(true, '通话已结束');
					}
					return core.toast('对方已取消','back');
				}else if(socketdata.mode=='join'){
					this.JoinRoom();
					this.joinuids.push(socketdata.uid);
					this.connecting = true;
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
				}
			},
			doHangup(){
				//取消、挂断
				let text = this.connecting ? '通话结束' : '已取消';
				return this.LeaveRoom(this.connecting, text);
			},
			doRefuse(){
				//拒绝
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
				// #ifdef APP-PLUS
				return this.JoinRoomApp();
				// #endif
				// #ifdef MP-WEIXIN
				return this.JoinRoomWxApp();
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
					self.voiceConnected();
					self.createStream();
					core.toast('请使用听筒接听')
				}).catch(error => {
					console.error('进房失败 ' + error);
					self.LeaveRoom();
				});
				// #endif
			},
			voiceConnected(){
				this.connecting = true;
				this.startTime();
				if(this.data.frompage!='call'){
					this.joined = true;
				}
			},
			LeaveRoom(autoleave=false,toast=""){
				if(this.data.roomid<=0) return core.toast('未成功接入会话','','error');
				this.hasHangup = true;
				let self = this;
				core.post('trtc/leave',function(res){
					if(self.client.connected){
						if(self.joinuids.indexOf(self.userId)>-1){
							// #ifdef APP-PLUS
							self.LeaveRoomApp(toast);
							// #endif
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
						self.connecting = false;
						self.client.connected = false;
					}
					if(!autoleave){
						return core.back();
					}else if(res.type!='success'){
						core.report(res);
					}
				},{roomid:this.data.roomid});
			}
			// #ifdef MP-WEIXIN
			,
			async JoinRoomWxApp(){
				const pusherConfig = {
					mode:"RTC",
					enableCamera:false,
					enableMic:true
				}
				try{
					this.TrtcClient.createPusher(pusherConfig);
					const options = {
						roomID: this.data.roomid,
						sdkAppID: this.data.rtcinfo.sdkAppId,
						userID: this.data.rtcinfo.userId,
						userSig: this.data.rtcinfo.userSig
					}
					const res = this.TrtcClient.enterRoom(options);
					if(typeof(res.url)!='string' || res.url==""){
						return core.toast('音频初始化失败');
					}
					this.pusher.url = res.url;
					this.TrtcClient.getPusherInstance().start();
					this.client.connected = true;
					this.joinuids.push(this.userId);
					this.voiceConnected();
					core.toast('请使用听筒接听');
				}catch(e){
					//TODO handle the exception
					console.log(e);
					return core.toast('音频初始化失败');
				}
			},
			LeaveRoomWxApp(toast=""){
				let self = this;
				try{
					const res = this.TrtcClient.exitRoom();
					console.log("退出房间结果：", res);
					self.pusher.url = "";
					self.playerList = res.playerList;
					if(toast!=""){
						core.toast(toast, 'back');
					}
				}catch(e){
					//TODO handle the exception
					console.log("退出房间失败：", e);
					core.toast(e.message);
				}
			},
			BindTRTCEvent(){
				const TRTC_EVENT = this.TrtcClient.EVENT;
				let self = this;
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
					console.error('* 远端用户加入', event);
					if(intvalStream==null){
						self.voiceConnected();
					}
				})
				// 远端用户退出
				this.TrtcClient.on(TRTC_EVENT.REMOTE_USER_LEAVE, (event) => {
					console.error('* 远端用户退出', event);
				})
				// 远端用户推送音频
				this.TrtcClient.on(TRTC_EVENT.REMOTE_AUDIO_ADD, (event) => {
					console.log('* room 远端用户推送音频', event);
					const { player } = event.data;
					self.playerList = self.TrtcClient.setPlayerAttributes(player.streamID, { muteAudio: false });
				})
				// 远端用户取消推送音频
				this.TrtcClient.on(TRTC_EVENT.REMOTE_AUDIO_REMOVE, (event) => {
					console.log('* room REMOTE_AUDIO_REMOVE', event);
					//self.playerList = event.data.playerList;
				})
				//远端用户音量状态变更。
				this.TrtcClient.on(TRTC_EVENT.REMOTE_AUDIO_VOLUME_UPDATE, (event) => {
					console.log('* room REMOTE_AUDIO_VOLUME_UPDATE', event);
					self.playerList = event.data.playerList;
				})
			},
			initClientWx(rtcinfo){
				this.pusher = {
					url:"",
					roomID:this.data.roomid,
					userID:rtcinfo.userId,
					userSig:rtcinfo.userSig
				};
				try{
					wx.setKeepScreenOn({
						keepScreenOn: true
					});
					this.TrtcClient = new TRTC();
					this.BindTRTCEvent();
				}catch(e){
					//TODO handle the exception
					console.log(e);
					//throw new Error('TrtcClient init failure', e);
				}
			}
			// #endif
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
					console.log('远端流增加: ' + remoteStream.getId());
					if(intvalStream==null){
						self.voiceConnected();
					}
					//订阅远端流
					TClient.subscribe(remoteStream);
				});
				TClient.on('client-banned',event => {
					console.log('用户被动退出房间: ' , event);
					self.LeaveRoomH5();
					//用户被动退出房间
				});
				TClient.on('stream-subscribed',event => {
					const remoteStream = event.stream;
					console.log('远端流订阅成功：' + remoteStream.getId());
			
					// 播放远端流
					let remotePlayerElement = document.createElement('div');
					remotePlayerElement.id = 'remoteStream' + remoteStream.getId();
					document.body.appendChild(remotePlayerElement);
					remoteStream.play(remotePlayerElement.id);
				});
				TClient.on('mute-audio',event => {
					core.toast('对方已关闭麦克风');
				});
				TClient.on('unmute-audio',event => {
					core.toast('对方已打开麦克风');
				});
			},
			createStream(){
				localStream = TRTC.createStream({
					userId:this.data.rtcinfo.userId,
					audio: true,
					video: false,
				});
				localStream.initialize().catch(error => {
					console.error('初始化本地流失败 ' + error);
				}).then(function(e){
					console.log('初始化本地流成功:即将发布本地流');
					TClient.publish(localStream).catch(error => {
						console.error('发布本地流失败 ' + error);
					}).then(function(e){
						console.log('本地流发布成功:');
					});
				});
			}
			// #endif
			// #ifdef APP-PLUS
			,
			LeaveRoomApp(toast=""){
				this.trtcCloud.exitRoom();
				if(toast!=""){
					core.toast(toast, 'back');
				}
			},
			JoinRoomApp(){
				const params = {
					sdkAppId: this.data.rtcinfo.sdkAppId,  // Please replace with your own sdkAppId
					userId: this.data.rtcinfo.userId,       // Please replace with your own userId
					roomId: this.data.roomid,       // Please replace with your own room number 
					userSig: this.data.rtcinfo.userSig
				};
				this.trtcCloud.enterRoom(params, TRTCAppScene.TRTCAppSceneAudioCall);
				console.log("enterRoom:",params);
				//core.toast('请使用听筒接听');
			},
			createAppStream(e){
				if(e<0){
					console.log("加入房间失败：", e);
					return this.doHangup();
				}
				this.joinuids.push(this.userId);
				this.client.connected = true;
				this.voiceConnected();
				let self = this;
				console.log("成功加入房间，即将发布本地流...", e);
				this.trtcCloud.startLocalAudio(TRTCAudioQuality.TRTCAudioQualitySpeech);
				this.trtcCloud.on('onUserVoiceVolume', function(res){
					console.log("远端用户音量变化：",res);
				});
				this.trtcCloud.on('onRemoteUserEnterRoom', function(res){
					if(intvalStream==null){
						self.voiceConnected();
					}
					console.log("远端用户加入会话：",res);
				});
				this.trtcCloud.on('onUserAudioAvailable', function(res){
					console.log("远端用户麦克风状态变化：",res);
					if(!res.available){
						core.toast('对方已关闭麦克风');
					}else{
						core.toast('对方已打开麦克风');
					}
				});
				this.trtcCloud.on('onSendFirstLocalAudioFrame', function(res){
					console.log("本地语音流发布结果：",res);
				});
			},
			async requestAndroidPermission(permisionID) {
			    var result = await permision.requestAndroidPermission(permisionID);
				console.log(permisionID,result);
			    if(result == -1) {
			        core.toast("麦克风已被禁止", "back", "error");
			    }
			}
			// #endif
		}
	}
</script>

<style>
	page{background-color: #8799a3;}
	.cu-avatar.xl{width: 186upx; height: 186upx;}
</style>