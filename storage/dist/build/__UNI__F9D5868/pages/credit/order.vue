<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="padding-bottom" v-else>
			<view class="padding text-empty" v-if="data.list.length==0">
				<text>空空如也</text>
			</view>
			<view class="cu-card article" v-else>
				<view class="cu-item shadow padding-xs" style="padding-bottom: 20upx;" v-for="(item, index) in data.list" :key="index" @click="naviTo(item.url)">
					<view class="flex solid-bottom padding-xs justify-between margin-bottom-sm">
						<view class="text-grey">
							<text>订单号：{{item.tid}}</text>
						</view>
						<view class="text-lg" :class="'text-'+colors[item.status]">{{item.state}}</view>
					</view>
					<view class="content" style="padding-left: 10upx;">
						<image :src="item.setting.cover" mode="aspectFill"></image>
						<view class="desc">
							<view class="title" style="padding-left: 0;"><view class="text-cut">{{item.setting.title}}</view></view>
							<view class="text-content">
								{{item.setting.summary || item.setting.remark}}
								<view class="price text-red text-xl">
									<text class="text-xs">￥</text>{{item.amount}}
								</view>
							</view>
						</view>
					</view>
					<view class="flex padding-xs justify-end" v-if="item.status==0">
						<view class="btn-group">
							<button class="cu-btn round line-gray" @tap.stop="cancelOrder(index)">取消订单</button>
							<button class="cu-btn round margin-left" :class="'bg-'+theme.actcolor">立即支付</button>
						</view>
					</view>
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
				loadMore:false,
				loadIng:false,
				theme:core.style,
				colors:['red','green','gray','gray'],
				data:{
					title:"我的订单",
					list:[]
				}
			}
		},
		onLoad() {
			
		},
		onShow() {
			this.initData('credit/orders',{page:1});
		},
		onPullDownRefresh() {
			this.initData('credit/orders',{page:1});
			uni.stopPullDownRefresh();
		},
		methods:{
			initData(route, data={}, callback=false){
				this.loadIng = true;
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.loaded = true;
					that.loadIng = false;
					that.loadMore = res.list.length<15 ? false : true;
					that.page = data.page;
					if(data.page==1){
						that.data = res;
						uni.setNavigationBarTitle({
							title:res.title
						});
					}else{
						that.data.list = that.data.list.concat(res.list);
					}
					if(callback){
						callback(res);
					}
				},data);
			},
			doNestPage(){
				if(this.loadIng) return false;
				if(!this.loadMore) return core.toast("没有更多了~");
				this.initData('credit/orders',{page:this.page+1});
			},
			cancelOrder(index){
				let self = this;
				return core.confirm('取消订单后可能无法享受此优惠价格，是否确定要取消？','取消订单',function(){
					return core.post('credit/orders',function(res){
						if(res.type!='success') return core.report(res);
						self.data.list[index].status=2;
						self.data.list[index].state='已取消';
					},{tid:self.data.list[index].tid,cancelorder:"true"})
				});
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			}
		}
	}
</script>

<style>
	.cu-card.article>.cu-item .title{line-height: 50upx;}
</style>
