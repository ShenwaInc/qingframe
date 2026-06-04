<template>
	<view>
		<cu-custom bgColor="bg-green" :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="padding" v-else>
			<view class="cu-load loading" v-if="data.member.uid==0"></view>
			<view v-else>
				<view class="flex justify-between">
					<view class="padding-left text-white">
						<view class="text-bold text-xl">向他人转账</view>
						<view class="margin-top-xs">{{data.member.nickname}} (ID:{{data.member.uid}})</view>
					</view>
					<view class="padding-right">
						<view class="cu-avatar radius lg" :style="'background-image:url('+data.member.avatar+')'"></view>
					</view>
				</view>
				<view class="bg-white radius padding margin-top-xl" style="overflow: hidden;">
					<view class="cu-form-group">
						<view class="title">转账金额</view>
						<input type="digit" placeholder="输入要转账的金额(元)" v-model="postdata.amount" v-if="postdata.mode=='credit2'" name="amount"></input>
						<input type="number" placeholder="输入要转账的数量" v-model="postdata.amount" v-else name="amount"></input>
					</view>
					<view class="cu-form-group" v-if="data.transfer1">
						<view class="title">转账类型</view>
						<radio-group style="flex: 1;" @change="ChangeMode">
							<label>
								<radio class='red radio' :class="postdata.mode=='credit2'?'checked':''" :checked="postdata.mode=='credit2'" value="credit2"></radio>
								<text class="padding-left-sm">余额</text>
							</label>
							<label class="margin-left padding-left">
								<radio class='red radio' :class="postdata.mode=='credit1'?'checked':''" :checked="postdata.mode=='credit1'" value="credit1"></radio>
								<text class="padding-left-sm">{{data.creditname}}</text>
							</label>
						</radio-group>
					</view>
					<view class="cu-form-group">
						<view class="title">备注说明</view>
						<input placeholder="输入备注说明(选填)" type="text" v-model="postdata.remark" name="remark"></input>
					</view>
					<view class="margin-top flex flex-direction">
						<button class="cu-btn radius lg" :class="'bg-'+actcolor" @click="saveTransfer()">立即转账</button>
					</view>
					<view class="margin-top">
						<checkbox-group class="block" v-if="!data.isfriend" @change="CheckboxChange">
							<view class="flex">
								<checkbox class="round" :class="[postdata.makefriend==1?'checked':'',actcolor]" :checked="postdata.makefriend==1?true:false" value="1"></checkbox>
								<view class="padding-left-sm text-lg">添加对方为好友</view>
							</view>
						</checkbox-group>
						<view class="padding-tb">
							<view class="text-gray text-sm">
								<text class="cuIcon-infofill margin-right-xs text-yellow"></text> 资金将直接进入对方账户，请谨慎操作
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
				uid:0,
				platform:core.platform,
				actcolor:core.style.actcolor,
				loaded:false,
				postdata:{
					uid:0,
					amount:'',
					makefriend:1,
					savetransfer:"true",
					mode:"credit2",
					remark:""
				},
				data:{
					title:"转账",
					creditname:"积分",
					isfriend:false,
					member:{
						uid:0,
						nickname:"",
						avatar:""
					},
					transfer1:false
				}
			}
		},
		onLoad(options) {
			if(typeof(options.uid)!='undefined') this.uid = parseInt(options.uid);
			if(typeof(options.cid)!='undefined'){
				if(options.type!='normal') return core.toast('仅支持向个人转账','back');
				this.uid = parseInt(options.cid);
			}
			if(this.uid==0){
				return core.toast('无效的用户信息','back');
			}
			this.initData('credit/transfer',{uid:this.uid});
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
			ChangeMode(e){
				this.postdata.mode = e.detail.value;
			},
			CheckboxChange(e){
				this.postdata.makefriend = e.detail.value;
			},
			saveTransfer(){
				this.postdata.uid = this.data.member.uid;
				let that = this;
				return core.post('credit/transfer',function(res){
					let redirect = res.redirect;
					let message = res.message;
					if(res.type=='success'){
						if(redirect!='') return core.toast(message,redirect,'success');
						message = message.message;
						redirect = core.page('credit/pay',{tid:res.message.tid});
					}
					core.toast(message,redirect,res.type);
				},this.postdata)
			}
		}
	}
</script>

<style>
	page{background-color: #39b54a;}
</style>
