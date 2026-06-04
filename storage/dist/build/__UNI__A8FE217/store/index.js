import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		hasLogin: false,
		userId:0,
		userName: "",
		newMsg:0,
		userSign:"",
		authToken:"",
		Chatid:0,
		Chattype:"normal",
		textSize:"sm"
	},
	mutations: {
		readmsg(state,isclear=false){
			if(isclear){
				state.newMsg = 0;
			}else{
				state.newMsg -= 1;
			}
			if(typeof(plus)!='undefined'){
				plus.runtime.setBadgeNumber(state.newMsg);
			}
		},
		setnewmsg(state,news=0){
			state.newMsg = news;
			if(typeof(plus)!='undefined'){
				plus.runtime.setBadgeNumber(news);
			}
		},
		chatinit(state,dialog){
			state.Chatid = dialog.cid;
			state.Chattype = dialog.type;
		},
		getnewmsg(state){
			state.newMsg += 1;
			if(typeof(plus)!='undefined'){
				plus.runtime.setBadgeNumber(state.newMsg);
			}
		},
		login(state, data) {
			state.userName = data.nickname;
			state.userId = data.uid;
			state.userSign = data.usersign;
			state.authToken = data.token;
			state.hasLogin = true;
		},
		logout(state) {
			state.userName = "";
			state.authToken = "";
			state.userId = 0;
			state.userSign = "";
			state.hasLogin = false;
		},
		clientTextSize(state, Size){
			state.textSize = Size;
		}
	}
})

export default store
