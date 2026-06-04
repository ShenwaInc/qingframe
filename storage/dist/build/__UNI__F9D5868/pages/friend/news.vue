<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="bg-gray">
				<view class="cu-bar search">
					<view class="search-form round text-center text-lg" @click="naviTo('search/index',{type:'member'})">
						<text class="cuIcon-search"></text>
						<text>搜索{{ data.showuid.title=='' ? "" : data.showuid.title+"/" }}手机号</text>
					</view>
				</view>
				<view class="text-center text-lg text-show margin-top-xs">
					<text @click="copyUid()" class="margin-right-sm" v-if="data.showuid.title!=''">我的{{data.showuid.title}}：{{data.showuid.id}}</text>
					<text class="cuIcon-qrcode" @click="naviTo('member/account', {showcode:1})"></text>
				</view>
				<view class="cu-bar solid-top margin-top-sm">
					<view class="action text-show">
						<text>近期好友</text>
					</view>
				</view>
			</view>
			<view class="cu-list menu-avatar solid-top" v-if="data.newfriends.length>0">
				<view class="cu-item" @click="naviTo('member/index',{uid:item.uid})" v-for="(item, index) in data.newfriends" :key="index">
					<view class="cu-avatar radius lg" :style="'background-image:url('+item.avatar+');'"></view>
					<view class="content">
						<view><view class="text-cut">{{item.nickname}}</view></view>
						<view class="text-gray text-sm flex"> <view class="text-cut">{{item.note}}</view></view>
					</view>
					<view class="action">
						<text class="text-grey text-xs" v-if="item.fstatus==1">已添加</text>
						<text class="text-grey text-xs" v-else-if="item.fstatus==0">已拒绝</text>
						<text class="text-orange text-xs" v-else>待添加</text>
					</view>
				</view>
			</view>
			<view class="text-empty" v-else>空空如也</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				loaded:false,
				data:{
					title:"新的朋友",
					newfriends:[],
					showuid:{
						title:"",
						id:""
					},
					revote:0
				}
			}
		},
		onLoad() {
		},
		onShow() {
			this.initData('friend/news');
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
					if(res.revote>0){
						uni.$emit('PEviewfriend',{total:res.revote});
					}
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
			copyUid(){
				return core.copy(this.data.showuid.id+"");
			}
		}
	}
</script>

<style>
	.text-show{color: #666666;}
	.cu-list.menu-avatar>.cu-item .action{width: 196upx;}
	.cu-list.menu-avatar>.cu-item .content{width: calc(100% - 196upx - 120upx - 20upx);;}
	.search-form{background-color: #ffffff; color: #aaaaaa; font-size: 30upx;}
</style>
