<template>
	<view>
		<cu-custom :Barborder="'solid-bottom'" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="text-empty" v-if="data.members.length==0">
				<text>{{data.ranking.type==2?'正在获取定位...':text.text_empty}}</text>
			</view>
			<block v-else>
				<view class="cu-list menu-avatar">
					<view class="cu-item arrow" @click="naviTo(item.url)" v-for="(item, index) in data.members" :key="index">
						<view class="cu-avatar round lg" :style="'background-image:url('+item.avatar+');'">
							<view class="cu-tag badge" :class="'cuIcon-'+(item.gender==1?'male bg-blue':'female bg-pink')" v-if="item.gender>0"></view>
						</view>
						<view class="content">
							<view class="text-xl">{{item.nickname}}</view>
							<view class="text-gray text-sm flex">
								<view class="text-cut">
									{{item.summary || '这家伙很懒，什么都没留下'}}
								</view>
							</view>
						</view>
						<view class="action" v-if="data.ranking.type==2">
							<text class="cu-tag round light" :class="'bg-'+theme.actcolor">{{item.distance}}</text>
						</view>
					</view>
				</view>
				<view class="text-empty" v-if="!loadmore">
					<text>{{text.msg_no_anymore}}</text>
				</view>
			</block>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				rid:0,
				theme:core.style,
				text:core.langs,
				inwechat:core.inwechat,
				page:1,
				loaded:false,
				loadmore:false,
				posupdated:0,
				data:{
					title:"用户列表",
					ranking:{
						id:0,
						type:1
					},
					lbskey:"",
					members:[]
				}
			}
		},
		onLoad(options) {
			if(typeof(options.id)=='undefined' || !options.id) return core.toast('找不到该页面','back');
			this.rid = parseInt(options.id);
			this.loadMembers(1);
		},
		onShow() {
			
		},
		onPullDownRefresh() {
			this.loadMembers(1);
			uni.stopPullDownRefresh();
		},
		onReachBottom() {
			if(!this.loadmore || this.data.ranking.type==1) return false;
			this.loadMembers(this.page+1);
		},
		methods:{
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			updatepos(ret){
				let self = this;
				core.post("member/position",function(res){
					if(res.type=='success'){
						self.posupdated = 2;
						return self.loadMembers(1);
					}
					self.posupdated = 0;
					core.report(res);
				},{poslat:ret.lat,poslng:ret.lng});
			},
			updatePostion(){
				if(this.posupdated!=0 || this.data.ranking.type!=2) return false;
				this.posupdated = 1;
				core.getpos(this.updatapos, true);
			},
			loadMembers(page=1){
				let self = this;
				core.get("ranking",function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, page==1?true:false);
					}
					self.loadmore = res.members.length<15 ? false : true;
					if(page==1){
						self.data = res;
						uni.setNavigationBarTitle({
							title:res.title
						});
						if(res.ranking.type==2){
							self.updatePostion();
						}
					}else{
						self.data.members = self.data.members.concat(res.members);
					}
					self.page = page;
					self.loaded = true;
				},{id:this.rid,page:page})
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
</style>
