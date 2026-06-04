<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="solid-top">
			<view class="cu-list menu">
				<view class="cu-item arrow" @click="naviTo('credit/index')">
					<view class="content">
						<text class="text-grey">可提现余额</text>
					</view>
					<view class="action">
						<text class="text-red">￥{{data.balance}}</text>
					</view>
				</view>
				<view class="cu-item" v-if="data.setting.min>0">
					<view class="content">
						<text class="text-grey">最低提现金额</text>
					</view>
					<view class="action">
						<text class="text-red">￥{{data.setting.min}}</text>
					</view>
				</view>
				<view class="cu-item" v-if="data.setting.max>0">
					<view class="content">
						<text class="text-grey">最高提现金额</text>
					</view>
					<view class="action">
						<text class="text-red">￥{{data.setting.max}}</text>
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						<text class="text-grey">提现手续费</text>
					</view>
					<view class="action">
						<text class="text-red">{{data.setting.fee}}%</text>
					</view>
				</view>
			</view>
			<view class="margin-bottom-xs" :class="'bg-'+actcolor">
				<view class="content-nav nav">
					<view class="cu-item" v-for="(item, index) in data.setting.method" @click="TapMethod(index)" :class="data.cashmethod==index?'text-white cur':''" :key="index">{{item}}</view>
				</view>
			</view>
			<view class="method-tab">
				<view class="tab-item" v-for="(item, index) in data.setting.method" :key="index" :class="data.cashmethod==index?'active':''">
					<block v-if="index=='bank'">
						<view class="cu-form-group must">
							<view class="title">开户银行</view>
							<input placeholder="请输入开户银行名称" v-model="content.bank.name" name="name"></input>
						</view>
						<view class="cu-form-group must">
							<view class="title">所属支行</view>
							<input placeholder="请输入开户支行的网点名称" v-model="content.bank.branch" name="branch"></input>
						</view>
						<view class="cu-form-group must">
							<view class="title">真实姓名</view>
							<input placeholder="请输入银行卡开户姓名" v-model="content.bank.realname" name="realname"></input>
						</view>
						<view class="cu-form-group must border-bottom">
							<view class="title">银行账号</view>
							<input type="text" placeholder="请输入您的银行卡号" v-model="content.bank.account" name="account"></input>
						</view>
					</block>
					<block v-else-if="index=='wechat_transfer'">
						<view class="cu-form-group must border-bottom">
							<view class="title">微信号</view>
							<input placeholder="请输入您的微信号" v-model="content.wechat_transfer.account" name="wxaccount"></input>
						</view>
					</block>
					<block v-else-if="index=='alipay'">
						<view class="cu-form-group must">
							<view class="title">支付宝</view>
							<input placeholder="请输入支付宝账号" v-model="content.alipay.account" name="aliaccount"></input>
						</view>
						<view class="cu-form-group must border-bottom">
							<view class="title">真实姓名</view>
							<input placeholder="请输入支付宝认证姓名" v-model="content.alipay.realname" name="alirealname"></input>
						</view>
					</block>
				</view>
			</view>
			<view class="cu-list menu">
				<view class="cu-item solid-top">
					<view class="content">
						<text>提现金额</text><text class="text-red padding-left-xs">*</text>
					</view>
					<view class="action">
						<input placeholder="请输入您的提现金额" @input="InputAmount" :auto-focus="true" v-model="content.amount" name="amount" type="digit" />
					</view>
				</view>
				<view class="cu-item">
					<view class="content">
						<text>实际到账</text>
					</view>
					<view class="action">
						<text class="text-red">￥{{actual}}</text>
					</view>
				</view>
			</view>
			<view class="cu-form-group solid-top margin-bottom">
				<textarea maxlength="-1" v-model="content.remark" placeholder="请输入备注说明"></textarea>
			</view>
		</view>
		<view class="cu-bar tabbar foot noaf noshadow" v-if="data.setting.switch">
			<view class="action">
				<view class="flex flex-direction padding-left-xl padding-right-xl">
					<button @click="SubmitWithdraw()" :class="'bg-'+actcolor" class="cu-btn margin-bottom-sm lg round-topleft-none">提交申请</button>
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
				actcolor:core.style.actcolor,
				actual:0,
				content:{
					amount:0,
					alipay:{
						account:"",
						realname:""
					},
					wechat_transfer:{
						account:""
					},
					bank:{
						name:"",
						branch:"",
						realname:"",
						account:""
					},
					remark:""
				},
				data:{
					title:"申请提现",
					balance:0.00,
					cashmethod:"",
					hasfocus:false,
					setting:{
						switch:true,
						method:{
							bank:"银行卡",
							alipay:"支付宝",
							wechat_transfer:"微信转账",
							wechat:"微信钱包"
						},
						fee:0,
						min:0.00,
						max:0.00,
						cycle:0,
						notice:""
					},
					userset:{
						method:''
					}
				}
			}
		},
		onLoad() {
			var that = this;
			this.initData('credit/cash',{},function(res){
				if(res.cashmethod==''){
					if(res.setting.method.length==0) return core.toast('暂无可用的提现方式','back');
					for(let i in res.setting.method){
						that.data.cashmethod = i;
						break;
					}
				}
				if(typeof(res.userset.method)=='undefined') return;
				if(res.userset.method=='bank' && typeof(res.userset.account)=='string'){
					res.userset.account.replace("I","");
				}
				if(res.userset.method!='' && res.userset.method!='wechat'){
					for(let i in that.content[res.userset.method]){
						that.content[res.userset.method][i] = res.userset[i];
					}
				}
			});
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
			TapMethod(index){
				if(index=='wechat' && !this.data.hasfocus){
					return core.toast('请先关注微信公众号','bind/account','error');
				}
				this.data.cashmethod = index;
			},
			InputAmount(){
				if(this.data.setting.fee>0){
					this.actual = this.content.amount - (this.content.amount * (this.data.setting.fee/100));
				}
			},
			SubmitWithdraw(){
				let postdata = {remark:this.content.remark,amount:this.content.amount};
				if(postdata.amount<=0) return core.toast('请输入您要提现的金额');
				if(postdata.amount>this.data.balance) return core.toast('可提现金额不足');
				if(postdata.amount<this.data.setting.min) return core.toast('不能小于最低提现额');
				if(this.data.setting.max>0 && postdata.amount>this.data.setting.max) return core.toast('不能小于最低提现额');
				let Method = this.data.cashmethod;
				if(Method!='wechat'){
					postdata[Method] = this.content[Method];
				}
				postdata.cashmethod = Method;
				postdata.withdraw = "true";
				return core.post('credit/cash',function(res){
					return core.report(res);
				},postdata)
			}
		}
	}
</script>

<style>
	page{padding-bottom: 100upx;}
	.method-tab .tab-item{display: none;}
	.method-tab .tab-item.active{display: block;}
	.cu-form-group .title {
		min-width: calc(4em + 15px);
	}
</style>
