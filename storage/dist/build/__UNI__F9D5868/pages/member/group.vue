<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
			<block slot="right">
				<view class="action right" @click="naviTo('group/create')">
					<text class="cuIcon-add"></text>
				</view>
			</block>
		</cu-custom>
		<view class="text-empty" v-if="data.groups.length==0">{{text.text_empty}}</view>
		<view class="cu-list menu-avatar" v-else>
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
				<view class="action" style="width: 140upx;" v-if="item.applys>0">
					<text class="cu-tag round bg-red light">有新申请</text>
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
				uid:0,
				text:core.langs,
				data:{
					title:"我的群组",
					groups:[]
				}
			}
		},
		onLoad() {
			this.initData('member/group');
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
			}
		}
	}
</script>

<style>
	.cu-list.menu-avatar>.cu-item .content{width: calc(100% - 216upx) !important;}
</style>
