<template>
	<view>
		<cu-custom bgColor="bg-red" :isBack="true">
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<swaload :hasTopbar="true" v-if="!loaded"></swaload>
		<form v-else>
			<view class="padding">
				<view class="cu-form-group radius">
					<view class="title">红包金额</view>
					<input class="text-right" placeholder="0.00" v-model="postdata.amount" name="amount"></input>
					<text>元</text>
				</view>
				<view class="cu-form-group radius margin-top" v-if="ctype=='group' && postdata.touid==0">
					<view class="title">红包个数</view>
					<input :disabled="discounts" class="text-right" placeholder="填写红包数量" v-model="postdata.counts" name="counts"></input>
					<text>个</text>
				</view>
				<view class="text-gray margin-top-xs" v-if="ctype=='group'" @click="showUserPicker()">
					<text v-if="postdata.touid==0" class="margin-right-sm">共{{data.group.members}}位群成员</text>
					<text class="text-blue">发给{{postdata.touid>0?nickname:''}}{{postdata.touid==0?'指定群成员':'，点此修改'}}</text>
				</view>
				<radio-group class="block" @change="RadioChange" v-if="ctype=='group' && data.rankpack && postdata.touid==0">
					<view class="cu-form-group margin-top">
						<view class="title">红包类型</view>
						<view>
							<radio class='red radio' :class="postdata.type==0?'checked':''" :checked="postdata.type==0?true:false" value="0"></radio>
							<text @click="postdata.type=0" class="margin-left-xs" :class="postdata.type==0?'text-red':''">普通红包</text>
							<radio class='red radio margin-left' :class="postdata.type==1?'checked':''" :checked="postdata.type==1?true:false" value="1"></radio>
							<text @click="postdata.type=1" class="margin-left-xs" :class="postdata.type==1?'text-red':''">拼手气红包</text>
						</view>
					</view>
				</radio-group>
				<view class="cu-form-group radius margin-top" v-if="data.shownum==0">
					<view class="title">红包备注</view>
					<input placeholder="填写备注说明" v-model="postdata.remark" name="remark"></input>
					<text v-if="postdata.remark!=''" class="cuIcon-roundclose text-red" @click="postdata.remark=''"></text>
				</view>
				<view class="margin-top flex flex-direction">
					<button class="cu-btn bg-red lg" @click="doPostRedpacket()">发红包</button>
				</view>
			</view>
		</form>
		<view class="cu-modal bottom-modal" :class="showmodal=='userpicker'?'show':''" v-if="ctype=='group'">
			<view class="cu-modal-bg" @tap.stop="showmodal=''"></view>
			<view class="cu-dialog" @tap.stop="">
				<view class="cu-bar bg-white">
					<view class="action text-blue" @tap="showmodal=''">取消</view>
					<view class="action text-red" v-if="postdata.touid>0" @tap="doCancel()">清除</view>
				</view>
				<userpicker :Users="groupmembers" pStyle="userscorller" @onResult="doCheckUser"></userpicker>
			</view>
		</view>
	</view>
</template>

<script>
	import core from "@/core.js"
	import userpicker from "@/components/util/userpicker.vue"
	
	export default {
		components:{userpicker},
		data() {
			return {
				chatid:0,
				ctype:'normal',
				discounts:false,
				groupmembers:[],
				nickname:"",
				showmodal:"",
				theme:core.style,
				postdata:{
					redpacketpost:"true",
					amount:"",
					counts:0,
					touid:0,
					type:1,
					remark:"恭喜发财，大吉大利"
				},
				loaded:false,
				data:{
					title:"",
					member:{
						uid:0
					},
					group:{
						id:0,
						members:0
					},
					rankpack:false,
					maxcounts:0,
					curcounts:0,
					shownum:0
				}
			}
		},
		onLoad(options) {
			if(typeof(options.cid)=='undefined' || !options.cid) return core.toast('找不到该会话','back');
			this.chatid = parseInt(options.cid);
			if(typeof(options.type)!='undefined' && options.type!='') this.ctype = options.type;
			let postdata = {};
			if(this.ctype=='group'){
				postdata.gid = this.chatid;
			}else{
				postdata.uid = this.chatid;
			}
			let self = this;
			this.initData('redpacket/post',postdata,function(res){
				if(res.curcounts>0){
					self.postdata.counts = res.curcounts;
				}
				if(res.curcounts==res.maxcounts && res.maxcounts>0){
					self.discounts = true;
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
					that.data = res;
					that.loaded = true;
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
			doCancel(){
				this.postdata.touid = 0;
				this.nickname = "";
				this.showmodal = "";
			},
			showUserPicker(){
				if(this.groupmembers.length==0){
					let self = this;
					return core.get('group/members',function(res){
						if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
							return core.report(res);
						}
						self.groupmembers = res.members;
						self.showmodal = "userpicker";
					},{gid:this.chatid, fp:'picker'},'json',true);
				}
				this.showmodal = "userpicker";
			},
			doCheckUser(userinfo){
				this.nickname = userinfo.nickname;
				this.postdata.touid = userinfo.uid;
				this.postdata.remark = "仅限" + userinfo.nickname + "领取";
				this.showmodal = "";
			},
			RadioChange(e){
				this.postdata.type = parseInt(e.detail.value);
			},
			doPostRedpacket(){
				if(!this.postdata.amount) return core.toast('请输入正确的红包金额');
				let amount = parseFloat(this.postdata.amount);
				if(amount<=0) return core.toast('请输入正确的红包金额');
				let postdata = this.postdata;
				if(this.data.maxcounts>0 && postdata.counts>this.data.maxcounts){
					this.postdata.counts = this.data.maxcounts;
					return core.toast('红包数量过多');
				}
				if(this.postdata.remark==""){
					postdata.remark = "恭喜发财，大吉大利";
				}
				postdata.amount = amount;
				if(this.ctype=='group'){
					postdata.gid = this.data.group.id;
				}else{
					postdata.uid = this.data.member.uid;
				}
				let self = this;
				core.post("redpacket/post",function(res){
					let message = res.type=='success' ? res.message.message : res.message;
					let redirect = res.redirect;
					if(res.type=='success'){
						redirect = core.page('credit/pay',{tid:res.message.tid});
					}
					core.toast(message,redirect,res.type);
				},postdata);
			}
		}
	}
</script>

<style>
	.cu-form-group .title {width: 5em; text-align-last:justify;}
</style>
