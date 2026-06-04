<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
			<block slot="right">
				<view class="action right" @click="naviTo('search/index',{type:'group'})">
					<text class="cuIcon-search"></text>
				</view>
			</block>
		</cu-custom>
		<view>
			<view v-if="data.groups.length>0" class="cu-list menu-avatar">
				<view class="cu-item arrow" @click="naviTo('group/detail',{gid:item.id})" v-for="(item, index) in data.groups" :key="index">
					<view class="cu-avatar round lg" :style="'background-image:url('+item.pic+');'"></view>
					<view class="content">
						<view class="text-xl">
							<view class="text-cut">{{item.name}}</view>
						</view>
						<view class="text-gray text-sm flex">
							<view class="text-cut">
								{{item.description || '群主是相当的懒，啥都不肯说'}}
							</view>
						</view>
					</view>
				</view>
			</view>
			<view v-else>
				<view class="text-empty">
					{{text.text_empty}}
				</view>
				<view class="padding text-center">
					<button class="cu-btn" :class="'bg-'+theme.actcolor" @click="naviTo('group/create')">立即创建</button>
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
				text:core.langs,
				theme:core.style,
				page:1,
				loadmore:false,
				data:{
					title:"全部群组",
					groups:[]
				}
			}
		},
		onLoad() {
			this.loadGroups(1,true);
		},
		onShow() {
			
		},
		onReachBottom() {
			if(!this.loadmore) return core.toast('没有更多了~');
			this.loadGroups(this.page+1);
		},
		onPullDownRefresh() {
			this.loadGroups(1);
			uni.stopPullDownRefresh();
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
			loadGroups(page=1,isinit=false){
				let that = this;
				core.get("group",function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, isinit);
					}
					if(isinit){
						uni.setNavigationBarTitle({
							title:res.title
						});
					}
					that.page = page;
					if(page==1){
						that.data.groups = res.groups;
					}else{
						that.data.groups = that.data.groups.concat(res.groups);
					}
					that.loadmore = res.groups.length<20 ? false : true;
				},{page:page});
			},
			unSupport(){
				return core.toast('敬请期待');
			}
		}
	}
</script>

<style>
	.cu-list.menu-avatar:not(.special-content)>.cu-item.arrow .content{width: calc(100% - 48px - 30px - 60px);}
</style>
