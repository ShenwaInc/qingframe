<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="nav solid-bottom" v-if="data.passcode.passcode>0">
				<view class="flex text-center">
					<view class="cu-item flex-sub" @click="data.curview='recharge'" :class="data.curview=='recharge'?'cur text-'+theme.actcolor:''">余额充值</view>
					<view class="cu-item flex-sub" @click="data.curview='passcode'" :class="data.curview=='passcode'?'cur text-'+theme.actcolor:''">卡密充值</view>
				</view>
			</view>
			<view class="padding recharge" v-if="data.curview=='recharge'">
				<view class="grid col-5 padding-tb-sm">
					<view class="padding-xs">
						<button class="cu-btn sm block" @click="postdata.amount=5" :class="(postdata.amount==5?'bg-':'line-')+theme.actcolor">5元</button>
					</view>
					<view class="padding-xs">
						<button class="cu-btn sm block" @click="postdata.amount=10" :class="(postdata.amount==10?'bg-':'line-')+theme.actcolor">10元</button>
					</view>
					<view class="padding-xs">
						<button class="cu-btn sm block" @click="postdata.amount=20" :class="(postdata.amount==20?'bg-':'line-')+theme.actcolor">20元</button>
					</view>
					<view class="padding-xs">
						<button class="cu-btn sm block" @click="postdata.amount=50" :class="(postdata.amount==50?'bg-':'line-')+theme.actcolor">50元</button>
					</view>
					<view class="padding-xs">
						<button class="cu-btn sm block" @click="postdata.amount=100" :class="(postdata.amount==100?'bg-':'line-')+theme.actcolor">100元</button>
					</view>
				</view>
				<view class="cu-form-group radius">
					<input type="digit" placeholder="请输入充值金额" v-model="postdata.amount" name="amount"></input>
				</view>
				<view class="flex flex-direction margin-top">
					<button @click="doRecharge()" class="cu-btn lg" :class="'bg-'+theme.actcolor">立即充值</button>
				</view>
			</view>
			<view class="padding passcode" v-if="data.curview=='passcode'">
				<view class="cu-form-group radius">
					<input type="text" placeholder="请输入您的卡密串" v-model="postdata.passcode" name="passcode"></input>
				</view>
				<view class="flex flex-direction margin-top">
					<button @click="doPasscode()" class="cu-btn lg" :class="'bg-'+theme.actcolor">确认充值</button>
				</view>
				<view class="flex flex-direction margin-top" v-if="data.passcode.buylink!=''">
					<button @click="naviTo(data.passcode.buylink)" class="cu-btn lg" :class="'bg-blue'">购买卡密</button>
				</view>
			</view>
			<view class="padding" v-if="data.notice!=''">
				<rich-text :nodes="data.notice"></rich-text>
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
				theme:core.style,
				postdata:{
					amount:"",
					passcode:""
				},
				data:{
					title:"资金管理",
					curview:'recharge',
					notice:"",
					passcode:{
						passcode:0,
						switch:0,
						buylink:""
					}
				}
			}
		},
		onLoad() {
			this.initData('credit/recharge');
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
					that.loaded = true;
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
			doPasscode(){
				let passcode = this.postdata.passcode;
				if(!passcode) return core.toast('请输入您的卡密串');
				core.post('passcode/recharge',function(res){
					if(res.type=='success'){
						res.redirect = 'back';
					}
					core.report(res);
				},{dorecharge:"true",passcode:passcode});
			},
			doRecharge(){
				let amount = parseFloat(this.postdata.amount);
				if(amount<=0) return core.toast('请输入正确的充值金额');
				let self = this;
				core.post('credit/recharge',function(res){
					if(res.type!='success') return core.report(res);
					let redirect = res.redirect ? res.redirect : core.page('credit/pay',{tid:res.message.tid});
					core.toast('即将进入支付...',redirect,'success');
				},{dorecharge:'true',amount:amount});
			}
		}
	}
</script>

<style>
	.recharge .grid{margin-left: -10upx; margin-right: -10upx;}
	.recharge .grid .cu-btn{font-size: 28upx;}
</style>
