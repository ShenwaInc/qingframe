<template>
	<view>
		<cu-custom bgColor="bg-white" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-if="data.iscommission">
			<view class="bg-white padding-lr-xl padding-tb">
				<view class="radius" :class="'bg-'+theme.actcolor">
					<view class="padding-sm bg-img">
						<view class="cu-list menu-avatar padding-top-xs comment user-card">
							<view class="cu-item padding-bottom arrow" @click="naviTo('commission/qrcode')">
								<view class="cu-avatar round xl border" :style="'background-image:url('+userinfo.avatar+');'"></view>
								<view class="content">
									<view class="title"><text class="text-xxl text-cut">{{userinfo.nickname}}</text></view>
									<view class="text-cut text-lg text-content">
										{{data.commission.name}}
									</view>
								</view>
								<view class="action">
									<view class="text-lg"><text class="cuIcon-qr_code"></text></view>
								</view>
							</view>
						</view>
						<view class="flex details text-center padding-sm padding-top-0 solid-top">
							<view class="flex-sub" @click="naviTo('commission/teams')">
								<view class="text-bold">{{data.statistics.invites}}</view>
								<text>{{texts.invites}}</text>
							</view>
							<view class="flex-sub" @click="naviTo('commission/sales')">
								<view class="text-bold">{{data.statistics.total}}</view>
								<text>{{texts.orders}}</text>
							</view>
							<view class="flex-sub" @click="naviTo('commission/logs')">
								<view class="text-bold">{{data.statistics.commission}}</view>
								<text>{{texts.commissions}}</text>
							</view>
							<view class="flex-sub" @click="naviTo('credit/cash',{show:'cash'})">
								<view class="text-bold">{{data.statistics.withdraw}}</view>
								<text>{{texts.cashs}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<view class="bg-white padding-lr-xl padding-bottom" v-if="data.invitaCode>0">
				<view class="radius bg-gray shadow-blur">
					<view class="padding text-lg" @click="doCopy()">
						<text>{{texts.invitecode}}：</text>
						<text class="text-red">{{data.invitaCode}}</text>
						<text class="cuIcon-copy text-blue margin-left-sm"></text>
					</view>
					<view class="padding text-lg solid-top flex justify-between" @click="naviTo('commission/qrcode')">
						<text>{{texts.inviteqrcode}}</text>
						<view>
							<text class="cuIcon-qr_code" :class="'text-'+theme.actcolor"></text>
							<text class="cuIcon-right"></text>
						</view>
					</view>
				</view>
			</view>
			<view class="bg-white shadow-outside">
				<view class="cu-list grid col-4 no-border">
					<view class="cu-item" @click="naviTo('commission/teams')">
						<view class="image">
							<image :src="siteroot+'/addons/xfy_whotalk/plugin/commission/static/img_commission_teams.png'" mode="aspectFit"></image>
						</view>
						<text>{{texts.myinvite}}</text>
					</view>
					<view class="cu-item" @click="naviTo('commission/sales')">
						<view class="image">
							<image :src="siteroot+'/addons/xfy_whotalk/plugin/commission/static/img_commission_sales.png'" mode="aspectFit"></image>
						</view>
						<text>{{texts.orders}}</text>
					</view>
					<view class="cu-item" @click="naviTo('commission/logs')">
						<view class="image">
							<image :src="siteroot+'/addons/xfy_whotalk/plugin/commission/static/img_commission_logs.png'" mode="aspectFit"></image>
						</view>
						<text>{{texts.commissionlog}}</text>
					</view>
					<view class="cu-item" @click="naviTo('credit/cash')">
						<view class="image">
							<image :src="siteroot+'/addons/xfy_whotalk/plugin/commission/static/img_commission_cash.png'" mode="aspectFit"></image>
						</view>
						<text>{{texts.withdraw}}</text>
					</view>
				</view>
			</view>
			<view class="padding-bottom-xs">
				<view class="nav solid-bottom">
					<view class="cu-item cur" :class="'text-'+theme.actcolor">{{texts.invitesoon}}</view>
				</view>
				<view class="text-empty" v-if="data.invites.length==0">
					<text>空空如也</text>
				</view>
				<view class="cu-list menu" v-else>
					<view class="cu-item arrow" @click="naviTo('member/index',{uid:member.uid})" v-for="(member, index) in data.invites" :key="index">
						<view class="content">
							<image :src="member.avatar" class="png radius" mode="aspectFit"></image>
							<text class="text-boldm">{{member.nickname}}</text>
						</view>
						<view class="action">
							<text class="text-grey text-sm">{{member.regtime}}</text>
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
		computed: mapState(['userId', 'hasLogin']),
		data() {
			return {
				userinfo:core.userinfo,
				loaded:false,
				theme:core.style,
				siteroot:core.system.siteroot,
				data:{
					agenter:{
						uid:0,
						nickname:''
					},
					canapply:false,
					commission:{
						id:0,
						name:''
					},
					invites:[],
					invitaCode:0,
					iscommission:false,
					statistics:{
						amout:0,
						commission:0,
						invites:0,
						total:0,
						withdraw:0
					},
					title:"分销中心",
					texts:{}
				},
				texts:{
					'invites':"邀请人数",
					'orders':"分销订单",
					'commissions':"累计佣金",
					'cashs':"累计提现",
					'invitecode':"我的邀请码",
					'inviteqrcode':"我的推广码",
					'myinvite':"我的邀请",
					'commissionlog':"佣金明细",
					'withdraw':"申请提现",
					'invitesoon':"最近邀请"
				}
			}
		},
		onLoad() {
			if(!this.hasLogin) return core.toast('请先登录','login','error');
			this.initData('commission',{},function(res){
				if(!res.canapply && !res.iscommission) return core.toast('暂未开放','back','error');
				if(!res.iscommission) return core.navito('commission/apply',{},1);
			});
		},
		onShow() {
			
		},
		onPullDownRefresh() {
			this.initData('commission',{},function(res){
				if(!res.canapply && !res.iscommission) return core.toast('暂未开放','back','error');
				if(!res.iscommission) return core.navito('commission/apply',{},1);
			});
			uni.stopPullDownRefresh();
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					if(typeof(res.texts)!='undefined'){
						that.doTextRender(res.texts);
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
			doTextRender(texts){
				for(let i in texts){
					this.texts[i] = texts[i];
				}
			},
			doCopy(){
				let invitaCode = this.data.invitaCode + "";
				return core.copy(invitaCode);
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.user-card>.cu-item{padding-left: 190upx !important; background: none !important; width: 100%;}
	.user-card>.cu-item:last-child::after{display: none;}
	.user-card>.cu-item .text-bold{font-size: 34upx; font-family:PingFangSC-Medium,PingFang SC;}
	.user-card>.cu-item .text-content{padding-top: 16upx; font-size: 26upx; line-height: 48upx; min-height: 70upx; max-height: 165upx; overflow: hidden;}
	.user-card>.cu-item .cu-avatar.xl ~ .content{min-height: 128upx; left: 0 !important;}
	.user-card>.cu-item .content .right{position: absolute; right: 0; top: 0;}
	.user-card>.cu-item .content .title{font-size: 1.06rem !important; font-weight: 600; line-height: 60upx; padding-top: 15upx;}
	.user-card>.cu-item .content .title ~ .text-content{padding-top: 0; font-size: 30upx; min-height: unset;}
	.shadow-outside .cu-item{margin-bottom: 15upx;}
	.shadow-outside .cu-item .image{width: 120upx; height: 120upx; margin: 0 auto; margin-bottom: 0.4rem;}
	.shadow-outside .cu-item .image image{max-height: 100%;}
</style>
