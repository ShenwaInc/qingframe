<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="nav bg-white">
			<view class="flex text-center">
				<view class="cu-item flex-sub" @click="doCheckout(0)" :class="createmethod==0?'cur text-'+theme.actcolor:''">
					选择好友建群
				</view>
				<view class="cu-item flex-sub" @click="doCheckout(1)" :class="createmethod==1?'cur text-'+theme.actcolor:''">
					直接创建群组
				</view>
			</view>
		</view>
		<view :class="createmethod==0?'':'hidden'">
			<view v-if="data.friends.length>0">
				<view class="cu-bar padding-left light" :class="'bg-'+theme.actcolor">
					<text class="text-lg">请选择好友创建群组({{uids.length}}/50)</text>
				</view>
				<scroll-view scroll-y class="indexes" :scroll-into-view="'indexes-'+ indexes.listCurID" :style="[{height:'calc(100vh - '+ CustomBar + 'px - ' + FootBar + 'px)'}]" :scroll-with-animation="true" :enable-back-to-top="true">
					<block v-for="(item,index) in data.friends" :key="index">
						<view :class="'indexItem-' + item.letter" :id="'indexes-' + item.letter" :data-index="item.letter">
							<view class="padding-sm">{{item.letter}}</view>
							<view class="cu-list menu-avatar no-padding">
								<view class="cu-item" :class="uids.indexOf(value.fuid)>-1?'light bg-'+theme.actcolor:''" @click="doGroupCatch(value.fuid)" v-for="(value,key) in item.list" :key="key">
									<view class="cu-avatar round" :style="'background-image:url('+value.avatar+');'"></view>
									<view class="content">
										<view><view class="text-cut text-lg">{{value.remark}}</view></view>
									</view>
									<view class="action">
										<checkbox class="round" :class="[uids.indexOf(value.fuid)>-1?'checked':'',theme.actcolor]" :checked="uids.indexOf(value.fuid)>-1?true:false" :value="value.fuid+''"></checkbox>
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
				<text v-if="loaded">{{text.text_empty}}</text>
				<text v-else class="cu-load load-cuIcon loading">好友加载中...</text>
			</view>
		</view>
		<view :class="createmethod==1?'':'hidden'" class="padding bg-white solid-top createdata">
			<view class="padding">
				<view class="avatar shadow text-center" @click="ChooseCover()">
					<view class="cu-avatar xl round shadow" :style="'background-image:url('+pic+');'"></view>
					<view class="icon text-yellow"><text class="cuIcon-cameraadd"></text></view>
				</view>
			</view>
			<form>
				<view class="cu-form-group margin-top">
					<view class="title">群名称</view>
					<input placeholder="请输入群聊的名称" v-model="postdata.name" name="name"></input>
				</view>
				<view class="cu-form-group align-start">
					<view class="title">群介绍</view>
					<textarea maxlength="-1" v-model="postdata.description" placeholder="请简要说明这个群组"></textarea>
				</view>
				<view class="cu-form-group align-start hidden">
					<view class="title">群公告</view>
					<textarea maxlength="-1" v-model="postdata.notice" placeholder="请输入要发布的群公告"></textarea>
				</view>
				<view class="cu-list menu solid-top" style="margin-top: 0;">
					<view class="cu-item arrow" @click="showmodal='editfee'" v-if="data.joinfee==1">
						<view class="content">
							进群收费
						</view>
						<view class="action">
							<text class="cu-tag round bg-red light" v-if="postdata.fee>0">￥{{postdata.fee}}</text>
							<text class="cu-tag round light" :class="'bg-'+theme.actcolor" v-else>免费</text>
						</view>
					</view>
					<view class="cu-item">
						<view class="content">
							全员禁言
						</view>
						<view class="action">
							<switch class="switch" data-id="allowpost" @change="SwitchSet" :class="postdata.allowpost==1?'':'checked '+theme.actcolor" :checked="postdata.allowpost==1?false:true"></switch>
						</view>
					</view>
					<view class="cu-item">
						<view class="content">
							成员保护
						</view>
						<view class="action">
							<switch class="switch" data-id="protective" @change="SwitchSet" :class="postdata.protective==0?'':'checked '+theme.actcolor" :checked="postdata.protective==0?false:true"></switch>
						</view>
					</view>
					<view class="cu-item">
						<view class="content">
							允许加入
						</view>
						<view class="action">
							<switch class="switch" data-id="allowjoin" @change="SwitchSet" :class="postdata.allowjoin==0?'':'checked '+theme.actcolor" :checked="postdata.allowjoin==0?false:true"></switch>
						</view>
					</view>
					<view class="cu-item" v-if="postdata.allowjoin==1">
						<view class="content">
							加入需要审核
						</view>
						<view class="action">
							<switch class="switch" data-id="joinaudit" @change="SwitchSet" :class="postdata.joinaudit==0?'':'checked '+theme.actcolor" :checked="postdata.joinaudit==0?false:true"></switch>
						</view>
					</view>
					<view class="cu-item">
						<view class="content">
							隐藏群组
						</view>
						<view class="action">
							<switch class="switch" data-id="ishide" @change="SwitchSet" :class="postdata.ishide==0?'':'checked '+theme.actcolor" :checked="postdata.ishide==0?false:true"></switch>
						</view>
					</view>
				</view>
			</form>
		</view>
		<view class="cu-bar tabbar foot noshadow nobg">
			<view class="action">
				<view class="flex flex-direction padding-lr-xl padding-bottom">
					<button class="cu-btn shadow-blur lg round margin-lr-xl" :class="'bg-'+theme.actcolor" @click="doCreateGroup()">创建群聊<text class="text-red" v-if="data.pricetext!=''">({{data.pricetext}})</text></button>
					<view class="text-center text-lg padding-tb-sm" @click="naviTo(data.agreement)" v-if="data.agreement!=''">
						<text>已阅读并同意</text>
						<text :style="'color:'+theme.link">《创建群组协议》</text>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal" :class="showmodal=='editfee'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">进群收费</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view>
					<view class="cu-form-group text-left">
						<input type="digit" placeholder="输入进群费用(元),0为免费" v-model="postdata.fee" name="fee"></input>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn text-gray" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left-sm" :class="'line-'+theme.actcolor" @tap="postdata.fee=0;showmodal=''">免费</button>
						<button class="cu-btn margin-left-sm" :class="'bg-'+theme.actcolor" @click="showmodal=''">确定</button>
					</view>
				</view>
			</view>
		</view>
		<!-- #ifdef H5 || MP-WEIXIN -->
		<ImageCrop :cropImage="scrop.image" resFrom="profiles" :cropTempFile="scrop.tempfile" :isSquare="scrop.square" @cropRes="CropCover"></ImageCrop>
		<!-- #endif -->
	</view>
</template>

<script>
	import core from "@/core.js"
	import ImageCrop from "@/components/util/ImageCrop.vue"
	
	export default {
		components: {ImageCrop},
		data() {
			return {
				hidden:true,
				loaded:false,
				creating:false,
				showmodal:'',
				theme:core.style,
				text:core.langs,
				systemname:core.system.name,
				FootBar:139,
				indexes:{
					listCurID:'',
					CustomBar:this.CustomBar,
					listCur:'',
					boxTop:0,
					barTop:0
				},
				uids:[],
				createmethod:0,
				pic:core.system.defaultimg,
				postdata:{
					name:'',
					pic:'',
					description:'',
					allowpost:1,
					protective:0,
					allowjoin:1,
					joinaudit:0,
					fee:0,
					ishide:0,
					position:""
				},
				data:{
					title:"创建群组",
					confirmtext:'',
					pricetext:'',
					agreement:'',
					joinfee:1,
					friends:[],
					getpos:false
				},
				scrop:{
					image:"",
					width:192,
					square:false,
					tempfile:""
				}
			}
		},
		onLoad() {
			let that = this;
			this.initData('group/create',{},function(res){
				if(res.friends.length>0){
					that.indexes.listCur = res.friends[0].letter;
					setTimeout(function(){
						that.indexesReady();
					},100);
				}
				if(res.pricetext!=''){
					that.createmethod = 1;
				}
				if(res.getpos){
					core.getpos(function(res){
						that.postdata.position = res.lat+","+res.lng;
					}, false);
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
			doback(){
				return core.back();
			},
			SwitchSet(e){
				let setid = e.target.dataset.id;
				let setvalue = e.detail.value ? 1 : 0;
				if(setid=='allowpost'){
					setvalue = setvalue==1 ? 0 : 1;
				}
				this.postdata[setid] = setvalue;
			},
			ChooseCover(){
				var that = this;
				uni.chooseImage({
					count: 1, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					crop:{
						quality:100,
						width:this.scrop.width,
						height:this.scrop.width
					},
					success: (res) => {
						// #ifdef H5 || MP-WEIXIN
						that.scrop.tempfile = res.tempFilePaths[0];
						that.scrop.image = res.tempFiles[0].path;
						that.scrop.square = false;
						that.showmodal = 'ImageCroper';
						return uni.getImageInfo({
							src:res.tempFiles[0].path,
							success(img) {
								let size = Math.abs(img.width-img.height);
								if(size<10){
									that.scrop.square = true;
								}
							}
						});
						// #endif
						core.upload(res.tempFilePaths[0],function(res){
							that.pic = res.url;
							that.postdata.pic = res.path;
						}, 'image', 'attach/upload', {resfrom:"profiles"});
					}
				});
			},
			CropCover(res){
				this.pic = res.url;
				this.postdata.pic = res.path;
			},
			doCheckout(m=0){
				if(m==0 && this.data.pricetext!='') return core.toast('暂不支持选择好友建群');
				this.createmethod = m;
			},
			doGroupCatch(uid){
				let uidindex = this.uids.indexOf(uid);
				if(uidindex==-1){
					if(this.uids.length>=50) return core.toast('创建时最多可选50人');
					this.uids.push(uid);
				}else{
					this.uids.splice(uidindex,1);
				}
			},
			doCreateGroup(){
				if(this.creating) return false;
				if(this.createmethod==1){
					let postdata = this.postdata;
					if(postdata.name=='') return core.toast('请输入群组名称');
					let joinfee = 0;
					if(postdata.fee>0) joinfee = 1;
					let self = this;
					let submitcreate = function(){
						self.creating = true;
						core.post('group/create',function(res){
							if(res.type!='success'){
								self.creating = false;
							}
							core.toast(res.message,res.redirect,res.type);
						},{data:postdata,joinfee:joinfee,token:1},'json',true);
					}
					if(this.data.pricetext!=''){
						//要收钱的
						return core.confirm(this.data.confirmtext,'付费建群',function(){
							submitcreate();
						});
					}else{
						submitcreate();
					}
				}else{
					if(this.uids.length == 0) return core.toast('请选择要群聊的好友');
					if(this.uids.length==1){
						return core.navito('dialog/index',{type:'normal',cid:this.uids[0]},1);
					}
					this.creating = true;
					let self = this;
					core.post('group/create',function(res){
						if(res.type!='success'){
							self.creating = false;
						}
						core.toast(res.message,res.redirect,res.type);
					},{createsubmit:"true",uids:this.uids,position:this.postdata.position},'json',true);
				}
			},
			indexesReady(){
				if(this.data.agreement!='') this.FootBar = 175;
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
	.indexes .cu-item .action{padding-right: 130upx;}
	.createdata{padding-bottom: 110upx;}
</style>
