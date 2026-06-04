<template>
	<view>
		<cu-custom bgColor="bg-white" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="nav padding-xs bg-white solid-bottom">
				<view class="cu-item" :class="data.view==1?'cur text-'+theme.actcolor:''" @click="getMyteams(1,1)">直接邀请</view>
				<view v-if="data.levels>=2" class="cu-item" :class="data.view==2?'cur text-'+theme.actcolor:''" @click="getMyteams(1,2)">间接推广</view>
				<view v-if="data.levels==3" class="cu-item" :class="data.view==3?'cur text-'+theme.actcolor:''" @click="getMyteams(1,3)">额外用户</view>
			</view>
			<view class="text-empty" v-if="data.invites.length==0">
				<text class="text-lg">空空如也</text>
			</view>
			<view class="invites" v-else>
				<view class="cu-list menu">
					<view class="cu-item arrow" @click="naviTo('member/index',{uid:member.uid})" v-for="(member, index) in data.invites" :key="index">
						<view class="content">
							<image :src="member.avatar" class="png radius" mode="aspectFit"></image>
							<text class="text-boldm">{{member.nickname}}</text>
						</view>
						<view class="action">
							<text class="text-grey text-sm">{{member.regtime}}</text>
						</view>
					</view>
				</view>
				<view class="padding-sm text-center">
					<text class="text-grey">{{loadmore?'上滑加载更多':'没有更多了'}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				loaded:false,
				page:1,
				loadmore:true,
				theme:core.style,
				data:{
					title:"我的邀请",
					view:1,
					levels:1,
					invites:[]
				}
			}
		},
		onLoad() {
			this.getMyteams(1);
		},
		onShow() {
			
		},
		onReachBottom() {
			if(!this.loadmore) return false;
			this.getMyteams(this.page+1,this.data.view);
		},
		methods:{
			getMyteams(page=1,view=1){
				this.data.view = view;
				let self = this;				
				return core.get('commission/teams',function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, page==1);
					}
					self.loaded = true;
					if(page==1){
						self.data = res;
						uni.setNavigationBarTitle({
							title:res.title
						});
					}else{
						self.data.invites = self.data.invites.concat(res.invites);
					}
					self.loadmore = res.invites.length>=15;
					self.page = page;
				},{page:page,view:view});
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
</style>
