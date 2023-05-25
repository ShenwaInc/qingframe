var Loadajax = false;
if(typeof BaseUrl == 'undefined'){
    var BaseUrl = '/';
}
if(typeof Basetoken == 'undefined'){
    var Basetoken = '';
    let CsrfElem = $('meta[name="csrf-token"]');
    if(CsrfElem.length>0){
        Basetoken = CsrfElem.attr('content');
    }
}
(function(w) {
    w.Core = {
        logout: function () {
            let self = this;
            this.confirm('确定要退出当前账号？', function () {
                self.post('auth.logout',function (res){
                    self.report(res);
                });
            })
        },
        cacheclear:function (){
            this.get('console/util/cache',function (res){
                Core.report(res);
            },{do:"clear"},'json',true)
        },
        get: function (u, c, d, t, l) {
            return this.request(u, 'GET', d, t, c, l);
        },
        json: function (u, c, d, l) {
            return this.request(u, 'GET', d, 'json', c, l);
        },
        post: function (u, c, d, t, l) {
            return this.request(u, 'POST', d, t, c, l);
        },
        confirm: function (msg, success = false, cancle = false, option = {icon: 3, title: '温馨提示'}) {
            layer.confirm(msg, option, function (index) {
                if (typeof (success) == 'function') {
                    success();
                }
                layer.close(index);
            }, function () {
                if (typeof (cancle) == 'function') {
                    cancle();
                }
            });
        },
        request: function (u, m, d, t, c, l, f=null) {
            var url = this.url(u);
            var method = m ? m : 'GET';
            var data = d ? d : {};
            var datatype = t ? t : 'json';
            data.inajax = 1;
            if (l && !this.loading) {
                this.loading = layer.load(1, {shade: 0.3});
            }
            var callback = typeof (c) == 'function' ? c : false;
            var hreq = this;
            Loadajax = true;
            let AjaxObj = {
                url: url,
                type: method,
                dataType: datatype,
                data: data,
                success: function (res) {
                    //console.log('HttpRequestDone.');
                    if (hreq.loading !== 0) {
                        layer.close(hreq.loading);
                        hreq.loading = 0;
                    }
                    if (typeof (callback) == 'function') {
                        return callback(res);
                    }
                    hreq.report(res);
                },
                fail: function (e) {
                    console.log('请求失败', e);
                    if (hreq.loading !== 0) {
                        layer.close(hreq.loading);
                        hreq.loading = 0;
                    }
                    if (Loadajax) Loadajax = false;
                    if(typeof(f)=='function'){
                        f(e);
                    }else{
                        layer.msg('操作失败', {icon: 2});
                    }
                }
            }
            if (method === 'POST') {
                AjaxObj.data.submit = 1;
                if (typeof (AjaxObj.data._token) == 'undefined') {
                    AjaxObj.data._token = Basetoken;
                }
            }
            if (Basetoken !== '') {
                AjaxObj.headers = {
                    'X-CSRF-TOKEN': Basetoken
                }
            }
            return jQuery.ajax(AjaxObj);
        },
        report: function (res, timeOut=1200) {
            if (typeof (res) != 'object' && this.isJsonString(res)) {
                res = jQuery.parseJSON(res);
            }
            if (this.debug) {
                console.log(res);
            }
            if (typeof (res) != 'object') return false;
            let act = '',redirect = '';
            if (typeof (res.url) != 'undefined') {
                act = typeof (res.act) != 'undefined' ? res.act : '';
                redirect = res.url;
            }
            if (typeof (res.message) != 'undefined' && typeof (res.type) != 'undefined') {
                act = res.type;
                redirect = res.redirect;
                let icon = res.type === 'success' ? 1 : 2;
                layer.msg(res.message, {icon: icon});
            }
            if (redirect !== '') {
                let direction = function () {
                    w.location.href = redirect;
                }
                if (act==='redirect') return direction();
                setTimeout(direction, timeOut);
            }
        },
        isJsonString: function (str){
            try {
                if(typeof(jQuery.parseJSON(str)) == "object") {
                    return true;
                }
            } catch(e) {
            }
            return false;
        },
        url: function (route) {
            if (route.indexOf('http') === 0 || route.indexOf('index.php') > -1) return route;
            if (route === '' || !route) return BaseUrl;
            route = route.replace(new RegExp("^\/"), "");
            return BaseUrl + route.replaceAll('.', '/');
        },
        loading: 0,
        debug: false,
        StoragePicker(Elem, multi=false, CallBack=false) {
            let WindowId = 'storagepicker' + Wrandom(6);
            let PickerUrl = this.url("server/storage/picker");
            let PickerTitle = $(Elem).data("title")
            if(Elem.hasAttribute("data-url")){
                PickerUrl = $(Elem).attr("data-url");
            }
            if(Elem.hasAttribute("multiple")){
                multi = true;
            }
            let self = this;
            let PickerItem = function (PItem){
                let attachid = PItem.data('aid');
                if(PItem.hasClass("checked")){
                    let index = self.storagedata.storage.aids.indexOf(attachid);
                    PItem.removeClass("checked");
                    if (index>=0){
                        self.storagedata.storage.aids.splice(index, 1);
                        self.storagedata.storage.items.splice(index, 1);
                    }
                }else{
                    PItem.addClass("checked");
                    let item = {
                        aid:attachid,
                        path:PItem.data('path'),
                        url:PItem.data("url")
                    }
                    self.storagedata.storage.items.push(item);
                    self.storagedata.storage.aids.push(attachid);
                }
            }
            let PickerEvent = function (selector){
                let Ajaxwindow = $(selector);
                Ajaxwindow.find(".category").on("click","a[gitem]",function (){
                    let url = $(this).attr('href');
                    self.get(url, function (Html){
                        if(self.isJsonString(Html)){
                            var obj = jQuery.parseJSON(Html);
                            return self.report(obj);
                        }
                        Ajaxwindow.html(Html);
                        PickerEvent(selector);
                    },{inajax:1,ajaxhash:WindowId},'html')
                    return false;
                });
                Ajaxwindow.find('.pagination').on("click","a",function (){
                    let url = $(this).attr('href');
                    if (typeof(url)=="undefined" || url==="#" || url.indexOf("javascript:")===0){
                        url = "";
                    }
                    if (url==="" && typeof($(this).attr('page'))!='undefined'){
                        let page = $(this).attr('page');
                        url = PickerUrl;
                        url += (PickerUrl.indexOf("?")===-1 ? "?page=" : "&page=") + page;
                    }
                    if(url!==""){
                        self.get(url, function (Html){
                            if(self.isJsonString(Html)){
                                var obj = jQuery.parseJSON(Html);
                                return self.report(obj);
                            }
                            Ajaxwindow.html(Html);
                            PickerEvent(selector);
                        },{inajax:1,ajaxhash:WindowId},'html');
                    }
                    return false;
                });
                Ajaxwindow.find('.attachments').on("click",".attach-item",function (){
                    PickerItem($(this));
                    return false;
                });
                let UploadBtn = Ajaxwindow.find(".attach-uploader");
                layupload.render({
                    elem: UploadBtn.get()[0],
                    url:UploadBtn.data('url'),
                    done:function (res){
                        if(res.type!=='success'){
                            UploadBtn.removeClass("uploading").addClass('uploaderr');
                            return self.report(res);
                        }
                        let attach = res.data;
                        let Html = '<div class="layui-col-md2 layui-xs-4 attach-item" data-aid="'+attach.id+'" data-path="'+attach.attachment+'" data-url="'+attach.cover+'">' +
                            '<div class="attach-thumb" style="background-image: url('+attach.cover+')"></div>' +
                            '<div title="'+attach.filename+'" class="attach-name text-center">'+attach.filename+'</div>' +
                            '<div class="action attach-check">' +
                            '    <span class="layui-icon layui-icon-circle"></span>\n' +
                            '</div></div>';
                        if (Ajaxwindow.find('.attachments').find('.attach-item').length>=18){
                            Ajaxwindow.find('.attachments').find('.attach-item:last').remove();
                        }
                        Ajaxwindow.find('.attachments').prepend(Html);
                        UploadBtn.removeClass("uploading");
                    },
                    before:function (){
                        layui.element.progress('uploadprogress', '0%');
                        UploadBtn.addClass("uploading").removeClass('uploaderr');
                    },
                    data:{
                        token:Basetoken,
                        inputname:"file",
                        frompage:"picker"
                    },
                    headers:{
                        "X-CSRF-TOKEN":Basetoken
                    },
                    accept:UploadBtn.data('accept'),
                    error:function (e){
                        UploadBtn.removeClass("uploading");
                        layer.msg("上传失败，请重试", {icon:2});
                    },
                    progress:function (n, elem, res, index){
                        layui.element.progress('uploadprogress', n+'%');
                    }
                });
            }
            this.get(PickerUrl, function (Html){
                if(self.isJsonString(Html)){
                    var obj = jQuery.parseJSON(Html);
                    return self.report(obj);
                }
                let PickerHeight = (Math.min(810, w.innerHeight) - 30);
                let params = {
                    id:WindowId,
                    type:1,
                    content:Html,
                    title:PickerTitle,
                    shade:0.3,
                    area:["1080px", PickerHeight+"px"],
                    shadeClose:true,
                    skin:'fui-layer filepicker',
                    success:function(layero, index){
                        if(self.storagedata==null){
                            self.storagedata = {};
                        }
                        if (typeof(self.storagedata.storage)=='undefined'){
                            self.storagedata.storage = {items:[],aids:[]};
                        }
                        PickerEvent("#"+WindowId);
                    },
                    btnAlign:"c",
                    btn:["确定","取消"],
                    yes:function (index){
                        if (self.storagedata.storage.items.length>0){
                            if (multi){
                                if(typeof(CallBack)=='function'){
                                    return CallBack(self.storagedata.storage.items, index);
                                }
                                let inputname = $(Elem).next().val();
                                let items = self.storagedata.storage.items;
                                for(let i in items){
                                    let multiItem = '<div class="multi-item">\n' +
                                        '        <img src="'+items[i].url+'" class="img-responsive img-thumbnail">\n' +
                                        '        <input type="hidden" name="'+inputname+'[]" value="'+items[i].path+'" >\n' +
                                        '        <em class="close" title="删除这张图片" onclick="Core.StorageRmImg(this, true)">×</em>\n' +
                                        '    </div>'
                                    $(Elem).parent().next().append(multiItem);
                                }
                            }else {
                                let item = self.storagedata.storage.items[0];
                                if(typeof(CallBack)=='function'){
                                    return CallBack(item, index);
                                }
                                $(Elem).prev().find('input.layui-input').val(item.path);
                                $(Elem).parent().next().find('img.img-responsive').attr("src", item.url).removeClass('nopic');
                            }
                        }
                        layer.close(layer.index);
                    },
                    end:function (){
                        self.storagedata.storage = {items:[],aids:[]};
                    }
                }
                layer.open(params);
            },{inajax:1,ajaxhash:WindowId},'html',true);
            if (PickerTitle!=="图片选择器") return false;
        },
        MemberPicker(){
            let self = this;
            $('.member-picker').each(function (index,element) {
                let Elem = $(element);
                let PickerId = Elem.attr("data-pid");
                let PickerUrl = self.url("server/ucenter/picker");
                if(element.hasAttribute("data-url")){
                    PickerUrl = Elem.attr("data-url");
                }
                let InputVal = Elem.find("input.layui-input").val();
                let Curuid = parseInt($("#"+PickerId).val());
                if(Curuid>0){
                    Elem.find(".layui-form-select").addClass("selected");
                }
                let ClearUid = function (){
                    $("#"+PickerId).val(0);
                    Elem.find("input.layui-input").val("");
                    Elem.find(".layui-form-select").removeClass("selected").removeClass("layui-form-selected");
                    let avatar = $("#"+PickerId+"-avatar");
                    avatar.attr("src", avatar.data('val'));
                }
                Elem.on("click", ".layui-icon-close", ClearUid);
                Elem.on("click", ".layui-anim dd", function (e){
                    let Curdd = $(this);
                    Curuid = parseInt(Curdd.data('uid'));
                    if (Curuid===0){
                        return ClearUid();
                    }
                    InputVal = Curdd.data('nick');
                    $("#"+PickerId).val(Curuid);
                    Elem.find("input.layui-input").val(InputVal);
                    $("#"+PickerId+"-avatar").attr("src", Curdd.data('avatar'));
                    Elem.find(".layui-form-select").addClass("selected").removeClass("layui-form-selected");
                });
                Elem.on("input", ".layui-input", function (e){
                    let Input = $(this);
                    let nickname = Input.val();
                    Elem.find(".layui-form-select").addClass("layui-form-selected");
                    if (nickname==='' || nickname===InputVal) return false;
                    InputVal = nickname;
                    self.post(PickerUrl, function (Html){
                        if(self.isJsonString(Html)){
                            var obj = jQuery.parseJSON(Html);
                            return self.report(obj);
                        }
                        Html = '<dd data-uid="0"'+(Curuid===0 ? ' class="layui-select-tips layui-this"':'')+'>输入UID/昵称/手机号搜索</dd>' + Html;
                        Elem.find(".layui-anim").html(Html);
                    }, {inajax:1, keyword:nickname, uid:Curuid, mp:PickerId},'html');
                })
            });
        },
        StorageRmImg(Elem, multi=false) {
            if(multi){
                return $(Elem).parent().remove();
            }
            let img = $(Elem).prev();
            img.attr("src", img.data("val")).addClass("nopic").parent().prev().find('input.layui-input').val("");
        }
    };
})(window);
