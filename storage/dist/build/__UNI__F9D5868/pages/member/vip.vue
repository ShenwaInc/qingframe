<template>
	<view>
		<cu-custom bgColor="bg-black" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="cu-list menu-avatar">
				<view class="cu-item bg-black noaf">
					<view class="cu-avatar radius lg" :style="'background-image:url('+userinfo.avatar+');'"></view>
					<view class="content">
						<view>
							<view class="text-cut text-xl">
								{{userinfo.nickname}}
								<text class="cu-tag bg-orange round margin-left-sm">{{data.mygroup.title}}</text>
							</view>
						</view>
						<view class="text-gray text-sm flex">
							<view class="text-cut" v-if="data.group_level==0">{{data.expiredate}}</view>
							<view class="text-cut" v-else>
								{{data.upgradetext}}
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="bg-black">
				<view class="bg-white round-top">
					<view class="grid padding col-3 text-center groups" v-if="data.warpstyle">
						<view class="padding-xs" v-for="(item, index) in data.groups" :key="index" @click="doCheckGroup(item.groupid)">
							<view class="padding-sm radius-lg group-item" :class="groupid==item.groupid?'cur solids':'solid'">
								<view class="text-lg g-title text-cut">{{item.title}}</view>
								<view class="text-center margin-top-sm">
									<text :class="item.price==0?'text-sm':'text-price text-red text-lg'" v-if="data.group_level==0">{{item.price||'免费'}}{{item.price>0?'/'+paytypes[item.paytype]:''}}</text>
									<text class="text-red text-lg" v-else>{{item.credit}}{{data.creditname}}</text>
								</view>
							</view>
						</view>
					</view>
					<view class="padding groups" v-else>
						<scroll-view class="groups-scroll" :scroll-into-view="intoview" scroll-with-animation scroll-x>
							<view :id="'groupitem-'+item.groupid" class="padding margin-xs radius-lg group-item" :class="groupid==item.groupid?'cur solids':'solid'" v-for="(item, index) in data.groups" :key="index" @click="doCheckGroup(item.groupid)">
								<view class="text-lg g-title text-cut">{{item.title}}</view>
								<view class="text-center margin-top-sm">
									<text :class="item.price==0?'text-sm':'text-price text-red text-lg'" v-if="data.group_level==0">{{item.price||'免费'}}{{item.price>0?'/'+paytypes[item.paytype]:''}}</text>
									<text class="text-red text-lg" v-else>{{item.credit}}{{data.creditname}}</text>
								</view>
							</view>
						</scroll-view>
					</view>
					<advs :advs="advs.vips"></advs>
					<view class="solid-top">
						<view class="cu-bar bg-white">
							<view class="action">
								<text class="cuIcon-title text-orange"></text>
								{{groupcur.title}}专享特权
							</view>
						</view>
						<view class="cu-list menu privileges">
							<view class="cu-item" v-if="groupcur.makefriend==0">
								<view class="content text-red">
									<text class="cuIcon-roundclose"></text>
									<text>不可添加好友</text>
								</view>
							</view>
							<view class="cu-item" v-else>
								<view class="content">
									<text class="cuIcon-friendadd" :class="'text-'+theme.actcolor"></text>
									<text class="text-orange">可添加{{groupcur.friends||'无限'}}个好友</text>
								</view>
							</view>
							<view class="cu-item" v-if="groupcur.maxcreate==0">
								<view class="content text-red">
									<text class="cuIcon-roundclose"></text>
									<text>不可创建群组</text>
								</view>
							</view>
							<view class="cu-item" v-else>
								<view class="content">
									<text class="cuIcon-group_fill" :class="'text-'+theme.actcolor"></text>
									<text class="text-orange">最多可创建{{groupcur.maxcreate}}个群组</text>
								</view>
								<view class="action text-center text-red">
									<text v-if="groupcur.members==0">不限人数</text>
									<text v-else>群成员上限{{groupcur.members}}人</text>
								</view>
							</view>
							<view class="cu-item" v-if="groupcur.maxjoin==0">
								<view class="content text-red">
									<text class="cuIcon-roundclose"></text>
									<text>不可加入群组</text>
								</view>
							</view>
							<view class="cu-item" v-else>
								<view class="content">
									<text class="cuIcon-friend" :class="'text-'+theme.actcolor"></text>
									<text class="text-orange">最多可加入{{groupcur.maxjoin}}个群组</text>
								</view>
							</view>
							<view class="cu-item">
								<view class="content">
									<text class="cuIcon-roundcheckfill" :class="'text-'+theme.actcolor"></text>
									<text class="text-orange" v-if="groupcur.proportion==0">个人收益可免抽成</text>
									<text class="text-orange" v-else>个人收益收取{{groupcur.proportion}}%服务费</text>
								</view>
							</view>
							<block v-if="squareLimit">
								<view class="cu-item" v-if="groupcur.squareLimit==0">
									<view class="content text-red">
										<text class="cuIcon-roundclose"></text>
										<text>不可同步动态到广场</text>
									</view>
								</view>
								<view class="cu-item" v-else>
									<view class="content">
										<text class="cuIcon-camerafill" :class="'text-'+theme.actcolor"></text>
										<text class="text-orange">可同步{{groupcur.squareLimit}}条动态到广场</text>
									</view>
								</view>
							</block>
							<view class="cu-item" v-if="groupcur.hideadvs==0">
								<view class="content text-red">
									<text class="cuIcon-roundclose"></text>
									<text>站内展示所有广告</text>
								</view>
							</view>
							<view class="cu-item" v-else>
								<view class="content">
									<text class="cuIcon-roundcheckfill" :class="'text-'+theme.actcolor"></text>
									<text class="text-orange">全站无广告</text>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="cu-bar tabbar nobf shop foot noshadow justify-center" v-if="data.group_level==0" :class="[(groupcur.price==0?'hidden':''),(groupcur.groupid==data.mygroup.groupid && data.vipexpire==0)?'hidden':'']">
				<view class="flex flex-quadruple padding-lr-xl padding-bottom-sm">
					<view class="cu-btn bg-black round-only-left shadow-blur lg flex-quadruple">
						<text class="text-price text-orange">{{groupcur.price}}/{{paytypes[groupcur.paytype]}}</text>
					</view>
					<button @click="doUpgrade()" class="cu-btn bg-gradual-orange round-only-right shadow-blur lg flex-twice">{{groupcur.groupid==data.mygroup.groupid?'续费':'升级'}}</button>
				</view>
			</view>
			<view class="cu-bar tabbar nobf shop foot noshadow justify-center" v-else :class="(groupcur.credit==0 || groupcur.credit<=data.mygroup.credit?'hidden':'')">
				<view class="flex flex-quadruple padding-lr-xl padding-bottom-sm">
					<view class="cu-btn bg-black round-only-left shadow-blur lg flex-quadruple">
						<text class="text-orange">还需{{upgradecredit}}{{data.creditname}}</text>
					</view>
					<button @click="doUpgrade()" v-if="data.exchange" class="cu-btn bg-gradual-orange round-only-right shadow-blur lg flex-twice">升级</button>
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
				advs:{
					'vips':[]
				},
				data:{
					creditname:'积分',
					exchange:false,
					expiredate:'已到期',
					group_level:0,
					groups:[],
					isexpired:false,
					mycredit:0,
					mygroup:{
						groupid:0,
						title:"默认会员组",
						credit:0,
						price:0.00,
						paytype:0
					},
					title:"购买会员",
					upgradetext:'您已经是最高等级会员',
					upgradetip:false,
					vipexpire:0,
					warpstyle:false
				},
				frompage:"",
				groupid:0,
				groupcur:{
					groupid:0,
					title:"默认会员组",
					credit:0,
					paytype:0,
					price:0.00,
					friends:0,
					hideadvs:1,
					makefriend:1,
					maxcreate:0,
					maxjoin:0,
					members:0,
					proportion:0,
					squareLimit:5
				},
				intoview:'',
				loaded:false,
				squareLimit:true,
				paytypes:['月','季','年'],
				theme:core.style,
				upgradecredit:0,
				userinfo:core.userinfo
			}
		},
		onLoad(options) {
			if(options.fp) this.frompage = options.fp;
		},
		onUnload() {
			uni.$off('CoreInited',this.initSystem);
		},
		onShow() {
			if(!core.inited){
				return uni.$on('CoreInited',this.initSystem);
			}
			this.initData('member/vip');
		},
		onPullDownRefresh() {
			this.initData('member/vip');
			uni.stopPullDownRefresh();
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					if(!that.loaded){
						that.loaded = true;
						that.groupid = res.mygroup.groupid;
						that.intoview = 'groupitem-' + that.groupid;
					}
					let groupCur = res.groups[that.groupid];
					if(typeof(groupCur.squareLimit)=='undefined'){
						groupCur.squareLimit = 5;
						squareLimit = false;
					}
					that.groupcur = groupCur;
					if(typeof(res.creditname)=='undefined'){
						res.creditname = '积分';
					}
					that.data = res;
					core.cachecloud('advs',function(advs){
						that.advs.vips = advs.vips;
					});
					uni.setNavigationBarTitle({
						title:res.title
					});
					if(callback){
						callback(res);
					}
				},data);
			},
			initSystem(data){
				this.userinfo = data.userinfo;
				this.initData('member/vip');
			},
			doCheckGroup(groupid){
				this.groupid = groupid;
				let groupCur = this.data.groups[groupid];
				if(typeof(groupCur.squareLimit)=='undefined'){
					groupCur.squareLimit = 5;
				}
				this.groupcur = groupCur;
				this.upgradecredit = this.groupcur.credit - this.data.mycredit;
			},
			doUpgrade(){
				if(this.data.group_level>0){
					if(this.frompage=='credit'){
						core.back();
					}
					return core.navito('credit/index', {op: 'exchange'});
				}
				if(this.groupcur.price<this.data.mygroup.price && !this.data.isexpired) return core.toast('该等级低于您当前等级');
				let groupid = this.groupcur.groupid;
				let self = this;
				let Upgrader = function(){
					core.post('member/vip',function(res){
						if(res.type!='success') return core.toast(res.message);
						let redirect = core.page('credit/pay',{tid:res.message.tid});
						core.toast('即将进入支付...',redirect,'success')
					},{upgradevip:"true",groupid:groupid})
				};
				if(this.data.upgradetip && groupid!=this.data.mygroup.groupid){
					return core.confirm('升级后当前等级的未到期限将被清空，是否确定要升级？','温馨提示',Upgrader);
				}
				return Upgrader();
			},
			naviTo(page,data={}){
				return core.navito(page,data);
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF; padding-bottom: 80upx;}
	.cu-item.bg-black{background-color: #333333 !important;}
	.groups-scroll{white-space: nowrap;}
	.group-item{display: inline-block; min-width: 25%; text-align: center;}
	.grid .group-item{display: block; width: 100%;}
	.group-item .g-title{height: 50upx; line-height: 50upx;}
	.group-item.cur{background-color: #fde6d2;}
	.group-item.cur::after{border-color: #f37b1d;}
	.group-item.cur .g-title{color: #f37b1d;}
	.text-price.text-orange{color: #fef2ce; font-size: 34upx; font-weight: bold;}
	.privileges [class*="cuIcon-"]{font-size: 46upx; vertical-align: middle;}
</style>
