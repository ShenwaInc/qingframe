<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="padding-bottom-xs">
				<view class="nav bg-white solid-bottom">
					<view class="flex text-center">
						<view class="cu-item flex-sub" @click="Viewlogs(1,'credit2')" :class="data.op=='credit2'?'cur text-'+theme.actcolor:''">{{data.ops.credit2}}</view>
						<view class="cu-item flex-sub" @click="Viewlogs(1,'credit1')" :class="data.op=='credit1'?'cur text-'+theme.actcolor:''">{{data.ops.credit1}}</view>
					</view>
				</view>
			</view>
			<view class="padding text-center" v-if="data.logs.length==0">
				<text class="text-empty">空空如也</text>
			</view>
			<view class="logs padding-bottom" v-else>
				<view class="cu-list menu sm-border margin-top-sm">
					<view class="cu-item" v-for="(item, index) in data.logs" :key="index">
						<view class="content padding-tb-sm">
							<view class="text-black text-boldm">
								{{item.remark}}
							</view>
							<view class="text-gray text-sm">
								<view class="text-cut">{{item.createdate}}</view>
							</view>
						</view>
						<view class="action">
							<text class="text-lg text-red" v-if="item.num>0">+</text>
							<text class="text-xl" :class="item.num>0?'text-red':'text-black'">
								{{item.num}}
							</text>
						</view>
					</view>
					<view class="cu-item" v-if="loadmore" @click="Viewlogs(page+1)">
						<view class="text-center flex-sub">
							<text class="text-blue text-lg">查看更多</text>
						</view>
					</view>
					<view class="cu-item" v-else>
						<view class="text-empty flex-sub">没有更多了</view>
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
				loaded:false,
				page:1,
				loadmore:true,
				theme:core.style,
				data:{
					title:"资金记录",
					op:"credit2",
					ops:{
						"credit1":"积分",
						"credit2":"余额"
					},
					logs:[]
				}
			}
		},
		onLoad(options) {
			if(typeof(options.op)!='undefined' && options.op!=''){
				this.data.op = options.op;
			}
			this.Viewlogs(1,this.data.op);
		},
		onShow() {
			
		},
		onPullDownRefresh() {
			this.Viewlogs(1,this.data.op);
			uni.stopPullDownRefresh();
		},
		methods:{
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			Viewlogs(page=1,op=''){
				if(op==''){
					op = this.data.op;
				}
				let self = this;
				core.get('credit/record',function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined') return core.report(res);
					self.page = page;
					self.data.op = op;
					self.loadmore = res.logs.length<15 ? false : true;
					if(page==1){
						self.loaded = true;
						self.data = res;
						uni.setNavigationBarTitle({
							title:res.title
						});
					}else{
						self.data.logs = self.data.logs.concat(res.logs);
					}
				},{op:op,page:page});
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.logs .cu-item .content{max-width: calc(100% - 180upx);}
</style>
