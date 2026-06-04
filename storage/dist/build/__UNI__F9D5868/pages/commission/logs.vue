<template>
	<view>
		<cu-custom bgColor="bg-white" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="solid-top padding-top-sm" v-else>
			<view class="text-empty" v-if="data.logs.length==0">
				<text class="text-lg">空空如也</text>
			</view>
			<view class="logs" v-else>
				<view class="cu-list menu sm-border">
					<view class="cu-item" v-for="(item, index) in data.logs" :key="index">
						<view class="content padding-tb-sm">
							<view class="text-black text-boldm text-cut">
								<text>{{item.summary || "邀请用户消费获得奖励"}}</text>
							</view>
							<view class="text-gray text-sm">
								{{item.datetime}} <text class="padding-left-sm">{{item.level}}</text>
							</view>
						</view>
						<view class="action">
							<text class="text-lg text-red">+</text>
							<text class="text-xl text-red">
								{{item.commission}}
							</text>
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
				data:{
					title:"佣金明细",
					logs:[]
				}
			}
		},
		onLoad() {
			this.getMoreList(1);
		},
		onShow() {
			
		},
		methods:{
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			getMoreList(page=1){
				let self = this;
				return core.get('commission/logs',function(res){
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
						self.data.logs = self.data.logs.concat(res.logs);
					}
					self.loadmore = res.logs.length>=15;
					self.page = page;
				},{page:page});
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
</style>
