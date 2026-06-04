<template>
	<view>
		<view class="cu-custom" :style="'height:'+Custom_Bar+'px'" @tap="onNavTap">
			<view class="cu-bar fixed" :style="style" :class="[bgImage!=''?'none-bg text-white bg-img':'',(bgColor==''?Bg_Color:bgColor),Barborder]">
				<view class="action" @tap.stop="BackPage" v-if="isBack">
					<text class="cuIcon-back"></text>
				</view>
				<slot name="left"></slot>
				<view class="content" :style="'top:'+Status_Bar+'px'">
					<slot name="content" v-if="!inwechat || platform!='h5' || ShowForce"></slot>
				</view>
				<slot name="right"></slot>
			</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	
	export default {
		data() {
			return {
				Status_Bar: this.StatusBar,
				Custom_Bar: this.CustomBar,
				Bg_Color:core.style.navbg,
				inwechat:core.inwechat,
				platform:core.platform
			};
		},
		name: 'cu-custom',
		computed: {
			style() {
				var StatusBar= this.Status_Bar;
				var CustomBar= this.Custom_Bar;
				var bgImage = this.bgImage;
				var style = `height:${CustomBar}px;padding-top:${StatusBar}px;`;
				if (this.bgImage) {
					style = `${style}background-image:url(${bgImage});`;
				}
				return style
			}
		},
		props: {
			bgColor: {
				type: String,
				default: ''
			},
			isBack: {
				type: [Boolean, String],
				default: false
			},
			bgImage: {
				type: String,
				default: ''
			},
			Barborder:{
				type: String,
				default: ''
			},
			ShowForce:{
				type:[Boolean, String],
				default: false
			}
		},
		methods: {
			BackPage(e) {
				core.back();
			},
			onNavTap(e){
				this.$emit('navtap',e);
			}
		}
	}
</script>

<style>

</style>
