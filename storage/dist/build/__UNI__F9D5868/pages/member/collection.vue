<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="collection">
			<view v-if="data.collections.length==0" class="text-empty">
				{{text.text_empty}}
			</view>
			<view v-else>
				<view class="cu-card dynamic">
					<view class="cu-item" v-for="(item,index) in data.collections" :key="index">
						<view class="cu-list menu">
							<view class="cu-item arrow" @click="doViewCollection(item.id,index)">
								<view class="content">
									<view class="text-content">
										<text class="margin-right-xs" style="font-size: 40upx;" v-if="item.icon!=''" :class="[item.icon,'text-'+colors[item.type]]"></text>
										<text>{{item.remark}}</text>
									</view>
									<view class="cu-capsule radius">
										<view class="cu-tag sm" :class="'bg-'+theme.actcolor">
											来自
										</view>
										<view class="cu-tag sm" :class="'line-'+theme.actcolor">
											{{item.comefrom}}
										</view>
									</view>
									<view class="text-gray flex justify-between">
										<view>{{item.name}}</view>
										<view>{{item.datetime}}</view>
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>
				<view v-if="!loadmore" class="text-empty">
					{{text.msg_no_anymore}}
				</view>
			</view>
		</view>
		<view class="cu-modal" v-if="collection.id>0" :class="showmodal=='viewcollection'?'show':''">
			<view class="cu-modal-bg" @click="doCloseModal()"></view>
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">收藏详情</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="text-left">
					<view class="cu-card dynamic" v-if="collection.type=='album'">
						<view v-if="collection.source.id==0" class="text-empty">该动态已被删除</view>
						<view class="cu-item shadow" @click="naviTo(collection.url)" v-else>
							<view class="cu-list menu-avatar">
								<view class="cu-item arrow">
									<view class="cu-avatar round lg" :style="'background-image:url('+collection.source.avatar+');'"></view>
									<view class="content flex-sub">
										<view>{{collection.source.nickname}}</view>
										<view class="text-gray text-sm flex justify-between">
											{{collection.source.datetime}}
										</view>
									</view>
								</view>
							</view>
							<view class="text-content richtext margin-top-sm" style="margin-bottom: 0;">
								<rich-text :nodes="collection.source.content+''"></rich-text>
							</view>
							<view class="diary-tags margin-top-sm padding-lr" v-if="collection.source.pics!='' || collection.source.vid>0">
								<view class="cu-tag round light" :class="'bg-'+theme.actcolor" v-if="collection.source.pics!=''">
									<text>图片</text>
								</view>
								<view class="cu-tag round light" :class="'bg-'+theme.actcolor" v-if="collection.source.vid>0">
									<text>视频</text>
								</view>
							</view>
							<view class="text-gray text-lg text-right padding">
								<text class="cuIcon-attentionfill margin-lr-xs"></text> {{collection.source.views}}
								<text class="cuIcon-appreciatefill margin-lr-xs"></text> {{collection.source.praise}}
								<text class="cuIcon-messagefill margin-lr-xs"></text> {{collection.source.comment}}
							</view>
						</view>
					</view>
					<view class="cu-chat" v-else-if="collection.basic=='message'">
						<view v-if="collection.source.id==0" class="text-empty">该聊天记录已被删除</view>
						<msglist :message="collection.source" :userId="userId" :chatid="collection.source.chatid" :dialogtype="collection.source.gid>0?'group':'normal'" v-else></msglist>
						<view class="text-center padding-sm" @click="naviTo(collection.url)" :style="'color:'+theme.link" v-if="frompage==''">
							<text>进入聊天会话</text>
						</view>
					</view>
					<view class="cu-list menu">
						<view class="cu-item">
							<view class="content">
								<text class="text-grey">收藏于{{collection.datetime}}</text>
							</view>
						</view>
						<view class="cu-item" v-if="rename && frompage!='dialog'">
							<view class="content">
								<input type="text" :focus="inputfocus" v-model="collection.name" placeholder="请输入备注名称" />
							</view>
							<view class="action">
								<button @click="doSaveName(collection.id)" class="cu-btn sm bg-green">保存</button>
							</view>
						</view>
						<view class="cu-item" v-else>
							<view class="content">
								<text class="text-grey">备注：</text>
								<text>{{collection.name||'暂无备注'}}</text>
							</view>
							<view class="action text-blue text-lg" v-if="frompage==''" @click="rename=true,inputfocus=true;">
								<text class="cuIcon-edit"></text>
							</view>
						</view>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end solid-top">
					<view class="action">
						<block v-if="frompage=='dialog'">
							<button class="cu-btn margin-left" @click="doPostChat(collection.id)" :class="'bg-'+theme.actcolor">发送</button>
						</block>
						<block v-else>
							<button class="cu-btn line-red text-red" @tap="doRemoveCollection(collection.id)">删除</button>
							<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @tap="doCloseModal()">关闭</button>
						</block>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import msglist from "@/components/util/msglist.vue"
	
	export default {
		components: {msglist},
		computed: mapState(['userId']),
		data() {
			return {
				page:1,
				loading:false,
				loadmore:false,
				text:core.langs,
				theme:core.style,
				viewindex:-1,
				showmodal:"",
				frompage:"",
				rename:false,
				inputfocus:false,
				dialog:{
					cid:0,
					type:"normal",
					fp:"dialog"
				},
				colors:{
					html:'blue',
					text:'green',
					video:'orange',
					image:'cyan',
					album:'pink',
					location:"limegreen",
					audio:'mauve',
					transfer:'red',
					redpacket:'red'
				},
				data:{
					title:"我的收藏",
					collections:[],
					types:[],
					pagesize:15
				},
				collection:{
					id:0,
					basic:"message",
					content:"",
					datetime:"",
					name:"",
					url:"",
					poster:"",
					type:"text",
					comefrom:"",
					source:{
						id:0
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.fp)!='undefined') this.frompage = options.fp;
			if(this.frompage=='dialog'){
				this.dialog = options;
			}
		},
		onShow() {
			this.doLoadCollection(1);
		},
		onPullDownRefresh() {
			this.doLoadCollection(1);
			uni.stopPullDownRefresh();
		},
		onReachBottom() {
			if(!this.loadmore) return false;
			this.doLoadCollection(this.page+1);
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.data = res;
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
			doSaveName(cid){
				let self = this;
				return core.post('member/collection',function(res){
					core.report(res);
					if(res.type=='success'){
						self.rename = false;
						self.inputfocus = false;
						self.data.collections[self.viewindex].name = self.collection.name;
					}
				},{savename:"true",cid:cid,name:this.collection.name});
			},
			doCloseModal(){
				this.rename = false;
				this.inputfocus = false;
				this.viewindex = -1;
				this.showmodal = '';
			},
			doPostChat(cid){
				let postdata = this.dialog;
				postdata.id = cid;
				let self = this;
				core.post("dialog/collection",function(res){
					if(res.type!='success') return core.report(res);
					core.toast(res.message,res.redirect,'success');
				},postdata);
			},
			doRemoveCollection(cid){
				let that = this;
				core.confirm(this.text.text_delete_confirm,this.text.text_confirm_title,function(){
					core.get("member/delcollect",function(res){
						let redirect = res.type=='success' ? '' : res.redirect;
						if(res.type=='success'){
							that.data.collections.splice(that.viewindex,1);
							that.doCloseModal();
						}
						core.toast(res.message,redirect,res.type);
					},{cid:cid});
				});
			},
			doViewCollection(cid,index=0){
				if(!cid) return core.toast('无效的收藏编号');
				this.viewindex = index;
				let that = this;
				core.get("member/viewcollect",function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res);
					}
					that.collection = res;
					that.showmodal = 'viewcollection';
				},{cid:cid})
			},
			doLoadCollection(page=1){
				if(this.loading) return false;
				this.loading = true;
				let that = this;
				core.get('member/collection',function(res){
					that.loading = false;
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, page==1?true:false);
					}
					that.page = page;
					that.loadmore = res.collections.length<res.pagesize ? false : true;
					if(page==1){
						that.data = res;
						uni.setNavigationBarTitle({
							title:res.title
						});
					}else{
						that.data.collections = that.data.collections.concat(res.collections);
					}
				},{page:page});
			}
		}
	}
</script>

<style>
	.collection .cu-list.menu>.cu-item{padding: 20upx 30upx; padding-right: 90upx;}
	.collection .cu-capsule .cu-tag{margin: 0 !important;}
	.cu-modal .dynamic .text-content.richtext{max-height: 6.4em !important;}
</style>
