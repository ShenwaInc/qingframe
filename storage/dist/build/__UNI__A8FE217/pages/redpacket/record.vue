<template>
	<view>
		<cu-custom bgColor="bg-red" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="records" v-else>
			<view class="padding">
				<text class="text-lg">筛选：</text>
				<text class="cu-tag radius lg" @click="SelectView(1)" :class="selector.view==1?'bg-'+theme.actcolor:''">我发出的</text>
				<text class="cu-tag radius lg" @click="SelectGrab(1)" :class="selector.grab==1?'bg-'+theme.actcolor:''">未领完的</text>
				<text class="cu-tag radius lg" @click="SelectGrab(2)" :class="selector.grab==2?'bg-'+theme.actcolor:''">已领完的</text>
			</view>
			<view class="text-empty" v-if="data.records.length==0">
				<text>空空如也</text>
			</view>
			<block v-else>
				<view class="cu-list menu-avatar padding-lr" v-for="(item, index) in data.records" :key="index">
					<view class="cu-item radius arrow" @click="naviTo('redpacket/detail',{id:item.id})">
						<view class="cu-avatar lg round text-darkgold bg-white">
							<text class="cuIcon-redpacket_fill"></text>
						</view>
						<view class="content flex-sub text-white">
							<view class="flex justify-between padding-bottom-sm">
								<text class="text-xl">{{item.remark}}</text>
								<text>{{item.datetime}}</text>
							</view>
							<view class="flex justify-between">
								<view>
									<view class="cu-avatar sm round margin-right-xs" :style="'background-image: url('+item.avatar+')'"></view>
									<text>{{item.nickname}}</text>
								</view>
								<view class="btn-group" v-if="item.remain>0 && item.status==1">
									<button @tap.stop="doGrab(index)" class="cu-btn sm bg-green" v-if="!item.grabed">领取</button>
									<button @tap.stop="doRetrun(index)" class="cu-btn sm bg-blue margin-left-sm">退还</button>
								</view>
								<view v-else>
									<text class="text-gray">{{item.status==3?'已过期':'已领完'}}</text>
								</view>
							</view>
						</view>
					</view>
				</view>
				<view class="padding text-center margin-top-sm" @click="doViewMore()" v-if="hasmore">
					<text class="text-lg text-red">查看更多</text>
				</view>
				<view class="text-empty" v-else>
					<text>没有更多了~</text>
				</view>
			</block>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				data:{
					title:"红包记录",
					records:[]
				},
				hasmore:false,
				loaded:false,
				selector:{
					gid:0,
					uid:0,
					view:0,
					grab:0,
					page:1
				},
				theme:core.style
			}
		},
		onLoad(options) {
			if(typeof(options.gid)!='undefined' && options.gid){
				this.selector.gid = parseInt(options.gid);
			}
			this.initData('redpacket/record',this.selector);
		},
		onShow() {
			
		},
		onPullDownRefresh() {
			this.selector.page = 1;
			this.initData('redpacket/record',this.selector);
			uni.stopPullDownRefresh();
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					that.loaded = true;
					that.hasmore = res.records.length==15 ? true : false;
					if(data.page==1){
						that.data = res;
					}else{
						that.data.records = that.data.records.concat(res.records);
					}
					uni.setNavigationBarTitle({
						title:res.title
					});
					if(callback){
						callback(res);
					}
				},data);
			},
			doGrab(index){
				let packet = this.data.records[index];
				if(packet.grabed) return core.toast('您已领取过该红包');
				if(packet.remain==0) return core.toast('手慢了，该红包已领完~');
				let self = this;
				core.get("redpacket/grab",function(res){
					if(res.type!='success') return core.report(res);
					self.data.records[index].grabed = true;
					self.data.records[index].remain -= 1;
					core.toast('领取成功！','','success');
				},{id:packet.id});
			},
			doRetrun(index){
				let packet = this.data.records[index];
				if(packet.remain==0) return core.toast('该红包已被领完');
				let self = this;
				core.confirm("剩余金额将退回到用户的钱包，是否确定要退回？","退回红包",function(){
					core.get("redpacket/sendback",function(res){
						if(res.type!='success') return core.report(res);
						self.data.records[index].remain = 0;
						self.data.records[index].status = 3;
						core.toast('操作成功！','','success');
					},{id:packet.id});
				});
			},
			doViewMore(){
				this.selector.page += 1;
				this.initData('redpacket/record',this.selector);
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			},
			SelectView(view=0){
				this.selector.page = 1;
				this.selector.view = this.selector.view==view ? 0 : view;
				this.initData('redpacket/record',this.selector);
			},
			SelectGrab(grab=0){
				this.selector.page = 1;
				this.selector.grab = this.selector.grab==grab ? 0 : grab;
				this.initData('redpacket/record',this.selector);
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.records .cu-item{background-color: #e54d42 !important;}
	.cu-list.menu-avatar>.cu-item{height: 164upx;}
	.cu-list.menu-avatar:not(.special-content)>.cu-item.arrow .content{width: calc(100% - 96upx - 120upx);}
	.cu-list.menu-avatar>.cu-item.arrow:before{color: #FFFFFF;}
</style>
