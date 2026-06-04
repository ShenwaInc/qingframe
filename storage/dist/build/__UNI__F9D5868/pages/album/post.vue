<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<form>
			<view class="cu-list menu">
				<view class="cu-item">
					<view class="content">
						<image :src="data.member.avatar" class="png round" mode="aspectFit"></image>
						<text class="text-grey">{{data.member.nickname}}</text>
					</view>
				</view>
			</view>
			<view class="cu-form-group">
				<textarea maxlength="-1" auto-focus="true" v-model="postdata.content" placeholder="说点什么吧~"></textarea>
			</view>
			<view class="cu-bar solid-top bg-white padding-left padding-right justify-between">
				<view class="justify-start flex text-xxxl">
					<view :class="pics.length<9?'text-'+theme.actcolor:'text-gray'" @click="ChooseImage()">
						<text class="cuIcon-pic"></text>
					</view>
					<view class="margin-left" :class="postdata.vid==0?'text-'+theme.actcolor:'text-gray'" @click="ChooseVideo()">
						<text class="cuIcon-record"></text>
					</view>
					<view class="margin-left text-xxl" :class="'text-'+theme.actcolor" @click="ChoosePosition()" v-if="data.location">
						<text class="cuIcon-location"></text>
					</view>
					<view class="margin-left text-xxl" @click="showmodal='editsummary'" :class="'text-'+theme.actcolor">
						<text :class="postdata.summary==''?'cuIcon-comment':'cuIcon-commentfill'"></text>
					</view>
				</view>
				<view @click="doPostSquare()" :class="insquare?'text-'+theme.actcolor:''" v-if="data.square">
					<text class="margin-right-xs" :class="insquare?'cuIcon-squarecheckfill':'cuIcon-square'"></text>
					<text>同步到广场</text>
				</view>
			</view>
			<view class="cu-form-group" v-if="pics.length>0 || postdata.vid!=0">
				<view class="grid col-4 grid-square flex-sub">
					<view class="solids bg-img" v-if="postdata.vid!=0">
						<text class="cuIcon-recordfill"></text>
						<view class="cu-tag bg-red" @tap.stop="rmVideo()">
							<text class="cuIcon-close"></text>
						</view>
					</view>
					<view @click="doPreviewImage(pic)" class="bg-img" v-for="(pic,index) in pics" :key="index">
						<image :src="pic" mode="aspectFill"></image>
						<view class="cu-tag bg-red" @tap.stop="DelImg(index)">
							<text class="cuIcon-close"></text>
						</view>
					</view>
					<view class="solids" @click="ChooseImage()" v-if="pics.length<9">
						<text class="cuIcon-cameraadd"></text>
					</view>
				</view>
			</view>
			<view @click="ClearPos()" class="padding-sm padding-top-xs bg-white text-cut text-green text-lg" v-if="position.status==1">
				<text class="cuIcon-locationfill"></text>
				<text>{{position.name}}({{position.address}})</text>
				<text class="cuIcon-roundclose text-red margin-left-sm"></text>
			</view>
			<view class="grid col-3 bg-white padding-sm solid-top" v-if="data.tags.length>0">
				<view class="padding-xs" @click="doTaptag(tag)" v-for="(tag, index) in data.tags" :key="index">
					<view class="padding-sm text-center light" :class="tags.indexOf(tag)>-1?'bg-'+theme.actcolor:'bg-grey'">
						<view class="text-cut">{{tag}}</view>
					</view>
				</view>
			</view>
		</form>
		<view class="padding richtext" v-if="data.agreement!=''">
			<rich-text :nodes="data.agreement"></rich-text>
		</view>
		<view class="cu-bar tabbar foot noshadow">
			<view class="action">
				<view class="flex flex-direction margin-bottom-xl padding-lr-xl">
					<button class="cu-btn shadow-blur lg round margin-lr-xl" :class="'bg-'+theme.actcolor" @click="submitPost()">发布</button>
				</view>
			</view>
		</view>
		<view class="cu-modal" :class="showmodal=='editsummary'?'show':''">
			<view class="cu-modal-bg" @click="showmodal=''"></view>
			<view class="cu-dialog bg-white text-left">
				<view class="cu-bar solid-bottom bg-white justify-end">
					<view class="content">内容摘要</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding-sm">
					<textarea class="summary" maxlength="100" v-model="postdata.summary" :placeholder="summaryplaceholder"></textarea>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-green" @tap="ClearSummary()">取消</button>
						<button class="cu-btn bg-green margin-left" @tap="showmodal=''">确定</button>
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
				data:{
					title:"发表动态",
					member:{
						uid:0,
						nickname:"",
						avatar:""
					},
					agreement:"",
					location:false,
					square:false,
					squareLimit:0,
					summarylen:0,
					tags:[]
				},
				showmodal:"",
				summaryplaceholder:"请输入动态内容摘要",
				forward:'album/index?friend=1',
				insquare:true,
				theme:core.style,
				postdata:{
					content:"",
					tags:'',
					vid:0,
					summary:"",
					insquare:1
				},
				position:{
					status:0
				},
				tags:[],
				pics:[],
				thumbs:[]
			}
		},
		onLoad(options) {
			if(typeof(options.forward)!='undefined' && options.forward!='') this.forward = options.forward;
			let self = this;
			this.initData("album/post",{},function(res){
				if(res.summarylen>0){
					self.summaryplaceholder += "," + res.summarylen + "字以内";
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
					if(typeof(res.squareLimit)=='undefined'){
						res.squareLimit = 1;
					}
					that.data = res;
					if(res.squareLimit<=0){
						that.insquare = false;
					}
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
			ClearSummary(){
				this.postdata.summary = "";
				this.showmodal = "";
			},
			ClearPos(){
				this.position = {status:0}
			},
			ChoosePosition(){
				let self = this;
				uni.chooseLocation({
					success:function(res) {
						self.position = {
							name:res.name,
							address:res.address,
							latitude:res.latitude,
							longitude:res.longitude,
							status:1
						}
					},
					fail:function(e) {
						console.log('获取定位失败',e);
						core.toast('位置信息获取失败');
					}
				})
			},
			doPreviewImage(pic){
				uni.previewImage({
					current:pic,
					urls:this.pics
				})
			},
			doTaptag(tag=''){
				if(tag=='') return false;
				let hastag = this.tags.indexOf(tag);
				if(hastag==-1){
					if(this.tags.length>=3) return core.toast('最多选择三个标签');
					this.tags.push(tag);
					if(this.tags.length>=3) this.showmodal = '';
				}else{
					this.tags.splice(hastag,1);
				}
			},
			doPostSquare(){
				if(!this.insquare && this.data.squareLimit<=0){
					return core.confirm("您可同步到广场的额度不足，升级VIP等级可获取更多额度", "额度不足", function(){
						core.navito('member/vip');
					}, {confirmText:"升级VIP"});
				}
				this.insquare = !this.insquare;
			},
			ChooseImage(){
				if(this.pics.length>=9) return core.toast('最多上传9张图片');
				let that = this;
				// #ifdef H5
				if(core.inwechat){
					return core.initjwx(function(wx){
						wx.chooseImage({
							count: 9, // 默认9
							success: function (ret) {
								for (let i in ret.localIds){
									wx.uploadImage({
										localId: ret.localIds[i], // 需要上传的图片的本地ID，由chooseImage接口获得
										isShowProgressTips: 1, // 默认为1，显示进度提示
										success: function (result) {
											core.get('attach/wxmedia',function(res){
												if (res.type=='success'){
													that.pics.push(res.message.url);
													that.thumbs.push(res.message.path);
												}else {
													core.toast(res.message,res.redirect,res.type);
												}
											},{media_id:result.serverId});
										},fail:function (res) {
											util.toast("上传失败，请重试",'','error');
											console.log(res);
										}
									});
								}
							},fail:function (res) {
								util.toast("操作失败，请重试",'','error');
								console.log(res);
							}
						});
					});
				}
				// #endif
				uni.chooseImage({
					count: 9, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (ret) => {
						for(let i in ret.tempFilePaths){
							if(i>8) break;
							core.upload(ret.tempFilePaths[i],function(res){
								that.pics.push(res.url);
								that.thumbs.push(res.path);
							});
						}
					}
				});
			},
			DelImg(index){
				var that = this;
				return core.confirm('删除后不可恢复','确定要删除？',function(){
					that.pics.splice(index,1);
					that.thumbs.splice(index,1);
				});
			},
			rmVideo(){
				let that = this;
				return core.confirm('删除后不可恢复','确定要删除？',function(){
					that.postdata.vid = 0;
				});
			},
			ChooseVideo(){
				if(this.postdata.vid>0) return core.toast('视频已上传');
				let that = this;
				uni.chooseVideo({
					count: 1,
					sourceType: ['camera', 'album'],
					compressed:false,
					success: function (res) {
						core.upload(res.tempFilePath,function(ret){
							that.postdata.vid = ret.vid;
						},'video');
					}
				});
			},
			submitPost(){
				if(this.postdata.content==='' && this.thumbs.length==0 && this.postdata.vid==0) return core.toast('先说点什么吧~');
				let ForWard = this.forward;
				if(this.tags.length>0){
					this.postdata.tags = this.tags.join('|');
				}
				this.postdata.insquare = this.insquare ? 1 : 0;
				core.post('album/post',function(res){
					if(res.type!='success') return core.report(res);
					ForWard = res.redirect=='' ? ForWard : res.redirect;
					core.toast(res.message,ForWard,'success');
				},{savealbum:"true",data:this.postdata,position:this.position,thumbs:this.thumbs});
			}
		}
	}
</script>

<style>
	page{padding-bottom: 100upx;}
	.summary{width: auto; height: 180upx;}
</style>
