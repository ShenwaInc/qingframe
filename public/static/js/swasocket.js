(function(w) {
    w.Swaws = {
        init: function (UserSign, Server, Receive = false, Fail = false) {
            if (this.io != null) {
                this.io.close(10001);
                return this.init(UserSign, Server, Receive, Fail);
            }
            let WsSocket = new WebSocket(Server);
            let self = this;
            WsSocket.onopen = function (event) {
                let data = {
                    "Method": "User/Connect",
                    "Type": 0,
                    "Message": "用户" + UserSign + " 已连接服务器",
                    "fromId": UserSign,
                    "Data": {
                        "SiteRoot": window.location.protocol + '//' + window.location.host + '/'
                    }
                }
                WsSocket.send(JSON.stringify(data));
                console.log(data.Message);
            };
            WsSocket.onmessage = function (res) {
                let data = JSON.parse(res.data), socketdata;
                if (typeof (data.data) != 'object') return false;
                if (typeof (data.data.message) != 'undefined') {
                    if (typeof (data.data.message) == 'object') {
                        socketdata = data.data.message;
                    } else if (self.isJsonString(data.data.message)) {
                        socketdata = JSON.parse(data.data.message);
                    } else {
                        socketdata = {type: 'undefined', data: data.data.message};
                    }
                } else {
                    socketdata = {
                        type: data.method,
                        status: data.type,
                        data: data.data,
                        message: data.message
                    }
                }
                if (typeof (Receive) == 'function') {
                    return Receive(socketdata);
                } else {
                    console.log(socketdata);
                }
            }
            WsSocket.onclose = function (e) {
                console.log("Connection closed.", e);
                self.io = null;
                if (typeof (Fail) == 'function' && e.code!==1005) {
                    Fail();
                }
            }
            WsSocket.onerror = function (event) {
                if (typeof (Fail) == 'function') {
                    Fail();
                }
            };
            this.io = WsSocket;
        },
        isJsonString(str) {
            try {
                if (typeof (jQuery.parseJSON(str)) == "object") {
                    return true;
                }
            } catch (e) {
            }
            return false;
        },
        io: null
    };
})(window);
