<template>
	<view>
		<cu-custom bgColor="bg-red" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="bg-gray">
			<view class="head bg-white">
				<view class="head-rd bg-red"></view>
			</view>
			<view class="padding bg-white">
				<view class="cu-list menu-avatar margin-lr-xl">
					<view class="cu-item arrow noaf" @click="naviTo('member/index',{uid:data.redpacket.uid})">
						<view class="cu-avatar round lg" :style="'background-image:url('+data.redpacket.avatar+');'"></view>
						<view class="content" style="width: calc(100% - 48px - 60px);">
							<view class="text-black"><text class="text-lg">{{data.redpacket.nickname}}的红包</text></view>
							<view class="text-gray flex">
								<view class="text-cut">
									{{data.redpacket.remark}}
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="bg-white padding-xl text-center text-darkgold" v-if="data.received.id>0">
				<view>
					<text class="text-bold text-xsl">{{data.received.amount}}</text>
					<text class="text-lg margin-left-xs">元</text>
				</view>
				<view class="padding-bottom" @click="naviTo('credit/index')">
					<text>已存入钱包，可直接消费</text><text class="cuIcon-right"></text>
				</view>
			</view>
			<view class="bg-white margin-top">
				<view class="padding text-lg solid-bottom">
					<text>{{data.redpacket.counts}}个红包共{{data.redpacket.amount}}元</text>
					<text v-if="data.grabstr!=''">，{{data.grabstr}}被抢光</text>
				</view>
			</view>
			<view class="bg-white">
				<view class="text-empty" v-if="data.receivers.length==0">
					<text>还没有人领取该红包</text>
				</view>
				<view class="cu-list menu-avatar" v-else>
					<view class="cu-item" v-for="(item,index) in data.receivers" :key="index">
						<view class="cu-avatar round lg" :style="'background-image:url('+item.avatar+');'"></view>
						<view class="content">
							<view class="text-black"><text class="text-lg">{{item.nickname}}</text></view>
							<view class="text-gray flex">
								<view class="text-cut">
									{{item.datetime}}
								</view>
							</view>
						</view>
						<view class="action">
							<view class="text-red">
								<text class="text-lg">{{item.amount}}元</text>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-bar foot noshadow no-border text-gray justify-center" v-if="data.redpacket.status!=2">
			<text v-if="data.redpacket.status==1">超过24小时未领取的红包将自动退还</text>
			<text v-else>红包已过期，{{data.redpacket.balance}}元未领取</text>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				rid:0,
				data:{
					title:"红包详情",
					redpacket:{
						id:0,
						gid:0,
						touid:0,
						uid:0,
						nickname:0,
						avatar:core.system.defaultimg,
						status:0,
						amount:0,
						balance:0,
						counts:0,
						remain:0,
						remark:""
					},
					receivers:[],
					received:{
						id:0,
						amount:0
					},
					grabstr:""
				}
			}
		},
		onLoad(options) {
			if(typeof(options.id)=='undefined' || !options.id) return core.toast('无效的红包信息','back');
			this.rid = parseInt(options.id);
			this.initData("redpacket/detail",{id:this.rid});
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
	page{background-color: #FFFFFF; padding-bottom: 100upx;}
	.head{height: 100upx; overflow: hidden; position: relative;}
	.head-rd{width: 1600upx; height: 1600upx; bottom: 0; position: absolute; left: 50%; margin-left: -800upx; border-radius: 50%;}
	
</style>
