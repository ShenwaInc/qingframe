<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="padding-sm light bg-yellow flex justify-between" v-if="data.paylog.id>0" @click="naviTo('credit/order')">
				<text class="text-lg text-red">您有未支付的订单，请及时支付</text>
				<text class="cuIcon-right"></text>
			</view>
			<view class="padding">
				<view class="cu-card">
					<view class="cu-list menu-avatar">
						<view class="cu-item">
							<view class="cu-avatar round lg" :style="'background-image:url('+data.userinfo.avatar+');'"></view>
							<view class="content flex-sub">
								<view>{{data.userinfo.nickname}}</view>
								<view class="text-gray text-sm margin-top-xs">
									{{data.account}}：{{data.userinfo.userid}}
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="text-empty" v-if="data.numbers.length==0">
				<text>来晚了，靓号被抢光了~</text>
			</view>
			<view class="grid col-2 cutenumbers" v-else>
				<view class="cu-card text-center" v-for="(item, index) in data.numbers" :key="index">
					<view class="cu-item shadow" @click="doBuy(item.id)">
						<view class="text-lg padding-bottom-xs">{{item.numbers}}</view>
						<view class="text-price text-red">{{item.price}}</view>
					</view>
				</view>
			</view>
			<view class="text-center margin-top-xl" v-if="loadmore">
				<view class="text-blue" @click="doViewMore()">查看更多</view>
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
				loadmore:false,
				data:{
					title:"购买靓号",
					userinfo:{
						"avatar":core.userinfo.avatar,
						"nickname":core.userinfo.nickname,
						"userid":"",
						"uid":0
					},
					numbers:[],
					account:"UID",
					paylog:{
						id:0,
						tid:""
					}
				}
			}
		},
		onLoad() {
			let self = this;
			this.initData('cutenumber');
		},
		onShow() {
			
		},
		onPullDownRefresh() {
			this.initData('cutenumber');
			uni.stopPullDownRefresh();
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.loaded = true;
					that.loadmore = res.numbers.length<10 ? false : true;
					that.page = 1;
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
			doBuy(nid){
				let self = this;
				return core.confirm('提交后请在10分钟内支付，否则可能会被他人抢购','购买靓号',function(){
					core.post("cutenumber/create",function(res){
						if(res.type!='success') return core.report(res);
						core.toast("即将进入支付...","credit/pay?tid="+res.message.tid,"success");
					},{nid:nid,buynumber:"true"});
				});
			},
			doViewMore(){
				if(!this.loadmore) return core.toast('没有更多了~');
				let self = this;
				core.get("cutenumber/fetch",function(res){
					if(res.type!='success') return core.report(res);
					self.loadmore = res.numbers.length<10 ? false : true;
					self.page += 1;
					self.data.numbers = self.data.numbers.concat(res.numbers);
				},{page:this.page+1})
			}
		}
	}
</script>

<style>
	page{padding-bottom: 50upx;}
	.cutenumbers .cu-card .cu-item{position: relative; overflow: hidden; padding: 30upx 0; margin: 20upx 30upx;}
	.cutenumbers .cu-card .cu-item::after{position: absolute; content: '靓'; left: 0; top: 0; background-color: #e54d42; color: #FFFFFF; font-size: 24upx; padding: 5upx 10upx; border-radius: 50% 0;}
</style>
