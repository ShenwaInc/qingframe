<template>
	<view>
		<cu-custom bgColor="bg-dazzledark" :isBack="true" @navtap="onNavTap">
			<block slot="content">▼ {{data.title}}</block>
		</cu-custom>
		<swaload bgColor="dazzledark" :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="near-page" v-else>
			<nears :data="data.neardata" pagestyle="bg-dazzledark" navstyle="darkgold" actstyle="dazzledark" frompage="nearby" :showmenu="showbtmenu" @hidemenu="showbtmenu=false" @editinfo="doEditInfo()" @showshare="showshare=true"></nears>
		</view>
		<view class="cu-modal" :class="showmodal=='nearinfo'?'show':''">
			<view class="cu-modal-bg" @tap="showmodal=''"></view>
			<view class="cu-dialog bg-white">
				<view class="cu-bar justify-end">
					<view class="content">完善个人资料</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding-lr text-left">
					<scroll-view scroll-y class="nearscroll" show-scrollbar>
						<view class="head">
							<view class="flex" v-if="data.nearinfo.cover">
								<view class="cover flex-sub padding-right-xs">
									<image :src="data.nearinfo.cover" mode="heightFix"></image>
									<button @click="ChooseCover('cover')" class="cu-btn bg-gold sm round text-white">重新上传</button>
								</view>
								<view class="flex-twice justify-between align-center nearshow text-center">
									<view class="pic1 margin-bottom-sm">
										<view class="padding-xl solids" @click="ChooseCover('pic')" v-if="data.nearinfo.pics.length==0">
											<text class="cuIcon-cameraadd text-xl"></text>
										</view>
										<view class="viewpic" v-else>
											<image :src="data.nearinfo.pics[0]" mode="widthFix"></image>
											<view class="cu-tag bg-red sm" @tap.stop="DelImg()" v-if="data.nearinfo.pics.length==1">
												<text class='cuIcon-close'></text>
											</view>
										</view>
									</view>
									<view class="flex pic2">
										<view class="pic3 flex-sub padding-right-xs">
											<view class="padding-xl solids" @click="ChooseCover('pic')" v-if="data.nearinfo.pics.length<2">
												<text class="cuIcon-cameraadd text-xl"></text>
											</view>
											<view class="viewpic" v-else>
												<image :src="data.nearinfo.pics[1]" mode="widthFix"></image>
												<view class="cu-tag bg-red sm" @tap.stop="DelImg()" v-if="data.nearinfo.pics.length==2">
													<text class='cuIcon-close'></text>
												</view>
											</view>
										</view>
										<view class="pic4 flex-sub padding-left-xs">
											<view class="padding-xl solids" @click="ChooseCover('pic')" v-if="data.nearinfo.pics.length<3">
												<text class="cuIcon-cameraadd text-xl"></text>
											</view>
											<view class="viewpic" v-else>
												<image :src="data.nearinfo.pics[2]" mode="widthFix"></image>
												<view class="cu-tag bg-red sm" @tap.stop="DelImg()">
													<text class='cuIcon-close'></text>
												</view>
											</view>
										</view>
									</view>
								</view>
							</view>
							<view @click="ChooseCover('cover')" class="uploader text-center" v-else>
								<view class="nocover">
									<image src="@/static/images/img_no_cover.png" mode="aspectFit"></image>
								</view>
								<view class="text-mutede padding-xs">
									<text>点击上传个人展头像(竖向)</text>
								</view>
							</view>
						</view>
						<view class="cu-form-group must">
							<view class="title">昵称</view>
							<input type="text" placeholder="输入一个好听的昵称" name="nickname" v-model="data.profile.nickname"></input>
						</view>
						<view class="cu-form-group must">
							<view class="title">职业</view>
							<input type="text" placeholder="输入您当前的职业" name="occupation" v-model="data.profile.occupation"></input>
						</view>
						<view class="cu-form-group must">
							<view class="title">性别</view>
							<radio-group @change="ChangeGender">
								<label>
									<radio class="radio" :class="[data.profile.gender==2?'':'checked',theme.actcolor]" :checked="data.profile.gender==2?false:true" value="1"></radio>
									<text class="padding-left-sm">男生</text>
								</label>
								<label class="margin-left padding-left">
									<radio class="radio" :class="[data.profile.gender==2?'checked':'',theme.actcolor]" :checked="data.profile.gender==2?true:false" value="2"></radio>
									<text class="padding-left-sm">女生</text>
								</label>
							</radio-group>
						</view>
						<view class="cu-form-group must">
							<view class="title">生日</view>
							<picker mode="date" :value="datetime" start="1980-01-01" end="2010-01-01" @change="DateChange">
								<view class="picker">
									<text v-if="data.profile.birthday">{{data.profile.birthyear}}-{{data.profile.birthmonth}}-{{data.profile.birthday}}</text>
									<text v-else>请选择您的出生日期</text>
								</view>
							</picker>
						</view>
						<view class="flex cu-form-group must">
							<view class="flex-sub cu-form-group">
								<view class="title">身高</view>
								<input type="number" placeholder="单位CM" name="height" v-model="data.profile.height"></input>
							</view>
							<view class="flex-sub cu-form-group nobd">
								<view class="title">体重</view>
								<input type="number" placeholder="单位kg" name="weight" v-model="data.profile.weight"></input>
							</view>
						</view>
						<view class="cu-list menu solid-top">
							<view class="cu-item arrow" @click="naviTo('member/account')">
								<view class="content text-blackm">地区</view>
								<view class="action">
									<text class="text-gray">{{data.profile.residecity||'未知'}}</text>
								</view>
							</view>
						</view>
					</scroll-view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn" :class="'line-'+theme.actcolor+' text-'+theme.actcolor" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left" :class="'bg-'+theme.actcolor" @tap="doSaveProfile()">保存</button>
					</view>
				</view>
			</view>
		</view>
		<share :shareinfo="shareinfo" @closeshare="showshare=false" :showshare="showshare"></share>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	import share from "@/components/util/share.vue"
	import nears from "@/components/util/nears.vue"
	
	export default {
		components: {share,nears},
		computed: mapState(['userId']),
		data() {
			return {
				loaded:false,
				showmodal:"",
				datetime:"",
				showbtmenu:false,
				showshare:false,
				shareinfo:{
					url:'',
					title:"附近的人",
					providers:4,
					provider:{}
				},
				inwechat:core.inwechat,
				theme:core.style,
				hasmore:true,
				updateposed:false,
				selector:{
					gender:-1,
					order:'online',
					page:1
				},
				data:{
					title:"附近的人",
					neardata:{
						members:[],
						lbskey:"",
						hasposition:false,
						viewgender:0
					},
					nearinfo:{
						id:0,
						gender:0,
						cover:'',
						pics:[],
						status:0
					},
					profile:{
						birthyear:0,
						birthmonth:0,
						birthday:0,
						cover:"",
						gender:0,
						height:0,
						nickname:core.userinfo.nickname,
						occupation:"",
						pics:[],
						residecity:"",
						weight:""
					},
					shareinfo:{}
				}
			}
		},
		onLoad(options) {
			let self = this;
			this.initData('ranking/nearby',{},function(){
				if(typeof(options.editinfo)!='undefined' && options.editinfo==1){
					self.doEditInfo();
				}
			});
		},
		onShow() {
			
		},
		onShareAppMessage(e){
			return {
				title:this.shareinfo.title,
				path:core.page('ranking/nearby',{fromuid:this.userId}),
			}
		},
		onShareTimeline(e){
			return {
				title:this.shareinfo.title,
				query:'fromuid='+this.userId
			}
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				let page = parseInt(data.page);
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
					if(!that.loaded){
						if(res.profile.birthday>0){
							that.datetime = res.profile.birthyear + '-' + res.profile.birthmonth + '-' +  res.profile.birthday + '';
						}
						if(res.nearinfo.status!=1){
							that.doEditInfo();
						}
						if(typeof(res.shareinfo)!='undefined'){
							that.shareinfo = core.initshare(res.shareinfo.title,res.shareinfo.url,res.shareinfo.cover,res.shareinfo.desc);
						}
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
			doSaveProfile(){
				let profile = this.data.profile;
				if(profile.cover=='') return core.toast('请上传您的展示图');
				if(profile.nickname=='') return core.toast('昵称不能为空');
				if(profile.occupation=='') return core.toast('职业不能为空');
				if(profile.birthday==0) return core.toast('请选择您的出生日期');
				if(profile.weight==0) return core.toast('请填写您的体重');
				if(profile.height==0) return core.toast('请填写您的身高');
				if(profile.gender<=0) profile.gender = 1;
				let self = this;
				return core.post('ranking/nearby',function(res){
					if(res.type!='success') return core.report(res);
					self.showmodal = '';
					self.initData('ranking/nearby');
				},{saveprofile:"true",profile:profile});
			},
			DateChange(e){
				this.datetime = e.detail.value;
				let datetime = this.datetime.split('-');
				this.data.profile.birthyear = parseInt(datetime[0]);
				this.data.profile.birthmonth = parseInt(datetime[1]);
				this.data.profile.birthday = parseInt(datetime[2]);
			},
			ChangeGender(e){
				this.data.profile.gender = e.detail.value;
			},
			DelImg(){
				let imgindex = this.data.nearinfo.pics.length - 1;
				if(imgindex<0) return false;
				this.data.nearinfo.pics.splice(imgindex,1);
				this.data.profile.pics.splice(imgindex,1);
			},
			doEditInfo(){
				this.showmodal = 'nearinfo';
			},
			ChooseCover(itype){
				let self = this;
				return uni.chooseImage({
					count:1,
					crop:{
						quality:100,
						width:340,
						height:534
					},
					success: (res) => {
						core.upload(res.tempFilePaths[0],function(res){
							if(itype=='cover'){
								self.data.nearinfo.cover = res.url;
								self.data.profile.cover = res.path;
							}else{
								self.data.nearinfo.pics.push(res.url);
								self.data.profile.pics.push(res.path);
							}
						});
					}
				})
			},
			onNavTap(e){
				if(e.type=='click'){
					this.showbtmenu = true;
				}
			}
		}
	}
</script>

<style>
	page{background-color: #2a2833; color: #FFFFFF;}
	.near-page{position: relative;}
	.nearscroll{max-height: 48vh;}
	.head .cover image{height: 292upx; max-width: 186upx; display: block;}
	.pic1, .pic2{height: 136upx; overflow: hidden;}
	.nearshow image{width: 100% !important; display: block;}
	.cover{position: relative;}
	.cover .cu-btn{position: absolute; left: calc(73upx - 2em); bottom: 20upx;}
	.uploader{padding: 50upx 0; background: #F7F7F7;}
	.nocover image{width: 78upx; height: 63upx;}
	.viewpic .cu-tag{position: absolute; top: 0; right: 0;}
	.flex.cu-form-group{padding: 0;}
	.text-blackm, .cu-form-group > .title{color: #232323;}
	.cu-form-group > .title{min-width: 4em;}
</style>
