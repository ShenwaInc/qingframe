<template>
	<view class="bt-container">
		<view class="mainContent">
			<image :src="imageSrc" mode="aspectFit" class="image"></image>
			<view class="cropper" v-if="imageSrc && imageInfo" @touchstart="onTouchStart"
				@touchmove.stop.prevent="onTouchMove" :style="{
          width: cropperPosition.width + 'px',
          height: cropperPosition.height + 'px',
          left: cropperPosition.left - 1 + 'px',
          top: cropperPosition.top - 1 + 'px',
          transform: cropperPosition.transform,
		  transition: animate ? '0.2s' : 'none'
        }">
				<template v-if="showGrid">
					<view class="line row row1"></view>
					<view class="line row row2"></view>
					<view class="line col col1"></view>
					<view class="line col col2"></view>
				</template>
				<view @touchmove.stop.prevent="onHandleResize(-1, -1, $event)" class=" controler controler-left-top">
				</view>
				<view @touchmove.stop.prevent="onHandleResize(1, -1, $event)" class=" controler controler-right-top">
				</view>
				<view @touchmove.stop.prevent="onHandleResize(-1, 1, $event)" class=" controler controler-left-bottom">
				</view>
				<view @touchmove.stop.prevent="onHandleResize(1, 1, $event)" class=" controler controler-right-bottom">
				</view>
			</view>
		</view>
		<view class="slot">
			<slot />
		</view>
		<!-- #ifdef H5 -->
		<canvas canvas-id="bt-canvas" class="bt-canvas" :style="{
			width:dSize.width+'px',
			height:dSize.height+'px'
		}"></canvas>
		<!-- #endif -->
		<!-- #ifdef MP-WEIXIN -->
		<canvas type="2d" class="bt-canvas" :style="{
			width:dSize.width+'px',
			height:dSize.height+'px'
		}"></canvas>
		<!-- #endif -->
	</view>
</template>

<script>
	var X = 0,
		Y = 0,
		dX = 0,
		dY = 0,
		dW = 0,
		dH = 0,
		cW = 0,
		cH = 0,
		timer;
	/**
	 * better-cropper 图片裁切插件
	 */
	export default {
		name: "bt-cropper",
		props: {
			// 图片路径，支持网络路径和本地路径
			imageSrc: {
				type: String,
				default: ""
			},
			// 输出图片的格式，默认jpg
			fileType: {
				type: String,
				default: "jpg"
			},
			// 生成的图片的宽度
			dWidth: {
				type: Number,
				default: 1000
			},
			// 裁切比例，0表示自由
			ratio: {
				type: Number,
				default: 0
			},
			// 是否展示网格
			showGrid: {
				type: Boolean,
				default: false
			},
			// 图片质量，0-1 越大质量越好
			quality: {
				type: Number,
				default: 1
			},
		},
		data() {
			return {
				imageInfo: "",
				containerRect: "",
				startClientX: 0,
				startClientY: 0,
				startOffsetX: 0,
				startOffsetY: 0,
				offsetX: 0,
				offsetY: 0,
				startChangeWidth: 0,
				startChanaeHeight: 0,
				changeWidth: 0,
				changeHeight: 0,
				windowWidth: 375,
				startCenter: [0, 0],
				forceChangeWidth: 0,
				forceChangeHeight: 0,
				animate: false
			};
		},
		watch: {
			imageSrc: {
				handler(src) {
					this.imageInfo = ""
					this.getImageInfo()
				},
				immediate: true
			},
			ratio: {
				handler(ratio) {
					clearTimeout(timer)
					this.animate = true
					this.resetRatio()
					timer = setTimeout(() => {
						this.animate = false
					}, 200)
				},
				immediate: false
			},
			imagePosition() {
				this.resetRatio()
			}
		},
		computed: {
			imagePosition() {
				if (this.imageInfo && this.containerRect) {
					let imageAsp = this.imageInfo.width / this.imageInfo.height;
					let containerAsp = this.containerRect.width / this.containerRect.height;
					if (imageAsp < containerAsp) {
						// 廋图
						return {
							left: (this.containerRect.width -
									this.containerRect.height * imageAsp) /
								2,
							top: 0,
							width: this.containerRect.height * imageAsp,
							height: this.containerRect.height,
						};
					} else {
						// 胖图
						return {
							left: 0,
							top: (this.containerRect.height -
									this.containerRect.width / imageAsp) /
								2,
							width: this.containerRect.width,
							height: this.containerRect.width / imageAsp,
						};
					}
				} else {
					return {
						left: 0,
						top: 0,
						width: 0,
						height: 0,
					};
				}
			},
			cropperPosition() {
				let imagePosition = this.imagePosition;
				const position = {
					width: imagePosition.width + this.changeWidth + this.forceChangeWidth || 0,
					height: imagePosition.height + this.changeHeight + this.forceChangeHeight || 0,
					left: imagePosition.left || 0,
					top: imagePosition.top || 0,
					transform: "translate(" + this.offsetX + "px, " + this.offsetY + "px)" || "none",
				};
				return position
			},
			// 展示比例
			showRatio() {
				return this.imagePosition.width / (this.imageInfo.width || 1)
			},
			// 生成图片的大小
			dSize() {
				return {
					width: this.dWidth,
					height: this.dWidth * (this.cropperPosition.height / this.cropperPosition.width)
				}
			}
		},
		mounted() {
			this.windowWidth = uni.getSystemInfoSync().windowWidth
			this.getContainer();
		},
		methods: {
			getContainer() {
				uni.createSelectorQuery().in(this).select(".mainContent").boundingClientRect((res => {
					this.containerRect = res
				})).exec()
			},
			getImageInfo() {
				uni.getImageInfo({
					src: this.imageSrc,
					success: (res) => {
						this.imageInfo = res;
					},
					fail: (err) => {
						console.error(err)
						this.imageInfo = ""
						uni.showToast({
							title: "下载图片失败",
							icon: "none"
						})
					}
				});
			},
			resetCropperPosition() {
				return this.resetRatio()
			},
			resetRatio() {
				this.$nextTick(() => {
					if (this.imageInfo && this.ratio !== 0) {
						let imageRatio = this.imageInfo.width / this.imageInfo.height
						this.changeWidth = 0
						this.changeHeight = 0
						if (this.ratio != 0) {
							if (this.ratio > imageRatio || Math.abs(imageRatio - this.ratio) < 0.1 && imageRatio <
								1) {
								this.forceChangeHeight = -(this.imagePosition.height - this.imagePosition.width /
									this.ratio)
								this.offsetY = -this.forceChangeHeight / 2
								this.offsetX = 0
							} else {
								this.forceChangeWidth = -(this.imagePosition.width - this.imagePosition.height *
									this.ratio)
								this.offsetX = -this.forceChangeWidth / 2
								this.offsetY = 0
							}
						} else {
							this.forceChangeWidth = 0
							this.forceChangeHeight = 0
						}
					}
				})
			},
			onTouchStart(e) {
				this.startClientX = e.touches[0].clientX;
				this.startClientY = e.touches[0].clientY;
				this.startOffsetX = this.offsetX;
				this.startOffsetY = this.offsetY;
				this.startChangeWidth = this.changeWidth;
				this.startChanaeHeight = this.changeHeight;
				this.startCenter = [this.cropperPosition.width / 2 + this.offsetX, this.cropperPosition.height / 2 + this
					.offsetY
				]
			},
			onTouchMove(e) {
				X = this.startOffsetX + (e.touches[0].clientX - this.startClientX);
				Y = this.startOffsetY + (e.touches[0].clientY - this.startClientY);

				let cropperPosition = this.cropperPosition;
				if (X >= 0) {
					if (X + cropperPosition.width < this.imagePosition.width) {
						this.offsetX = X;
					} else {
						this.offsetX = this.imagePosition.width - cropperPosition.width;
					}
				} else {
					this.offsetX = 0;
				}
				if (Y >= 0) {
					if (Y + cropperPosition.height < this.imagePosition.height) {
						this.offsetY = Y;
					} else {
						this.offsetY = this.imagePosition.height - cropperPosition.height;
					}
				} else {
					this.offsetY = 0;
				}
			},
			onHandleResize(pX, pY, $event) {
				dX = $event.touches[0].clientX - this.startClientX;
				dY = $event.touches[0].clientY - this.startClientY;
				if (pX == 1) {
					dW = this.startChangeWidth + dX;
					cW = this.imagePosition.width + this.forceChangeWidth + dW;
					if (cW > 0) {
						if (cW + this.offsetX <= this.imagePosition.width) {
							if (this.ratio !== 0) {
								dH = dW / this.ratio
								cH = this.imagePosition.height + dH + this.forceChangeHeight
								if (cH > 0) {
									if (cH <= (this.imagePosition.height - this.offsetY)) {
										this.changeWidth = dW;
										this.changeHeight = dH;
									} else {
										// 高度触底
										this.changeHeight = -this.offsetY - this.forceChangeHeight
										this.changeWidth = (-this.offsetY - this.forceChangeHeight) * this.ratio;
									}
								}
							} else {
								this.changeWidth = dW;
							}
						} else {
							if (cH <= this.imagePosition.height) {
								// 碰到右边缘
								if (this.ratio == 0) {
									this.changeWidth = -(this.offsetX + this.forceChangeWidth);
								} else {
									if (this.imagePosition.height - this.offsetY - cW / this.ratio > 0) {
										this.changeWidth = -(this.offsetX + this.forceChangeWidth);
										this.changeHeight = this.changeWidth / this.ratio
									}
								}
							}
						}
					}
				}
				if (pX == -1) {
					dW = this.startChangeWidth - dX;
					cW = this.imagePosition.width + this.forceChangeWidth + dW;
					if (cW > 0) {
						if (this.startOffsetX + dX > 0) {
							if (this.ratio !== 0) {
								cH = cW / this.ratio
								if (this.imagePosition.height - cH - this.offsetY > 0) {
									this.changeHeight = dW / this.ratio
									this.changeWidth = dW;
									this.offsetX = this.startOffsetX + dX;
								} else {
									// 触底
									this.changeHeight = -this.offsetY - this.forceChangeHeight
									this.changeWidth = (-this.offsetY - this.forceChangeHeight) * this.ratio;
									this.offsetX = this.startOffsetX - (this.changeWidth - this.startChangeWidth);
								}
							} else {
								this.offsetX = this.startOffsetX + dX;
								this.changeWidth = dW;
							}
						} else {
							// 超出左边界
							if (this.ratio !== 0) {
								cH = cW / this.ratio
								if (this.imagePosition.height - cH - this.offsetY > 0) {
									this.offsetX = 0
									this.changeWidth = (this.startChangeWidth + this.startOffsetX);
									this.changeHeight = (this.startChangeWidth + this.startOffsetX) / this.ratio
								}
							} else {
								this.offsetX = 0
								this.changeWidth = (this.startChangeWidth + this.startOffsetX);
							}
						}
					}
				}
				if (this.ratio !== 0) {
					return
				}
				if (pY == 1) {
					dH = this.startChanaeHeight + dY;
					cH = this.imagePosition.height + this.forceChangeHeight + dH;
					if (cH > 0) {
						if (cH + this.offsetY < this.imagePosition.height) {
							this.changeHeight = dH;
						} else {
							this.changeHeight = -this.offsetY;
						}
					}
				}
				if (pY == -1) {
					cH = this.imagePosition.height + this.startChanaeHeight - dY
					if (cH > 0) {
						if (this.startOffsetY + dY > 0) {
							this.offsetY = this.startOffsetY + dY;
							this.changeHeight = this.startChanaeHeight - dY;
						}
					}
				}
				// }
			},
			// 开始裁切
			async crop() {
				let canvas, image, ctx,err,res;
				// #ifdef MP-WEIXIN
				canvas = await new Promise((resolve) => {
					uni
						.createSelectorQuery().in(this)
						.select(".bt-canvas")
						.node((res) => {
							resolve(res.node);
						})
						.exec();
				});
				image = canvas.createImage();
				image.src = this.imageSrc;
				await new Promise((resolve) => (image.onload = resolve));
				canvas.width = this.dSize.width;
				canvas.height = this.dSize.height;
				ctx = canvas.getContext("2d");
				// #endif
				// #ifdef H5
				image = this.imageSrc
				ctx = uni.createCanvasContext("bt-canvas", this)
				// #endif
				let {
					left,
					top,
					width,
					height
				} = this.cropperPosition;
				ctx.drawImage(
					image,
					this.offsetX / this.showRatio,
					this.offsetY / this.showRatio,
					this.cropperPosition.width / this.showRatio,
					this.cropperPosition.height / this.showRatio,
					0,
					0,
					this.dSize.width,
					this.dSize.height
				);
				// #ifdef H5
				await new Promise((resolve) => ctx.draw(true, resolve));
				// #endif
				// 在vue3里面，只能写成这种回调形式，否则报错
				[err,res] = await new Promise(resolve=>{
					uni.canvasToTempFilePath({
						// #ifdef MP-WEIXIN
						canvas,
						// #endif
						// #ifdef H5
						canvasId: "bt-canvas",
						// #endif
						fileType: this.fileType,
						destWidth: this.dSize.width,
						destHeight: this.dSize.height,
						quality: this.quality,
						success(res) {
							resolve([null,res])
						},
						fail(err) {
							resolve([err,null])
						}
					});
				}) 
				return [err, res]
			},
		},
	};
</script>

<style lang="scss" scoped>
	.bt-container {
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		height: 100%;
		background-color: #0e1319;
		padding-top: 30rpx;
		position: relative;
		overflow: hidden;

		.bt-canvas {
			position: absolute;
			left: 100%;
			top: 0;
			width: 300px;
			height: 300px;
		}

		.mainContent {
			flex: 1;
			display: flex;
			justify-content: center;
			align-items: center;
			margin: 0 30rpx;
			position: relative;

			.image {
				width: 100%;
				height: 100%;
			}

			.cropper {
				position: absolute;
				border: 1px solid #eee;
				box-sizing: content-box;
				transform-origin: center center;
				outline: 999px solid rgba(0, 0, 0, 0.5);
				will-change: transform;
				// will-change: transfrom;

				.line {
					position: absolute;
					// background-color: #eee;
				}

				.row {
					width: 100%;
					height: 0px;
					left: 0;
					border-top: 1px dashed #007AFF;
				}

				.col {
					height: 100%;
					width: 0px;
					border-left: 1px dashed #007AFF;
				}

				.row1 {
					top: 33%;
				}

				.row2 {
					top: 66%;
				}

				.col1 {
					left: 33%;
				}

				.col2 {
					left: 66%;
				}

				.controler {
					position: absolute;
					width: 40rpx;
					height: 40rpx;
					background-color: #E4E7ED;
					border-radius: 99px;
					z-index: 99;
					box-shadow: 0 0 10rpx #333;
				}

				.controler-left-top {
					left: -20rpx;
					top: -20rpx;
				}

				.controler-right-top {
					right: -20rpx;
					top: -20rpx;
				}

				.controler-left-bottom {
					left: -20rpx;
					bottom: -20rpx;
				}

				.controler-right-bottom {
					right: -20rpx;
					bottom: -20rpx;
				}
			}
		}

		.slot {
			position: relative;
			padding-top: 20rpx;
		}

	}
</style>
