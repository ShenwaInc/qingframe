<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="light padding-sm" :class="data.applyinfo.status==2?'bg-red':'bg-orange'" v-if="data.applyinfo.id>0">
			<text class="text-lg" v-if="data.applyinfo.status==2">您的申请已被拒绝：{{data.applyinfo.remark}}</text>
			<text class="text-lg" v-else>您的申请已提交，请耐心等待审核</text>
		</view>
		<view class="padding">
			<view class="cu-form-group radius">
				<textarea maxlength="-1" v-model="data.applyinfo.content" placeholder="请输入您的进群申请理由"></textarea>
			</view>
			<view class="flex flex-direction margin-top">
				<button class="cu-btn lg" :class="'bg-'+actcolor" @click="doApplyJoin()">提交申请</button>
			</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				gid:0,
				actcolor:core.style.actcolor,
				submiting:false,
				data:{
					title:"申请进群",
					group:{
						id:0,
						name:""
					},
					applyinfo:{
						id:0,
						content:"",
						status:0,
						remark:""
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.gid)=='undefined' || !options.gid) return core.toast('无效的群组信息','back');
			this.gid = parseInt(options.gid);
			this.initData('group/join',{gid:this.gid});
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
			unSupport(){
				return core.toast('敬请期待');
			},
			doApplyJoin(){
				if(this.submiting) return false;
				if(this.data.applyinfo.content=='') return core.toast("请认真填写进群申请");
				let postdata = {
					gid:this.data.group.id,
					applyjoin:"true",
					data:{
						content:this.data.applyinfo.content
					}
				}
				this.submiting = true;
				let that = this;
				core.post('group/join',function(res){
					that.submiting = false;
					let redirect = res.type=='success' ? 'back' : res.redirect;
					core.toast(res.message,redirect,res.type);
				},postdata);
			}
		}
	}
</script>

<style>
</style>
