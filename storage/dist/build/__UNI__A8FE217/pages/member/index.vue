<template>
	<view>
		<cu-custom bgColor="bg-white" :isBack="true">
			<block slot="content"></block>
		</cu-custom>
		<swaload :hasTopbar="false" v-if="!loaded"></swaload>
		<view class="bg-white">
			<view class="cu-list menu-avatar padding-top-xs">
				<view class="cu-item padding-bottom" style="height: auto;">
					<view class="cu-avatar radius xl" @click="doPrevImg(data.member.avatar)" style="top: 0;" :style="'background-image:url('+data.member.avatar+');'"></view>
					<view class="content" style="top: 0;">
						<view class="text-xxxl text-bold text-black">
							{{data.friend.remark || data.member.nickname}}
							<text class="margin-left-xs" :class="data.member.gender==1?'text-blue cuIcon-people':'text-pink cuIcon-friendfamous'" v-if="data.member.gender>0"></text>
						</view>
						<view class="text-content margin-top-xs">
							<view class="text-grey" v-if="data.friend.id>0 && data.member.nickname!=data.friend.remark">昵称：{{data.member.nickname}}</view>
							<block v-if="data.userid!='' && (data.friend.status==1 || data.member.uid==userId) && !groupprotective">
								<view class="text-grey" @click="doCopy(user_id)">
									{{data.userid}}：{{data.userid=='ID'?data.member.uid:data.member.userid}}
									<text class="cuIcon-copy margin-left-xs"></text>
								</view>
							</block>
							<view class="text-grey">
								地区：
								<text v-if="data.member.resideprovince=='' && data.member.residecity==''">未知地区</text>
								<text v-else>
									{{data.member.resideprovince}}  {{data.member.residecity}}
								</text>
							</view>
							<block v-if="data.details.length>0">
								<view class="text-grey" :class="item.extra" v-for="(item, index) in data.details" :key="index">
									{{item.title}}：
									<text>{{item.value}}</text>
								</view>
							</block>
						</view>
					</view>
				</view>
			</view>
			<view class="cu-list menu" style="margin-top: 0;">
				<view class="cu-item arrow" @click="showmodal='remark'" v-if="data.friend.status==1">
					<view class="content">设置备注</view>
				</view>
				<view class="cu-item arrow" @click="naviTo('album/index',{uid:data.member.uid})" v-if="data.actions.moments && !groupprotective">
					<view class="content">相册</view>
					<view class="action" style="width: 60%;" v-if="data.albums.length>0">
						<view class="grid col-4 grid-square">
							<view class="bg-img" style="margin-bottom: 0;" v-for="(pic,index) in data.albums" :key="index">
								<image :src="pic" mode="aspectFill"></image>
							</view>
						</view>
					</view>
				</view>
				<view class="cu-item" v-if="data.member.msn!='' && data.friend.status==1">
					<view class="content">
						<text>微信号</text>
					</view>
					<view class="action">
						<text class="text-grey">{{data.member.msn}}</text>
						<text class="margin-left-sm cuIcon-copy" :class="'text-'+theme.actcolor" @tap="doCopy(data.member.msn)"></text>
					</view>
				</view>
				<view class="cu-item" style="padding: 20upx 30upx;">
					<view class="content">
						<text>个性签名</text>
					</view>
					<view class="action" style="max-width: 70%;">
						<text class="text-grey">{{data.member.bio || '这家伙很懒，什么都没留下'}}</text>
					</view>
				</view>
				<block v-if="data.profiles.length>0">
					<view class="cu-item" :class="item.url==''?'':'arrow'" @click="naviTo(item.url)" v-for="(item,index) in data.profiles" :key="index">
						<view class="content">
							<text>{{item.title}}</text>
						</view>
						<view class="action" style="max-width: 70%;">
							<text :class="item.extra">{{item.value}}</text>
						</view>
					</view>
				</block>
				<block v-if="!groupprotective">
					<!-- #ifdef MP-WEIXIN -->
					<button class="cu-item" open-type="share">
						<view class="content">
							<text class="text-lg text-darkblue">分享此名片</text>
						</view>
					</button>
					<!-- #endif -->
					<!-- #ifndef MP-WEIXIN -->
					<view class="cu-item" @click="showshare=true">
						<view class="content text-center">
							<text class="text-lg text-darkblue">分享此名片</text>
						</view>
					</view>
					<!-- #endif -->
				</block>
			</view>
		</view>
		<view class="margin-top-sm bg-white padding-top" v-if="data.friend.status!=1 && data.tofriend.apply>0">
			<view class="text-lg padding-lr solid-bottom padding-bottom-sm">
				<text class="text-darkblue">
					对方请求添加为好友：
				</text>
				<text class="text-gray">{{data.tofriend.note}}</text>
			</view>
			<view class="cu-list menu text-center">
				<view class="cu-item" @click="doagree()">
					<view class="content">
						<text class="text-lg text-darkblue">通过验证</text>
					</view>
				</view>
				<view class="cu-item" @click="doIgnore()">
					<view class="content">
						<text class="text-lg text-gray">忽略</text>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-list menu margin-top-sm text-darkblue text-center" v-if="userId!=data.member.uid">
			<block v-if="!groupprotective">
				<block v-if="data.actions.sendmsg">
					<view class="cu-item" @click="doBack()" v-if="frompage=='dialog' && data.group.id==0">
						<view class="content">
							<text class="text-lg">发消息</text>
						</view>
					</view>
					<view class="cu-item" @click="naviTo('dialog/index',{cid:data.member.uid,type:'normal',exkey:extrakey})" v-else>
						<view class="content">
							<text class="text-lg">发消息</text>
						</view>
					</view>
				</block>
				<view class="cu-item" @click="naviTo('credit/transfer',{uid:data.member.uid})" v-if="data.actions.transfer">
					<view class="content">
						<text class="text-lg">转账</text>
					</view>
				</view>
			</block>
			<view class="cu-item" @click="ClearMsg()" v-if="localChat.message.length>0">
				<view class="content">
					<text class="text-lg text-red">清空聊天记录</text>
				</view>
			</view>
			<view class="cu-item" @click="doremove()" v-if="data.friend.status==1">
				<view class="content">
					<text class="text-lg text-red">移出通讯录</text>
				</view>
			</view>
			<view class="cu-item" @click="domakefriend()" v-else-if="data.actions.makefriend && !groupprotective">
				<view class="content">
					<text class="text-lg">添加到通讯录</text>
				</view>
			</view>
			<view class="cu-item" @click="doshielding()" v-if="data.actions.shielding">
				<view class="content" v-if="data.friend.status==0 && data.friend.id>0">
					<text class="text-lg">取消屏蔽</text>
				</view>
				<view class="content" v-else>
					<text class="text-lg text-gray">屏蔽此用户</text>
				</view>
			</view>
			<view class="cu-item" @click="naviTo('report/index', {type:'user', id:data.member.uid})" v-if="data.actions.report">
				<view class="content">
					<text class="text-lg">投诉</text>
				</view>
			</view>
		</view>
		<view class="cu-list menu mt0 solid-top text-center" v-if="data.navs.length>0">
			<view class="cu-item" @click="naviTo(item.url)" v-for="(item,index) in data.navs" :key="index">
				<view class="content" :class="item.extra">
					<text>{{item.title}}</text>
				</view>
			</view>
		</view>
		<view class="cu-modal" v-if="data.friend.id>0" :class="showmodal=='remark'?'show':'hidden'">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">修改备注名</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view>
					<view class="cu-form-group text-left">
						<input placeholder="输入对方的备注名称" v-model="friendremark" name="input"></input>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-gray" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @click="doremark()">确定</button>
					</view>
				</view>
			</view>
		</view>
		<share :shareinfo="shareinfo" @closeshare="showshare=false" :showshare="showshare"></share>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import share from "@/components/util/share.vue"
	
	export default {
		computed: mapState(['userId']),
		components: {share},
		data() {
			return {
				loaded:false,
				uid:0,
				fromgid:0,
				localChat:{
					lastid:0,
					firstid:0,
					message:[],
					title:""
				},
				frompage:'',
				groupprotective:false,
				showmodal:"",
				friendremark:"",
				theme:core.style,
				platform:core.platform,
				showshare:false,
				extrakey:"",
				user_id:'',
				shareinfo:{
					url:'',
					providers:4,
					provider:{}
				},
				data:{
					title:"名片",
					userid:"",
					member:{
						avatar: core.system.logo,
						bio: "",
						gender: 1,
						groupid:0,
						ismanager:false,
						msn: "",
						nickname: "用户昵称",
						residecity: "",
						resideprovince: "",
						uid: 0,
						userid: ""
					},
					actions:{
						sendmsg:false,
						makefriend:false,
						moments:false,
						shielding:false,
						transfer:false,
						report:false
					},
					navs:[],
					profiles:[],
					details:[],
					validation_apply:0,
					uid:0,
					friend:{
						id:0,
						status:0,
						remark:""
					},
					tofriend:{
						id:0,
						apply:0
					},
					group:{
						id:0,
						ismanager:false
					},
					albums:[],
					shareinfo:{
						title:"",
						cover:"",
						url:"",
						desc:""
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.q)!='undefined'){
				let query = decodeURIComponent(options.q);
				if(typeof(options.uid)=='undefined') options.uid = core.urlParams(query, 'uid');
			}
			if(typeof(options.cid)!='undefined' && options.type=='normal'){
				options.uid = options.cid;
				options.fp = 'dialog';
			}
			if(typeof(options.uid)=='undefined' || !options.uid) return core.toast('无效的用户编号','back');
			this.uid = options.uid;
			if(typeof(options.fg)!='undefined') this.fromgid = parseInt(options.fg);
			if(typeof(options.fp)!='undefined') this.frompage = options.fp;
			this.initData('member',{uid:this.uid,fg:this.fromgid});
			let self = this;
			core.cacheread("normal_"+this.uid,function(cache){
				self.localChat = {
					lastid:cache.data.lastid,
					firstid:cache.data.firstid,
					message:cache.data.message,
					title:cache.data.title
				}
			});
		},
		onShareAppMessage(e){
			return {
				title:this.data.shareinfo.title,
				path:core.page('member/index',{uid:this.uid,fromuid:this.userId}),
			}
		},
		onShareTimeline(e){
			return {
				title:this.data.shareinfo.title,
				query:'uid='+this.uid+'&fromuid='+this.userId
			}
		},
		methods:{
			initData(route, data={}, callback=false){
				let that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.loaded = true;
					that.data = res;
					that.friendremark = res.friend.id>0 ? res.friend.remark : '';
					let groupprotective = false;
					if(res.group.id>0){
						if(res.group.protective && res.friend.status!=1){
							groupprotective = true;
						}
						if(res.group.ismanager || res.member.ismanager){
							that.extrakey = 'gm' + res.group.id;
						}
					}
					if(res.friend.status!=1 && res.tofriend.apply>0){
						uni.$emit('PEviewfriend',{total:1});
					}
					that.groupprotective = groupprotective;
					if(res.userid!=''){
						let user_id = res.userid=='ID' ? res.member.uid : res.member.userid;
						that.user_id = user_id + "";
					}
					uni.setNavigationBarTitle({
						title:res.title
					});
					if(groupprotective){
						// #ifdef H5
						if(core.inwechat){
							core.initjwx(function(jweixin){
								jweixin.hideMenuItems({
									menuList: ['menuItem:copyUrl','menuItem:openWithQQBrowser','menuItem:openWithSafari','menuItem:share:appMessage','menuItem:share:timeline','menuItem:share:qq','menuItem:share:weiboApp','menuItem:favorite','menuItem:share:facebook','menuItem:share:QZone','menuItem:originPage']
								});
							});
						}
						// #endif
					}else{
						that.shareinfo = core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
					}
					if(callback){
						callback(res);
					}
				},data);
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			doBack(){
				return core.back();
			},
			doCopy(text){
				return core.copy(text+"");
			},
			doPrevImg(pic){
				uni.previewImage({
					current:pic,
					urls:[pic]
				})
			},
			ClearMsg(){
				let cacheKey = "normal_"+this.uid;
				let self = this;
				core.confirm("确定要清空该会话的聊天记录吗？","清空聊天记录",function(){
					let Callback = function(){
						uni.$emit("ClearMessages", {
							type:"normal",
							chatid:self.uid
						});
						core.toast("已清空", '', "success");
					}
					if(self.localChat.message.length>0){
						self.localChat.message = [];
						core.cacheset(cacheKey, self.localChat);
					}
					Callback();
				});
			},
			doremove(){
				if(this.data.friend.status!=1) return core.toast('对方还不是您的好友');
				let that = this;
				return core.confirm('删除好友将同时删除与对方的聊天记录','删除好友',function(){
					core.get('member/rmfriend',function(res){
						let redirect = res.type=='success' ? '' : res.redirect;
						if(res.type=='success'){
							that.initData('member',{uid:that.uid,fg:that.fromgid});
							if(that.frompage=='dialog' && that.fromgid==0){
								redirect = 'home';
							}
						}
						return core.toast(res.message,redirect,res.type);
					},{uid:that.data.member.uid});
				});
			},
			domakefriend(){
				if(this.data.friend.status==1) return core.toast('对方已经是好友');
				if(this.data.validation_apply==1) return this.naviTo('friend/apply',{uid:this.uid});
				let that = this;
				return core.get('member/makefriend',function(res){
					if(typeof(res.type)=='undefined') return that.naviTo('friend/apply',{uid:that.uid});
					if(res.type=='success'){
						that.initData('member',{uid:that.uid,fg:that.fromgid});
					}
					let redirect = res.type=='success' ? '' : res.redirect;
					return core.toast(res.message,redirect,res.type);
				},{uid:this.data.member.uid});
			},
			doagree(){
				if(this.data.friend.status==1) return core.toast('对方已经是好友');
				let that = this;
				return core.get('member/makefriend',function(res){
					if(res.type=='success'){
						that.initData('member',{uid:that.uid,fg:that.fromgid});
						uni.$emit('DialogPost',{cid:that.data.member.uid,type:'normal',spct:'你们已经是好友了，开始聊天吧！',avatar:that.data.member.avatar,title:that.data.member.nickname})
					}
					let redirect = res.type=='success' ? '' : res.redirect;
					return core.toast(res.message,redirect,res.type);
				},{uid:this.data.member.uid,confirmfriend:"yes"});
			},
			doIgnore(){
				let that = this;
				core.confirm('是否确定要忽略本次好友申请？','忽略好友申请',function(){
					core.post("member/makefriend",function(res){
						return core.toast(res.message,res.redirect,res.type);
					},{uid:that.data.member.uid,ignoreapply:"true"});
				})
			},
			doremark(){
				if(this.friendremark=='') return core.toast(core.langs.text_please_input_remark);
				let that = this;
				return core.post('member',function(res){
					if(res.type=='success'){
						that.data.friend.remark = that.friendremark;
						that.showmodal = '';
					}
					let redirect = res.type=='success' ? '' : res.redirect;
					return core.toast(res.message,redirect,res.type);
				},{uid:this.data.member.uid,setremark:'true',remark:this.friendremark});
			},
			doshielding(){
				let that = this;
				let shielding = function(){
					core.get('member/shielding',function(res){
						if(res.type=='success'){
							that.initData('member',{uid:that.uid,fg:that.fromgid});
						}
						let redirect = res.type=='success' ? '' : res.redirect;
						return core.toast(res.message,redirect,res.type);
					},{uid:that.data.member.uid})
				}
				if(this.data.friend.status==0){
					shielding();
				}else{
					return core.confirm('屏蔽后将不再收到消息及动态的更新','屏蔽用户',function(){
						shielding();
					})
				}
			}
		}
	}
</script>

<style>
	.mt0{margin-top: 0px;}
	.cu-list.menu-avatar>.cu-item .content{position: relative; left: auto !important; width: calc(100% - 188upx);}
</style>
