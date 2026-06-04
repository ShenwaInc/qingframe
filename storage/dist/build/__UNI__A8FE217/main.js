import Vue from 'vue'
import App from './App'

import swasocket from "@/swasocket.js"
import swaLoad from './components/util/swaload.vue'
import cuCustom from './colorui/components/cu-custom.vue'
Vue.component('swaload',swaLoad);
Vue.component('cu-custom',cuCustom);

Vue.config.productionTip = false;

import store from './store'
Vue.prototype.$store = store;
Vue.prototype.ColorList = {
	"red":"#e54d42",
	"orange":"#f37b1d",
	"yellow":"#fbbd08",
	"olive":"#8dc63f",
	"green":"#39b54a",
	"cyan":"#1cbbb4",
	"blue":"#0081ff",
	"purple":"#6739b6",
	"mauve":"#9c26b0",
	"pink":"#e03997",
	"brown":"#a5673f",
	"grey":"#8799a3",
	"gray":"#f0f0f0",
	"black":"#333333",
	"dazzledark":"#2a2833",
	"white":"#ffffff",
	"limegreen":"#05c160",
}

App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()

//#ifdef APP-PLUS || APP-PLUS-NVUE
let MainApp = plus.android.runtimeMainActivity();
//为了防止快速点按返回键导致程序退出重写quit方法改为隐藏至后台  
let doPlusHideApp = function() {
    MainApp.moveTaskToBack(false);
	return false;
};
//重写toast方法如果内容为 ‘再次返回退出应用’ 就隐藏应用，其他正常toast 
plus.nativeUI.toast = (function(title,styles={}) {
    if (title=='再按一次退出应用' || title=='再按一次返回键退出') {
        doPlusHideApp();
		return false;
    } else {
		styles.title = title;
        uni.showToast(styles);
    }
});
//#endif