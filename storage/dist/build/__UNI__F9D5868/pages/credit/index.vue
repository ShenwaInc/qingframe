<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="cu-list menu-avatar solid-top">
				<view class="cu-item" :class="data.switchs.vip?'arrow':''" @click="naviTo(data.switchs.vip?'member/vip':'',{fp:'credit'})">
					<view class="cu-avatar radius lg" :style="'background-image:url('+data.userinfo.avatar+');'"></view>
					<view class="content">
						<view>
							<view class="text-cut text-xl">
								{{data.userinfo.nickname}}
								<text class="cu-tag bg-red round margin-left-sm light">{{data.userinfo.group.title}}</text>
							</view>
						</view>
						<view class="text-gray text-sm flex">
							<view class="text-cut">{{data.leveltext}}</view>
						</view>
					</view>
				</view>
			</view>
			<view class="padding text-center text-lg">
				<view class="radius grid padding-tb-xl" :class="['bg-'+theme.actcolor,'col-'+(data.credit.switch?3:2)]">
					<view @click="naviTo('credit/payment')">
						<view class="text-xxxl margin-bottom-xs">
							<text class="cuIcon-barcode"></text>
						</view>
						<text>收付款</text>
					</view>
					<view class="solid-left" @click="naviTo(data.urls.credit2,{title:'资金明细'})">
						<view class="text-xxxl margin-bottom-xs">
							<text class="cuIcon-moneybag"></text>
						</view>
						<text>￥{{data.userinfo.credit2}}</text>
					</view>
					<view v-if="data.credit.switch" @click="naviTo(data.urls.credit1,{title:data.credit.name+'明细'})" class="solid-left">
						<view class="text-xxxl margin-bottom-xs">
							<text class="cuIcon-choiceness"></text>
						</view>
						<text>{{data.userinfo.credit1}}</text>
					</view>
				</view>
			</view>
			<view class="cu-list menu">
				<view class="cu-item arrow" @click="naviTo(data.urls.recharge,{title:data.texts.recharge})" v-if="data.switchs.recharge">
					<view class="content">
						<text class="text-lg">{{data.texts.recharge}}</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="naviTo('credit/cash')" v-if="data.switchs.cash">
					<view class="content">
						<text class="text-lg">{{data.texts.cash}}</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="doExchange()" v-if="data.switchs.exchange">
					<view class="content">
						<text class="text-lg">{{data.texts.exchange}}{{data.credit.name}}</text>
					</view>
					<view class="action">
						<view class="cu-tag round bg-orange light">1元兑换{{data.credit.proportion}}{{data.credit.name}}</view>
					</view>
				</view>
			</view>
			<view class="cu-list menu">
				<view class="cu-item arrow" @click="naviTo('credit/order')">
					<view class="content">
						<text class="text-lg">我的订单</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="naviTo(data.urls.credit2)">
					<view class="content">
						<text class="text-lg">资金明细</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="naviTo(data.urls.credit1)" v-if="data.credit.switch">
					<view class="content">
						<text class="text-lg">{{data.credit.name}}明细</text>
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
				theme:core.style,
				loaded:false,
				operate:"",
				data:{
					title:"我的钱包",
					credit:{
						"switch":true,
						"name":'积分',
						"proportion":0
					},
					switchs:{
						recharge:true,
						cash:true,
						exchange:true,
						vip:true
					},
					texts:{
						recharge:'余额充值',
						cash:'余额提现',
						exchange:'余额转积分',
						balance:"我的余额"
					},
					leveltext:'',
					urls:{
						recharge:"",
						credit2:"",
						credit1:""
					},
					userinfo:{
						uid:0,
						credit1:0,
						credit2:0.00,
						group:{
							title:""
						}
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.op)!='undefined'){
				this.operate = options.op + "";
			}
		},
		onShow() {
			let self = this;
			this.initData('credit',{},function(res){
				if(self.operate=='exchange'){
					self.doExchange();
				}
			});
		},
		onPullDownRefresh() {
			this.initData('credit');
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
					that.loaded = true;
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
			doExchange(){
				this.operate = "";
				if(this.data.userinfo.credit2<=0) return core.toast("您的余额不足");
				let placeholderText = "1元余额兑换" + this.data.credit.proportion + this.data.credit.name;
				let self = this;
				uni.showModal({
					title:"输入兑换金额(元)",
					confirmText:"兑换",
					editable:true,
					placeholderText:placeholderText,
					success:function(ret){
						if(ret.confirm){
							let amount = parseFloat(ret.content);
							if(amount<=0 || !ret.content){
								return core.toast("请输入正确的金额");
							}
							if(amount>self.data.userinfo.credit2){
								return core.toast("您的可用余额不足");
							}
							core.post('credit/exchange',function(res){
								core.toast(res.message,"",res.type);
								if(res.type=='success'){
									self.initData('credit');
								}
							},{
								amount:amount,
								saveexchange:"true"
							});
						}
					}
				})
			}
		}
	}
</script>

<style>
</style>
