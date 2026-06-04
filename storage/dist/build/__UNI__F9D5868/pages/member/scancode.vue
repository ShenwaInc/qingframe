<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="contianer">
			<view class="text-empty" v-if="data.mode==''">
				<view class="cu-load load-cuIcon loading">正在获取扫码结果</view>
			</view>
			<view class="scan-result" v-else>
				<view v-if="data.mode=='codelogin'" class="codelogin padding-top-xl">
					<view class="padding text-center margin-top-xl">
						<view class="cu-avatar round xl margin-left" :style="[{ backgroundImage:'url(' + logo + ')' }]">
							<view class="cu-tag badge" :class="'bg-'+theme.actcolor">Pc</view>
						</view>
					</view>
					<view class="text-center padding">
						<text class="text-xxxl">{{systemname}} PC端登录确认</text>
						<view class="margin-top-sm">
							<text class="text-gray text-lg">自动同步最近的消息</text>
						</view>
					</view>
					<view class="margin-xl padding-lr-xl">
						<view class="padding-xl flex flex-direction">
							<button @click="doCodeLogin()" class="cu-btn lg" :class="'bg-'+theme.actcolor">登录</button>
						</view>
						<view class="text-center text-darkblue" @click="doBack()">
							<text class="text-lg">取消登录</text>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	
	export default {
		computed: mapState(['userId','hasLogin']),
		data() {
			return {
				scandata:"",
				theme:core.style,
				systemname:core.system.name,
				logo:core.system.logo,
				data:{
					title:"扫码结果",
					mode:"",
					data:""
				}
			}
		},
		onLoad(options) {
			if(!this.hasLogin) return core.toast('请先登录','login','error');
			if(typeof(options.data)=='undefined') return core.toast('扫描失败，请重试','back','error');
			this.scandata = options.data;
			this.initData('member/scancode',{data:this.scandata});
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
			doBack(){
				return core.back();
			},
			doCodeLogin(){
				core.post('member/scancode',function(res){
					if(res.type!='success') return core.report(res);
					core.toast(res.message,'back','success');
				},{hash:this.data.data,confirmlogin:"true"});
			}
		}
	}
</script>

<style>
</style>
