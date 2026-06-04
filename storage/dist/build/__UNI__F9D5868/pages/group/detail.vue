<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="cu-list menu-avatar">
			<view class="cu-item">
				<view class="cu-avatar radius lg" @click="doPrevImg(data.group.pic)" :style="'background-image:url('+data.group.pic+');'"></view>
				<view class="content flex-sub">
					<view class="text-grey">{{data.group.name}}</view>
					<view class="text-gray text-sm flex justify-between">
						ID:{{data.group.id}}
						<view class="text-gray text-lg">
							<text class="cuIcon-friend margin-lr-xs"></text> {{data.group.members}}人
							<text class="cuIcon-time margin-right-xs margin-left"></text> {{data.group.datetime}}
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-list menu" style="margin-top: 0;">
			<view class="cu-item" style="padding: 20upx 30upx;" v-if="data.group.description!=''">
				<view class="content">
					<text>群简介</text>
				</view>
				<view class="action" style="max-width: 70%;">
					<text class="text-grey">{{data.group.description}}</text>
				</view>
			</view>
		</view>
		<view class="padding bg-white margin-bottom solid-top" v-if="data.group.notice!='' && data.groupmember.status==1">
			<scroll-view scroll-y class="group-notice padding bg-grey light radius" show-scrollbar>
				<rich-text class="text-lg" :nodes="data.group.notice" :selectable="true"></rich-text>
			</scroll-view>
		</view>
		<view class="cu-list menu solid-bottom" v-if="data.group.uid>0">
			<view class="cu-item arrow" @click="naviTo('member/index',{uid:data.group.uid})">
				<view class="content">
					<text class="cuIcon-friendfamous text-green"></text>
					群主
				</view>
				<view class="action">
					<text class="text-darkblue">{{data.group.nickname}}</text>
				</view>
			</view>
		</view>
		<view :class="data.groupmember.status==1?'':'margin-bottom'" v-if="data.qrcode!=''">
			<view class="cu-list menu solid-bottom">
				<view class="cu-item arrow noaf" @click="showqrcode=!showqrcode" :class="showqrcode?'arrow-up':''">
					<view class="content">
						<text class="cuIcon-qr_code" :class="'text-'+theme.actcolor"></text>
						<text>分享群</text>
					</view>
				</view>
				<view class="padding-lr-xl bg-white" :class="showqrcode?'':'hidden'">
					<view class="padding-xs text-center account-qrcode" @click="previewimg(data.qrcode)">
						<image mode="widthFix" :src="data.qrcode" class="png qrcode"></image>
					</view>
					<view class="margin-lr-xl flex flex-direction padding-bottom">
						<button class="cu-btn lg radius" :class="'bg-'+theme.actcolor" open-type="share" v-if="platform=='wxapp'">
							<text>分享给好友</text>
						</button>
						<view class="cu-btn lg radius" :class="'bg-'+theme.actcolor" @click="showshare=true" v-else>
							<text>分享给好友</text>
						</view>
						<block v-if="data.ismanager">
							<view class="cu-btn radius lg bg-blue margin-top-sm" @click="doCopy(data.entry)">
								<text>复制直接进群链接</text>
							</view>
						</block>
					</view>
				</view>
			</view>
		</view>
		<view v-if="data.groupmember.status==1">
			<view class="cu-list menu">
				<view class="cu-item arrow" @click="naviTo('group/members',{gid:data.group.id})">
					<view class="content">
						<text class="cuIcon-group text-orange"></text>
						群成员
					</view>
					<view class="action" :class="data.applys>0?'newapply':''" style="max-width: 72%;" v-if="data.members.length>0">
						<view class="cu-tag badge" v-if="data.applys>0">{{data.applys}}</view>
						<view class="cu-avatar-group">
							<view @tap.stop="naviTo('member/index',{uid:item.uid,fg:data.group.id})" class="cu-avatar round sm" :style="'background-image:url('+item.avatar+');'" v-for="(item,index) in data.members" :key="index"></view>
						</view>
					</view>
				</view>
				<view class="cu-item arrow" v-if="data.group.uid==userId" @click="naviTo('group/profile',{gid:data.group.id})">
					<view class="content">
						<text class="cuIcon-settings text-blue"></text>
						<text>群资料</text>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						<text class="cuIcon-selectionfill text-yellow"></text>
						<text>群等级</text>
					</view>
					<view class="action">
						<text class="cu-tag round bg-gold light">{{data.group.members_max==0?'最高等级':data.group.members_max+'人群'}}</text>
						<button class="cu-btn sm radius margin-left-sm bg-orange" @tap="naviTo('member/vip')" v-if="data.group.upgradevip">升级</button>
					</view>
				</view>
				<view class="cu-item arrow" v-if="data.ismanager" @click="naviTo('redpacket/record',{gid:data.group.id})">
					<view class="content">
						<text class="cuIcon-redpacket_fill text-red"></text>
						<text>红包记录</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="showmodal='nickname'">
					<view class="content">
						我的群昵称
					</view>
					<view class="action">
						<view class="cu-tag round bg-limegreen light">{{data.groupmember.nickname}}</view>
					</view>
				</view>
			</view>
			<view class="cu-list menu solid-top" style="margin-top: 0;" v-if="data.group.uid==data.groupmember.uid">
				<view class="cu-item arrow" @click="showmodal='editfee'" v-if="data.joinfee==1">
					<view class="content">
						进群收费
					</view>
					<view class="action">
						<view class="cu-tag round bg-red light" v-if="data.group.fee>0">￥{{data.group.fee}}</view>
						<view class="cu-tag round bg-limegreen light" v-else>免费</view>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						全员禁言
					</view>
					<view class="action">
						<switch class="switch" data-id="allowpost" @change="SwitchSet" :class="[data.group.allowpost==1?'':'checked',theme.actcolor]" :checked="data.group.allowpost==1?false:true"></switch>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						成员保护
					</view>
					<view class="action">
						<switch class="switch" data-id="protective" @change="SwitchSet" :class="[data.group.protective==0?'':'checked',theme.actcolor]" :checked="data.group.protective==0?false:true"></switch>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						允许加入
					</view>
					<view class="action">
						<switch class="switch" data-id="allowjoin" @change="SwitchSet" :class="[data.group.allowjoin==0?'':'checked',theme.actcolor]" :checked="data.group.allowjoin==0?false:true"></switch>
					</view>
				</view>
				<view class="cu-item" v-if="data.group.allowjoin==1">
					<view class="content">
						加入需要审核
					</view>
					<view class="action">
						<switch class="switch" data-id="joinaudit" @change="SwitchSet" :class="[data.group.joinaudit==0?'':'checked',theme.actcolor]" :checked="data.group.joinaudit==0?false:true"></switch>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						隐藏群组
					</view>
					<view class="action">
						<switch class="switch" data-id="ishide" @change="SwitchSet" :class="[data.group.ishide==0?'':'checked',theme.actcolor]" :checked="data.group.ishide==0?false:true"></switch>
					</view>
				</view>
			</view>
			<view class="cu-list menu text-darkblue text-center">
				<view class="cu-item" @click="doBack()" v-if="frompage=='dialog'">
					<view class="content">
						<text class="text-lg">开始聊天</text>
					</view>
				</view>
				<view class="cu-item" @click="naviTo('dialog/index',{cid:data.group.id,type:'group'})" v-else>
					<view class="content">
						<text class="text-lg">开始聊天</text>
					</view>
				</view>
				<view class="cu-item" @click="ClearMsg()">
					<view class="content text-red">
						<text class="text-lg">清空聊天记录</text>
					</view>
				</view>
				<view class="cu-item" @click="doExitGroup()">
					<view class="content">
						<text class="text-lg text-red">{{data.group.uid==data.groupmember.uid?'解散群组':'退出群聊'}}</text>
					</view>
				</view>
				<view class="cu-item" @click="naviTo('report/index', {id:data.group.id, type:'group'})" v-if="data.isreport">
					<view class="content">
						<text class="text-lg">投诉</text>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-list menu text-darkblue text-center" v-else-if="data.group.allowjoin==1">
			<view class="cu-item" @click="doJoinGroup()">
				<view class="content" v-if="data.group.fee>0">
					<text class="text-lg">付费进群</text>
					<text class="text-lg text-red">（￥{{data.group.fee}}）</text>
				</view>
				<view class="content" v-else>
					<text class="text-lg">申请加入群聊</text>
				</view>
			</view>
			<view class="cu-item" @click="naviTo('report/index', {id:data.group.id, type:'group'})" v-if="data.isreport">
				<view class="content">
					<text class="text-lg">投诉</text>
				</view>
			</view>
		</view>
		<view class="cu-modal" v-if="data.group.uid==userId" :class="showmodal=='editfee'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">进群收费</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view>
					<view class="cu-form-group text-left">
						<input type="digit" placeholder="输入进群费用(元),0为免费" v-model="postdata.fee" name="fee"></input>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn text-gray" @tap="showmodal=''">取消</button>
						<button class="cu-btn line-green margin-left-sm" @tap="postdata.fee=0">免费</button>
						<button class="cu-btn margin-left-sm" :class="'bg-'+theme.actcolor" @click="doEdit('fee')">确定</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal" v-if="data.groupmember.status==1" :class="showmodal=='nickname'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">修改群昵称</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view>
					<view class="cu-form-group text-left">
						<input type="text" placeholder="输入新的群昵称" v-model="postdata.nickname" name="nickname"></input>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-gray" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @click="doEdit('nickname')">确定</button>
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
				gid:0,
				platform:core.platform,
				frompage:"",
				showqrcode:false,
				showshare:false,
				shareinfo:{
					url:'',
					providers:4,
					provider:{}
				},
				showmodal:"",
				submiting:false,
				theme:core.style,
				postdata:{
					nickname:"",
					fee:0
				},
				data:{
					title:"群资料",
					applys:0,
					entry:"",
					group:{
						id:0,
						name:"",
						pic:core.system.defaultimg,
						datetime:"",
						members:0,
						members_max:0,
						description:"",
						uid:0,
						nickname:"",
						fee:0,
						allowpost:1,
						protective:0,
						allowjoin:1,
						joinaudit:0,
						ishide:0,
						upgradevip:false
					},
					ismanager:false,
					isreport:false,
					joinfee:1,
					members:[],
					qrcode:"",
					groupmember:{
						id:0,
						status:0,
						nickname:""
					},
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
				if(typeof(options.gid)=='undefined') options.gid = core.urlParams(query, 'gid');
			}
			if(typeof(options.cid)!='undefined' && options.type=='group'){
				options.gid = options.cid;
				options.fp = 'dialog';
			}
			if(typeof(options.gid)=='undefined' || options.gid=='') return core.toast('无效的群组信息','back');
			this.gid = parseInt(options.gid);
			if(typeof(options.fp)!='undefined') this.frompage = options.fp;
		},
		onShow() {
			this.initData('group/detail',{gid:this.gid});
		},
		onShareAppMessage(e){
			return {
				title:this.data.shareinfo.title,
				path:core.page('group/detail',{gid:this.gid,fromuid:this.userId}),
			}
		},
		onShareTimeline(e){
			return {
				title:this.data.shareinfo.title,
				query:'gid='+this.gid+'&fromuid='+this.userId
			}
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.data = res;
					if(res.groupmember.status==1){
						that.postdata.nickname = res.groupmember.nickname;
					}
					that.postdata.fee = res.group.fee;
					uni.setNavigationBarTitle({
						title:res.title
					});
					that.shareinfo = core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
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
				return core.copy(text);
			},
			previewimg(image){
				return uni.previewImage({
					urls:[image]
				})
			},
			doPrevImg(pic){
				uni.previewImage({
					current:pic,
					urls:[pic]
				})
			},
			ClearMsg(){
				let chatid = this.data.group.id;
				core.confirm("确定要清空该会话的聊天记录吗？","清空聊天记录",function(){
					let Callback = function(){
						uni.$emit("ClearMessages", {
							type:"group",
							chatid:chatid
						});
						core.toast("已清空", '', "success");
					}
					let CacheKey = 'group_' + chatid;
					core.cacheread(CacheKey,function(cache){
						core.cacheset(CacheKey, {
							lastid:cache.data.lastid,
							firstid:cache.data.firstid,
							message:[],
							title:cache.data.title
						});
						Callback();
					}, function(){
						Callback();
					});
				});
			},
			doJoinGroup(){
				if(this.submiting) return false;
				if(this.data.group.allowjoin==0) return core.toast('该群暂不支持加入');
				if(this.data.group.fee>0 || this.data.group.joinaudit==0){
					this.submiting = true;
					let that = this;
					return core.post("group/join",function(res){
						that.submiting = false;
						let redirect = res.redirect;
						if(res.type=='success'){
							if(typeof(res.message.tid)!='undefined'){
								redirect = core.page('credit/pay',{tid:res.message.tid,fp:'detail'});
								return core.toast(res.message.message,redirect,res.type);
							}else{
								that.initData('group/detail',{gid:that.data.group.id});
								redirect = '';
							}
						}else if(res.type=='info'){
							that.initData('group/detail',{gid:that.data.group.id});
							res.type = 'error';
						}
						core.toast(res.message,redirect,res.type);
					},{gid:this.data.group.id});
				}else{
					return core.navito('group/join',{gid:this.data.group.id});
				}
			},
			doExitGroup(){
				let that = this;
				let act = this.data.group.uid==this.userId ? '解散群组' : '退出群聊';
				let text = this.data.group.uid==this.userId ? '解散后所有相关记录和资料都将被删除且无法恢复' : '退出后您将无法看到之前的聊天记录';
				core.confirm(text,act,function(){
					core.post("group/set",function(res){
						let redirect = res.type=='success' ? 'home' : res.redirect;
						core.toast(res.message,redirect,res.type);
					},{exitsubmit:"true",gid:that.data.group.id})
				});
			},
			doEdit(setid=''){
				let setvalue = this.postdata[setid];
				if(setid=='nickname' && setvalue=='') return core.toast('昵称不能为空');
				let that = this;
				core.post('group/detail',function(res){
					if(res.type=='success'){
						if(setid=='nickname'){
							that.data.groupmember[setid] = setvalue;
						}else{
							that.data.group[setid] = setvalue;
						}
						that.showmodal = '';
					}else{
						core.toast(res.message,res.redirect,res.type);
					}
				},{setid:setid,setvalue:setvalue,saveset:"true",gid:this.data.group.id});
			},
			SwitchSet(e){
				let setid = e.target.dataset.id;
				let setvalue = e.detail.value ? 1 : 0;
				if(setid=='allowpost'){
					setvalue = setvalue==1 ? 0 : 1;
				}
				let that = this;
				core.post('group/detail',function(res){
					if(res.type=='success'){
						that.data.group[setid] = setvalue;
					}else{
						core.toast(res.message,res.redirect,res.type);
					}
				},{setid:setid,setvalue:setvalue,saveset:"true",gid:this.data.group.id});
			}
		}
	}
</script>

<style>
	.cu-item .newapply{position: relative;}
	.cu-item .newapply .badge{top: 50%; margin-top: -14upx; right: -24upx;}
	.cu-list.menu>.cu-item.arrow.arrow-up:before{
		content: "\e6de";
	}
	
	.group-notice{max-height: 30vh; font-size: 32upx; line-height: 50upx; text-indent: 64upx; text-align: left; white-space: pre-wrap; overflow: hidden;}
	
	.account-qrcode{position: relative;}
	.account-qrcode .qrcode{width: 480upx;}
</style>
