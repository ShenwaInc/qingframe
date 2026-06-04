<template>
	<view>
		<cu-custom :isBack="isBack" bgColor="bg-white">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view class="padding" v-else>
			<view class="head" v-if="data.requires.indexOf('pics')>=0">
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
			<view class="cu-form-group must" v-if="data.requires.indexOf('nickname')>=0">
				<view class="title">昵称</view>
				<input type="text" placeholder="输入一个好听的昵称" name="nickname" v-model="data.profile.nickname"></input>
			</view>
			<view class="cu-form-group must" v-if="data.requires.indexOf('gender')>=0">
				<view class="title">性别</view>
				<radio-group @change="ChangeGender">
					<label>
						<radio class="radio" :class="[data.profile.gender==2?'':'checked',theme.actcolor]" :checked="data.profile.gender==2?false:true" value="1"></radio>
						<text class="padding-left-sm">男</text>
					</label>
					<label class="margin-left padding-left">
						<radio class="radio" :class="[data.profile.gender==2?'checked':'',theme.actcolor]" :checked="data.profile.gender==2?true:false" value="2"></radio>
						<text class="padding-left-sm">女</text>
					</label>
				</radio-group>
			</view>
			<view class="cu-form-group must" v-if="data.requires.indexOf('birthday')>=0">
				<view class="title">生日</view>
				<picker mode="date" :value="datetime" start="1950-01-01" end="2020-01-01" @change="ChangeDate">
					<view class="picker">
						<text v-if="data.profile.birthday">{{data.profile.birthyear}}-{{data.profile.birthmonth}}-{{data.profile.birthday}}</text>
						<text v-else>请选择您的出生日期</text>
					</view>
				</picker>
			</view>
			<view class="flex cu-form-group must" v-if="data.requires.indexOf('height')>=0">
				<view class="flex-sub cu-form-group">
					<view class="title">身高</view>
					<input type="number" placeholder="单位CM" name="height" v-model="data.profile.height"></input>
				</view>
				<view class="flex-sub cu-form-group nobd">
					<view class="title">体重</view>
					<input type="number" placeholder="单位kg" name="weight" v-model="data.profile.weight"></input>
				</view>
			</view>
			<view class="cu-form-group" v-if="data.requires.indexOf('qq')>=0">
				<view class="title">QQ号</view>
				<input type="text" placeholder="请输入您的QQ号" name="qq" v-model="data.profile.qq"></input>
			</view>
			<view class="cu-form-group" v-if="data.requires.indexOf('constellation')>=0">
				<view class="title">星座</view>
				<picker :range="constellations" @change="ChangeCons">
					<view class="text-right" :class="data.profile.constellation==''?'text-gray':''">{{data.profile.constellation || '请选择星座'}}<text class="cuIcon-right"></text></view>
				</picker>
			</view>
			<view class="cu-form-group" v-if="data.requires.indexOf('zodiac')>=0">
				<view class="title">生肖</view>
				<picker :range="zodiacs" @change="ChangeZodiac">
					<view class="text-right" :class="data.profile.zodiac==''?'text-gray':''">{{data.profile.zodiac || '请选择生肖'}}<text class="cuIcon-right"></text></view>
				</picker>
			</view>
			<view class="cu-form-group" v-if="data.requires.indexOf('affectivestatus')>=0">
				<view class="title">情感状态</view>
				<picker :range="affectivestatus" @change="ChangeAffect">
					<view class="text-right" :class="data.profile.affectivestatus==''?'text-gray':''">{{data.profile.affectivestatus || '请选择情感状态'}}<text class="cuIcon-right"></text></view>
				</picker>
			</view>
			<view class="cu-form-group must" v-if="data.requires.indexOf('occupation')>=0">
				<view class="title">职业</view>
				<input type="text" placeholder="输入您当前的职业" name="occupation" v-model="data.profile.occupation"></input>
			</view>
			<block v-else-if="(data.requires.indexOf('occupationchoose')>=0 || data.requires.indexOf('occupationmulti')>=0) && data.occupations.length>0">
				<view class="padding-sm">
					<text class="text-lg">您的身份是：</text>
				</view>
				<view class="text-center grid col-3 occupations">
					<view @click="ChooseOccupat(index)" v-for="(occupat,index) in data.occupations" :key="index" class="padding-lr-sm padding-tb-xs">
						<view class="cu-tag radius lg" :class="occupat.title==data.profile.occupation?'bg-'+theme.actcolor:''">
							<text class="text-lg text-cut">{{occupat.title}}</text>
						</view>
					</view>
				</view>
				<block v-if="tags.length>0">
					<view class="padding-sm">
						<text class="text-lg">适合您的标签(多选)：</text>
					</view>
					<view class="text-center grid col-3 occupations">
						<view @click="ChooseTag(tag)" v-for="(tag,index) in tags" :key="index" class="padding-lr-sm padding-tb-xs">
							<view class="cu-tag radius lg" :class="data.profile.tags.indexOf(tag)>=0?'bg-'+theme.actcolor:''">
								<text class="text-lg text-cut">{{tag}}</text>
							</view>
						</view>
					</view>
				</block>
			</block>
			<view class="cu-form-group must" v-if="data.requires.indexOf('interest')>=0">
				<view class="title">兴趣爱好</view>
				<input type="text" placeholder="输入您的兴趣爱好" name="interest" v-model="data.profile.interest"></input>
			</view>
			<block v-else-if="data.requires.indexOf('interestchoose')>=0 && data.interests.length>0">
				<view class="padding-sm">
					<text class="text-lg">您感兴趣的：</text>
				</view>
				<view class="text-center grid col-3 occupations">
					<view @click="ChooseInterest(index)" v-for="(occupat,index) in data.interests" :key="index" class="padding-lr-sm padding-tb-xs">
						<view class="cu-tag radius lg" :class="occupat==data.profile.interest?'bg-'+theme.actcolor:''">
							<text class="text-lg text-cut">{{occupat}}</text>
						</view>
					</view>
				</view>
			</block>
		</view>
		<view class="cu-bar tabbar border shop foot noaf noshadow">
			<view class="btn-group" v-if="frompage=='near'">
				<view class="flex padding-lr-xl">
					<button @click="doSumbit()" class="cu-btn round-only-left padding-lr-xl flex-twice" :class="'bg-'+theme.actcolor">
						<text class="padding-lr-xl">保存</text>
					</button>
					<button @click="doClearExit()" class="cu-btn round-only-right bg-red">
						清除位置并退出
					</button>
				</view>
			</view>
			<view class="btn-group" v-else>
				<button @click="doSumbit()" class="cu-btn round padding-lr-xl" :class="'bg-'+theme.actcolor">
					<text class="padding-lr-xl">完成</text>
				</button>
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
				isBack:true,
				postdata:{},
				theme:core.style,
				frompage:"",
				datetime:"",
				constellations:["白羊座","金牛座","双子座","巨蟹座","狮子座","处女座","天秤座","天蝎座","射手座","摩羯座","水瓶座","双鱼座"],
				zodiacs:["鼠","牛","虎","兔","龙","蛇","马","羊","猴","鸡","狗","猪"],
				affectivestatus:["单身","热恋中","已婚","离异"],
				tags:[],
				data:{
					title:"完善资料",
					requires:[],
					profile:{
						birthyear:0,
						birthmonth:0,
						birthday:0,
						cover:"",
						gender:0,
						height:0,
						nickname:core.userinfo.nickname,
						occupation:"",
						interests:"",
						pics:[],
						tags:[],
						residecity:"",
						weight:""
					},
					nearinfo:{
						id:0,
						gender:0,
						cover:'',
						pics:[],
						status:0
					},
					occupations:[],
					interests:[]
				}
			}
		},
		onLoad(options) {
			if(typeof(options.fp)!='undefined'){
				this.frompage = options.fp;
				if(options.fp=='register'){
					this.isBack = false;
				}
			}
			let self = this;
			this.initData('ranking/profile',{},function(res){
				//console.log(res.requires.indexOf('avatar'))
				if(res.requires.indexOf('birthday')>=0 && res.profile.birthyear>0){
					self.datetime = res.profile.birthyear + "-" + res.profile.birthmonth + "-" + res.profile.birthday;
				}
				if(res.requires.indexOf('gender')>=0 && res.profile.gender==0){
					self.data.profile.gender = 1;
				}
				if(res.requires.indexOf('occupationmulti')>=0 && res.profile.occupation!=''){
					if(res.occupations.length>0){
						for(let i in res.occupations){
							if(res.occupations[i].title==res.profile.occupation){
								self.tags = res.occupations[i].tags;
								break;
							}
						}
					}
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
			doClearExit(){
				let self = this;
				return core.confirm('退出后别人也无法通过附近找到你','清除位置',function(){
					core.post('ranking/nearby',function(res){
						self.showmodal = '';
						if(res.type!='success') return core.report(res);
						core.toast(res.message,'home','success');
					},{clearexit:"true"})
				},{},function(){
					self.showmodal = '';
				});
			},
			doSumbit(){
				let profile = this.data.profile;
				let self = this;
				return core.post('ranking/profile',function(res){
					if(res.type!='success') return core.report(res);
					let redirect = self.frompage=='register' ? 'home' : 'back';
					if(res.redirect!=''){
						redirect = res.redirect;
					}
					core.toast(res.message, redirect, 'success');
				},{saveprofile:"true",profile:profile});
			},
			ChangeCons(e){
				this.data.profile.constellation = this.constellations[e.detail.value];
			},
			ChangeZodiac(e){
				this.data.profile.zodiac = this.zodiacs[e.detail.value];
			},
			ChangeAffect(e){
				this.data.profile.affectivestatus = this.affectivestatus[e.detail.value];
			},
			ChangeDate(e){
				this.datetime = e.detail.value;
				let datetime = this.datetime.split('-');
				this.data.profile.birthyear = parseInt(datetime[0]);
				this.data.profile.birthmonth = parseInt(datetime[1]);
				this.data.profile.birthday = parseInt(datetime[2]);
			},
			ChangeGender(e){
				this.data.profile.gender = e.detail.value;
			},
			ChooseTag(tag){
				let index = this.data.profile.tags.indexOf(tag);
				if(index>=0){
					this.data.profile.tags.splice(index,1);
				}else{
					this.data.profile.tags.push(tag);
				}
			},
			ChooseOccupat(index){
				this.data.profile.occupation = this.data.occupations[index].title;
				this.tags = this.data.occupations[index].tags;
				this.data.profile.tags = [];
			},
			ChooseInterest(index){
				this.data.profile.interest = this.data.interests[index];
			},
			DelImg(){
				let imgindex = this.data.nearinfo.pics.length - 1;
				if(imgindex<0) return false;
				this.data.nearinfo.pics.splice(imgindex,1);
				this.data.profile.pics.splice(imgindex,1);
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
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF; padding-bottom: 100upx;}
	.head .cover image{height: 292upx; max-width: 186upx; display: block;}
	.pic1, .pic2{height: 136upx; overflow: hidden;}
	.nearshow image{width: 100% !important; display: block;}
	.cover{position: relative;}
	.cover .cu-btn{position: absolute; left: calc(73upx - 2em); bottom: 20upx;}
	.uploader{padding: 50upx 0; background: #F7F7F7;}
	.nocover image{width: 78upx; height: 63upx;}
	.viewpic{position: relative;}
	.viewpic .cu-tag{position: absolute; top: 0; right: 0;}
	.flex.cu-form-group{padding: 0;}
	.text-blackm, .cu-form-group > .title{color: #232323;}
	.cu-form-group > .title{width: 5em;}
	.occupations .cu-tag{width: 100%;}
</style>
