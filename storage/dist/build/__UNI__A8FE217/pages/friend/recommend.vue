<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view v-if="data.friends.length>0">
			<view class="cu-bar padding-left light" :class="'bg-'+actcolor">
				<text class="text-lg">请选择您要邀请的好友</text>
			</view>
			<scroll-view scroll-y class="indexes" :scroll-into-view="'indexes-'+ indexes.listCurID" :style="[{height:'calc(100vh - '+ CustomBar + 'px - 50px)'}]" :scroll-with-animation="true" :enable-back-to-top="true">
				<block v-for="(item,index) in data.friends" :key="index">
					<view :class="'indexItem-' + item.letter" :id="'indexes-' + item.letter" :data-index="item.letter">
						<view class="padding-sm">{{item.letter}}</view>
						<view class="cu-list menu-avatar no-padding">
							<view class="cu-item" @click="doShareCard(value.fuid)" v-for="(value,key) in item.list" :key="key">
								<view class="cu-avatar round" :style="'background-image:url('+value.avatar+');'"></view>
								<view class="content">
									<view><view class="text-cut text-lg">{{value.remark}}</view></view>
								</view>
							</view>
						</view>
					</view>
				</block>
			</scroll-view>
			<view class="indexBar" :style="[{height:'calc(100vh - ' + indexes.CustomBar + 'px - 165px)'}]">
				<view class="indexBar-box" @touchstart="tStart" @touchend="tEnd" @touchmove.stop="tMove">
					<view class="indexBar-item" v-for="(item,index) in data.friends" :key="index" :id="index" @touchstart="getCur" @touchend="setCur"> {{item.letter}}</view>
				</view>
			</view>
			<view v-show="!hidden" class="indexToast">
				{{indexes.listCur}}
			</view>
		</view>
		<view class="text-empty" v-else>
			{{text.text_empty}}
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				hidden:true,
				actcolor:core.style.actcolor,
				text:core.langs,
				indexes:{
					listCurID:'',
					CustomBar:this.CustomBar,
					listCur:'',
					boxTop:0,
					barTop:0
				},
				postdata:{
					uid:0,
					gid:0,
					dorecommend:"true"
				},
				data:{
					title:"好友邀请",
					friends:[],
					group:{
						id:0
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.cid)!='undefined' && options.type=='group'){
				options.gid = options.cid;
			}
			if(typeof(options.gid)=='undefined' || !options.gid) return core.toast('无效的群组信息','back');
			this.postdata.gid = parseInt(options.gid);
			let that = this;
			this.initData('friend/recommend',{gid:this.postdata.gid},function(res){
				if(res.friends.length>0){
					that.indexes.listCur = res.friends[0].letter;
					setTimeout(function(){
						that.indexesReady();
					},100);
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
			doShareCard(uid){
				this.postdata.uid = uid;
				let postdata = this.postdata;
				return core.confirm('确定要邀请该好友进群吗？','邀请好友',function(){
					core.post('friend/recommend',function(res){
						let redirect = res.type=='success' ? '' : res.redirect;
						core.toast(res.message,redirect,res.type);
					},postdata);
				});
			},
			indexesReady(){
				let that = this;
				uni.createSelectorQuery().select('.indexBar-box').boundingClientRect(function(res) {
					that.indexes.boxTop = res.top
				}).exec();
				uni.createSelectorQuery().select('.indexes').boundingClientRect(function(res) {
					that.indexes.barTop = res.top
				}).exec();
			},
			//滑动选择Item
			tMove(e) {
				let y = e.touches[0].clientY,
					offsettop = this.indexes.boxTop,
					that = this;
				//判断选择区域,只有在选择区才会生效
				if (y > offsettop) {
					let num = parseInt((y - offsettop) / 20);
					if(num<that.data.friends.length){
						this.indexes.listCur = that.data.friends[num].letter;
					}
				};
			},
			getCur(e) {
				this.hidden = false;
				this.indexes.listCur = this.data.friends[e.target.id].letter;
			},
			setCur(e) {
				this.hidden = true;
				this.indexes.listCur = this.indexes.listCur
			},
			//触发全部开始选择
			tStart() {
				this.hidden = false
			},
			
			//触发结束选择
			tEnd() {
				this.hidden = true;
				this.indexes.listCurID = this.indexes.listCur
			},
		}
	}
</script>

<style>
</style>
