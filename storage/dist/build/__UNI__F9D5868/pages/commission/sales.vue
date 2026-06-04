<template>
	<view>
		<cu-custom bgColor="bg-white" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="nav padding-xs solid-bottom">
				<view class="cu-item" @click="doGetOrders(1,index)" :class="data.view==index?'cur text-'+theme.actcolor:''" v-for="(state, index) in views" :key="index">
					{{state}}
				</view>
			</view>
			<view class="text-empty" v-if="data.orders.length==0">
				<text class="text-lg">空空如也</text>
			</view>
			<view class="orders" v-else>
				<view class="cu-card article">
					<view class="cu-item shadow padding-xs" style="padding-bottom: 20upx;" v-for="(item, index) in data.orders" :key="index">
						<view class="flex solid-bottom padding-xs justify-between margin-bottom-sm">
							<view class="text-grey">
								<text>订单号：{{item.tid}}</text>
							</view>
							<view :class="item.commissionlog.id==0?'text-gray':'text-green'">{{item.commissionlog.remark}}</view>
						</view>
						<view @click="naviTo(item.setting.redirect)" class="content" style="padding-left: 10upx;">
							<image :src="item.setting.cover" mode="aspectFill"></image>
							<view class="desc">
								<view class="title" style="padding-left: 0;"><view class="text-cut">{{item.setting.title}}</view></view>
								<view class="text-content">
									订单金额：￥{{item.amount}}
									<view class="price text-red text-xl">
										分销佣金：<text class="text-xs">￥</text>{{item.commissionlog.commission}}
									</view>
								</view>
							</view>
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
				views:["已完成", "待支付"],
				data:{
					title:"分销订单",
					orders:[],
					view:0
				}
			}
		},
		onLoad() {
			this.doGetOrders(1);
		},
		onShow() {
			
		},
		onReachBottom() {
			if(this.loadmore=='noMore') return false;
			this.doGetOrders(this.page+1,this.data.view);
		},
		methods:{
			doGetOrders(page=1,view=0){
				this.data.view = view;
				let self = this;				
				return core.get('commission/sales',function(res){
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
						self.data.orders = self.data.orders.concat(res.orders);
					}
					self.loadmore = res.orders.length>=15;
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
	.cu-card.article>.cu-item .title{line-height: 72upx;}
</style>
