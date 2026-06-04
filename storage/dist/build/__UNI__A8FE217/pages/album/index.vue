<template>
	<view :class="'page-text-'+textSize">
		<view class="status_bar" :class="theme.navbg" v-if="pagescroll>=headheight"></view>
		<view class="head">
			<view class="albumbg" @click="showmodal='actionsheet'" v-if="data.member.uid==userId">
				<image mode="widthFix" :src="data.albumbgurl" class="poster"></image>
			</view>
			<view class="albumbg" @click="doPreviewImage(data.albumbgurl,-1)" v-else>
				<image mode="widthFix" :src="data.albumbgurl" class="poster"></image>
			</view>
			<view class="cu-bar fixed" :class="pagescroll<headheight?'topbar':theme.navbg">
				<view class="action" @click="doBack()">
					<text class="cuIcon-back"></text>
				</view>
				<view class="content">{{data.title}}</view>
				<!-- #ifndef MP-WEIXIN -->
				<view class="action" v-if="data.allowpost" @click="naviTo('album/post'+(data.member.uid==userId?'?forward=back':''))">
					<text class="cuIcon-cameraadd"></text>
				</view>
				<!-- #endif -->
			</view>
			<view class="userbar flex justify-end padding" @click="naviTo('member/index',{uid:data.member.uid})">
				<view class="padding-top-sm">
					<text class="text-shadow text-lg text-bold text-white">{{data.member.nickname}}</text>
				</view>
				<view class="padding-left-sm">
					<view class="cu-avatar lg radius" :style="'background-image:url('+data.member.avatar+')'"></view>
				</view>
			</view>
		</view>
		<swaload v-if="!loaded"></swaload>
		<view v-else>
			<view class="cu-bar search bg-white" v-if="data.allowpost">
				<view class="cu-avatar round" :style="'background-image:url('+myavatar+')'"></view>
				<view class="search-form round" @tap="naviTo('album/post')">
					<text class="cuIcon-camerafill"></text>
					<text class="text-gray">今日风和日丽，来说点什么吧~</text>
				</view>
				<view class="action" @click="showmodal='citypicker'" v-if="data.location">
					<text>{{curcity||'地区'}}</text>
					<text class="cuIcon-triangledownfill"></text>
				</view>
			</view>
			<scroll-view scroll-x class="bg-white nav solid-top" scroll-with-animation v-if="data.tags.length>0">
				<view class="cu-item" :class="viewtag==''?'cur text-'+theme.actcolor:''" @click="doTaptag('')">全部</view>
				<view class="cu-item" :class="viewtag==tag?'cur text-'+theme.actcolor:''" v-for="(tag,index) in data.tags" :key="index" @click="doTaptag(tag)">
					{{tag}}
				</view>
			</scroll-view>
			<view>
				<view v-if="data.albums.length!=0">
					<view class="cu-card dynamic">
						<block v-for="(item, index) in data.albums" :key="index">
							<moment :index="index" :item="item" @onResult="onResult"></moment>
							<advs :advs="advs.pengyouquan" :extraclass="'margin-lr'" v-if="index==data.advsindex && data.advsindex>0"></advs>
						</block>
					</view>
					<view v-if="!loadmore" class="padding-xl text-center text-gray">
						<text>没有更多了~</text>
					</view>
				</view>
				<view class="text-empty" v-else-if="!loading">
					<text>空空如也</text>
				</view>
				<view v-if="loading" class="cu-load bg-gray loading"></view>
			</view>
			<view class="cu-modal bottom-modal" v-if="data.member.uid==userId" :class="showmodal=='actionsheet'?'show':''">
				<view class="cu-dialog">
					<view class="padding">
						<view class="flex flex-direction">
							<button @click="doChangeBg()" class="cu-btn bg-green lg">更换背景图</button>
							<button @click="showmodal=''" class="cu-btn bg-grey lg margin-top">取  消</button>
						</view>
					</view>
				</view>
			</view>
			<citypicker :ShowPicker="showmodal=='citypicker'" @onResult="doChangeCity" @onCancel="showmodal=''" :city="curcity" title="按地区筛选" textbtn="浏览" textclear="清空" @onClear="doClearCity"></citypicker>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import advs from "@/components/util/advs.vue"
	import swaload from "@/components/util/swaload.vue"
	import moment from "@/components/util/moment.vue"
	import citypicker from "@/components/util/citypicker.vue"
	
	export default {
		components: {advs, swaload, citypicker, moment},
		computed: mapState(['userId', 'textSize', 'userName']),
		data() {
			return {
				apiroute:"album",
				cacheKey:"Albumsalbum0",
				curcity:"",
				curprovince:"",
				loaded:false,
				loading:false,
				uid:0,
				page:1,
				viewtag:'',
				pagescroll:0,
				headheight:250,
				showmodal:"",
				barstatus:0,
				posters:[],
				theme:core.style,
				loadmore:true,
				myavatar:core.userinfo.avatar,
				advs:{
					pengyouquan:[]
				},
				data:{
					title:"广场",
					uid:0,
					advsindex:0,
					albumbgurl:core.system.siteroot+"/addons/xfy_whotalk/static/bg_album.jpg",
					albums:[],
					allowpost:false,
					citypicker:false,
					location:true,
					tags:[],
					member:{
						uid:0,
						nickname:"",
						avatar:core.system.logo
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
			if(typeof(options.uid)!='undefined' && options.uid!='') this.uid = parseInt(options.uid);
			if(typeof(options.sm)!='undefined') this.showmodal = options.sm;
			if(typeof(options.myself)!='undefined' && options.myself!=''){
				this.uid = this.userId;
			}
			if(typeof(options.friend)!='undefined' && options.friend!=''){
				this.apiroute = "album/friends";
				this.data.title = "朋友圈";
				uni.setNavigationBarTitle({
					title:"朋友圈"
				});
			}
			this.cacheKey = "Albums" + this.apiroute + this.uid;
			let self = this;
			core.cacheread(this.cacheKey, function(res){
				self.data.albumbgurl = res.data.albumbgurl;
				self.data.albums = res.data.albums;
				self.data.member = res.data.member;
				self.loaded = true;
			},function(){
				self.data.member.uid = self.uid || self.userId;
				self.data.member.nickname = self.data.member.uid==self.userId ? self.userName : '加载中...';
			});
			core.get(this.apiroute,function(res){
				self.data = res;
				self.loaded = true;
				self.saveLoacl();
				self.loadmore = res.albums.length<15 ? false : true;
				if(res.advsindex>0){
					core.cachecloud('advs',function(advs){
						self.advs.pengyouquan = advs.pengyouquan;
					});
				}
				self.loading = false;
				uni.createSelectorQuery().select('.albumbg').boundingClientRect(function(bgRes) {
					self.headheight = bgRes.height;
					// #ifdef APP-PLUS
					self.headheight -= core.Client.statusBarHeight;
					// #endif
				}).exec();
				core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
				uni.setNavigationBarTitle({
					title:res.title
				});
			},{uid:this.uid});
		},
		onShow() {
			if(this.data.uid>0 && this.page==1){
				this.loadAlbums(1);
			}
		},
		onPullDownRefresh() {
			this.doTaptag('');
			uni.stopPullDownRefresh();
		},
		onReachBottom() {
			if(!this.loadmore) return false;
			this.loading = true;
			this.loadAlbums(this.page+1);
		},
		onShareAppMessage(e){
			return {
				title:this.data.shareinfo.title,
				path:core.page('album/index',{fromuid:this.userId}),
			}
		},
		onShareTimeline(e){
			return {
				title:this.data.shareinfo.title,
				query:'fromuid='+this.userId
			}
		},
		onPageScroll(e) {
			this.pagescroll = e.scrollTop;
			// #ifdef APP-PLUS
			if(this.pagescroll>=this.headheight){
				if(this.barstatus==0){
					plus.navigator.setStatusBarStyle("dark");
					this.barstatus = 1;
				}
			}else{
				if(this.barstatus==1){
					plus.navigator.setStatusBarStyle("light");
					this.barstatus = 0;
				}
			}
			// #endif
		},
		methods:{
			loadAlbums(page=1, CallBack=false){
				let that = this;
				core.get(this.apiroute,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.page = page;
					that.loadmore = res.albums.length<15 ? false : true;
					that.showmodal = "";
					if(page==1){
						that.data = res;
						that.saveLoacl();
					}else{
						that.data.albums = that.data.albums.concat(res.albums);
					}
					that.loading = false;
					if(typeof(CallBack)=='function'){
						CallBack(res);
					}
				},{page:page,uid:this.uid,tag:this.viewtag,city:this.curcity});
			},
			saveLoacl(){
				let data = {
					albumbgurl:this.data.albumbgurl,
					member:this.data.member,
					albums:this.data.albums
				};
				core.cacheset(this.cacheKey, data);
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			onResult(res){
				let index = res.index;
				if(res.method=='praise'){
					this.data.albums[index].praised = res.action=='cancel' ? false : true;
					this.data.albums[index].praise = res.html;
				}
			},
			doTaptag(tag=''){
				this.viewtag = tag;
				this.loadAlbums(1);
			},
			doChangeBg(){
				if(this.userId!=this.data.member.uid) return core.toast('这不是你的相册~');
				let that = this;
				uni.chooseImage({
					count: 1, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (ret) => {
						core.upload(ret.tempFilePaths[0],function(res){
							core.post('member/setting',function (obj) {
								that.showmodal = '';
								if(obj.type=='success'){
									that.data.albumbgurl = res.url;
								}
								let redirect = obj.type=='success' ? '' : obj.redirect;
								core.toast(obj.message,redirect,obj.type);
							},{setkey:'albumbgurl',setvalue:res.path,savespset:"true"});
						});
					}
				});
			},
			doChangeCity(e){
				if(e.city==this.curcity) return this.showmodal = '';
				this.curcity = e.city;
				this.loadAlbums(1);
			},
			doClearCity(e){
				this.curcity = '';
				this.loadAlbums(1);
			},
			doBack(){
				return core.back();
			}
		}
	}
</script>

<style>
	.status_bar{width: 100%; height: var(--status-bar-height); position: fixed; left: 0; top: 0; z-index: 99;}
	.head{position: relative;}
	.head .cu-bar{top: var(--status-bar-height);}
	.userbar{position: absolute; right: 0; bottom: 0;}
	.userbar, .cu-bar.topbar,.topbar .cuIcon-cameraadd:before{text-shadow: 0 0 2px #000;}
	.topbar{box-shadow:none !important;}
	.topbar .action{color: #FFFFFF;}
	.topbar .content{display: none;}
	.albumbg{max-height: 560upx; overflow: hidden;}
	.albumbg image{width: 100%; min-height: 400upx; display: block;}
	.cu-card.dynamic>.cu-item>.text-content{margin-bottom: 0;}
	.cu-card.dynamic>.cu-item .onlyimg{height: auto; max-height: 320upx; overflow: hidden;}
	.chat-video .bg-video{height: auto !important; max-height: 360upx !important;}
	.chat-video .bg-video image{min-height: 360upx; width: 100%;}
</style>
