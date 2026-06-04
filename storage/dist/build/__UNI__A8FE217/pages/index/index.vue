<template>
	<view class="contianer" :class="['wp-'+platform, 'page-text-'+textSize]">
		<block v-if="hasLogin && navkey!='moment' && loaded">
			<cu-custom v-if="!(inwechat && platform=='h5')" :bgColor="theme.bgnav" :isBack="false" Barborder="solid-bottom">
				<!-- #ifdef MP -->
				<block slot="left">
					<view class="text-xxl action" @click="naviTo('album/post')" v-if="(navkey=='more' || navkey=='moment') && data.switchs.albumpost">
						<text class="cuIcon-cameraadd"></text>
					</view>
					<view class="text-xxl action" @click="showmodal='popupover'" v-else-if="navigation.popup.length>0">
						<text class="cuIcon-roundadd"></text>
					</view>
				</block>
				<!-- #endif -->
				<block slot="content">{{data.title}}</block>
				<!-- #ifndef MP -->
				<block slot="right">
					<view class="text-xxl action right" @click="naviTo('album/post')" v-if="(navkey=='more' || navkey=='moment') && data.switchs.albumpost">
						<text class="cuIcon-cameraadd"></text>
					</view>
					<block v-else>
						<view class="text-xxl action right">
							<text class="cuIcon-search" @click="naviTo('search/index')" v-if="data.switchs.search"></text>
							<text class="cuIcon-roundadd" @click="showmodal='popupover'" v-if="navigation.popup.length>0"></text>
						</view>
					</block>
				</block>
				<!-- #endif -->
			</cu-custom>
		</block>
		<aboutus v-if="!hasLogin || (!isnetwork && !loaded)" :auditmode="auditmode" :NetworkStatus="isnetwork" :refresh="momentRefresh" :btnstyle="theme.actcolor" :runshow="runshows"></aboutus>
		<view v-else>
			<view class="splash bg-gray" v-if="!inited">
				<image :src="splash" mode="aspectFill"></image>
				<view class="loader">
					<view class="cu-load loading text-white"></view>
				</view>
			</view>
			<view class="main-pages">
				<view class="light bg-red padding-sm text-lg flex justify-between" v-if="disconnect" @click="socketretry()">
					<view>
						<text class="cuIcon-warnfill"></text><text class="margin-left-sm text-red">{{isnetwork?'服务器连接失败':'网络连接不可用'}}</text>
					</view>
					<view class="action text-blue">
						<text>重试</text>
					</view>
				</view>
				<view class="mainpageitem" :class="navkey=='dialogs'?'active':''">
					<advs :advs="advs.maintop"></advs>
					<view class="cu-list menu-avatar dialoglist" v-if="data.dialogs.length>0">
						<view class="cu-item" :class="item.istop?'istop':''" @longpress="doActionDialog(index)" @click="naviTo(item.url)" v-for="(item, index) in data.dialogs" :key="index">
							<view class="cu-avatar radius lg" :style="'background-image:url('+item.avatar+');'"></view>
							<view class="content">
								<view class="dialog-title"><view class="text-cut">{{item.title}}</view></view>
								<view class="text-gray text-sm flex"><view class="text-cut"><text class="text-red margin-right-xs" v-if="item.isDraft">[草稿]</text>{{item.nicknametext}}{{item.message}}</view></view>
							</view>
							<view class="action">
								<view class="text-grey text-xs">{{item.datetime}}</view>
								<view class="cu-tag round bg-red sm" v-if="item.noread>0">{{item.noread}}</view>
							</view>
						</view>
					</view>
					<view class="padding solid-top" v-else>
						<view class="text-empty">空无一人，快找个人聊聊吧~</view>
					</view>
				</view>
				<view class="mainpageitem" :class="navkey=='contacts'?'active':''">
					<view class="cu-list menu">
						<view class="cu-item arrow" @click="naviTo('friend/news')">
							<view class="content">
								<text class="cuIcon-friendadd text-grey text-xl"></text>
								<text class="text-lg">新的朋友</text>
							</view>
							<view class="action" v-if="data.newfriends>0">
								<view class="cu-tag round bg-red">{{data.newfriends}}</view>
							</view>
						</view>
						<view class="cu-item arrow" v-if="data.switchs.group" @click="naviTo('group/mine')">
							<view class="content">
								<text class="cuIcon-group text-grey text-xl"></text>
								<text class="text-lg">{{text.text_groupname}}</text>
							</view>
						</view>
					</view>
					<view class="margin-top" v-if="friends.data.length>0">
						<scroll-view scroll-y class="indexes" :scroll-into-view="'indexes-'+ indexes.listCurID" :style="[{height:'calc(100vh - '+ CustomBar + 'px - '+indexes.spacehei+'px)'}]" :scroll-with-animation="true" :enable-back-to-top="true">
							<block v-for="(item,index) in friends.data" :key="index">
								<view :class="'indexItem-' + item.letter" :id="'indexes-' + item.letter" :data-index="item.letter">
									<view class="padding-sm">{{item.letter}}</view>
									<view class="cu-list menu-avatar no-padding">
										<view class="cu-item" @click="naviTo('member/index',{uid:value.fuid})" v-for="(value,key) in item.list" :key="key">
											<view class="cu-avatar round" :style="'background-image:url('+value.avatar+');'"></view>
											<view class="content">
												<view><view class="text-cut text-lg">{{value.remark}}</view></view>
											</view>
										</view>
									</view>
								</view>
							</block>
							<view class="padding text-center">
								<text class="text-lg">共{{friends.totals}}位好友</text>
							</view>
						</scroll-view>
						<view class="indexBar" :style="[{height:'calc(100vh - ' + indexes.CustomBar + 'px - '+indexes.spacehei+'px)'}]">
							<view class="indexBar-box" @touchstart="tStart" @touchend="tEnd" @touchmove.stop="tMove">
								<view class="indexBar-item" v-for="(item,index) in friends.data" :key="index" :id="index" @touchstart="getCur" @touchend="setCur"> {{item.letter}}</view>
							</view>
						</view>
						<view v-show="!hidden" class="indexToast">
							{{indexes.listCur}}
						</view>
					</view>
					<view class="text-empty" v-else>
						<text v-if="friends.inited">{{text.text_empty}}</text>
						<text v-else class="cu-load load-cuIcon loading">好友加载中...</text>
					</view>
				</view>
				<view class="mainpageitem" :class="navkey=='more'?'active':''">
					<view class="cu-list menu" v-if="data.switchs.square || data.switchs.album">
						<view v-if="data.switchs.square" class="cu-item arrow" @click="naviTo('album/index')">
							<view class="content">
								<text class="cuIcon-discover text-red" style="font-size: 40upx;"></text>
								<text class="text-lg">广场</text>
							</view>
						</view>
						<view v-if="data.switchs.album" class="cu-item arrow" @click="naviTo('album/index',{friend:1})">
							<view class="content">
								<text class="ifont icon-pengyouquan" style="font-size: 38upx;"></text>
								<text class="text-lg">{{text.text_friends_album}}</text>
							</view>
						</view>
					</view>
					<view class="cu-list menu" v-if="data.switchs.groupentry">
						<view class="cu-item arrow" @click="naviTo('group/index')">
							<view class="content">
								<text class="cuIcon-group text-blue" style="font-size: 40upx;"></text>
								<text class="text-lg">全部群组</text>
							</view>
						</view>
					</view>
					<discovery :advsdata="advs.discovery" :switchs="data.switchs" :navigation="navigation.more"></discovery>
				</view>
				<view class="mainpageitem" :class="navkey=='mine'?'active':''">
					<view class="cu-list menu-avatar solid-top">
						<view class="cu-item arrow" @click="naviTo('member/account')">
							<view class="cu-avatar radius lg" :style="'background-image:url('+data.userinfo.avatar+');'"></view>
							<view class="content">
								<view>
									<view class="text-cut text-xl">
										{{data.userinfo.nickname}}
										<text class="cu-tag bg-orange round margin-left-sm light">{{data.userinfo.grouptitle}}</text>
									</view>
								</view>
								<view class="text-gray text-sm flex" v-if="data.userid!=''">
									<view class="text-cut">{{data.userid}} : {{data.userid=='ID'?data.userinfo.uid:data.userinfo.userid}}</view>
								</view>
							</view>
							<view class="action">
								<view class="text-grey text-lg">
									<text class="cuIcon-qr_code"></text>
								</view>
							</view>
						</view>
					</view>
					<view class="cu-list menu" v-if="data.switchs.credit">
						<view class="cu-item arrow" @click="naviTo('credit/index')">
							<view class="content">
								<text class="cuIcon-pay" :class="'text-'+theme.actcolor" style="font-size: 40upx;"></text>
								<text class="text-lg">钱包</text>
							</view>
						</view>
					</view>
					<view class="upgradevip margin" v-if="data.upgradevip>0">
						<view class="radius bg-black shadow" @tap="naviTo('member/vip')">
							<view class="flex text-gold align-center padding">
								<view class="title flex-sub text-xxl text-bold">VIP专区</view>
								<view class="action">
									<text class="margin-right-sm">尊享VIP特权</text>
									<button class="cu-btn round bg-gold">{{data.upgradevip==1?'立即开通':'立即升级'}}</button>
								</view>
							</view>
						</view>
					</view>
					<view class="cu-list menu">
						<view class="cu-item arrow" @click="naviTo('album/index',{uid:data.userinfo.uid})">
							<view class="content">
								<text class="cuIcon-pic text-mauve" style="font-size: 40upx;"></text>
								<text class="text-lg">相册</text>
							</view>
						</view>
						<view class="cu-item arrow" @click="naviTo('member/collection')">
							<view class="content">
								<text class="cuIcon-favor text-orange" style="font-size: 40upx;"></text>
								<text class="text-lg">收藏</text>
							</view>
						</view>
						<!-- #ifndef H5 -->
						<view class="cu-item arrow" @click="naviTo('player/storage')">
							<view class="content">
								<text class="cuIcon-down text-cyan" style="font-size: 40upx;"></text>
								<text class="text-lg">下载</text>
							</view>
						</view>
						<!-- #endif -->
						<view class="cu-item arrow" v-if="data.switchs.group" @click="naviTo('member/group')">
							<view class="content">
								<text class="cuIcon-group text-blue" style="font-size: 40upx;"></text>
								<text class="text-lg">群组</text>
							</view>
							<view class="action" v-if="data.groupapplys>0">
								<view class="cu-tag round bg-red sm">{{data.groupapplys}}</view>
							</view>
						</view>
					</view>
					<view class="cu-list menu">
						<view class="cu-item arrow" @click="naviTo('member/setting')">
							<view class="content">
								<text class="cuIcon-settings text-cyan" style="font-size: 40upx;"></text>
								<text class="text-lg">设置</text>
							</view>
						</view>
						<view class="cu-item arrow" v-if="client.upgrade" @click="checkUpgrade()">
							<view class="content">
								<text class="cuIcon-pullup text-orange" style="font-size: 40upx;"></text>
								<text class="text-lg">检测更新</text>
							</view>
							<view class="action">
								<view class="cu-tag round bg-red">发现新版本</view>
							</view>
						</view>
					</view>
				</view>
				<view class="mainpageitem near-page" :class="navkey=='nearby'?'active':''">
					<nears :autoInit="momentRefresh" :tabbar="true" navstyle="solid-bottom" @editinfo="naviTo('ranking/profile', {fp:'near'})" @showshare="showshare=true" v-if="navkey=='nearby'"></nears>
				</view>
				<dynamic class="mainpageitem" :class="navkey=='moment'?'active':''" @showshare="showshare=true" :refresh="momentRefresh" :navigation="navigation" :switchs="data.switchs"></dynamic>
			</view>
			<view class="cu-bar tabbar foot" :class="theme.footbg" v-if="data.mainnavs.length>0">
				<view class="action" :class="[(navkey==item.key?'cur text-'+theme.actcolor:''),item.extra]" @click="checkoutbar(index)" v-for="(item, index) in data.mainnavs" :key="index">
					<view :class="item.icon+(navkey==item.key?'fill':'')">
						<view class="cu-tag badge" v-if="item.key=='dialogs' && newMsg>0">{{newMsg}}</view>
						<view class="cu-tag badge" v-if="item.key=='contacts' && data.newfriends>0">{{data.newfriends}}</view>
						<block v-if="item.key=='mine'">
							<view class="cu-tag badge" v-if="data.groupapplys>0">{{data.groupapplys}}</view>
							<view class="cu-tag badge" v-else-if="client.upgrade"></view>
						</block>
					</view>
					{{item.name}}
				</view>
			</view>
			<!-- #ifdef H5 -->
			<view class="suspense" @click="showmodal='popupover'" v-if="inwechat">
				<text class="bg-white" :class="['text-'+theme.actcolor,'cuIcon-'+(showmodal=='popupover'?'rounddown':'roundaddfill')]"></text>
			</view>
			<!-- #endif -->
			<view class="popupover" :class="[(showmodal=='popupover'?'active':''),(inwechat?'inwechat':'')]" v-if="navigation.popup.length>0">
				<view class="popupover-bg" @tap="showmodal=''"></view>
				<view class="radius popcontainer shadow-blur">
					<view class="cu-list menu">
						<view class="cu-item" @click="naviTo(item.url)" v-for="(item,index) in navigation.popup" :key="index">
							<view class="content">
								<text :class="item.icon==''?'cuIcon-link':item.icon"></text>
								<text>{{item.name}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="cu-modal bottom-modal" :class="showmodal=='actiondialog'?'show':''" v-if="data.dialogs.length>0">
				<view class="cu-modal-bg" @tap="showmodal=''"></view>
				<view class="cu-dialog">
					<view class="padding">
						<view class="flex flex-direction">
							<button @click="doRmDialog()" class="cu-btn bg-red lg">删除</button>
							<button @click="doSetTop()" class="cu-btn lg margin-top" :class="'bg-'+theme.actcolor">{{data.dialogs[actionindex].istop?'取消':''}}置顶</button>
							<button @click="showmodal=''" class="cu-btn bg-grey lg margin-top">取  消</button>
						</view>
					</view>
				</view>
			</view>
		</view>
		<advs :isPopup="true" :advs="advs.splash"></advs>
		<share :shareinfo="shareinfo" @closeshare="showshare=false" :showshare="showshare"></share>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import store from '@/store/index.js'
	import core from "@/core.js"
	import swasocket from "@/swasocket.js"
	import dynamic from "@/components/util/dynamic.vue"
	import aboutus from "@/components/util/aboutus.vue"
	import advs from "@/components/util/advs.vue"
	import share from "@/components/util/share.vue"
	import nears from "@/components/util/nears.vue"
	import discovery from "@/components/util/discovery.vue"
	// #ifdef APP-PLUS
	const jpushModule = uni.requireNativePlugin("JG-JPush");
	// #endif
	
	export default {
		components: {aboutus,advs,share, nears, discovery, dynamic},
		computed: mapState(['userId', 'hasLogin', 'userName', 'userSign','newMsg','Chatid', 'Chattype', 'textSize']),
		data() {
			return {
				runshows:core.system.runshow,
				inited:false,
				inwechat:core.inwechat,
				isalive:core.socketisalive,
				disconnect:false,
				isnetwork:true,
				loaded:false,
				runConstruct:false,
				jpushState:false,
				theme:core.style,
				splash:core.system.splash,
				client:core.system.client,
				actionindex:0,
				advs:{
					'maintop':[],
					'splash':[],
					'discovery':[]
				},
				auditmode:{
					status:false,
					text:"立即体验",
					url:'auth/index',
					login:''
				},
				dialogs:[],
				momentRefresh:0,
				navkey:"",
				redirecturl:"",
				platform:core.platform,
				showmodal:'',
				showshare:false,
				shareinfo:{
					url:'',
					title:"附近的人",
					providers:4,
					provider:{}
				},
				hidden:true,
				text:core.langs,
				indexes:{
					listCurID:'',
					CustomBar:this.CustomBar,
					listCur:'',
					boxTop:0,
					barTop:0,
					spacehei:165
				},
				friends:{
					inited:false,
					data:[],
					totals:0
				},
				navigation:{
					more:[],
					popup:[]
				},
				data:{
					title:"首页",
					dialogs:[],
					mainnavs:[],
					noreads:0,
					newfriends:0,
					groupapplys:0,
					navkey:"",
					switchs:{
						credit:core.system.creditswitch,
						square:true,
						album:true,
						albumpost:true,
						group:core.system.groupswitch,
						groupentry:true,
						search:true
					},
					upgradevip:0,
					userid:"",
					userinfo:{
						uid:0,
						nickname:"未登录",
						avatar:core.system.logo,
						userid:"",
						grouptitle:"默认用户",
						groupid:0
					},
					userset:{},
					shareinfo:{
						"title":"",
						"desc":"",
						"cover":"",
						"url":""
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.nk)!='undefined'){
				this.navkey = options.nk;
			}
			if(typeof(options.rd)!='undefined'){
				this.redirecturl = options.rd;
			}
			this.initData();
			let self = this;
			uni.$on('NetworkStatusChange', function(res){
				console.log("网络状态发生变化：", res);
				if(!res.isConnected){
					self.isalive = false;
					self.disconnect = true;
				}
				self.isnetwork = res.isConnected;
			});
			// #ifndef H5
			uni.getNetworkType({
				success:function(e) {
					if(e.networkType=='none'){
						self.isnetwork = false;
					}
				}
			});
			// #endif
		},
		onShow() {
			if(!this.loaded || !this.hasLogin) return false;
			//已登录未初始化
			if(!this.inited){
				return this.initData();
			}
			//已初始化刷新首页数据
			this.doRefresh();
		},
		onUnload() {
			uni.$off('DialogPost');
			uni.$off('DialogLaunch');
			uni.$off('SRdialog',this.socketrecive);
			uni.$off('SRmakefriend',this.socketrecive);
			uni.$off('SRgroupapplys',this.socketrecive);
			uni.$off('SRrmdialog',this.socketrecive);
			uni.$off('CoreInited');
			uni.$off('PEviewfriend');
			uni.$off('HaveDraft');
			uni.$off('checkoutBar');
		},
		onShareAppMessage(e){
			return {
				title:this.data.shareinfo.title,
				path:core.page('index/index',{fromuid:this.userId}),
				imageUrl:this.data.shareinfo.cover,
				desc:this.data.shareinfo.desc
			}
		},
		onShareTimeline(e){
			return {
				title:this.data.shareinfo.title,
				query:'fromuid='+this.userId,
				imageUrl:this.data.shareinfo.cover
			}
		},
		onPullDownRefresh() {
			if(!this.hasLogin){
				this.initSystem();
				this.momentRefresh += 1;
			}else{
				let self = this;
				this.doRefresh(function(){
					self.initNavigation(true);
					core.cachecloud('advs',function(advs){
						self.advs = advs;
					},true);
				}, true);
			}
			uni.stopPullDownRefresh();
		},
		methods:{
			initSystem(){
				let self = this;
				core.initsys(core.userinfo.token,function(){
					self.theme = core.style;
					self.text = core.langs;
					self.inwechat = core.inwechat;
					self.runshows = core.system.runshow;
					self.splash = core.system.splash;
					uni.setNavigationBarTitle({
						title:core.system.name
					});
				});
			},
			initData(callback=false){
				let that = this;
				uni.$on('CoreInited', function(Core){
					that.initData();
					that.theme = Core.style;
					that.platform = Core.platform;
					that.text = Core.langs;
					that.runshows = Core.system.runshow;
					that.splash = Core.system.splash;
					that.client = Core.system.client;
					that.inwechat = Core.inwechat;
					that.momentRefresh += 1;
					uni.setNavigationBarTitle({
						title:Core.system.name
					});
				});
				if(!core.inited) {
					return core.toast('初始化中...');
				}
				this.runshows = core.system.runshow;
				if(!this.hasLogin){
					// #ifdef MP
					if(core.system.auditmode.status){
						this.auditmode = core.system.auditmode;
					}
					// #endif
					core.cachecloud('advs',function(advs){
						that.advs = advs;
					});
					this.loaded = true;
					if(typeof(doPlusOauth)=='function'){
						doPlusOauth(this,core);
					}
					return uni.setNavigationBarTitle({
						title:core.system.name
					});
				}
				core.get('appmain',function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res),core.logout();
					}
					that.data = res;
					that.loaded = true;
					if(!that.inited){
						that.initNavigation();
						core.cachecloud('advs',function(advs){
							that.advs = advs;
						});
					}
					that.inited = true;
					core.userset = res.userset;
					if(that.navkey==''){
						that.navkey = res.navkey;
						if(res.navkey=='contacts'){
							that.initFriends();
						}
					}
					that.doSetBadge(res.noreads);
					uni.setNavigationBarTitle({
						title:res.title
					});
					if(res.releasedate!=core.system.releasedate){
						that.initSystem();
					}else{
						if(res.userinfo.groupid!=core.userinfo.groupid){
							core.userinfo.groupid = res.userinfo.groupid;
							core.cacheclear();
						}
						if(that.client.version==core.version && that.client.upgrade){
							core.initsys();
						}
					}
					if(!res.switchs.group){
						that.indexes.spacehei = 115;
					}
					that.shareinfo = core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
					for(let i in res.dialogs){
						that.dialogs[i] = res.dialogs[i].type+''+res.dialogs[i].cid;
						that.data.dialogs[i].isDraft = false;
					}
					if(res.mainnavs.length>0){
						core.cacheset('mainnavs',res.mainnavs);
					}
					if(callback){
						callback(res);
					}
					if(that.redirecturl!=''){
						return core.navito(that.redirecturl),that.redirecturl = '';
					}
				});
				if(!this.runConstruct){
					this.runConstruct = true;
					this.theme.footbg = this.theme.navbg.replace("gradual-", "");
					this.initsocket(core.system.socket.type);
					uni.$on('SwaSocketConnect',function(sket){
						that.isalive = true;
						that.disconnect = false;
						try{
							if(typeof(jpushModule)=='object'){
								if(core.device=='android'){
									jpushModule.initJPushService();
								}
								jpushModule.addConnectEventListener(result=>{
									console.log("极光推送服务：", result.connectEnable);
								});
								jpushModule.addTagAliasListener(result=>{
									//console.log("设置极光别名：", result);
									if(result.sequence===1 && typeof(result.alias)!='undefined'){
										that.initJPush();
									}
								});
								jpushModule.setAlias({
									'alias':"j"+sket.fromId,
									'sequence':1
								});
							}
						}catch(e){
							//TODO handle the exception
							console.log(JSON.stringify(e));
						}
					});
					uni.$on('SwaSocketError',function(e){
						that.isalive = false;
					});
					uni.$on('SwaSocketDisconnect',function(e){
						that.disconnect = true;
					});
					uni.$on('checkoutBar', function(res){
						that.checkoutbar(res.index);
					});
				}
			},
			initJPush(jyJPush){
				this.jpushState = true;
				let self = this;
				jpushModule.getRegistrationID(result=>{
					if(typeof(result.registerID)=='string' && result.registerID!=""){
						core.get("jpush/client", function(res){
						}, {clientid:result.registerID});
					}
					jpushModule.setBadge(self.newMsg);
				});
				jpushModule.addNotificationListener(result=>{
					let notificationEventType = result.notificationEventType;
					if(notificationEventType==='notificationOpened'){
						//点击推送内容
						let extras = result.extras;
						if(typeof(extras.redirect)!='undefined'){
							let extraData = typeof(extras.param)=='object' ? extras.param : {};
							let navType = (extras.redirect.indexOf('dialog/index')!=-1 && self.Chatid>0) ? 1 : 0;
							return core.navito(extras.redirect, extraData, extraData);
						}
						return true;
					}
				})
			},
			initFriends(){
				if(this.friends.inited) return true;
				let self = this;
				return core.get('friend',function(res){
					if(typeof(res.type)!='undefined') return core.report(res);
					self.friends.inited = true;
					self.friends.data = res.data;
					self.friends.totals = res.totals;
					if(res.data.length>0){
						self.indexes.listCur = res.data[0].letter;
						setTimeout(function(){
							self.indexesReady();
						},500);
					}
				});
			},
			initNavigation(nocache=false){
				let self = this;
				return core.cachecloud('navigation',function(res){
					self.navigation = res;
				},nocache);
			},
			initsocket(scktype){
				let self = this;
				uni.$on('DialogPost',this.postevent);
				uni.$on('DialogLaunch',function(e){
					let ctypeid = e.type + '' + e.cid;
					let dialogindex = self.dialogs.indexOf(ctypeid);
					if(dialogindex<0) return false;
					self.data.dialogs[dialogindex].noread = 0;
					let noread = self.newMsg - self.data.dialogs[dialogindex].noread;
					self.doSetBadge(noread);
				});
				uni.$on('SRdialog',this.socketrecive);
				uni.$on('SRmakefriend',this.socketrecive);
				uni.$on('SRgroupapplys',this.socketrecive);
				uni.$on('SRrmdialog',this.socketrecive);
				uni.$on('PEviewfriend',function(res){
					let revote = typeof(res.total)=='undefined' ? 1 : res.total;
					self.data.newfriends -= revote;
				});
				uni.$on('HaveDraft', this.eventDraft);
			},
			socketrecive(socketdata){
				//console.log(socketdata);
				if(socketdata.type=='dialog'){
					let ctypeid = socketdata.dialogtype + '' + socketdata.dialogid;
					if(socketdata.dialogid!=this.Chatid || socketdata.dialogtype!=this.Chattype){
						//增加总未读消息数
						store.commit('getnewmsg');
						//增加列表未读消息数量
						if(this.dialogs.indexOf(ctypeid)==-1){
							//消息列表不存在，则往列表添加聊天
							let dialog = {
								cid:socketdata.dialogid,
								type:socketdata.dialogtype,
								avatar:socketdata.dialogtype=='group'?socketdata.cover:socketdata.avatar,
								title:socketdata.title,
								nicknametext:'',
								message:socketdata.spct,
								url:"",
								datetime:'刚刚',
								noread:1,
								url:'',
								istop:0
							}
							if(typeof(socketdata.url)=='undefined'){
								dialog.url = core.page('dialog/index',{cid:socketdata.dialogid, type:socketdata.dialogtype});
							}else{
								dialog.url = socketdata.url;
							}
							this.doAddDialog(dialog, ctypeid);
						}else{
							//消息列表存在，则增加未读消息数量
							let dialogindex = this.dialogs.indexOf(ctypeid);
							this.data.dialogs[dialogindex].noread += 1;
							//同时调换顺序到最前面
							if(dialogindex!=0){
								let dialog = this.data.dialogs[dialogindex];
								this.data.dialogs.splice(dialogindex,1);
								this.dialogs.splice(dialogindex,1);
								this.doAddDialog(dialog, ctypeid);
							}
						}
					}
					if(this.dialogs.indexOf(ctypeid)>-1){
						//更新消息列表
						let dialogindex = this.dialogs.indexOf(ctypeid);
						this.data.dialogs[dialogindex].datetime = '刚刚';
						this.data.dialogs[dialogindex].nicknametext = socketdata.dialogtype=='normal'||socketdata.nickname=='' ? '' : socketdata.nickname + '：';
						this.data.dialogs[dialogindex].message = socketdata.spct;
					}
				}else if(socketdata.type=='makefriend'){
					this.data.newfriends += 1;
				}else if(socketdata.type=='groupapplys'){
					this.data.groupapplys += 1;
				}else if(socketdata.type=='rmdialog'){
					//从列表中删除会话
					let ctypeid = socketdata.ctypeid;
					this.doRmDialogFromList(ctypeid);
				}
			},
			socketretry(){
				if(!this.hasLogin) return false;
				if(!this.isnetwork) return core.toast('暂无可用网络');
				if(swasocket.Connected){
					swasocket.Logout(core.system.socket.type);
				}
				core.initsocket(core.userinfo.usersign, core.system.socket);
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			doRefresh(Callback=false, refresh=false){
				this.showmodal = '';
				if(this.navkey=='contacts'){
					this.friends.inited = false;
					return this.initFriends();
				}
				if(this.navkey=='moment' || this.navkey=='nearby'){
					return true;
				}
				let self = this;
				core.get('appmain',function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res),core.logout();
					}
					let TempMsgs = self.data.dialogs;
					self.data = res;
					let dialogs = [];
					for(let i in res.dialogs){
						let dialogId = res.dialogs[i].type+''+res.dialogs[i].cid;
						dialogs[i] = dialogId;
						let oldIndex = self.dialogs.indexOf(dialogId);
						if(oldIndex>=0){
							self.data.dialogs[i].isDraft = TempMsgs[oldIndex].isDraft;
						}
					}
					self.dialogs = dialogs;
					self.doSetBadge(res.noreads);
					if(!self.isalive){
						self.socketretry();
					}
					if(typeof(Callback)=='function'){
						Callback(res);
					}
				},{refresh:1});
			},
			doActionDialog(index){
				this.actionindex = index;
				this.showmodal = 'actiondialog';
			},
			doSetBadge(num){
				store.commit('setnewmsg',num);
				if(this.jpushState){
					jpushModule.setBadge(num);
				}
			},
			doSetTop(){
				let dialog = this.data.dialogs[this.actionindex];
				let self = this;
				core.post("dialog/settop",function(res){
					if(res.type!='success'){
						return core.report(res);
					}
					self.data.dialogs[self.actionindex].istop = !dialog.istop;
					self.showmodal = '';
					return self.doRefresh();
				},{"dosettop":"true",cid:dialog.cid,type:dialog.type});
			},
			doAddDialog(dialog,ctypeid=''){
				if(ctypeid==''){
					let ctypeid = dialog.type + '' + dialog.cid;
				}
				if(dialog.istop>0 || this.data.dialogs.length==0){
					this.data.dialogs.unshift(dialog);
					this.dialogs.unshift(ctypeid);
				}else{
					let index = 0;
					for(let i in this.data.dialogs){
						if(this.data.dialogs[i].istop==0){
							break;
						}
						index += 1;
					}
					this.data.dialogs.splice(index, 0, dialog);
					this.dialogs.splice(index, 0, ctypeid);
				}
				return true;
			},
			doRmDialog(){
				this.showmodal = '';
				if(this.actionindex<0) return false;
				let chatid = this.data.dialogs[this.actionindex].cid;
				let dialogtype = this.data.dialogs[this.actionindex].type;
				let self = this;
				core.confirm(this.text.text_dialog_delete,this.text.text_confirm_delete,function(){
					core.get("dialog/rmdialog",function(res){
						if(res.type=='success'){
							self.doRmDialogFromList(dialogtype+chatid);
							let CacheKey = dialogtype + '_' + chatid;
							core.cacheremove(CacheKey);
						}
					},{cid:chatid,dialogtype:dialogtype})
				});
			},
			doRmDialogFromList(ctypeid){
				let dialogindex = this.dialogs.indexOf(ctypeid);
				if(dialogindex>-1){
					let noreads = parseInt(this.data.dialogs[dialogindex].noread);
					this.data.dialogs.splice(dialogindex,1);
					this.dialogs.splice(dialogindex,1);
					if(noreads>0){
						this.doSetBadge(this.newMsg-noreads);
					}
				}
			},
			indexesReady(){
				let that = this;
				uni.createSelectorQuery().select('.indexBar-box').boundingClientRect(function(res) {
					that.indexes.boxTop = res.top
				}).exec();
				uni.createSelectorQuery().select('.indexes').boundingClientRect(function(res) {
					that.indexes.barTop = res.top
				}).exec();
			},
			//滑动选择Item
			tMove(e) {
				let y = e.touches[0].clientY,
					offsettop = this.indexes.boxTop,
					that = this;
				//判断选择区域,只有在选择区才会生效
				if (y > offsettop) {
					let num = parseInt((y - offsettop) / 20);
					if(num<that.friends.data.length){
						that.indexes.listCur = that.friends.data[num].letter;
					}
				};
			},
			getCur(e) {
				this.hidden = false;
				this.indexes.listCur = this.friends.data[e.target.id].letter;
			},
			setCur(e) {
				this.hidden = true;
				this.indexes.listCur = this.indexes.listCur
			},
			//触发全部开始选择
			tStart() {
				this.hidden = false
			},
			
			//触发结束选择
			tEnd() {
				this.hidden = true;
				this.indexes.listCurID = this.indexes.listCur
			},
			eventDraft(res){
				let dialogindex = this.dialogs.indexOf(res.id);
				if(dialogindex<0) return false;
				let dialogs = this.data.dialogs[dialogindex];
				dialogs.isDraft = res.isDraft;
				this.data.dialogs.splice(dialogindex,1, dialogs);
			},
			postevent(data={}){
				let ctypeid = data.type + '' + data.cid;
				let dialogindex = this.dialogs.indexOf(ctypeid);
				if(dialogindex==-1){
					//消息列表未存在
					let dialog = {
						cid:data.cid,
						type:data.type,
						avatar:data.avatar,
						title:data.title,
						nicknametext:data.type=='group'?'我：':'',
						message:data.spct,
						datetime:'刚刚',
						noread:0,
						url:core.page('dialog/index',{cid:data.cid,type:data.type}),
						istop:0
					};
					return this.doAddDialog(dialog);
				}
				//更新未读消息数
				let noreads = parseInt(this.data.dialogs[dialogindex].noread);
				if(noreads>0){
					this.doSetBadge(this.newMsg-noreads);
				}
				//将发送的内容到消息列表
				this.data.dialogs[dialogindex].datetime = '刚刚';
				this.data.dialogs[dialogindex].nicknametext = data.type=='normal' ? '' : '我：';
				this.data.dialogs[dialogindex].message = data.spct;
				this.data.dialogs[dialogindex].noread = 0;
				if(dialogindex!=0){
					let dialog = this.data.dialogs[dialogindex];
					this.data.dialogs.splice(dialogindex,1);
					this.dialogs.splice(dialogindex,1);
					return this.doAddDialog(dialog, ctypeid);
				}
			},
			checkoutbar(index){
				if(this.data.mainnavs[index].url!='') return core.navito(this.data.mainnavs[index].url);
				uni.hideLoading();
				let navkey = this.data.mainnavs[index].key;
				this.navkey = navkey;
				if(navkey=='contacts'){
					this.initFriends();
				}
			},
			checkUpgrade(){
				let self = this;
				core.confirm('新版本（V'+this.client.version+'），更多好玩优质内容邀请您体验','发现新版本',function(){
					core.navito(self.client.upgradeurl);
				},{
					cancelText:"本次忽略"
				}, function(){
					self.client.upgrade = false;
				});
			}
		}
	}
</script>

<style>
	page{padding-bottom: 120upx;}
	.dialoglist .cu-item.istop{background-color: #f0f0f0 !important;}
	.dialoglist .cu-item.istop::after{border-color: #CDCDCD !important;}
	.dialoglist .istop:last-child{margin-bottom: -1upx;}
	.dialoglist .cu-item .action{width: 130upx; text-align: right; padding-right: 8upx;}
	.dialoglist .cu-item .content{width: calc(100% - 96upx - 220upx);}
	.splash{position: fixed; left: 0; top: 0; width: 100%; height: 100%; z-index: 999;}
	.splash image{width: 100%; height: 100%;}
	.splash .loader{position: absolute; left: 50%; top: 50%; width: 200upx; margin-left: -100upx; background: rgba(0,0,0,0.3); border-radius: 6upx; margin-top: -42upx;}
	.main-pages .mainpageitem{display: none; min-height: calc(100vh - 120upx - 90upx);}
	.main-pages .mainpageitem.active{display: block;}
	.popcontainer{position: absolute; right: 20upx; background: #2C405A; padding: 10upx 20upx; top: calc(90upx + var(--status-bar-height));}
	.popcontainer .cu-item{background: none !important; color: #FFFFFF; min-height: 90upx; padding:0 10upx;}
	.page-text-lg .popcontainer .cu-item{min-height: 96upx;}
	.page-text-lg .popcontainer .cu-item .content {font-size: 34upx;}
	.page-text-lg .cu-custom .text-xxl *{font-size: 40upx;}
	.page-text-xl .popcontainer .cu-item{min-height: 102upx;}
	.page-text-xl .popcontainer .cu-item .content {font-size: 38upx;}
	.page-text-xl .cu-custom .text-xxl *{font-size: 44upx;}
	.popcontainer .cu-item::after{border-color: #666666 !important;}
	.popcontainer .cu-item text[class*=cuIcon]{font-size: 36upx;}
	.popcontainer::after{width: 20upx; height: 20upx; background: #2C405A; content: ''; right: 16upx; top: -8upx; position: absolute; transform: rotate(45deg); border-radius: 4upx;}
	.wp-ttapp .popcontainer, .wp-wxapp .popcontainer{right: auto; left: 20upx;}
	.wp-ttapp .popcontainer::after, .wp-wxapp .popcontainer::after{right: auto; left: 16upx;}
	.near-page{position: relative;}
	
	/* #ifdef H5 */
	.suspense{position: fixed; right: 20upx; bottom:  150upx; font-size: 72upx;}
	.suspense .bg-white{border-radius: 50%;}
	.suspense .cuIcon-roundrightfill::before{transform: rotate(90deg);}
	.popupover.inwechat .popcontainer{top: auto; bottom: calc(150upx + 72upx + 30upx);}
	.popupover.inwechat .popcontainer::after{top: auto; right: 26upx;}
	/* #endif */
</style>
