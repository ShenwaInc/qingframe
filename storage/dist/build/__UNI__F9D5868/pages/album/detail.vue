<template>
	<view :class="'page-text-'+textSize">
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<block v-else>
			<view class="cu-card dynamic no-card">
				<view class="cu-item shadow">
					<view class="cu-list menu-avatar">
						<view class="cu-item">
							<view class="cu-avatar round lg" @tap.stop="naviTo('member/index',{uid:data.album.uid})" :style="'background-image:url('+data.album.avatar+');'"></view>
							<view class="content flex-sub">
								<view>{{data.album.nickname}}</view>
								<view class="text-sm flex justify-between">
									<text class="text-gray">{{data.album.datetime}}</text>
									<text class="text-orange" v-if="data.album.status==0">审核中</text>
								</view>
							</view>
						</view>
					</view>
					<view class="text-content richtext margin-top-sm" style="margin-bottom: 0;" @tap.stop="naviTo(data.album.url)">
						<rich-text :nodes="data.album.content+''"></rich-text>
					</view>
					<view @click="naviTo('player/index',{vid:data.album.vid})" class="chat-video padding-lr margin-top-sm" v-if="data.album.vid>0">
						<view class="bg-img onlyimg bg-video radius" style="height: 380upx; overflow: hidden;">
							<image :src="data.album.poster" style="width: 100%; min-height: 380upx;" mode="aspectFill" @error="posterReload(data.album.poster)"></image>
						</view>
					</view>
					<view class="grid flex-sub padding-lr margin-top-sm" :class="data.album.extraclass" v-if="data.album.pics.length>0">
						<view @click="doPreviewImage(pic)" class="bg-img" :class="data.album.extraclass==''?'onlyimg':''" v-for="(pic,key) in data.album.pics" :key="key">
							<image mode="aspectFill" :src="pic"></image>
						</view>
					</view>
					<view class="diary-tags margin-top-sm padding-lr" v-if="data.album.tags.length>0">
						<view class="cu-tag round light" :class="'bg-'+theme.actcolor" v-for="(tag, key) in data.album.tags" :key="key">
							<text>{{tag}}</text>
						</view>
					</view>
					<view @click="doOpenLocation()" v-if="data.album.position.status==1" class="padding-lr margin-top-sm" :class="'text-'+theme.actcolor">
						<text class="cuIcon-location"></text>
						<text class="">{{data.album.position.address}}({{data.album.position.distance}})</text>
					</view>
					<view class="padding-lr flex flex-direction margin-top-sm" v-if="data.ismanager">
						<view class="cu-btn lg round" @click="doAudit(1)" :class="'bg-'+theme.actcolor" v-if="data.album.status==0">
							<text>审核通过</text>
						</view>
						<view class="cu-btn lg round bg-orange" @click="doAudit(0)" v-else>
							<text>下架该动态</text>
						</view>
					</view>
					<view class="text-gray text-xl flex justify-end padding">
						<view>
							<text class="cuIcon-attentionfill margin-lr-xs"></text> {{data.album.views}}
						</view>
						<view @click="doPraise()">
							<text class="cuIcon-appreciatefill margin-lr-xs" :class="data.album.praised?'text-'+theme.actcolor:''"></text> {{data.album.praise}}
						</view>
						<view @tap="doReply(0)">
							<text class="cuIcon-messagefill margin-lr-xs"></text> {{data.album.comment}}
						</view>
						<view v-if="data.album.ismanager || data.ismanager" @click="doRemove()">
							<text class="cuIcon-deletefill text-red margin-lr-xs"></text>
						</view>
						<block v-if="data.album.status>0">
							<view @click="doCollect()" class="margin-left-xs">
								<text class="cuIcon-favorfill margin-lr-xs" :class="'cuIcon-favor'+(data.album.collected?'fill text-'+theme.actcolor:'')"></text>
							</view>
							<view v-if="platform=='wxapp'">
								<button class="nobtn cu-btn bg-white sm" style="font-size: 34upx; margin-top: -2upx;" open-type="share">
									<text class="cuIcon-share text-gray"></text>
								</button>
							</view>
							<view class="margin-left-xs" @click="showshare=true" v-else>
								<text class="cuIcon-share"></text>
							</view>
						</block>
						<view class="margin-left-sm" v-if="data.isreport" @click="naviTo('report/index', {type:'moment', id:data.album.id})">
							<text class="text-lg">投诉</text>
						</view>
					</view>
					<advs :advs="advs.moment" :extraclass="'padding-sm bg-gray'"></advs>
					<view class="cu-list menu-avatar comment solid-top" v-if="data.comments.length>0">
						<view class="cu-item" v-for="(comment, index) in data.comments" :key="index">
							<view class="cu-avatar round" @tap.stop="naviTo('member/index',{uid:comment.uid})" :style="'background-image:url('+comment.avatar+');'"></view>
							<view class="content">
								<view class="text-grey">{{comment.nickname}}</view>
								<view class="text-gray text-content text-df">
									{{comment.content}}
								</view>
								<view class="bg-gray radius margin-tb-sm comments" v-if="comment.comments.length>0">
									<view class="cu-list menu">
										<view class="cu-item sm nobg" v-for="(comm,key) in comment.comments" :key="key">
											<view class="content text-sm text-cut">
												<text :style="'color:'+theme.link">{{comm.nickname}}：</text>
												<text class="text-warp">{{comm.content}}</text>
											</view>
											<view class="action" style="width: auto;">
												<text class="text-xs text-gray">{{comm.datetime}}</text>
												<text @click="doReply(comment.id,comm.nickname,true)" class="text-sm text-gray cuIcon-message margin-left-xs"></text>
												<text @click="doRemoveCm(comm.id)" v-if="userId==comm.uid || data.album.ismanager" class="text-sm text-red cuIcon-delete margin-left-xs"></text>
											</view>
										</view>
									</view>
								</view>
								<view class="margin-top-sm flex justify-between">
									<view class="text-gray text-df">{{comment.datetime}}</view>
									<view class="flex">
										<view @click="doPraiseCm(comment.id,index)" :class="comment.praised?'text-'+theme.actcolor:'text-gray'">
											<text class="cuIcon-appreciatefill"></text> {{comment.praise}}
										</view>
										<view class="text-gray padding-left-sm" @click="doReply(comment.id,comment.nickname,false)">
											<text class="cuIcon-messagefill"></text> {{comment.comment}}
										</view>
										<view class="text-red padding-left-sm" v-if="userId==comment.uid || data.album.ismanager" @click="doRemoveCm(comment.id)">
											<text class="cuIcon-delete"></text>
										</view>
									</view>
								</view>
							</view>
						</view>
					</view>
					<view class="text-empty bg-gray" v-else>
						<text>评论区空空如也，快来抢沙发吧~</text>
					</view>
				</view>
			</view>
			<view class="commentname bg-grey radius padding-sm" v-if="commentname!=''">
				<text>回复{{commentname}}</text>
			</view>
			<view class="cu-bar tabbar input foot" v-if="data.album.status>0">
				<view class="action" @click="doPraise()">
					<view :class="data.album.praised?'cuIcon-appreciatefill text-'+theme.actcolor:'cuIcon-appreciate text-gray'">
						<view class="cu-tag badge" v-if="data.album.praise!=0">{{data.album.praise}}</view>
					</view>
				</view>
				<input @confirm="doComment" :focus="autofocus" @blur="autofocus=false;commentname='';" :class="comment.content!=''?'hasval':''" v-model="comment.content" placeholder="来一条高能的评论" :adjust-position="true" cursor-spacing="10" class="solid-bottom" maxlength="300"></input>
				<button class="cu-btn shadow-blur" :class="'bg-'+theme.actcolor" v-if="comment.content!=''" @click="doComment">发表</button>
			</view>
		</block>
		<share :shareinfo="shareinfo" @closeshare="showshare=false" :showshare="showshare"></share>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import share from "@/components/util/share.vue"
	import advs from "@/components/util/advs.vue"
	
	export default {
		computed: mapState(['userId', 'textSize']),
		components: {share,advs},
		data() {
			return {
				aid:0,
				loaded:false,
				comment:{
					aid:0,
					cid:0,
					content:"",
					type:"album"
				},
				theme:core.style,
				platform:core.platform,
				showshare:false,
				shareinfo:{
					url:'',
					providers:4,
					provider:{}
				},
				advs:{
					'moment':[]
				},
				InputBottom:0,
				autofocus:false,
				commentname:"",
				data:{
					title:"查看动态",
					album:{
						addtime: 0,
						avatar: core.system.logo,
						collected:false,
						comment: 0,
						comments: {id: 0},
						content: "",
						dateline: 0,
						datetime: "",
						extraclass: "",
						id: 0,
						ismanager:false,
						nickname: "",
						openid: 0,
						pics: [],
						poster: "",
						position:{status:0},
						praise: 0,
						praised: false,
						pralog: {},
						status: 1,
						tags:[],
						uid: 0,
						uniacid: 1,
						url: "",
						vid: 0,
						views: 0,
						warned: 0
					},
					ismanager:false,
					isreport:false,
					comments:[],
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
				if(typeof(options.aid)=='undefined') options.aid = core.urlParams(query, 'aid');
			}
			if(typeof(options.aid)=='undefined' || !options.aid) return core.toast('找不到该动态','back');
			this.aid = parseInt(options.aid);
			this.comment.aid = this.aid;
			let self = this;
			this.initData('album/detail',{aid:this.aid},function(res){
				core.cachecloud('advs',function(advs){
					self.advs.moment = advs.moment;
				});
			});
		},
		onShow() {
			
		},
		onResize(e){
			console.log(e);
		},
		onShareAppMessage(e){
			return {
				title:this.data.shareinfo.title,
				path:core.page('album/detail',{aid:this.aid,fromuid:this.userId}),
			}
		},
		onShareTimeline(e){
			return {
				title:this.data.shareinfo.title,
				query:'aid='+this.aid+'&fromuid='+this.userId
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
					that.shareinfo = core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
					if(callback){
						callback(res);
					}
				},data);
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			doOpenLocation(){
				let locations = this.data.album.position;
				// #ifdef H5
				if(core.inwechat){
					return core.initjwx(function(jwx){
						jwx.openLocation({
							latitude:locations.latitude,
							longitude:locations.longitude,
							name:locations.name,
							address:locations.address
						});
					});
				}
				// #endif
				// #ifdef MP-WEIXIN
				return wx.openLocation({
					latitude:locations.latitude,
					longitude:locations.longitude,
					name:locations.name,
					address:locations.address
				});
				// #endif
				let locationurl = 'https://apis.map.qq.com/uri/v1/marker?marker=coord:'+locations.latitude+','+locations.longitude+';title:'+locations.name+';addr:'+locations.address+'&referer='+core.system.name;
				core.navito('web/index',{title:'查看位置','url':encodeURIComponent(locationurl)});
			},
			doAudit(status=0){
				if(!this.data.ismanager) return core.toast('您无权作此操作');
				let confirmtext = this.data.album.status==0 ? '审核通过后将出现在广场/朋友圈，是否确定要通过？' : '下架后将无法在广场/朋友圈显示，是否确定要下架？';
				let self = this;
				core.confirm(confirmtext,'审核动态',function(){
					core.post('album/audit',function(res){
						if(res.type=='success'){
							self.data.album.status = status;
						}
						core.report(res);
					},{aid:self.data.album.id,status:status});
				});
			},
			doCollect(){
				if(this.data.album.collected) return core.toast('请勿重复收藏');
				let self = this;
				return core.post('album/collect',function(res){
					let redirect = res.redirect;
					if(res.type=='success'){
						self.data.album.collected = true;
						redirect = '';
					}
					core.toast(res.message,redirect,res.type);
				},{docollect:"true",aid:this.data.album.id})
			},
			posterReload(pic){
				let reload = 1;
				if(pic.indexOf('?reload')>-1){
					reload += 1;
				}
				var that = this;
				setTimeout(function(){
					that.data.album.poster = pic+'?reload='+reload;
				},2000);
			},
			doPreviewImage(pic){
				uni.previewImage({
					current:pic,
					urls:this.data.album.pics
				})
			},
			doPraiseCm(cid,index){
				if(cid==0) return core.toast('参数错误');
				let that = this;
				core.get("comment/praise",function(res){
					if(res.type=='success'){
						that.data.comments[index].praised = res.action=='cancel' ? false : true;
						that.data.comments[index].praise = res.html;
					}else{
						core.toast(res.message,res.redirect,res.type);
					}
				},{cid:cid})
			},
			doRemove(){
				if(!this.data.album.ismanager && !this.data.ismanager) return core.toast('您无权作此操作');
				let that = this;
				core.confirm('删除后该动态下的所有评论等数据都将被清理且不可恢复','删除动态',function(){
					core.get('album/delete',function(res){
						let redirect = res.type=='success' ? 'back' : res.redirect;
						core.toast(res.message,redirect,res.type);
					},{aid:that.data.album.id});
				});
			},
			doRemoveCm(cid=0){
				if(cid==0) return core.toast('参数错误');
				let that = this;
				core.confirm('删除后该评论下的所有点赞和回复将被清理且不可恢复，是否确定要删除？','删除评论',function(){
					core.get("comment/delete",function(res){
						let redirect = res.type=='success' ? '' : res.redirect;
						if(res.type=='success'){
							that.comment.cid = 0;
							that.initData('album/detail',{aid:that.aid});
						}
						core.toast(res.message,redirect,res.type);
					},{cid:cid});
				});
			},
			doReply(cid=0,nickname='',autorpl=false){
				this.comment.cid = cid;
				this.commentname = nickname;
				if(autorpl){
					this.comment.content = '回复 '+nickname+'：';
				}else{
					this.comment.content = '';
				}
				this.autofocus = true;
			},
			doComment(){
				if(this.comment.content==='') return core.toast('先说点什么吧~');
				let that = this;
				core.post("comment/post",function(res){
					let redirect = res.type=='success' ? '' : res.redirect;
					if(res.type=='success'){
						that.initData('album/detail',{aid:that.aid});
						that.comment.cid = 0;
						that.comment.content = '';
					}
					core.toast(res.message,redirect,res.type);
				},{data:this.comment});
			},
			doPraise(){
				let that = this;
				core.get("album/praise",function(res){
					if(res.type=='success'){
						that.data.album.praised = res.action=='cancel' ? false : true;
						that.data.album.praise = res.html;
					}else{
						core.toast(res.message,res.redirect,res.type);
					}
				},{aid:this.data.album.id});
			},
			InputFocus(e) {
				this.InputBottom = e.detail.height;
				console.log(e.detail.height);
			}
		}
	}
</script>

<style>
	page{padding-bottom: 120upx;}
	.commentname{position: fixed; bottom: 120upx; left: 100upx;}
	.commentname::after{content: ' '; width: 20upx; height: 20upx; position: absolute; left: 50%; margin-left: -10upx; bottom: -10upx; transform: rotate(45deg); background-color: #8799a3;}
	.foot.input input.solid-bottom{flex: 5;}
	.foot.input input.solid-bottom.hasval{flex: 4;}
	.comments .cu-item{min-height: 66upx; height: auto !important; padding:10upx 10upx 10upx 20upx;}
	.comments .cu-item .text-sm{font-size: 26upx;}
	.cu-card.dynamic>.cu-item .only-img{height: auto;}
	.page-text-xl .cu-card.dynamic>.cu-item>.text-content{font-size: 40upx !important;}
	.page-text-xl .comment .text-content{font-size: 38upx; padding-top: 10upx;}
	.page-text-lg .cu-card.dynamic>.cu-item>.text-content{font-size: 36upx !important;}
	.page-text-lg .comment .text-content{font-size: 34upx; padding-top: 5upx;}
</style>
