import Vue from 'vue'
import siteinfo from "@/siteinfo.js"
import store from '@/store/index.js'
import swasocket from "@/swasocket.js"
var core = {
	version:siteinfo.version,
	agentid:0,
	appid:"",
	apitoken:'',
	platform:'app',
	loading : false,
	appshowing:true,
	inited:false,
	connected:false,
	debug:false,
	inwechat:false,
	jweixin:null,
	device:'android',
	Client:{
		screenHeight:0,
		screenWidth:0,
		windowHeight:0,
		statusBarHeight:0,
		CustomBar:74
	},
	clientSet:{},
	system:{
		name:'Whotalk',
		logo:'../../static/logo.png',
		siteroot:'',
		socket:{
			type:'local',
			server:''
		},
		splash:'../../static/splash.png',
		client:{
			upgrade:false,
			version:"",
			upgradeurl:""
		},
		imgcompress:100,
		defaultimg:"",
		outershare:true,
		releasedate:0,
		groupswitch:false,
		creditswitch:false,
		channelswitch:false,
		runshow:{
			type:"splash",
			data:[],
			tabbar:[],
			button:{
				text:"立即体验",
				url:"auth/index",
				style:"round bg-limegreen"
			}
		},
		auditmode:{
			"status":false
		}
	},
	style:{
		navbg:'bg-gray',
		footbg:'bg-gray',
		actcolor:'limegreen',
		link:'#0081ff',
		chatbg:'#96d9dc',
		active:'#04be02'
	},
	langs:{
		text_back:'返回',
		text_contentover:'松开手开始加载',
		text_contentdown:'下拉加载更早的信息',
		text_confirm_title:'温馨提示',
		text_confirm_delete:"确定要删除吗？",
		text_delete_confirm:'删除后不可恢复，是否确定要删除？',
		text_friends_album:"朋友圈",
		text_groupname:'群组',
		text_empty:'空空如也',
		text_please_input_remark:'请填写备注信息',
		text_alert_app_action:"请在APP上使用此功能",
		text_dialog_delete:"您可以通过联系人重新发起会话",
		msg_no_anymore:"没有更多了~"
	},
	userinfo:{uid:0,groupid:0,token:''},
	socketisalive:false,
	socketretry:0,
	userset:{
		'remind_voice':1,
		'remind_vibrate':1,
		'post_center':0,
		'talk_private':1,
		'validation_apply':0,
		'allow_search':1,
		'album_open':1,
		'language':'zh'
	},
	init:function(userstate=''){
		this.platform = this.initplatform();
		this.apitoken = siteinfo.token;
		var that = this;
		if((userstate!='' && this.userinfo.uid==0) || this.debug){
			this.initsys(userstate);
		}else{
			this.cacheread('_xfy_whotalk_system_session_',function(res){
				that.initpage(res.data);
			},function(e){
				that.initsys();
			});
		}
	},
	initsys:function(userstate='',callback=false){
		let postdata = {v:this.version};
		if(userstate!=''){
			this.userinfo.token = userstate;
			postdata.state = userstate;
		}
		let that = this;
		this.get('appconfig',function(res){
			if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined') return that.report(res);
			if(typeof(res.system)=='undefined') return that.toast('服务器连接失败:undefined');
			if(res.member.uid==0){
				res.member = that.userinfo;
			}
			that.connected = true;
			that.initpage(res);
			// #ifdef APP-PLUS
			//APP检测升级
			let upgradeUrl = typeof(res.system.client.upgradeurl)=='undefined' ? '' : res.system.client.upgradeurl;
			if(res.system.client.upgrade && upgradeUrl!=""){
				that.confirm('新版本（V'+res.system.client.version+'），更多好玩优质内容邀请您体验','发现新版本',function(){
					that.navito(res.system.client.upgradeurl);
				});
			}
			// #endif
			//清理本地缓存
			uni.clearStorage();
			that.cacheset('_xfy_whotalk_system_session_',res);
			if(typeof(callback)=='function') callback();
		},postdata);
	},
	initpage : function(data){
		this.system = data.system;
		this.style = data.style;
		this.style.footbg = data.style.navbg;
		this.langs = data.langs;
		this.userinfo = data.member;
		this.inwechat = data.system.inwechat;
		if(data.system.closesystem){
			let platform = this.platform;
			return this.confirm(data.system.closetext,'系统维护通知',function(){
				if(platform=='app'){
					return plus.runtime.quit();
				}else{
					core.back();
				}
			},{showCancel:false});
		}
		if(data.member.uid>0){
			this.login(data.member);
		}
		let textSize = typeof(data.style.textSize)=='undefined' ? 'sm' : data.style.textSize;
		this.clientSet = this.cachereadsync("_system_client_session_", {});
		if(typeof(this.clientSet.textSize)!='undefined'){
			textSize = this.clientSet.textSize;
		}
		store.commit('clientTextSize', textSize);
		this.inited = true;
		uni.$emit('CoreInited',this);
	},
	inituser : function(){
		if(this.userinfo.uid==0) return false;
		//初始化用户信息
		if(this.userinfo.usersign!='' && typeof(this.system.socket)!='undefined'){
			//初始化socket服务器
			this.initsocket(this.userinfo.usersign,this.system.socket);
		}
	},
	initsocket:function(usersign='',options={}){
		if(usersign=='' || typeof(options.type)=='undefined' || typeof(options.server)=='undefined' || typeof(this.userinfo.usersid)=='undefined'){
			console.log('SOCKET配置未完全')
			return false;
		}
		//引入
		swasocket.UserSign = usersign;
		let that = this;
		if(options.type=='tim'){
			swasocket.TIM = require('tim-js-sdk');
			let confs = {
				SDKAppID: options.server
			};
			let TimSocket = swasocket.TIM.create(confs);
			TimSocket.setLogLevel(1);
			TimSocket.on(swasocket.TIM.EVENT.MESSAGE_RECEIVED, function(event){
				//接收到TIM即时消息
				let socketdata = JSON.parse(event.data[0].payload.text);
				uni.$emit('SwaSocketMessage',socketdata);
				return swasocket.Received(socketdata);
			});
			TimSocket.on(swasocket.TIM.EVENT.ERROR, function(event){
				//TIM报错
				return uni.$emit('SwaSocketError',e);
			});
			TimSocket.on(swasocket.TIM.EVENT.MESSAGE_REVOKED, swasocket.TimREVOKED);
			TimSocket.on(swasocket.TIM.EVENT.KICKED_OUT, function(){
				core.get('checkclient',function (ret) {
					if (ret.type!='success'){
						core.logout();
						core.toast('已在其它设备上登录...','login');
					}
				});
			});
			TimSocket.on(swasocket.TIM.EVENT.SDK_READY, function(event){
				that.socketisalive = true;
				that.SocketOnline(1);
				swasocket.Heartbeat = true;
				swasocket.TIMSdkReady(event);
				uni.$emit('SwaSocketConnect',{fromId:usersign});
			});
			TimSocket.login({userID: this.userinfo.usersid, userSig: usersign});
			swasocket.Socket = TimSocket;
			swasocket.SocketType = 'tim';
		}else if(options.type=='local'){
			let Socket,heartInterval = null;
			// #ifdef MP-TOUTIAO
			Socket = tt.connectSocket({
				url:options.server,
				success: (res) => {
					// 创建成功的回调
					console.log("创建成功", res);
				},
				fail: (e) => {
					// 创建失败的回调
					console.log("Socket连接失败:", res);
				}
			});
			Socket.onOpen(function (res) {
				swasocket.Connected = true;
				console.log('WebSocket连接已打开！用户开始登陆：'+usersign);
				swasocket.Send({
					"Method": "User/Connect",
					"Type": 0,
					"Message": "用户" + usersign + "已连接服务器",
					"fromId": usersign,
					"Data":{
						"SiteRoot":that.system.siteroot,
						"Platform":"ttapp",
						"UserId":that.userinfo.uid
					}
				},options.type);
				that.socketisalive = true;
				swasocket.Ready(usersign);
			});
			Socket.onClose(function(res){
				console.log('Socket连接已断开：',res);
				swasocket.Connected = false;
				that.SocketOnline(0);
				if(res.code!=1000 && res.code!=1006){
					setTimeout(function(){
						return that.initsocket(usersign,options);
					},2000);
				}
			});
			Socket.onMessage(function (res) {
				if(typeof(res)=='string' && that.isJsonString(res)){
					res = JSON.parse(res);
				}
				let data = null;
				if(typeof(res.data)=='object'){
					data = res.data;
				}else{
					data = JSON.parse(res.data);
				}
				if(typeof(data)!='object' || data==null) return false;
				if(data.type==1 && data.method==='User/Connect'){
					swasocket.Connected = true;
					swasocket.Heartbeat = true;
					that.socketisalive = true;
					that.socketretry = 0;
					that.SocketOnline(1);
					console.log('客户端'+usersign+'已上线！');
					//已连接服务器
					uni.$emit('SwaSocketConnect',{fromId:usersign});
					//发送心跳
					heartInterval = setInterval(function(){
						let sendHeart = that.SocketHeart(usersign);
						//console.log("发送心跳结果：",sendHeart);
						if(!sendHeart){
							clearInterval(heartInterval);
							heartInterval = null;
						}
					},15000);
				}
				if(typeof(data.data)=='undefined' || data.data==null) return false;
				if(typeof(data.data.message)!='undefined'){
					let socketdata = typeof(data.data.message)=='object' ? data.data.message : JSON.parse(data.data.message);
					return swasocket.Received(socketdata);
				}
			});
			Socket.onError(function(e){
				console.log('SocketTaskError:',e);
				swasocket.Connected = false;
				Socket.close();
				that.initsocket(usersign, options);
			});
			// #endif
			// #ifndef MP-TOUTIAO
			Socket = uni.connectSocket({
			    url: options.server,
				complete: (res)=> {
					//console.log(res);
				},
				success:(res)=>{
					//console.log(res);
				},
				fail:function(e){
					console.log('SOCKET连接失败：',e);
					that.toast('通讯服务连接失败');
					//1分钟后重新连接
					setTimeout(function(){
						that.initsocket(usersign, options);
					}, 6000)
				}
			});
			Socket.onError(function(e){
				console.log('SocketTaskError:',e);
				swasocket.Connected = false;
				Socket.close({code:3019,reason:e.errMsg});
				return uni.$emit('SwaSocketError',e);
			});
			Socket.onOpen(function(){
				//console.log('WebSocket连接已打开！用户开始登陆：'+usersign);
				swasocket.Send({
					"Method": "User/Connect",
					"Type": 0,
					"Message": "用户" + usersign + "已连接服务器",
					"fromId": usersign,
					"Data":{
						"SiteRoot":that.system.siteroot,
						"Platform":that.platform,
						"UserId":that.userinfo.uid
					}
				},options.type);
				swasocket.Ready(usersign);
			});
			Socket.onMessage(function(res){
				if(typeof(res)=='string' && that.isJsonString(res)){
					res = JSON.parse(res);
				}
				let data = null;
				if(typeof(res.data)=='object'){
					data = res.data;
				}else{
					data = JSON.parse(res.data);
				}
				if(typeof(data)!='object' || data==null) return false;
				if(data.type==1 && data.method==='User/Connect'){
					swasocket.Connected = true;
					swasocket.Heartbeat = true;
					that.socketisalive = true;
					that.socketretry = 0;
					that.SocketOnline(1);
					console.log('客户端'+usersign+'已上线！');
					//已连接服务器
					uni.$emit('SwaSocketConnect',{fromId:usersign});
					//发送心跳
					heartInterval = setInterval(function(){
						let sendHeart = that.SocketHeart(usersign);
						//console.log("发送心跳结果：",sendHeart);
						if(!sendHeart){
							clearInterval(heartInterval);
							heartInterval = null;
						}
					},15000);
				}
				if(typeof(data.data)!='object' || data.data==null) return false;
				if(typeof(data.data.message)!='undefined'){
					let socketdata;
					if(that.isJsonString(data.data.message)){
						socketdata = JSON.parse(data.data.message);
					}else{
						socketdata = {type:data.method,message:data.data.message};
					}
					uni.$emit('SwaSocketMessage',socketdata);
					return swasocket.Received(socketdata);
				}
			});
			Socket.onClose(function(res){
				console.log('Socket连接已断开：',res);
				swasocket.Connected = false;
				swasocket.Heartbeat = false;
				if(heartInterval!=null){
					clearInterval(heartInterval);
					heartInterval = null;
				}
				that.SocketOnline(0);
				uni.$emit('SwaSocketError',res);
				if((res.code!=1000 && res.code!=1006) || typeof(res.code)=='undefined'){
					if(that.socketretry>=5){
						uni.$emit('SwaSocketDisconnect',res);
						return that.toast('通讯服务器连接失败');
					}
					that.socketretry += 1;
					console.log('开始第'+that.socketretry+'次重新连接');
					setTimeout(function(){
						return that.initsocket(usersign,options);
					},2000);
				}
			});
			//#endif
			swasocket.SocketType = 'local';
			swasocket.Socket = Socket;
		}else{
			let VueSocketIO = require('vue-socket.io');
			let Socket = new VueSocketIO({
				debug:false,
				connection:options.server
			});
			// 连接后登录
			Socket.io.on('connect', function(){
				Socket.io.emit('login', usersign);
				console.log('SocketConnectSuccess.');
				swasocket.Ready(usersign);
			});
			swasocket.SocketType = 'third';
			swasocket.Socket = Socket;
			Vue.use(Socket);
		}
		swasocket.Core = this;
	},
	SocketHeart(FromId){
		//if(swasocket.Connected==false) return false;
		if(!swasocket.Heartbeat){
			console.log('已经失去心跳急需抢救');
			uni.closeSocket({
				code:3019,
				reason:'LossHeartbeat',
				success:function(res){
					//console.log(res);
				},
				fail:function(e){
					console.log(e);
				}
			});
			return false;
		}
		swasocket.Heartbeat = false;
		swasocket.Send({
			"Method": "Message/Heartbeat",
			"Type": 0,
			"Message": "",
			"fromId": FromId,
			"data":{}
		});
		return true;
	},
	SocketOnline(isOnline=0){
		if(this.userinfo.uid==0) return true;
		this.get('member/offline', function(res){
			
		}, {isOnline:isOnline});
	},
	initshare : function(title,url,cover='',summary=''){
		// #ifdef H5
		if(this.inwechat){
			let sharedata = {
				title:title,
				desc:summary,
				link:url,
				imgUrl:cover
			};
			core.initjwx(function(jweixin){
				jweixin.updateAppMessageShareData(sharedata);
				jweixin.updateTimelineShareData(sharedata);
				jweixin.onMenuShareWeibo(sharedata);
				jweixin.showMenuItems({
					menuList:['menuItem:share:appMessage','menuItem:share:timeline','menuItem:share:qq','menuItem:share:weiboApp','menuItem:favorite','menuItem:share:facebook','menuItem:share:QZone']
				});
			});
		}
		// #endif
		return {title: title, url: url,cover: cover,summary: summary};
	},
	// #ifdef H5
	initjwx:function(callback,debug=false){
		if(!this.inwechat) return false;
		if(this.jweixin==null){
			let jwx = require('jweixin-module');
			let url = location.href.split('#')[0];
			var that = this;
			this.get('getjssdk',function(res){
				res.jsApiList = ['checkJsApi','chooseImage','downloadVoice','getLocation','hideMenuItems','onMenuShareWeibo','onVoiceRecordEnd','openLocation','pauseVoice','playVoice','scanQRCode','showMenuItems','startRecord','stopRecord','stopVoice','updateAppMessageShareData','updateTimelineShareData','uploadImage','uploadVoice'];
				res.openTagList = ['wx-open-launch-weapp', 'wx-open-launch-app'],
				res.debug = debug || that.debug;
				jwx.config(res);
				jwx.ready(function(){
					that.jweixin = jwx;
					jwx.hideMenuItems({
						menuList: ['menuItem:copyUrl','menuItem:openWithQQBrowser','menuItem:openWithSafari']
					});
					if(typeof(callback)=='function'){
						return callback(jwx);
					}
				});
				jwx.error(function(res){
					that.jweixin = null;
					if(core.debug) core.toast(res.msg);
					console.log(res);
				});
			},{url:url});
			return true;
		}else if(typeof(callback)=='function'){
			callback(this.jweixin);
			return true;
		}
	},
	// #endif
	login : function(data,cache=true){
		if(!data.uid) return this.toast('Unknown UID');
		this.userinfo = data;
		store.commit('login',data);
		this.inituser();
		if(cache){
			var system_session = {system:this.system,member:this.userinfo,style:this.style,langs:this.langs};
			this.cacheset('_xfy_whotalk_system_session_',system_session);
		}
		console.log('用户'+data.nickname+'登录成功！');
		return true;
	},
	logout : function(){
		this.get('member/logout',function(res){
			console.log("即将退出...",res);
		});
		this.userinfo = {uid:0,token:''};
		//退出socket
		swasocket.Logout(this.system.socket.type);
		this.cacheclear();
		store.commit('logout');
		return true;
	},
	scan : function(callback){
		// #ifdef H5
		if(this.inwechat){
			return this.initjwx(function(jwx){
				jwx.scanQRCode({
					needResult:0,
					scanType: ["qrCode","barCode"]
				});
			});
		}else if(typeof(plus)=='undefined'){
			return this.toast(this.langs.text_alert_app_action);
		}
		// #endif
		// #ifdef H5 || APP-PLUS
		return this.navito('index/scan');
		// #endif
		let self = this;
		uni.scanCode({
		    success: function (res) {
		        if(typeof(callback)=='function'){
		        	return callback(res);
		        }else{
					self.scanResult(res.result);
		        }
		    },
			fail:function(e){
				console.log('扫码失败：');
				console.log(e);
			}
		});
	},
	scanResult(text="", navType=0){
		if(text=="") return this.toast("扫描结果为空");
		text = text + "";
		if(text.match(/^((https|http|tel|file|weapp|wap|net|core)?:)[^\s]+/) || text.indexOf('/pages/')>-1){
			return core.navito(res.result, {}, navType);
		}
		this.confirm(text,'扫描结果',function(){
			core.copy(text);
		},{confirmText:'复制到剪切板'});
	},
	static:function(file){
		if(file=='' || file.indexOf('http')===0) return file;
		let siteRoot = this.system.siteroot=='' ? siteinfo.siteroot.replace('/app/index.php', '') : this.system.siteroot;
		return siteRoot + '/addons/xfy_whotalk/static/' + file;
	},
	copy:function(text='', toast='已复制到剪切板'){
		if(text=='') return this.toast('复制的内容为空');
		return uni.setClipboardData({
			data: text,
			success: function () {
				core.toast(toast,'','success');
			},
			fail:function(e){
				console.log(e);
				core.toast('复制失败');
			}
		});
	},
	getpos(CallBack,toast=true){
		let callback = function(res){
			//console.log('获取到位置信息',res);
			uni.$emit('getLocation', res);
			if(typeof(CallBack)=='function') return CallBack(res);
		}
		let incomplete = function(msg="获取位置失败"){
			uni.hideLoading();
			if(toast) core.toast(msg);
		}
		// #ifdef H5
		if(typeof(doPlusGeoLocation)=='function'){
			return doPlusGeoLocation(callback);
		}
		if(this.inwechat){
			return this.initjwx(function(wx){
				wx.getLocation({
					success: function (res) {
						callback({lat:res.latitude,lng:res.longitude});
					},
					cancel: function (res) {
						incomplete('已取消授权');
					},
					fail:function (res) {
						incomplete();
					}
				});
			});
		}
		// #endif
		let self = this;
		uni.getLocation({
			success(ret) {
				callback({lat:ret.latitude,lng:ret.longitude});
			},
			fail(e) {
				// #ifdef H5
				if(typeof(qq)!='undefined' && ALBSKEY!=''){
					let geolocation = new qq.maps.Geolocation(ALBSKEY,self.system.name);
					let options = {timeout: 20000};
					return geolocation.getLocation(function (pos) {
						//console.log('获取当前位置成功:',pos);
						callback({lat:pos.lat,lng:pos.lng});
					}, function (err) {
						console.log('getLocation fail.', err);
						incomplete('获取当前位置失败');
					}, options);
				}
				// #endif
				console.log('getLocation fail.', e);
				incomplete('获取位置信息失败');
			}
		});
	},
	back:function(delta=1){
		// #ifdef H5
		if(this.inwechat) this.jweixin = null;
		// #endif
		let pages = getCurrentPages();
		if(pages.length>1){
			uni.navigateBack({
				delta:delta
			});
		}else{
			uni.reLaunch({
				url:'/pages/index/index'
			})
		}
	},
	cachecloud : function(cname,callback,nocache=false){
		let cachekey = 'xfy_whotalk_'+cname;
		let loadcloud = function(){
			core.get(cname,function(res){
				if(typeof(res.type)!='undefined') return console.log('更新缓存失败('+cname+')：'+res.message);
				core.cacheset(cachekey,res);
				if(typeof(callback)=='function'){
					return callback(res);
				}
			});
		}
		if(nocache) return loadcloud();
		return this.cacheread(cachekey,function(res){
			if(typeof(callback)=='function'){
				return callback(res.data);
			}
		},loadcloud);
	},
	cacheread : function(key,callback,fail){
		let cachekey = key + '_' + siteinfo.uniacid;
		let getstorage = {key:cachekey};
		if(typeof(callback)=='function'){
			getstorage.success = callback;
		}
		getstorage.fail = function(e){
			//console.log('缓存读取失败：', e, cachekey)
			if(typeof(fail)=='function'){
				fail();
			}
		}
		return uni.getStorage(getstorage);
	},
	cachereadsync(key, Default=null){
		let cachekey = key + '_' + siteinfo.uniacid;
		let Cache = uni.getStorageSync(cachekey);
		if(Cache=="") return Default;
		return Cache;
	},
	cacheremove(key){
		let cachekey = key + '_' + siteinfo.uniacid;
		return uni.removeStorageSync(cachekey);;
	},
	cacheset : function(key,data,callBack){
		let cachekey = key + '_' + siteinfo.uniacid;
		let self = this;
		uni.setStorage({key:cachekey,data:data,fail:function(e){
			console.log('写入缓存失败：', e);
		},success:function(){
			//console.log('写入缓存成功:',cachekey);
			if(typeof(callBack)=='function'){
				callBack();
			}
		}});
	},
	cacheclear : function(){
		let storages = {
			system:this.system,
			member:this.userinfo,
			style:this.style,
			langs:this.langs
		}
		//清理本地缓存
		uni.clearStorage();
		//重写本地缓存
		this.cacheset('_xfy_whotalk_system_session_',storages);
	},
	confirm:function(msg,title,callback,options={},Cancel){
		options = options || {};
		options.content = msg;
		if(title) options.title = title;
		if(typeof(options.success)=='undefined' && typeof(callback)=='function'){
			options.success = function(res){
				if(res.confirm){
					callback();
				}else if(res.cancel && typeof(Cancel)=='function'){
					Cancel();
				}
			};
		}
		return uni.showModal(options);
	},
	get : function (u,c,d,t,l){
		return this.request(u,'GET',d,t,c,l);
	},
	post : function (u,c,d,t,l) {
		return this.request(u,'POST',d,t,c,l);
	},
	serve : function(server, route, callBack, data={}, type='GET'){
		let baseurl = baseurl = siteinfo.siteroot + '?server='+server;
		baseurl += '&i='+siteinfo.uniacid;
		if(route!="") baseurl += "&ctrl="+route;
		baseurl = baseurl.replace('app/index.php','api/server.php');
		let self = this;
		return this.request(baseurl,type,data,'json',function(res){
			console.log("微服务请求接口",baseurl,res);
			if(res.type!="success") return self.report(res);
			if(typeof(callBack)=='function'){
				callBack(res.message);
			}
		});
	},
	urlParams : function(url, name){
		var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
		var r = url.match(reg);
		return r == null ? null : unescape(r[2]);
	},
	url : function(route="",action='api'){
		if (route.indexOf('http')==0 || route.indexOf('/index.php')>-1) return route;
		route = route.replace('/','.');
		var baseurl = siteinfo.siteroot + '?i='+siteinfo.uniacid+'&m=xfy_whotalk';
		if(action=='api'){
			baseurl = baseurl.replace('app/index.php','app/swaapi.php');
		}else{
			baseurl += '&c=entry&do=' + action;
			//#ifdef H5
			if(baseurl.indexOf('http')!==0){
				baseurl = this.system.siteroot + baseurl;
			}
			//#endif
		}
		if (route=='' || !route) return baseurl;
		return baseurl + '&r=' + route + '&unifrom='+this.platform;
	},
	page : function(url='index/index',data = {}){
		if(url=='' || url=='#' || url.indexOf('javascript:')==0) return '';
		// ***#/pages/***
		if(url.indexOf('#/pages/')>0){
			let urls = url.split('#/pages/');
			url = urls[1];
		}else{
			// *** /#/pages/ ***
			url = url.replace('#/pages/','',url);			
		}
		for (let key in data) {
			url += (url.indexOf('?') == -1 ? '?' : '&') + key + '=' + data[key]
		}
		if(url.indexOf('http')==0){
			url = '/pages/web/index?url=' + this.encodeurl(url);
		}
		if(url.indexOf('/pages/')!=0) url = '/pages/' + url;
		return url;
	},
	encodeurl(uri=''){
		uri = uri.replace(/\//g,'%2F');
		uri = uri.replace(':','%3A');
		uri = uri.replace(/\&/g,'%26');
		uri = uri.replace(/\=/g,'%3D');
		return uri.replace('?','%3F');
	},
	upload:function(file,callback,type="image",apiname="attach/upload",params={}){
		let formdata = params || {};
		formdata.submit = 1;
		formdata.unifrom = this.platform;
		formdata.type = type;
		formdata.deviceplatform = this.device;
		formdata.uploadfile = "file";
		let showLoad = typeof(params.loading)=='undefined' ? true : params.loading;
		if(this.userinfo.uid>0){
			formdata.authkey = this.userinfo.token;
		}else{
			formdata.uniauth = "unlogin";
		}
		if(!this.loading && showLoad){
			this.loading = true;
			uni.showLoading({
				mask:true,
				title:'上传中...'
			});
		}
		var that = this;
		let UploadObj = {
			url: core.url(apiname), 
			name: 'file',
			formData: formdata,
			success: function(uploadres){
				var res = JSON.parse(uploadres.data);
				if(res.type!='success') return that.report(res);
				if(typeof(callback)=='function'){
					return callback(res.message);
				}
				console.log(res);
			},
			fail:function(e){
				that.toast('上传失败');
				console.log('文件上传失败：');
				console.log(e);
			},
			complete:function(){
				if(that.loading && showLoad){
					uni.hideLoading();
					that.loading = false;
				}
			}
		};
		if(typeof(file)=='string'){
			UploadObj.filePath = file;
		}else{
			UploadObj.file = file;
		}
		return uni.uploadFile(UploadObj);
	},
	navitoWxapp(appInfo){
		let url = decodeURIComponent(appInfo), webUrl = "", originalId = "";
		let WxappInfo = url.split(",");
		if(typeof(WxappInfo[2])!='undefined'){
			webUrl = WxappInfo[2];
		}
		// #ifdef H5 || APP-PLUS
		if(typeof(WxappInfo[3])!='undefined'){
			originalId = WxappInfo[3];
		}
		return this.navito("wxapp/index", {
			appid:WxappInfo[0],
			path:encodeURIComponent(WxappInfo[1]),
			weburl:webUrl,
			original:originalId
		});
		// #endif
		return uni.navigateToMiniProgram({
			appId:WxappInfo[0],
			path:WxappInfo[1],
			fail:function(e){
				//console.log("小程序打开失败：", e);
				return webUrl=="" ? core.toast("打开小程序失败") : navito(webUrl);
			}
		});
	},
	navito : function(page='',data={},type = 0){
		if(page.indexOf('core::')===0){
			let propertys = page.split("::");
			let instance = this[propertys[1]];
			if(typeof(instance)!='function') return instance;
			if(typeof(propertys[2])!='undefined'){
				return this[propertys[1]](propertys[2]);
			}
			return this[propertys[1]]();
		}
		if(page.indexOf('tel:')===0){
			return uni.makePhoneCall({
				phoneNumber:page.replace('tel:','')
			});
		}
		if(page.indexOf('file:')===0){
			return this.navito('player/storage', {
				url:this.encodeurl(page.replace('file:',''))
			});
		}
		if(page.indexOf('weapp:')===0){
			return this.navitoWxapp(page.replace('weapp:',''));
		}
		if(page.indexOf('wap:http')==0){
			page = page.replace('wap:','');
			// #ifdef H5
			return window.open(page,'_blank');
			// #endif
		}
		if(page.indexOf('net:http')==0){
			let weburl = page.replace('net:','');
			if(Object.getOwnPropertyNames(data).length>0){
				for (let key in data) {
					weburl += (weburl.indexOf('?') == -1 ? '?' : '&') + key + '=' + data[key]
				}
			}
			// #ifdef APP-PLUS
			return plus.runtime.openURL(weburl,function(e){
				core.navito(weburl);
			})
			// #endif
			// #ifdef H5
			return window.open(weburl,'_blank');
			// #endif
			page = weburl;
			data = {};
		}
		var url = this.page(page,data);
		if(url=='') return false;
		// #ifdef APP-PLUS
		url = url.replace("trtc/video", "trtc/videox");
		// #endif
		// #ifdef H5
		if(this.inwechat) this.jweixin = null;
		// #endif
		switch (type) {
			case 0:
				uni.navigateTo({
					url,
					fail: console.log
				})
				break;
			case 1:
				uni.redirectTo({
					url,
					fail: console.log
				})
				break;
			case 2:
				uni.reLaunch({
					url,
					fail: console.log
				})
				break;
			case 3:
				uni.navigateBack({
					delta: url,
					fail: console.log
				})
				break;
			case 4:
				uni.switchTab({
					url: url,
					fail: console.log
				})
				break;
		}
	},
	request : function (u,m,d,t,c,l) {
		var url = this.url(u);
		var method = m ? m : 'GET';
		var data = d ? d : {};
		var datatype = t ? t : 'json';
		data.inajax = 1;
		data.deviceplatform = this.device;
		if (method=='POST'){
			data.submit = 1;
		}
		if(this.userinfo.uid>0){
			data.authkey = this.userinfo.token;
		}
		if (l && !this.loading){
			this.loading = true;
			uni.showLoading({
				mask:true,
				title:'加载中'
			});
		}
		let self = this;
		let CallBack = function(res){
			if(res.data){
				if(typeof(res.data.releasedate)!='undefined' && u!='appconfig' && u!='appmain'){
					if(res.data.releasedate!=self.system.releasedate){
						self.initsys();
					}
				}
			}
			if (typeof(c)=='function'){
				return c(res.data);
			}
			self.report(res.data);
			if(self.loading){
				self.loading = false;
				uni.hideLoading();
			}
		}
		if(typeof(doPlusRequest)=='function'){
			return doPlusRequest(url,method,datatype,data,CallBack,self);
		}
		return uni.request({
			url:url,
			method:method,
			dataType:datatype,
			data:data,
			success:CallBack,
			fail:function(e){
				uni.showToast({
					title:'网络链接失败',
					icon:'none'
				});
			},
			complete:function(res){
				if(self.loading){
					self.loading = false;
					uni.hideLoading();
				}
			}
		});
	},
	report : function(res, initpage = false){
		if(res===null) res = {message:"敬请期待",redirect:'back',type:"error"};
		if(typeof(res)!='object' && this.isJsonString(res)){
			res = JSON.parse(res);
		}
		if (typeof(res)!='object') return false;
		if(res.redirect=='' && initpage){
			res.redirect = 'back';
		}
		if(typeof(res.url)!='undefined'){
			var act = typeof(res.type)=='undefined' ? 'undefined' : res.type;
			if (act=='redirect'){
				return this.navito(res.url, 1);
			}
		}
		if(typeof(res.message)!='undefined'){
			this.toast(res.message,res.redirect,res.type);
		}
	},
	toast : function(m,u,t,d=1){
		var toastinfo = {title:m,duration:1200};
		if(this.loading){
			uni.hideLoading();
			this.loading = false;
		}
		var that = this;
		if(u){
			let redirect = function(){
				uni.hideToast();
				if(u=='back'){
					that.back();
				}else if(u=='home'){
					that.navito('index/index', {}, 2);
				}else if(u=='login'){
					let _options = {act:'login'};
					let Pages = getCurrentPages();
					let curpage = Pages[(Pages.length-1)];
					if(typeof(curpage)!='undefined'){
						_options.rd = that.page(curpage.route.replace('pages/',''),curpage.options);
						_options.rd = encodeURIComponent(_options.rd);
					}
					that.navito('auth/index', _options, 1);
				}else{
					that.navito(u,{},d);
				}
			};
			if(t=='redirect'){
				return redirect();
			}
			toastinfo.complete = function(){
				setTimeout(redirect,1200);
			}
		}
		toastinfo.icon = (t=='success' || t=='loading') ? t : 'none';
		uni.showToast(toastinfo);
	},
	gmTime(time){
		if(time==0) return '刚刚';
		let timestamp = time*1000;
		var d = new Date(timestamp), now = new Date();
		if(now.getTime()-timestamp<10000) return '刚刚';
		//console.log(timestamp, now.getTime(), d.toString());
		let year = d.getFullYear();
		let day = d.getDate();
		let hours = d.getHours();
		let month = d.getMonth() + 1;
		let minute = d.getMinutes();
		if(minute<10){
			minute = "0" + minute + "";
		}
		let datetime = month + "月" + day + "日 " + (hours>19?'晚上':'') + hours+':'+minute;
		if(year==(now.getFullYear())){
			let nowDate = now.getDate(), nowMonth = now.getMonth() + 1;
			if(month==nowMonth && day==nowDate){
		    	//当天的
		    	datetime = (hours>19?'晚上':'') + hours+':'+minute;
		    }else if(nowMonth==month){
		    	if(nowDate-day==1){
		        	datetime = "昨天 " + (hours>19?'晚上':'') + hours+':'+minute;
		        }
		    }
		}else{
			datetime = year + "年" + datetime;
		}
		return datetime;
	},
	launchapp(urlschemes,route='index/index'){
		//其它客户端唤醒APP，仅支持H5
		if(!urlschemes) return false;
		// #ifdef H5
		let appurl = urlschemes+'://'+route;
		if(document.getElementById('AppUrlSchemes')){
			document.getElementById('AppUrlSchemes').src = appurl;
		}else{
			let div = document.createElement("div");
			div.setAttribute("style", "display: none;visibility: hidden; width: 0; height: 0; overflow: hidden;");
			div.innerHTML = '<iframe id="AppUrlSchemes" src="'+appurl+'" frameborder="0" width="0" height="0"></iframe></div>';
			document.body.appendChild(div);
		}
		// #endif
		return true;
	},
	isJsonString : function(str){
		try {
			if(typeof(JSON.parse(str)) == "object") {
				return true;
			}
		} catch(e) {
		}
		return false;
	},
	initplatform(){
		//#ifdef H5
		return 'h5';
		//#endif
		//#ifdef APP-PLUS || APP-PLUS-NVUE
		return 'app';
		//#endif
		//#ifdef MP-WEIXIN
		return 'wxapp';
		//#endif
		//#ifdef MP-ALIPAY
		return 'aliapp';
		//#endif
		//#ifdef MP-BAIDU
		return 'bdapp';
		//#endif
		//#ifdef MP-TOUTIAO
		return 'ttapp';
		//#endif
		//#ifdef MP-QQ
		return 'qqapp';
		//#endif
		//#ifdef MP-360
		return 'qhapp';
		//#endif
	}
}
module.exports = core;