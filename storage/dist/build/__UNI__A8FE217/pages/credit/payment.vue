<template>
	<view class="paymentpage" :class="showcode=='receive'?'bg-orange':'bg-green'">
		<cu-custom :bgColor="showcode=='receive'?'bg-orange':'bg-green'" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="padding" v-if="action=='receive'">
				<view class="cu-load loading" v-if="data.member.uid==0"></view>
				<view v-else>
					<view class="flex justify-between">
						<view class="padding-left text-white">
							<view class="text-bold text-xl">向他人收款</view>
							<view class="margin-top-xs">{{data.member.nickname}} (ID:{{data.member.uid}})</view>
						</view>
						<view class="padding-right">
							<view class="cu-avatar radius lg" :style="'background-image:url('+data.member.avatar+')'"></view>
						</view>
					</view>
					<view class="bg-white radius padding margin-top-xl" style="overflow: hidden;">
						<view class="cu-form-group">
							<view class="title">收款金额</view>
							<input placeholder="输入要收款的金额(元)" type="digit" v-model="receivedata.amount" name="amount"></input>
						</view>
						<view class="cu-form-group margin-top">
							<view class="title">备注说明</view>
							<input placeholder="输入备注说明(选填)" type="text" v-model="receivedata.remark" name="remark"></input>
						</view>
						<view class="margin-top flex flex-direction">
							<button class="cu-btn radius lg" :class="'bg-'+actcolor" @click="Receivepayment()">立即收款</button>
						</view>
					</view>
				</view>
			</view>
			<view class="padding" :class="action=='show'?'':'hidden'">
				<view class="bg-white radius" style="overflow: hidden;">
					<view class="nav bg-gray">
						<view class="flex text-center">
							<view class="cu-item flex-sub" @click="showcode='pay'" :class="showcode=='pay'?'cur text-limegreen':''">
								付款
							</view>
							<view class="cu-item flex-sub" @click="showcode='receive'" :class="showcode=='receive'?'cur text-limegreen':''">
								收款
							</view>
						</view>
					</view>
					<view class="padding-xl text-center bg-white">
						<view v-if="showcode=='pay'">
							<image class="qrcode" mode="widthFix" :src="data.paycode"></image>
							<view class="text-blue margin-top-xs" @click="doRefresh()">
								<text class="cuIcon-refresh margin-right-xs"></text>
								<text>刷新</text>
							</view>
						</view>
						<image class="qrcode" mode="widthFix" :src="data.receivecode" v-else></image>
					</view>
					<view class="cu-list menu" v-if="showcode=='pay'">
						<view class="cu-item arrow" @click="naviTo('credit/recharge')">
							<view class="content padding-tb-sm">
								<view>
									<text class="cuIcon-moneybag text-green margin-right-xs"></text> 当前可用余额{{data.balance}}元
								</view>
								<view class="text-gray text-sm">
									<text class="cuIcon-infofill margin-right-xs text-yellow"></text> 资金将直接进入对方账户，请谨慎操作
								</view>
							</view>
							<view class="action">
								<text class="text-grey text-sm">去充值</text>
							</view>
						</view>
					</view>
				</view>
				<view class="radius margin-top-sm" style="overflow: hidden;">
					<view class="cu-list menu">
						<view class="cu-item arrow" @click="doScan()">
							<view class="content">
								<text class="cuIcon-scan text-green" style="font-size: 40upx;"></text>
								<text class="text-grey">打开扫一扫</text>
							</view>
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
				action:'show',
				showcode:'pay',
				loaded:false,
				paysecret:"",
				actcolor:core.style.actcolor,
				receivedata:{
					amount:'',
					remark:''
				},
				data:{
					title:"收付款",
					paycode:"",
					receivecode:"",
					balance:"",
					member:{
						uid:0
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.act)!='undefined' && options.act!='') this.action = options.act;
			if(typeof(options.ps)!='undefined' && options.ps!='') this.paysecret = options.ps;
			let that = this;
			this.initData('credit/payment',{act:this.action,paysecret:this.paysecret},function(res){
				if(that.action=='show') that.reloadCode();
			});
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
			unSupport(){
				return core.toast('敬请期待');
			},
			doRefresh(){
				this.initData('credit/payment',{refresh:1});
			},
			Receivepayment(){
				let amount = Math.floor(this.receivedata.amount);
				if(amount<=0) return core.toast('请输入正确的收款金额');
				let postdata = this.receivedata;
				postdata.paytoken = this.data.member.paytoken;
				postdata.uid = this.data.member.uid;
				postdata.receivepayment = "true";
				core.post('credit/payment',function(res){
					let redirect = res.type=='success' ? 'back' : res.redirect;
					core.toast(res.message,redirect,res.type);
				},postdata);
			},
			reloadCode(){
				if(this.action=='show'){
					this.initData('credit/payment');
				}
				let that = this;
				setTimeout(function(){
					that.reloadCode();
				},60000)
			},
			doScan(){
				return core.scan();
			}
		}
	}
</script>

<style>
	page{background-color: #39b54a;}
	.paymentpage{min-height: 100vh;}
	.qrcode{width: 360upx;}
	.cu-form-group .title {
		min-width: calc(4em + 15px);
	}
</style>
