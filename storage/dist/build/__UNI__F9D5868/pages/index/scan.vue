<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="content">{{data.title}}</block>
			<!-- #ifdef APP-PLUS -->
			<block slot="right">
				<view class="action" @click="doScanAlbum()">
					<text>相册</text>
				</view>
			</block>
			<!-- #endif -->
		</cu-custom>
		<view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	var PlusBarcode = null;
	
	export default {
		data() {
			return {
				data:{
					title:"扫一扫"
				}
			}
		},
		onLoad() {
			this.doScan();
		},
		onShow() {
			
		},
		onUnload() {
			if(typeof(doPlusScanClose)=='function'){
				doPlusScanClose();
			}
			if(PlusBarcode){
				PlusBarcode = null;
			}
		},
		methods:{
			doScan(){
				// #ifdef H5
				if(typeof(doPlusScan)=='function'){
					return doPlusScan("scancontainer",this.scanresult);
				}
				// #endif
				// #ifdef APP-PLUS
				const currentWebview = this.$mp.page.$getAppWebview();
				let self = this, top = this.CustomBar;
				if(!PlusBarcode){
					PlusBarcode = plus.barcode.create('barcode', [plus.barcode.QR, plus.barcode.EAN13], {
						top:top+'px',  
						left:'0px',  
						width: '100vw',  
						height: '100vh',  
						position: 'fixed'  
					});
					PlusBarcode.onmarked = function(type, result){
						self.scanresult({result:result});
					}
					currentWebview.append(PlusBarcode);
				}
				return PlusBarcode.start();
				// #endif
				if(typeof(plus)=='undefined') return core.toast('请在APP内使用扫一扫','back');
			},
			scanresult(res){
				core.scanResult(res.result, 1);
			}
			// #ifdef APP-PLUS
			,
			doScanAlbum(){
				let self = this;
				uni.chooseImage({
					count:1,
					sourceType:['album'],
					success:function(res){
						plus.barcode.scan(res.tempFilePaths[0], function(type, result){
							self.scanresult({result:result});
						},
						function(e){
							core.toast("扫码失败，请重试");
						});
					},
					fail:function(e){
						core.toast("选择图片失败");
					}
				});
			}
			// #endif
		}
	}
</script>

<style>
</style>
