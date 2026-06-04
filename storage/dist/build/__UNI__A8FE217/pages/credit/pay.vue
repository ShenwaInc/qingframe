<template>
	<view>
		<view class="webpay" v-if="webpay.status">
			<web-view name="whotalkwebview" :webview-styles="webviewStyles" :src="webpay.url"></web-view>
		</view>
		<view class="goods-section padding-top-xs">
			<!-- 商品列表 -->
			<view class="g-item">
				<image :src="data.paylog.setting.cover"></image>
				<view class="right">
					<view class="title clamp text-cut padding-bottom-xs">{{data.paylog.setting.title}}</view>
					<view class="spec"><text>{{data.paylog.setting.summary}}</text></view>
					<view class="price-box">
						<text class="price text-red text-boldm">￥{{data.paylog.actual}}</text>
						<text class="number">x 1</text>
					</view>
				</view>
			</view>
		</view>
		<!-- 金额明细 -->
		<view class="yt-list">
			<view class="yt-list-cell b-b">
				<text class="cell-tit clamp">商品金额</text>
				<text class="cell-tip">￥{{data.paylog.actual}}</text>
			</view>
			<view class="yt-list-cell b-b">
				<text class="cell-tit clamp">优惠金额</text>
				<text class="cell-tip red">-￥{{data.discountamout}}</text>
			</view>
			<view class="yt-list-cell b-b">
				<text class="cell-tit clamp">还需支付</text>
				<text class="cell-tip red">￥{{data.paylog.amount}}</text>
			</view>
		</view>
		<view class="pay-type-list">
			<view class="type-item b-b" :class="payprovider.wxpay?'':'hidden'" @click="payment='wxpay'">
				<view class="icon">
					<text class="ifont icon-wechat"></text>
				</view>
				<view class="con">
					<text class="tit">微信支付</text>
					<text>微信支付，安全便捷</text>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked="payment=='wxpay'" /></radio>
				</label>
			</view>
			<view class="type-item b-b" :class="payprovider.alipay?'':'hidden'" @click="payment='alipay'">
				<view class="icon">
					<text class="ifont icon-alipay"></text>
				</view>
				<view class="con">
					<text class="tit">支付宝支付</text>
					<text>支付宝，知托付</text>
				</view>
				<label class="radio">
					<radio value="" color="#fa436a" :checked="payment=='alipay'" /></radio>
				</label>
			</view>
			<view class="type-item" :class="data.payments.balance?'':'hidden'" @click="data.balance>=data.paylog.amount?payment='balance':false">
				<view class="icon">
					<text class="ifont icon-payment"></text>
				</view>
				<view class="con">
					<text class="tit" :class="data.balance<data.paylog.amount?'text-gray':''">余额支付</text>
					<text>可用余额 ¥{{data.balance}}</text>
				</view>
				<label class="radio">
					<radio value="" :disabled="data.balance<data.paylog.amount?true:false" color="#fa436a" :checked="payment=='balance'" /></radio>
				</label>
			</view>
		</view>
		<view class="padding-sm" v-if="data.paylog.setting.expire>0">
			<text class="text-red">订单将于{{data.paylog.setting.expiredate}}失效，请及时支付</text>
		</view>
		<advs :advs="advs.payment" :extraclass="'padding-sm bg-gray'"></advs>
		<view class="cu-bar bg-white tabbar shop foot">
			<button class="action">
				<text class="text-orange text-xxl text-bold padding-left">￥{{data.paylog.amount}}</text>
			</button>
			<view v-if="platform=='wxapp' && data.needbind" class="btn-group margin-bottom-sm">
				<button open-type="getUserInfo" @getuserinfo="WxappLogin" withCredentials="true" style="width: 80%;" class="cu-btn round shadow-blur lg" :class="'bg-'+theme.actcolor">立即支付</button>
			</view>
			<view class="btn-group margin-top-xs margin-bottom-sm" v-else>
				<button @click="doPay" style="width: 80%;" class="cu-btn round shadow-blur lg" :class="'bg-'+theme.actcolor">立即支付</button>
			</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	import advs from "@/components/util/advs.vue"
	
	export default {
		components: {advs},
		data() {
			return {
				tid:'',
				advs:{
					'payment':[]
				},
				loaded:false,
				paying:false,
				frompage:'',
				payment:'',
				showmodal:'',
				deduct:0,
				theme:core.style,
				payHandle:false,
				payprovider:{
					alipay:false,
					wxpay:false
				},
				platform:core.platform,
				couponid:0,
				webpay:{
					status:false,
					url:""
				},
				webviewStyles:{
					progress : {
						color:core.style.active
					}
				},
				data:{
					needbind:false,
					title:"收银台",
					"payments" : {
						"alipay":0,
						"wxpay":0,
						"balance":false
					},
					"paylog" : {
						"id": 0,
						"uniacid": 0,
						"uid": 0,
						"nickname": "",
						"module": "",
						"tid": "",
						"nid": "",
						"cid": 0,
						"amount": 0.00,
						"actual": 0.00,
						"deduct": 0,
						"discount": 0,
						"proportion": 0.00,
						"brokerage": 0.00,
						"commission": 0.00,
						"profit": 0.00,
						"setting": {
							"coupon": 0,
							"discount": 0,
							"deduct": 0,
							"deductmax": 0,
							"expire":0,
							"expiredate":"长期",
							"title": "",
							"cover": "",
							"summary": "",
							"specid": 0,
							"type": ""
						},
						"status": 0,
						"remark": "",
						"paytime": 0,
						"addtime": 0,
						"dateline": 0
					},
					"discounttext" : "",
					"discountamout" :0.00,
					"deductmax" :0,
					"deductamount" :0.00,
					"coupons" : [],
					"balance" :0.00,
					"coupon" : {
						"id":0,
						"name":""
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.tid)=='undefined' || options.tid=='') return core.toast('无效的订单号','back');
			this.tid = options.tid;
			if(typeof(options.fp)=='string') this.frompage = options.fp;
			let self = this;
			core.cachecloud('advs',function(advs){
				if(typeof(advs.payment)!='undefined'){
					self.advs.payment = advs.payment;
				}
			});
		},
		onShow() {
			if(this.paying){
				this.doPayResult();
			}else{
				this.initData('credit/defray',{tid:this.tid});
			}
		},
		onPullDownRefresh(){
			this.initData('credit/defray',{tid:this.tid});
			uni.stopPullDownRefresh();
		},
		onUnload() {
			uni.$off('SRpayresult');
		},
		methods:{
			initData(route, data={}, callback=false){
				let that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					uni.$on('SRpayresult',function(socketres){
						if(that.payHandle) return false;
						//console.log('获取到支付结果',socketres);
						if(socketres.tid==that.tid){
							that.payHandle = true;
							core.toast('支付成功',socketres.redirect,'success');
						}
					})
					that.data = res;
					if(res.paylog.deduct>0){
						that.deduct=1;
					}
					that.couponid = res.paylog.cid;
					let hasPayment = false;
					if(res.payments.balance){
						hasPayment = true;
						if(that.payment=='') that.payment = 'balance';
					}
					if(res.payments.wxpay>0){
						hasPayment = true;
						that.payprovider.wxpay = true;
					}
					if(res.payments.alipay>0){
						hasPayment = true;
						that.payprovider.alipay = true;
					}
					// #ifndef H5
					if(!that.loaded){
						uni.getProvider({
						    service: 'payment',
						    success: function (ret) {
						        if(ret.provider.length==0 && !res.payments.balance) return hasPayment?false:core.toast('暂无可用的支付方式');
								for(var p in ret.provider){
									let paytype = ret.provider[p];
									if(that.payment=='' && res.payments[paytype]>0) that.payment = paytype;
									if(res.payments[paytype]>0){
										that.payprovider[paytype] = true;
									}
								}
						    }
						});
					}
					// #endif
					// #ifdef MP-WEIXIN
					that.payprovider.alipay = false;
					that.data.payments.alipay = 0;
					// #endif
					uni.setNavigationBarTitle({
						title:res.title
					});
					that.loaded = true;
					if(callback){
						callback(res);
					}
				},data);
			},
			doPayResult(toast=false){
				let self = this;
				core.get('credit/payquery',function(rep){
					self.paying = false;
					self.webpay.status = false;
					if(self.payHandle) return true;
					if(rep.type!='success') return toast ? core.report(rep) : false;
					let redirect = self.data.paylog.setting.redirect;
					if(self.frompage==''){
						redirect = 'back';
					}
					//console.log("支付跳转链接：", redirect);
					self.payHandle = true;
					core.toast('支付成功',redirect,'success');
				},{tid:this.data.paylog.tid,paytype:this.data.payments[this.payment]},'json',true);
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			WxappLogin(){
				var self = this;
				wx.getUserProfile({
					desc:'用于完善会员资料',
					success(infoRes) {
						uni.login({
							provider: 'weixin',
							success: (res) => {
								let postdata = {provider:'wxapp',userinfo:infoRes.userInfo, code:res.code,appsecret:core.apitoken}
								return core.post('member/bind',function(ret){
									if(ret.type!='success') return core.report(ret);
									self.data.needbind = false;
									self.doPay();
								},postdata);
							}
						});
					},
					fail(e) {
						console.log(e);
					}
				});
			},
			doPay(){
				if(this.payment=='') return core.toast('请选择支付方式');
				if(this.payment=='balance'){
					if(this.data.balance<this.data.paylog.amount) return core.toast('您的余额不足');
					let that = this;
					core.post('credit/pay',function(res){
						if(res.type!='success') return core.report(res);
						if(that.payHandle) return true;
						that.payHandle = true;
						let redirect = res.redirect=='' ? that.data.paylog.setting.redirect : res.redirect;
						if(that.frompage==''){
							redirect = 'back';
						}
						core.toast(res.message,redirect,'success');
					},{tid:this.data.paylog.tid,payment:'balance'});
				}else{
					if(!this.payprovider[this.payment]) return core.toast('暂不支持此支付方式');
					let payment = this.data.payments[this.payment];
					this.paying = true;
					if(payment==2 || this.platform=='h5'){
						let paymenturl = core.url(this.payment,'unipay') + '&tid='+this.tid+'&authkey='+core.userinfo.token;
						// #ifdef H5
						if(typeof(plus)!='undefined'){
							paymenturl = paymenturl.replace('unifrom=h5','') + '&unifrom=h5plus';
							wap2app.open(paymenturl);
							return true;
						}
						// #endif
						this.webpay.url = paymenturl;
						this.webpay.status = true;
						return true;
					}
					var that = this;
					return core.post('credit/submit',function(res){
						if(res.type!='success') return core.report(res);
						let payload = {
							provider : that.payment,
							orderInfo : res.message.orderinfo,
							success : function(ret){
								return console.log("支付成功：", ret);
							},
							fail : function(err){
								core.toast('支付失败，请重试');
								console.log('fail:' + JSON.stringify(err));
							},
							complete(e) {
								if(typeof(e.errCode)!='undefined' && e.errCode!=0){
									return core.toast(e.errMsg);
								}
								that.doPayResult(true);
							}
						}
						if(that.platform=='wxapp'){
							payload.timeStamp = String(res.message.timestamp);
							payload.signType = 'MD5';
							payload.nonceStr = res.message.noncestr;
							payload.package = res.message.package;
							payload.paySign = res.message.paysign;
						}else if(that.platform=='ttapp'){
							payload.service = that.payment=='alipay' ? 4 : 3;
						}
						return uni.requestPayment(payload);
					},{tid:this.data.paylog.tid,platform:this.platform,payment:this.payment});
				}
			}
		}
	}
</script>

<style lang="scss">
	.cu-bar.foot{z-index: 0;}
	
	.goods-section {
		margin-top: 16upx;
		background: #fff;
		padding-bottom: 1px;
	
		.g-header {
			display: flex;
			align-items: center;
			height: 84upx;
			padding: 0 30upx;
			position: relative;
		}
	
		.logo {
			display: block;
			width: 50upx;
			height: 50upx;
			border-radius: 100px;
		}
	
		.name {
			font-size: 30upx;
			color: #606266;
			margin-left: 24upx;
		}
	
		.g-item {
			display: flex;
			margin: 20upx 30upx;
	
			image {
				flex-shrink: 0;
				display: block;
				width: 140upx;
				height: 140upx;
				border-radius: 4upx;
			}
	
			.right {
				flex: 1;
				padding-left: 24upx;
				overflow: hidden;
			}
	
			.title {
				font-size: 30upx;
				color: #303133;
			}
	
			.spec {
				font-size: 26upx;
				color: #909399;;
			}
	
			.price-box {
				display: flex;
				align-items: center;
				font-size: 32upx;
				color: #303133;
				padding-top: 10upx;
	
				.price {
					margin-bottom: 4upx;
				}
				.number{
					font-size: 26upx;
					color: #606266;
					margin-left: 20upx;
				}
			}
	
			.step-box {
				position: relative;
			}
		}
	}
	.yt-list {
		margin-top: 16upx;
		background: #fff;
	}
	
	.yt-list-cell {
		display: flex;
		align-items: center;
		padding: 10upx 30upx 10upx 40upx;
		line-height: 70upx;
		position: relative;
	
		&.cell-hover {
			background: #fafafa;
		}
	
		&.b-b:after {
			left: 30upx;
		}
	
		.cell-icon {
			height: 32upx;
			width: 32upx;
			font-size: 22upx;
			color: #fff;
			text-align: center;
			line-height: 32upx;
			background: #f85e52;
			border-radius: 4upx;
			margin-right: 12upx;
	
			&.hb {
				background: #ffaa0e;
			}
	
			&.lpk {
				background: #3ab54a;
			}
	
		}
	
		.cell-more {
			align-self: center;
			font-size: 24upx;
			color: #909399;
			margin-left: 8upx;
			margin-right: -10upx;
		}
	
		.cell-tit {
			flex: 1;
			font-size: 26upx;
			color: #909399;
			margin-right: 10upx;
		}
	
		.cell-tip {
			font-size: 26upx;
	
			&.disabled {
				color: #909399;
			}
	
			&.active {
				color: #fa436a;
			}
			&.red{
				color: #fa436a;
			}
		}
	
		&.desc-cell {
			.cell-tit {
				max-width: 90upx;
			}
		}
	
		.desc {
			flex: 1;
			font-size: 28upx;
			color: #303133;
		}
	}
	
	.pay-type-list {
		margin-top: 20upx;
		background-color: #fff;
		padding-left: 40upx;
		
		.type-item{
			height: 120upx;
			padding: 20upx 0;
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding-right: 30upx;
			font-size: 30upx;
			position:relative;
		}
		
		.icon{
			width: 100upx;
			height: 100upx;
			margin-right: 10upx;
			line-height: 100upx;
			text-align: center;
		}
		.ifont{
			font-size: 88upx;
		}
		.icon-erjiye-yucunkuan {
			color: #fe8e2e;
		}
		.icon-weixinzhifu {
			color: #36cb59;
		}
		.icon-alipay, .icon-wechat {
			font-size: 78upx;
		}
		.tit{
			font-size: 32upx;
			color: #303133;
			margin-bottom: 4upx;
		}
		.tit.text-gray{color: #aaaaaa;}
		.con{
			flex: 1;
			display: flex;
			flex-direction: column;
			font-size: 24upx;
			color: #909399;
		}
	}
</style>
