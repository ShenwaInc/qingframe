

## bt-cropper 图片裁切
> **组件名：bt-cropper**


图片裁切组件，在页面中裁切图片，输出裁切后的图片


> **注意事项**
> 为了避免错误使用，给大家带来不好的开发体验，请在使用组件前仔细阅读下面的注意事项，可以帮你避免一些错误。
> - 组件需要依赖 `sass` 插件 ，请自行手动安装
> - `bt-cropper` 并不能完全阻止页面滚动，可在使用 `bt-cropper` 的页面手动去做一些处理，禁止页面滚动，请在`pages.json`中使用他的页面添加`"disableScroll": true`
> - 容器使用百分比布局，所以包裹层需要手动指定高度，推荐设定为`100vh`
> - 目前只适配了H5和微信小程序，其他平台暂未适配，如有需要可以留言或者自行修改代码
> - 如使用过程中有任何问题，或者您有一些好的建议，欢迎联系作者QQ:1097122362


### 安装方式

本组件符合[easycom](https://uniapp.dcloud.io/collocation/pages?id=easycom)规范，`HBuilderX 2.5.5`起，只需将本组件导入项目，在页面`template`中即可直接使用，无需在页面中`import`和注册`components`。

### 基本用法 

**示例**

```html
<template>
	<view class="container">
		<bt-cropper ref="cropper" :imageSrc="imageSrc">
			<!-- 你想插入的内容 -->
			<button @click="crop">裁切</button>
		</bt-cropper>
	</view>
</template>
<style>
	.container{
		height:100vh;
	}
</style>
```


```javascript
export default {
   methods:{
      crop(){
        // 通过组件定义的ref调用cropper方法，返回一个promise对象
        this.$refs.cropper.crop().then(([err,res])=>{
			if(!err){
				console.log(res)
			}else{
				console.err(err)
			}
		})
      }
   }
}

```

### 限定裁切比例

bt-cropper，指定ratio即可设置裁切框的宽高比，如果你想让用户自由缩放，将ratio设置为0即可

**示例**

```html
<bt-cropper ref="cropper" :ratio="16/9" :imageSrc="imageSrc">
	<!-- 你想插入的内容 -->
	<button @click="crop">裁切</button>
</bt-cropper>
```

## API

### Popup Props 

|属性名|类型|默认值|说明|
|:-:|:-:|:-:|:-:|
|ratio|number|0|裁切图像的宽高比，0表示自由比例|
|dWidth|number|1000|生成的图片的宽度,单位：px|
|imageSrc|String|''|原图的路径，支持本地路径和网络路径|
|fileType|String|'jpg'|目标文件的类型，只支持 'jpg' 或 'png'。默认为 'jpg'|
|quality|Number|1|图片的质量，取值范围为 (0, 1]，不在范围内时当作1.0处理|
|showGrid|Boolean|false|是否显示中心网格线，默认不显示|

### Popup Methods

|方法名称|说明|参数|
|:-:|:-:|:-:|
|crop|裁剪图片|crop() ，开始绘制并开始裁剪图片，返回Promise对象|
|resetCropperPosition|重置裁剪框的位置和大小|-|

## 帮助
在使用中如遇到无法解决的问题，请提 [Issues](https://gitee.com/xiaojiang1996/better-uni-cropper/issues) 或者加我QQ/微信:1097122362。
