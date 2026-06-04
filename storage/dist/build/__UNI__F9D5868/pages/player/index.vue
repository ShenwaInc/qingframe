<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="head bg-white">
				<view class="cover">
					<video id="myVideo" :autoplay="data.autoplay" show-mute-btn="true" :title="data.title" :src="playurls[playmode]" :poster="data.video.poster"></video>
					<view class="flex padding justify-between">
						<view class="text-gray">发布时间：{{data.video.datetime}}</view>
						<view class="text-sm">
							<view class="cu-tag" :class="(playmode=='no'?'bg-':'line-')+theme.actcolor" @click="playmode='no'">原画</view>
							<view class="cu-tag" v-if="playurls.hd!=''" :class="(playmode=='hd'?'bg-':'line-')+theme.actcolor" @click="playmode='hd'">高清</view>
							<view class="cu-tag" v-if="playurls.sd!=''" :class="(playmode=='sd'?'bg-':'line-')+theme.actcolor" @click="playmode='sd'">标清</view>
							<view class="cu-tag bg-orange" v-if="data.downable" @click="doDownload()"><text>下载</text></view>
						</view>
					</view>
				</view>
			</view>
			<view class="user-card padding-sm bg-gray">
				<view class="cu-list menu menu-avatar shadow radius" v-if="data.video.uid>0 && data.viewauthor">
					<view class="cu-item arrow" @click="naviTo('member/index',{uid:data.video.uid})">
						<view class="cu-avatar radius lg" :style="'background-image:url('+data.video.avatar+');'"></view>
						<view class="content">
							<view><view class="text-cut">{{data.video.nickname}}</view></view>
							<view class="text-gray text-sm flex"> <view class="text-cut">{{data.video.bio||'这家伙很懒，什么都没留下'}}</view></view>
						</view>
					</view>
				</view>
			</view>
			<view class="video-comment">
				<view class="cu-bar solid-bottom">
					<view class="action title-style-3">
						<text class="text-lg text-bold text-black">大家说({{data.comment}})</text>
					</view>
					<view class="action">
						<button class="cu-btn sm round-only-left" :class="'bg-'+theme.actcolor" @click="showmodal='docomment'">我来评论</button>
						<button class="cu-btn sm round-only-right bg-orange" open-type="share" v-if="platform=='wxapp'">分享</button>
						<button class="cu-btn sm round-only-right bg-orange" @click="showshare=true" v-else>分享</button>
					</view>
				</view>
				<comment :showempty="true" @reload="loadComment(1)" :comments="data.comments"></comment>
				<view class="text-center padding margin-top" @click="loadComment(commentpage+1)" v-if="commentmore">
					<view class="text-blue">更多精彩评论</view>
				</view>
			</view>
		</view>
		<view class="cu-modal bottom-modal" :class="showmodal=='docomment'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white">
					<view class="action text-grey" @tap="showmodal=''">取消</view>
					<view class="action text-green" @tap="docomment(0)">发表</view>
				</view>
				<view class="padding-sm bg-white">
					<view class="cu-form-group bg-gray text-left">
						<textarea maxlength="-1" v-model="commentcontent" placeholder="据说友爱发言的人都比较受欢迎"></textarea>
					</view>
				</view>
			</view>
		</view>
		<share :shareinfo="shareinfo" @closeshare="showshare=false" :showshare="showshare"></share>
		<advs :isPopup="true" :advs="advs.player"></advs>
	</view>
</template>

<script>
	import core from "@/core.js"
	import comment from "@/components/util/comment.vue"
	import share from "@/components/util/share.vue"
	import advs from "@/components/util/advs.vue"
	// #ifdef APP-PLUS
	import permision from "@/TrtcCloud/permission.js"
	// #endif
	
	export default {
		components: {comment, share, advs},
		data() {
			return {
				vid:0,
				loaded:false,
				showmodal:'',
				theme:core.style,
				playmode:'no',
				playurls:{
					'no':'',
					'hd':'',
					'sd':''
				},
				advs:{
					'player':[]
				},
				commentmore:false,
				commentpage:1,
				commentcontent:"",
				platform:core.platform,
				showshare:false,
				shareinfo:{
					url:'',
					providers:4,
					provider:{}
				},
				data:{
					title:"播放器",
					autoplay:true,
					comment:0,
					comments:[],
					downable:true,
					video:{
						id:0,
						name:"",
						poster:"",
						videourl:"",
						videohd:"",
						videosd:"",
						datetime:"",
						uid:0,
						nickname:"",
						avatar:"",
						bio:""
					},
					viewauthor:0,
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
			if(typeof(options.vid)=='undefined' || !options.vid) return core.toast('无效的视频编号','back');
			this.vid = options.vid;
			var that = this;
			this.initData('player',{vid:this.vid},function(res){
				that.playurls = {
					'no':res.video.videourl,
					'hd':res.video.videohd,
					'sd':res.video.videosd
				};
				that.commentmore = res.comments.length<15 ? false : true;
				that.shareinfo = core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
				core.cachecloud('advs',function(advs){
					that.advs.player = advs.player;
				});
			});
		},
		onShow() {
			
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					if(typeof(res.downable)=='undefined'){
						res.downable = true;
					}
					that.data = res;
					that.loaded = true;
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
			unSupport(){
				return core.toast('敬请期待');
			},
			doDownload(){
				let downUrl = this.playurls[this.playmode];
				return core.navito("player/storage",{url:core.encodeurl(downUrl)});
			},
			docomment(cid=0){
				let postdata = {
					content:this.commentcontent,
					aid:this.data.video.id,
					cid:cid,
					type:'video'
				};
				var that = this;
				return core.post("comment/post",function(res){
					if(res.type!='success') return core.toast(res.message);
					that.showmodal = '';
					that.commentcontent = '';
					core.toast(res.message,'','success');
					that.loadComment(1);
				},{data:postdata});
			},
			loadComment(page=1){
				let postdata = {
					type:'video',
					aid:this.data.video.id,
					page:page
				}
				var that = this;
				return core.get('comment',function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined') return core.report(res);
					that.commentmore = res.comments.length<15 ? false : true
					that.commentpage = page;
					that.data.comment = res.total;
					if(page>1){
						that.data.comments = that.data.comments.concat(res.comments);
					}else{
						that.data.comments = res.comments;
					}
				},postdata)
			},
			async requestAndroidPermission(permisionID) {
			    var result = await permision.requestAndroidPermission(permisionID);
				//console.log(permisionID,result);
			    return result;
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
</style>
