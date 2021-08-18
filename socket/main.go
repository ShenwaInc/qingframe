/*
@Time : 2020/8/20 下午8:19
@Author : xiukang
@File : main.go
@Software: GoLand
*/

package main

import (
	"fmt"
	"github.com/gorilla/websocket"
	"net/http"
	"strings"
	"sync"
	"time"
	"xfy_whotalk_socket/controllers/message"
	"xfy_whotalk_socket/model"
	"xfy_whotalk_socket/routers"
	"xfy_whotalk_socket/runtime"
	"xfy_whotalk_socket/server"
	"xfy_whotalk_socket/server/user"
)

var upgrader = websocket.Upgrader{

	CheckOrigin: func(r *http.Request) bool {
		return true
	},
}

var mutex sync.Mutex

// 用户退出
func logout(msg model.ReceiveMessage) {
	runtime.Info.Println("disconnect:", msg)

	mutex.Lock()
	delete(model.Clients, msg.FromId)
	mutex.Unlock()

	msg.Client.Close()
}

// 用户登陆
func login(loginMsg model.ReceiveMessage) (code int16, res string) {

	runtime.Info.Println("connect:", loginMsg)

	if loginMsg.Method != "User/Connect" {
		return 1, "参数有误或未登陆"
	}
	userServer := user.NewUserServer()
	code, res = userServer.Connect(loginMsg)
	return
}

// ws消息处理
func onWsMessage(w http.ResponseWriter, r *http.Request) {
	c, err := upgrader.Upgrade(w, r, nil)
	if err != nil {
		runtime.Error.Println(err)
		return
	}

	var loginMsg model.ReceiveMessage
	c.ReadJSON(&loginMsg)

	// 1.验证域名黑名单
	domain := loginMsg.Data["SiteRoot"]
	if domain == nil || domain == "" {
		runtime.Info.Println("domain is null")
		c.Close()
		return
	}
	baseServer := server.NewBaseServer()

	code, res := baseServer.CheckDomainWhite(domain.(string))
	if code > 0 {
		runtime.Info.Println("check domain white error:" + res + " (" + c.RemoteAddr().String() + ")")
		c.Close()
		return
	}

	// 2.接收到用户连接,执行登录
	loginMsg.Client = c
	code, res = login(loginMsg)
	if code > 0 {
		runtime.Info.Println("connect error:" + res)
		c.Close()
		return
	}
	// 关闭连接需要修改
	defer logout(loginMsg)

	// 1.处理当前用户获取系统消息
	var userMsg model.ReceiveMessage

	// 系统监听用户消息
	for {

		err = c.ReadJSON(&userMsg)
		if err != nil {
			runtime.Error.Println("收到消息 json解析err:", err)
			if strings.Contains(fmt.Sprint(err), "websocket: close") {
				break
			}

		}
		userMsg.Client = c
		runtime.Info.Println("收到消息：->", userMsg)
		model.MessageBroadcast <- userMsg
	}

}

/**
处理用户消息
*/
func handleMessages() {
	for {
		// 获取到管道里的所有数据 code为系统处理业务逻辑失败需要记录的问题
		msg := <-model.MessageBroadcast
		code, res := routers.GetRouter(msg)
		if code != 0 {
			runtime.Error.Println(res)
			model.ErrorMessage = append(model.ErrorMessage, msg)
		}
	}
}

/**
处理错误的消息
*/
func handleErrorMessages() {
	for {
		for i, _ := range model.ErrorMessage { //range returns both the index and value
			// 错误的删除掉
			mutex.Lock()
			{
				runtime.Info.Println("处理错误消息")

				model.ErrorMessage = append(model.ErrorMessage[:i], model.ErrorMessage[i+1:]...)
			}
			// 释放锁，允许其他
			mutex.Unlock()
		}
		time.Sleep(time.Second * 1)
	}
}

// 代码初始化
func init() {
	runtime.Info.Println("系统初始化")
	// 1.修复用户会话与系统会话
	//sessionServer := &session.InitSession{}
	//go sessionServer.Index()
}

// 更新消息入mysql
func updateMessgeToDb() {
	//messageServer := &message.UpdateMessageQueueServer{}
	//for {
	//	messageServer.Index()
	//}

}

// 更新系统会话入mysql
func updateSessionToDb() {
	//messageServer := &session.UpdateSessionQueueServer{}
	//for {
	//	messageServer.Index()
	//}
}
func main() {

	httpRouter := routers.NewMuxHandler()
	// 需要手动维护

	// 添加http路由
	httpRouter.Handle("/api/message/sendMessageToUser", &message.SendMessageToUser{})
	// 添加http路由
	httpRouter.Handle("/api/message/addDomainWhite", &message.AddDomainWhite{})

	// 添加ws路由
	httpRouter.HandleFunc("/wss", onWsMessage)

	// 处理用户消息
	go handleMessages()
	//处理错误消息
	//go handleErrorMessages()
	//更新消息队列入mysql
	//go updateMessgeToDb()
	// 更新系统会话入mysql
	//go updateSessionToDb()

	runtime.Info.Println("websocket start at 127.0.0.1:3000")
	err := http.ListenAndServe(":3000", httpRouter)
	if err != nil {
		panic("ListenAndServe: " + err.Error())
	}

}
