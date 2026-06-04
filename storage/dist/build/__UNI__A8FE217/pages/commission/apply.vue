<template>
	<view>
		<cu-custom bgColor="bg-white" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="padding" v-else>
			<advs :advs="advs.commission" extraclass="margin-bottom-sm"></advs>
			<view v-if="currview=='apply'">
				<form>
					<view class="cu-form-group must">
						<view class="title">真实姓名</view>
						<input placeholder="请输入您的真实姓名" v-model="postdata.realname" name="realname"></input>
					</view>
					<view class="cu-form-group must">
						<view class="title">手机号</view>
						<input placeholder="请输入您的联系电话" disabled type="number" v-model="postdata.mobile" name="mobile"></input>
					</view>
					<view class="cu-form-group align-start">
						<view class="title">申请备注</view>
						<textarea maxlength="-1" v-model="applyremark" placeholder="请输入申请备注信息(选填)"></textarea>
					</view>
					<view class="flex flex-direction margin-top-sm padding-lr">
						<button class="cu-btn lg text-boldm" :class="'bg-'+theme.actcolor" @click="doSubmit()">提交申请</button>
					</view>
				</form>
			</view>
			<view v-else>
				<view class="padding text-center margin-top">
					<image class="waitting" :class="data.applyinfo.status==2?'refused':''" :src="applyimg"></image>
				</view>
				<view class="text-center padding" v-if="data.applyinfo.status==0">
					<view><text class="title text-bold text-xxl text-black">申请资料已提交</text></view>
					<view class="text-gray text-lg padding">您的申请信息已提交，将在3个工作日内完成审核</view>
				</view>
				<view class="text-center padding" v-else-if="data.applyinfo.status==2">
					<view><text class="title text-bold text-xxl text-black">申请信息未通过</text></view>
					<view class="text-gray text-lg padding">{{data.applyinfo.remark}}</view>
				</view>
				<view class="flex flex-direction padding-bottom">
					<button class="cu-btn round lg shadow margin-bottom text-boldm" :class="'bg-'+theme.actcolor" @click="doback()">我知道了</button>
					<view class="text-center" v-if="data.applyinfo.status==2">
						<text class="text-lg text-yellow" @click="currview='apply'">再次申请</text>
					</view>
				</view>
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
				loaded:false,
				currview:'',
				advs:{
					'commission':[]
				},
				applyimg:core.system.siteroot + '/addons/xfy_whotalk/static/images/img_lector_apply.png',
				applyremark:'',
				postdata:{
					realname:'',
					mobile:''
				},
				inputfocus:'',
				theme:core.style,
				data:{
					title:"申请成为分销商",
					userinfo:{
						realname:'',
						mobile:''
					},
					applyinfo:{
						id:0,
						status:0,
						remark:''
					}
				}
			}
		},
		onLoad() {
			let self = this;
			this.initData('commission/apply',{},function(res){
				if(res.userinfo.mobile==''){
					return core.toast('请先绑定手机号','bind/mobile');
				}
				if(res.applyinfo.id>0){
					self.postdata = res.applyinfo.profile;
				}else{
					self.currview = 'apply';
					self.postdata = res.userinfo;
				}
				core.cachecloud('advs',function(advs){
					self.advs.commission = advs.commission;
				});
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
			doback(){
				return core.back();
			},
			doSubmit(){
				if(this.postdata.realname=='') return core.toast('请输入您的真实姓名');
				let self = this;
				return core.post('commission/apply',function(res){
					if(res.type!='success') return core.report(res);
					core.toast(res.message,res.redirect,'success');
				},{profile:this.postdata,remark:this.applyremark,submitapply:"true"});
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.cu-form-group .title{width: 5em;}
	.waitting{height: 485upx; width: 500upx; margin: 0 auto;}
	.waitting.refused{-webkit-filter: grayscale(100%); filter: grayscale(100%);}
</style>
