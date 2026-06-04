<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="padding">
			<view class="cu-form-group radius">
				<textarea maxlength="-1" v-model="postdata.note" placeholder="请输入好友申请理由"></textarea>
			</view>
			<view class="cu-form-group margin-top">
				<view class="title">设置备注</view>
				<input placeholder="请输入对方的备注名称" v-model="postdata.remark" name="input"></input>
			</view>
			<view class="flex flex-direction margin-top">
				<button class="cu-btn lg" :class="'bg-'+actcolor" @click="domakefriend()">申请添加对方为好友</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	
	export default {
		computed: mapState(['userId', 'hasLogin', 'userName']),
		data() {
			return {
				uid:0,
				actcolor:core.style.actcolor,
				postdata:{
					uid:0,
					applyfriend:"true",
					note:"申请添加为好友",
					remark:""
				},
				data:{
					title:"申请添加好友",
					member:{
						uid:0
					},
					uid:0,
					friend:{
						id:0
					},
					tofriend:{
						id:0
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.uid)=='undefined' || !options.uid) return core.toast('无效的用户编号','back');
			this.uid = options.uid;
			let that = this;
			this.initData('member/makefriend',{uid:this.uid},function(res){
				that.postdata.uid = res.member.uid;
				if(res.friend.id>0 && res.friend.apply!=0){
					that.postdata.note = res.friend.note;
					that.postdata.remark = res.friend.remark;
				}else{
					that.postdata.note = '我是' + that.userName;
					that.postdata.remark = res.member.nickname;
				}
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
			domakefriend(){
				if(this.postdata.note=='') return core.toast('请输入申请理由');
				return core.post('member/makefriend',function(res){
					let redirect = res.redirect;
					if(res.type=='success') redirect = 'back';
					return core.toast(res.message,redirect,res.type);
				},this.postdata);
			}
		}
	}
</script>

<style>
	.cu-form-group .title {
		min-width: calc(4em + 15px);
	}
</style>
