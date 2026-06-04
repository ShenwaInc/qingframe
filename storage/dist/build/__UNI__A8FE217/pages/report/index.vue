<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view v-else>
			<view class="bg-gray padding">
				<text class="text-lg">请选择投诉该{{data.typeName}}的原因：</text>
			</view>
			<view class="cu-list menu">
				<view class="cu-item arrow" @click="doReport(index)" v-for="(item, index) in data.reasons" :key="index">
					<view class="content">
						<text class="text-lg">{{item}}</text>
					</view>
				</view>
				<view class="cu-item arrow" @click="postmore=true">
					<view class="content">
						<text class="text-lg">其它</text>
					</view>
				</view>
			</view>
			<view v-if="postmore">
				<view class="bg-gray padding">
					<text class="text-lg">补充说明：</text>
				</view>
				<view class="cu-form-group">
					<textarea maxlength="300" v-model="postdata.description" placeholder="请说明投诉的具体内容(300字内)"></textarea>
				</view>
				<view class="cu-bar bg-white">
					<view class="action">
						图片上传(选填)
					</view>
					<view class="action">
						{{pics.length}}/4
					</view>
				</view>
				<view class="cu-form-group">
					<view class="grid col-4 grid-square flex-sub">
						<view class="bg-img" v-for="(item,index) in pics" :key="index" @tap="ViewImage(index)">
						 <image :src="item" mode="aspectFill"></image>
							<view class="cu-tag bg-red" @tap.stop="DelImg(index)">
								<text class='cuIcon-close'></text>
							</view>
						</view>
						<view class="solids" @tap="ChooseImage" v-if="pics.length<9">
							<text class='cuIcon-cameraadd'></text>
						</view>
					</view>
				</view>
				<view class="padding bg-white" @click="submitReport()">
					<button class="cu-btn block bg-blue lg">
						<text>提交投诉</text>
					</button>
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
				type:"",
				rid:0,
				postdata:{
					reason:"",
					description:"",
					pics:[]
				},
				postmore:false,
				pics:[],
				data:{
					title:"投诉",
					typeName:"",
					reasons:[]
				}
			}
		},
		onLoad(options) {
			if(typeof(options.type)=='undefined') return core.toast('无效的举报类型','back');
			if(typeof(options.id)=='undefined') return core.toast('无效的举报内容','back');
			this.type = options.type;
			this.rid = options.id;
			this.initData('report', {id:this.rid, type:this.type});
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
			doReport(index){
				this.postdata.reason = this.data.reasons[index];
				this.postmore = false;
				this.postdata.description = "";
				this.saveReport();
			},
			submitReport(){
				if(this.postdata.description==""){
					return core.toast('请输入补充说明！');
				}
				this.postdata.reason = "其它";
				this.saveReport();
			},
			saveReport(){
				let self = this;
				let postdata = this.postdata;
				postdata.id = this.rid;
				postdata.type = this.type;
				core.confirm("举报信息一经查实对方将受到严厉惩罚，如举报内容不实，您可能会受到警告或处罚。", "确定要举报该"+this.data.typeName+"?",function(){
					core.post("report/post",function(res){
						console.log("举报信息提交结果：",res);
						core.toast("举报信息已提交","back", "success");
					}, postdata);
				});
			},
			DelImg(index){
				var that = this;
				return core.confirm('删除后不可恢复','确定要删除？',function(){
					that.pics.splice(index,1);
					that.postdata.pics.splice(index,1);
				});
			},
			ViewImage(index){
				uni.previewImage({
					current:index,
					urls:this.pics
				})
			},
			ChooseImage(){
				if(this.pics.length>=4) return false;
				let that = this;
				// #ifdef H5
				if(core.inwechat){
					return core.initjwx(function(wx){
						wx.chooseImage({
							count: 4, // 默认9
							success: function (ret) {
								for (let i in ret.localIds){
									wx.uploadImage({
										localId: ret.localIds[i], // 需要上传的图片的本地ID，由chooseImage接口获得
										isShowProgressTips: 1, // 默认为1，显示进度提示
										success: function (result) {
											core.get('attach/wxmedia',function(res){
												if (res.type=='success'){
													that.pics.push(res.message.url);
													that.postdata.pics.push(res.message.path);
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
					count: 4, //默认9
					sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album'], //从相册选择
					success: (ret) => {
						for(let i in ret.tempFilePaths){
							if(i>8) break;
							core.upload(ret.tempFilePaths[i],function(res){
								that.pics.push(res.url);
								that.postdata.pics.push(res.path);
							});
						}
					}
				});
			}
		}
	}
</script>

<style>
</style>