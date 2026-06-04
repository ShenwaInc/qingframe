<template>
	<view :class="'page-text-'+textSize">
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
			<block slot="right">
				<view class="action" @click="editState=!editState">
					<text>{{editState?'完成':'管理'}}</text>
				</view>
			</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<view :class="editState?'editing':'normal'" v-else>
			<view class="text-empty" v-if="data.downList.length==0 && data.fileList.length==0">
				<text>暂无下载记录</text>
			</view>
			<view class="cu-list menu margin-bottom" v-else>
				<view class="cu-item" @click="downCheck(index)" v-for="(item, index) in data.downList" :key="index">
					<view class="content padding-tb-sm">
						<view>
							<text class="margin-right-xs" :class="item.checked?'cuIcon-squarecheck text-'+theme.actcolor:'cuIcon-square text-gray'" v-if="editState"></text>
							<text class="margin-right-xs" :class="item.icon" v-else></text>
							<text class="text-lg">{{item.name}}</text>
						</view>
						<view class="flex margin-xs">
							<view class="cu-progress round xs">
								<view class="bg-blue" :style="[{ width:item.progress+'%'}]"></view>
							</view>
							<text class="margin-left-sm text-sm">{{item.progress}}%</text>
						</view>
					</view>
					<view class="action" v-if="!editState">
						<view class="cu-btn sm bg-green shadow radius" @tap.stop="downloadAbort(index)" v-if="item.status==0">取消</view>
						<view class="cu-btn sm bg-orange shadow radius" @tap.stop="downloadFile(index)" v-else>重试</view>
					</view>
				</view>
				<view class="cu-item" @click="downCheck(index,true)" v-for="(item, index) in data.fileList" :key="index">
					<view class="content padding-tb-sm">
						<view>
							<text class="margin-right-xs" :class="item.checked?'cuIcon-squarecheck text-'+theme.actcolor:'cuIcon-square text-gray'" v-if="editState"></text>
							<text class="margin-right-xs" :class="item.icon" v-else></text>
							<text class="text-lg">{{item.name}}</text></view>
						<view class="text-sm text-gray">{{item.createAt}}<text class="margin-left">{{item.sizeName}}</text></view>
					</view>
					<view class="action" v-if="!editState">
						<view class="cu-btn sm bg-blue shadow radius" @tap.stop="openFile(index)">打开</view>
					</view>
				</view>
			</view>
			<view class="cu-bar bg-white tabbar border shop foot" v-if="editState">
				<view class="action" @click="downCheckAll()">
					<view :class="editCheck?'cuIcon-squarecheckfill text-'+theme.actcolor:'cuIcon-square text-gray'"></view>
					全选
				</view>
				<view class="bg-orange submit" @click="downClear()">删除记录</view>
				<view class="bg-red submit" @click="downClear(true)">删除源文件</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex'
	import core from "@/core.js"
	// #ifdef APP-PLUS
	import permision from "@/TrtcCloud/permission.js"
	// #endif
	var downloadIos = [];
	
	export default {
		computed: mapState(['textSize']),
		data() {
			return {
				data:{
					title:"文件管理",
					fileList:[],
					downList:[]
				},
				editCheck:false,
				editState:false,
				loaded:false,
				theme:core.style
			}
		},
		onLoad(options) {
			//获取下载历史
			this.downList();
			let filename = typeof(options.name)=='undefined' ? '' : decodeURIComponent(options.name);
			if(typeof(options.url)!='undefined' && options.url!=''){
				this.saveFile(options.url, filename)
			}
		},
		onShow() {
		},
		onUnload() {
			downloadIos = [];
			this.downCache();
		},
		methods:{
			saveFile(url, fileName=""){
				// #ifdef APP-PLUS
				if(plus.os.name == "iOS"){
					let recorder = permision.judgeIosPermission("photoLibrary");
					if(!recorder){
						return core.confirm("是否要现在去给APP授权？", "暂无文件权限", function(){
							uni.openAppAuthorizeSetting({});
						}, {confirmText:"去授权", cancelText:"以后再说"});
					}
				}else{
					let result = this.requestAndroidPermission('android.permission.WRITE_EXTERNAL_STORAGE');
					if(result==-1){
						return core.toast("未授予文件存储权限");
					}
				}
				// #endif
				let downUrl = decodeURIComponent(url);
				// #ifdef H5
				if(typeof(doPlusSavefile)=='function'){
					doPlusSavefile(downUrl);
				}
				// #endif
				let fileInfo = {
					progress:0,
					status:0,
					size:0,
					checked:false,
					src:downUrl,
					name:fileName
				};
				if(fileInfo.name==""){
					let names = downUrl.split('/');
					fileInfo.name = names[names.length - 1];
				}
				fileInfo.icon = this.downIcon(fileInfo.name);
				if(fileInfo.name.length>20){
					fileInfo.name = fileInfo.name.slice(0,10) + '...' + fileInfo.name.slice(-10);
				}
				let downLens = this.data.downList.push(fileInfo);
				this.downloadFile(downLens-1);
			},
			downList(){
				let self = this;
				core.cacheread('DownloadFileStorage', function(res){
					self.data.downList = res.data.downs;
					self.data.fileList = res.data.files;
					self.loaded = true;
				}, function(){
					self.loaded = true;
				});
			},
			downloadAbort(downIndex){
				if(typeof(downloadIos[downIndex])=='undefined' || downloadIos[downIndex]==null) return true;
				let downloadTask = downloadIos[downIndex];
				downloadTask.abort();
				downloadIos[downIndex] = null;
				this.data.downList[downIndex].status = 3;	//取消下载
			},
			downloadFile(downIndex){
				let downUrl = this.data.downList[downIndex].src;
				if(typeof(downloadIos[downIndex])!='undefined' && downloadIos[downIndex]!=null){
					if(this.data.downList[downIndex].status==0){
						return true;
					}
				}
				this.data.downList[downIndex].status = 0;
				this.data.downList[downIndex].progress = 0;
				let downItem = this.data.downList[downIndex];
				let self = this;
				let downloadTask = uni.downloadFile({
					url:downUrl,
					success(res) {
						downloadIos[downIndex] = null;
						uni.saveFile({
							tempFilePath:res.tempFilePath,
							success:function(resp) {
								let fileItem = {
									filePath:resp.savedFilePath,
									name:downItem.name,
									src:downItem.src,
									checked:false,
									size:downItem.size,
									sizeName:self.downSize(downItem.size),
									createAt:"刚刚"
								}
								if(fileItem.name==""){
									let fileName = fileItem.filePath.replace(/\\/g, "/");
									let names = fileName.split('/');
									fileItem.name = names[names.length - 1];
								}
								fileItem.icon = self.downIcon(fileItem.name);
								if(fileItem.name.length>20){
									fileItem.name = fileItem.name.slice(0,10) + '...' + fileItem.name.slice(-10);
								}
								uni.getSavedFileInfo({
									filePath:resp.savedFilePath,
									success(fileInfo) {
										fileItem.size = fileInfo.size;
										fileItem.sizeName = self.downSize(fileInfo.size);
										fileItem.createAt = core.gmTime(fileInfo.createTime);
										self.data.fileList.unshift(fileItem);
										self.data.downList.splice(downIndex, 1);
										self.downCache();
									},
									fail(e){
										self.data.fileList.unshift(fileItem);
										self.data.downList.splice(downIndex, 1);
										self.downCache();
									}
								});
							},
							fail:function(e){
								downItem.status = 2;
								self.data.downList.splice(downIndex, 1, downItem);
								self.downCache();
								core.toast("文件保存失败");
							}
						});
					},
					fail(e) {
						downItem.status = 3;
						self.data.downList.splice(downIndex, 1, downItem);
						core.toast("下载失败");
					}
				});
				downloadTask.onProgressUpdate(function(res){
					if(downItem.progress==0){
						downItem.size = res.totalBytesExpectedToWrite;
					}
					downItem.progress = res.progress;
					if(res.progress==100){
						downItem.status = 1;
					}
					self.data.downList.splice(downIndex, 1, downItem);
					self.downCache();
				});
				self.downCache();
				downloadIos[downIndex] = downloadTask;
			},
			downCache(){
				core.cacheset('DownloadFileStorage', {
					downs:this.data.downList,
					files:this.data.fileList
				});
			},
			downCheck(index, isFile=false){
				if(!this.editState) return false;
				if(isFile){
					let fileList = this.data.fileList;
					fileList[index].checked = fileList[index].checked ? false : true;
					this.data.fileList = fileList;
				}else{
					this.data.downList[index].checked = !this.data.downList[index].checked;
				}
			},
			downCheckAll(){
				this.editCheck = !this.editCheck;
				for(let i in this.data.downList){
					this.data.downList[i].checked = this.editCheck;
				}
				for(let j in this.data.fileList){
					this.data.fileList[j].checked = this.editCheck;
				}
			},
			downClear(rmFile=false){
				let text = rmFile ? '确认要删除下载记录和本地源文件吗？' : '确认要删除所选记录吗？';
				let self = this;
				core.confirm(text, "删除下载记录", function(){
					for(let i in self.data.fileList){
						if(self.data.fileList[i].checked){
							if(rmFile){
								uni.removeSavedFile({
									filePath:self.data.fileList[i].filePath
								});
							}
							self.data.fileList.splice(i, 1);
						}
					}
					for(let j in self.data.downList){
						if(self.data.downList[j].checked){
							self.data.downList.splice(j, 1);
						}
					}
					core.toast("文件清理成功！", "", "success");
					self.downCache();
					downloadIos = [];
				});
			},
			downIcon(fileName){
				let Exts = {
					sound:['mp3','amr','aac', 'm4a', 'wav','ogg'],
					video:['mp4','avi','mv','mov','rmvb','3gp','vod','mov'],
					docs:['txt','pdf','xls','doc','xlsx','docx','ppt','pptx','csv'],
					image:['png','jpg','git','webp','bmp','jpeg']
				};
				let fileExt = (fileName.match(/\.([\w]+)$/)[1]).toLowerCase();
				let icon = 'cuIcon-questionfill text-gray';
				if(Exts.docs.indexOf(fileExt)>=0){
					icon = 'cuIcon-formfill text-cyan';
				}else if(Exts.image.indexOf(fileExt)>=0){
					icon = "cuIcon-picfill text-green";
				}else if(Exts.video.indexOf(fileExt)>=0){
					icon = "cuIcon-videofill text-blue"
				}else if(Exts.sound.indexOf(fileExt)>=0){
					icon = "cuIcon-musicfill text-mauve"
				}
				return icon;
			},
			downSize(size){
				let sizeName = size + 'b';
				if(size>1048576){
					sizeName = (size/1048576).toFixed(1) + 'Mb';
				}else if(size>1024){
					sizeName = (size/1024).toFixed(1) + 'Kb';
				}
				return sizeName;
			},
			openFile(index){
				let filePath = this.data.fileList[index].filePath;
				// #ifdef APP-PLUS
				let fileExt = (filePath.match(/\.([\w]+)$/)[1]).toLowerCase();
				if(core.device=='android' && fileExt=='apk'){
					return plus.runtime.install(filePath);
				}
				// #endif
				return uni.openDocument({
					filePath:filePath
				});
			},
			async requestAndroidPermission(permisionID) {
			    var result = await permision.requestAndroidPermission(permisionID);
			    return result;
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.normal .cu-list .cu-item .content{max-width: calc(100% - 120upx); overflow: hidden;}
	.cu-list .cu-item .content [class*="cuIcon-"]{font-size: 40upx;}
	.cu-progress + .text-sm{margin-top: -8upx;}
</style>