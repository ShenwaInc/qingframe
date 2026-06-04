<template>
	<view>
		<cu-custom :isBack="true">
			<block slot="backText">返回</block>
			<block slot="content">{{data.title}}</block>
		</cu-custom>
		<view class="cu-bar bg-white solid-bottom" v-if="data.newapplys.length>0">
			<view class="action">
				<text class="cuIcon-title text-orange"></text>
				新成员申请
			</view>
		</view>
		<view class="cu-list menu-avatar" v-if="data.newapplys.length>0">
			<view class="cu-item arrow" v-if="item.status==0" @click="doAudit(item.uid,index)" v-for="(item, index) in data.newapplys" :key="index">
				<view class="cu-avatar radius lg" :style="'background-image:url('+item.avatar+');'"></view>
				<view class="content">
					<view><view class="text-cut">{{item.nickname}}</view></view>
					<view class="text-gray text-sm flex"> <view class="text-cut">{{item.content}}</view></view>
				</view>
				<view class="action">
					<view class="text-grey text-xs">{{item.datetime}}</view>
				</view>
			</view>
		</view>
		<view class="cu-bar bg-white solid-bottom" :class="data.newapplys.length>0?'margin-top':''">
			<view class="action">
				<text class="cuIcon-title text-orange"></text>
				群成员
			</view>
			<view class="action" v-if="data.group.ismanager">
				<text class="text-gray">长按管理成员</text>
			</view>
		</view>
		<userpicker :Users="data.members" Mode="grid" @onLongtap="doEdit" @onResult="doTouch" :AddUrl="recommend"></userpicker>
		<view class="cu-modal bottom-modal" :class="showmodal=='editactions'?'show':''" v-if="data.group.ismanager">
			<view class="cu-dialog">
				<view class="padding">
					<view class="flex flex-direction">
						<button @click="showmodal='kickout'" class="cu-btn bg-red lg">移出群组</button>
						<button @click="showForbiddens()" class="cu-btn bg-orange lg margin-top">设置禁言</button>
						<block v-if="data.group.isfounder">
							<button class="cu-btn lg margin-top bg-yellow" @click="doSetManager(0)" v-if="edititem.ismanager">取消管理员</button>
							<button class="cu-btn lg margin-top" @click="doSetManager(1)" :class="'bg-'+actcolor" v-else>设为管理员</button>
						</block>
						<button @click="doEditCancel()" class="cu-btn bg-grey lg margin-top">取  消</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal bottom-modal" :class="showmodal=='forbiddenactions'?'show':''" v-if="data.group.ismanager">
			<view class="cu-dialog">
				<view class="cu-bar bg-white">
					<view class="action text-red" @tap="doEditCancel()">取消</view>
					<view class="action" :class="'text-'+actcolor" @tap="doForbidden()">确定</view>
				</view>
				<view class="grid col-4 padding-sm">
					<view v-for="(item,index) in forbiddens.items" class="padding-xs" :key="index">
						<button class="cu-btn orange lg block" :class="forbiddens.index==index?'bg-'+actcolor:'line-'+actcolor" @tap="forbiddens.index=index">
							{{item}}
						</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal" v-if="data.group.ismanager" :class="showmodal=='refusedapply'?'show':''">
			<view class="cu-dialog">
				<view class="cu-bar bg-white justify-end">
					<view class="content">拒绝入群申请</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view>
					<view class="cu-form-group text-left">
						<input type="text" placeholder="请输入拒绝的原因" v-model="auditdata.remark" name="remark"></input>
					</view>
				</view>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-gray" @tap="showmodal=''">取消</button>
						<button class="cu-btn margin-left" :class="'bg-'+actcolor" @click="doSubmitrf()">拒绝</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal bottom-modal" :class="showmodal=='auditdialog'?'show':''">
			<view class="cu-modal-bg" @click="showmodal=''"></view>
			<view class="cu-dialog">
				<view class="padding">
					<view class="flex flex-direction">
						<button @click="doAgree()" class="cu-btn bg-green lg">同  意</button>
						<button @click="doRefused()" class="cu-btn bg-red lg margin-top">拒  绝</button>
						<button @click="showmodal=''" class="cu-btn bg-grey lg margin-top">取  消</button>
					</view>
				</view>
			</view>
		</view>
		<view class="cu-modal" :class="showmodal=='kickout'?'show':''">
			<view class="cu-modal-bg" @click="showmodal=''"></view>
			<view class="cu-dialog bg-white">
				<view class="cu-bar justify-end">
					<view class="content">移出{{data.groupname}}</view>
					<view class="action" @tap="showmodal=''">
						<text class="cuIcon-close text-red"></text>
					</view>
				</view>
				<view class="padding text-xl text-left">
					<text>确定将该成员移出{{data.groupname}}?</text>
				</view>
				<checkbox-group class="block" @change="CheckboxChange">
					<view class="cu-form-group">
						<view class="title">撤回该成员发言</view>
						<checkbox @ :class="edititem.clearchat?'checked':''" :checked="edititem.clearchat?true:false" value="clearchat"></checkbox>
					</view>
					<view class="cu-form-group">
						<view class="title">不再接受该申请</view>
						<checkbox :class="edititem.reject?'checked':''" :checked="edititem.reject?true:false" value="reject"></checkbox>
					</view>
				</checkbox-group>
				<view class="cu-bar bg-white justify-end">
					<view class="action">
						<button class="cu-btn line-green text-green" @tap="showmodal=''">取消</button>
						<button class="cu-btn bg-green margin-left" @tap="doRemove()">确定</button>
					</view>
				</view>
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
				gid:0,
				actcolor:core.style.actcolor,
				actindex:0,
				showmodal:'',
				recommend:"",
				edititem:{
					uid:0,
					user:{uid:0},
					ismanager:false,
					clearchat:0,
					reject:0
				},
				forbiddens:{
					index:0,
					items:['取消','1小时','6小时','1天','7天','15天','30天','永久']
				},
				auditdata:{
					gid:0,
					uid:0,
					remark:'',
					status:1,
					auditsubmit:"true"
				},
				data:{
					title:"群成员",
					groupname:'群组',
					newapplys:[],
					members:[],
					group:{
						id:0,
						uid:0,
						ismanager:false,
						isfounder:false
					}
				}
			}
		},
		onLoad(options) {
			if(typeof(options.gid)=='undefined' || !options.gid) return core.toast('无效的群组信息','back');
			this.gid = parseInt(options.gid);
			this.recommend = core.page('friend/recommend',{gid:this.gid});
		},
		onShow() {
			this.initData('group/members',{gid:this.gid,'fp':"picker"});
		},
		onPullDownRefresh() {
			this.initData('group/members',{gid:this.gid,'fp':"picker"});
			uni.stopPullDownRefresh();
		},
		methods:{
			initData(route, data={}, callback=false){
				var that = this;
				core.get(route,function(res){
					if(typeof(res.message)!='undefined' && typeof(res.type)!='undefined'){
						return core.report(res, true);
					}
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
			showForbiddens(){
				this.forbiddens.index = 0;
				if(this.edititem.user.notalking==1){
					this.forbiddens.index = 7;
				}
				this.showmodal = 'forbiddenactions';
			},
			doEditCancel(){
				this.edititem = {
					uid:0,
					ismanager:false
				};
				this.showmodal = '';
			},
			doTouch(item){
				return core.navito('member/index',{uid:item.uid,fg:item.cid});
			},
			doEdit(user){
				let uid = user.uid;
				if(!this.data.group.ismanager) return core.navito('member/index',{uid:uid,fg:this.gid});
				if(uid==this.data.group.uid) return core.toast('您无权操作该成员');
				if(user.ismanager>0){
					if(!this.data.group.isfounder) return core.toast('您无权操作该成员');
					this.edititem.ismanager = true;
				}
				this.edititem.user = user;
				this.edititem.uid = uid;
				this.showmodal = 'editactions';
			},
			doSetManager(ismanager){
				if(!this.data.group.isfounder) return core.toast('您无权操作该成员');
				if(ismanager==this.edititem.ismanager){
					core.toast('设置成功！','','success');
					return this.doEditCancel();
				}
				let self = this;
				return core.post('group/members',function(res){
					if(res.type!='success') return core.report(res);
					self.doEditCancel();
					self.initData('group/members',{gid:self.gid,'fp':"picker"});
					core.toast(res.message,'','success');
				},{op:'setmanager',ismanager:ismanager,uid:this.edititem.uid,gid:this.data.group.id});
			},
			doForbidden(){
				if(!this.data.group.ismanager) return core.toast('您无权操作该成员');
				if(!this.data.group.isfounder && this.edititem.ismanager) return core.toast('您无权操作该成员');
				let self = this;
				let forbidden = this.forbiddens.index;
				return core.post('group/members',function(res){
					if(res.type=='success'){
						self.doEditCancel();
						self.initData('group/members',{gid:self.gid,'fp':"picker"});
					}
					core.toast(res.message,res.redirect,res.type);
				},{op:'setforbidden',forbidden:forbidden,uid:this.edititem.uid,gid:this.data.group.id});
			},
			doAudit(uid,actindex=0){
				this.auditdata.uid = uid;
				this.actindex = actindex;
				this.auditdata.gid = this.data.group.id;
				this.showmodal = 'auditdialog';
			},
			doRefused(){
				this.auditdata.remark = this.data.newapplys[this.actindex].remark;
				this.showmodal = 'refusedapply';
			},
			doSubmitrf(){
				this.auditdata.status = 2;
				this.showmodal = '';
				let that = this;
				core.post("group/members",function(res){
					let redirect = res.type=='success' ? '' : res.redirect;
					if(res.type=='success'){
						that.data.newapplys.splice(that.actindex,1);
					}
					core.toast(res.message,redirect,res.type);
				},that.auditdata);
			},
			CheckboxChange(e){
				this.edititem.clearchat = this.edititem.reject = 0;
				if(e.detail.value.length==0) return true;
				for(let i in e.detail.value){
					let key = e.detail.value[i];
					this.edititem[key] = 1;
				}
			},
			doRemove(){
				let postdata = {
					uid:this.edititem.uid,
					auditsubmit:"true",
					gid:this.data.group.id,
					remove:1,
					clearchat:this.edititem.clearchat,
					reject:this.edititem.reject
				}
				let that = this;
				core.post("group/members",function(res){
					let redirect = res.type=='success' ? '' : res.redirect;
					if(res.type=='success'){
						that.doEditCancel();
						that.initData('group/members',{gid:that.gid,'fp':"picker"});
					}
					core.toast(res.message,redirect,res.type);
				},postdata);
			},
			doAgree(){
				this.auditdata.status = 1;
				this.auditdata.remark = '';
				this.showmodal = '';
				let that = this;
				return core.confirm('是否确定同意该用户进群？','同意进群',function(){
					core.post("group/members",function(res){
						let redirect = res.type=='success' ? '' : res.redirect;
						if(res.type=='success'){
							let applyinfo = that.data.newapplys[that.actindex];
							applyinfo.status = 1;
							that.data.members.push(applyinfo);
							that.data.newapplys.splice(that.actindex,1);
						}
						core.toast(res.message,redirect,res.type);
					},that.auditdata);
				});
			}
		}
	}
</script>

<style>
	page{background: #FFFFFF;}
</style>
