const innerAudioContext = uni.createInnerAudioContext();
var swasocket = {
	Handels:{
	},
	Callbacks:{
		"dialog":null
	},
	TIM:null,
	TRTC:{
		connecting:false,
		userId:''
	},
	ChatId:"",
	Connected:false,
	Playing:false,
	Heartbeat:false,
	Socket:null,
	SocketType:"local",
	UserSign:"",
	Core:{
		platform:'h5',
		userset:{}
	},
	Logout(type='local'){
		if(type=='local'){
			// #ifdef MP-TOUTIAO
			this.Socket.close({
				code:1000,
				reason:"logout"
			});
			// #endif
			// #ifndef MP-TOUTIAO
			uni.closeSocket({
				code:1000,
				reason:"logout"
			});
			// #endif
		}else if(type=='tim'){
			this.Socket.logout();
		}else{
			this.Socket.io.emit('logout');
		}
		this.Connected = false;
	},
	Ready:function(UserSign){
		if(typeof(UserSign)=='string' && UserSign!="") this.UserSign = UserSign;
		innerAudioContext.autoplay = false;
		innerAudioContext.src = this.Core.system.siteroot + '/addons/xfy_whotalk/static/notice.mp3';
	},
	Received:function(socketdata){
		if(socketdata.type=='Message/Heartbeat'){
			this.Heartbeat = true;
			return this.Connected = true;
		}
		//console.log('接收到SOCKET消息：',socketdata);
		if(typeof(socketdata.IsDebug)!='undefined'){
			console.log(socketdata);
		}
		if(typeof(socketdata.type)!='undefined'){
			let Linstener = 'SR'+socketdata.type;
			uni.$emit(Linstener,socketdata);
		}
		if(socketdata.type=='dialog'){
			let chatid = socketdata.dialogtype + "" + socketdata.dialogid;
			let forceVibrate = false;
			if(chatid==this.ChatId && this.ChatId!=""){
				return true;
			}
			if(this.Core.userset.remind_voice==1){
				if(this.Playing){
					forceVibrate = true;
				}else{
					innerAudioContext.src = this.Core.system.siteroot + '/addons/xfy_whotalk/static/notice.mp3';
					innerAudioContext.play();
				}
			}
			if(this.Core.userset.remind_vibrate==1 || forceVibrate){
				uni.vibrateLong();
			}
		}
		if(socketdata.type=='trtcinvite'){
			if(typeof(this.Handels.mainpage=='function')){
				let dialogdata = {
					avatar:socketdata.avatar,
					nickname:socketdata.nickname,
					dialogid:socketdata.uid,
					mid:socketdata.mid,
					type:'dialog',
					dialogtype:'normal',
					spct:socketdata.spct
				}
				uni.$emit('SRdialog', dialogdata);
			}
			console.log('接收到实时语音视频邀请：'+socketdata.mode);
			if(this.TRTC.connecting){
				this.Core.post('trtc/busy',function(res){},{roomid:socketdata.roomid});
			}else{
				this.Core.navito('trtc/'+socketdata.mode,{cid:socketdata.uid,fp:socketdata.frompage});
			}
		}
	},
	Send:function(socketdata={},scktype='local',server=''){
		if(scktype=='local'){
			if(typeof(socketdata.fromId)=='undefined' || !socketdata.fromId){
				socketdata.fromId = this.UserSign;
			}
			//console.log(socketdata);
			// #ifdef MP-TOUTIAO
			this.Socket.send({
				data:JSON.stringify(socketdata),
				success:function(res){
					//console.log('Socket数据发送成功');
				},
				fail:(res)=> {
					console.log('Socket数据发送失败：',res);
				}
			});
			// #endif
			// #ifndef MP-TOUTIAO
			uni.sendSocketMessage({
				data:JSON.stringify(socketdata),
				success:function(res){
					//console.log('Socket数据发送成功');
				},
				fail:(res)=> {
					console.log('Socket数据发送失败：');
					console.log(res);
				}
			});
			// #endif
		}else if(scktype=='tim'){
			if(this.Socket==null) return console.log("TimSocket加载失败"),!1;
			let self = this;
			let promise = this.Socket.sendMessage(socketdata);
			promise.then(function(imResponse) {
				// 发送成功
				//console.log(imResponse);
				//checkoffline(sendchatid,dialogtype);
			}).catch(function(imError) {
				// 发送失败
				let data = JSON.parse(socketdata.payload.text);
				if(imError.code==10015 && typeof(data.dialogid)!='undefined'){
					return self.Core.get('group/timbuild',function(res){
						if(res.type=='success'){
							self.Send(socketdata,scktype,server);
						}
					},{gid:data.dialogid});
				}
				if(imError.code==10007 && data.type=='dialog'){
					return self.Core.get('group/timjoin',function(res){
						if(res.type=='success'){
							self.Send(socketdata,scktype,server);
						}
					},{gid:data.dialogid});
				}
			});
		}else if(server!=''){
			uni.request({
				url:server,
				data:socketdata,
				method:"POST",
				dataType:"jsonp",
				success:function(res){
					console.log(res);
				}
			})
		}
	},
	TimREVOKED:function(event) {
	    // 收到消息被撤回的通知
	    console.log(event.data);
	},
	TIMSdkReady:function(event){
		// TIM接口初始化完成调用
		this.Ready(event);
	}
}
module.exports = swasocket;