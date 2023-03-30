(function(w) {
    w.Swaws = {
        onDisconnect:null,
        onConnect:null,
        Heartbeat:false,
        HeartInterval:null,
        UserSign:"",
        socketRetry:0,
        init: function (UserSign, Server, Receive = false, Fail = false) {
            this.UserSign = UserSign;
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
                let socketData = {};
                let data = null;
                if(typeof(res.data)=='object'){
                    data = res.data;
                }else{
                    data = JSON.parse(res.data);
                }
                if (typeof (data) != 'object' || data==null) return false;
                if(data.type===1 && data.method==='User/Connect'){
                    self.Heartbeat = true;
                    self.socketRetry = 0;
                    self.HeartInterval = setInterval(function (){
                        let sendHeart = self.doHeartbeat();
                        if (!sendHeart){
                            clearInterval(self.HeartInterval);
                            self.HeartInterval = null;
                        }
                    }, 15000);
                    if (typeof (self.onConnect)=='function'){
                        return self.onConnect(data);
                    }
                    if (typeof (Receive) == 'function') {
                        return Receive({
                            type:"WssConnect",
                            UserId:self.UserSign
                        });
                    }
                }
                if (data.method==='Message/Heartbeat'){
                    self.Heartbeat = true;
                    return true;
                }
                if(typeof(data.data)!='object' || data.data==null) return false;
                if (typeof (data.data.message) != 'undefined') {
                    if (typeof (data.data.message) == 'object') {
                        socketData = data.data.message;
                    } else if (self.isJsonString(data.data.message)) {
                        socketData = JSON.parse(data.data.message);
                    } else {
                        socketData = {type: 'undefined', data: data.data.message};
                    }
                } else {
                    socketData = {
                        type: data.method,
                        status: data.type,
                        data: data.data,
                        message: data.message
                    }
                }
                if (typeof (Receive) == 'function') {
                    return Receive(socketData);
                } else {
                    console.log(socketData);
                }
            }
            WsSocket.onclose = function (e) {
                console.log("Connection closed.", e);
                self.io = null;
                self.Heartbeat = false;
                if (typeof (Fail) == 'function' && e.code!==1005) {
                    Fail();
                }
                if (typeof (self.onDisconnect)=='function'){
                    self.onDisconnect();
                }
                if((e.code!==1000 && e.code!==1006) || typeof(e.code)=='undefined'){
                    if(self.socketRetry>=5){
                        return console.error("通讯服务器连接失败");
                    }
                    self.socketRetry += 1;
                    console.log('开始第'+self.socketRetry+'次重新连接');
                    setTimeout(function(){
                        return self.init(UserSign, Server, Receive, Fail);
                    },2000);
                }
            }
            WsSocket.onerror = function (event) {
                self.Heartbeat = false;
                if (typeof (Fail) == 'function') {
                    Fail();
                }
            };
            this.io = WsSocket;
            return WsSocket;
        },
        Send: function (data, userIds){
            let socketData = {
                "Method": "Message/SendToUsers",
                "Type": 0,
                "Message": JSON.stringify(data),
                "data":{
                    "userIds":userIds
                }
            };
            return this.io.send(JSON.stringify(socketData));
        },
        doHeartbeat:function (){
            if (!this.Heartbeat){
                console.log("已经失去心跳急需抢救");
                this.io.close(3019);
                return false;
            }
            this.Heartbeat = false;
            let data = {
                "Method": "Message/Heartbeat",
                "Type": 0,
                "Message": "",
                "fromId": this.UserSign,
                "data":{}
            };
            this.io.send(JSON.stringify(data));
            return true;
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
