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
        request: function (u, m, d, t, c, l) {
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
                    if (hreq.loading !== 0) {
                        layer.close(hreq.loading);
                        hreq.loading = 0;
                    }
                    if (Loadajax) Loadajax = false;
                    layer.msg('操作失败', {icon: 2});
                    console.log(e);
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
        report: function (res) {
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
                setTimeout(direction, 1200);
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
            return BaseUrl + route.replaceAll('.', '/');
        },
        loading: 0,
        debug: false
    };
})(window);
