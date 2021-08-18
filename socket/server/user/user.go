package user

import (
	"sync"
	"xfy_whotalk_socket/model"
	"xfy_whotalk_socket/server"
)

type User struct {
	server.BaseServer
}

func NewUserServer() *User {
	return &User{}
}

var mutex sync.Mutex

// @Title Connect
// @Description 用户链接
// @Param   message model.ReceiveMessage  用户消息
// @return   code  int16  返回码
// @return   message  string   消息

func (u *User) Connect(message model.ReceiveMessage) (code int16, res string) {
	// 1.接收到用户连接,执行登录
	var returnMessage model.SendMessage
	returnMessage.Type = 1
	returnMessage.Method = message.Method

	l := len([]rune(message.FromId))

	if l == 0 || l < 10 {
		returnMessage.Code = 10001
		returnMessage.Message = "用户信息有误"
		message.Client.WriteJSON(returnMessage)
		return
	}

	if _, ok := model.Clients[message.FromId]; ok {
		//存在
		returnMessage.Code = 10002
		returnMessage.Message = "该用户已连接"
		message.Client.WriteJSON(returnMessage)
		return
	}

	mutex.Lock()
	model.Clients[message.FromId] = message.Client
	mutex.Unlock()

	// 群发消息
	// 获取在id
	var userIds []string

	// 返回线用户
	var returnData map[string][]string
	returnData = make(map[string][]string)
	returnData["userIds"] = userIds
	for userId, _ := range model.Clients {
		if userId != message.FromId {
			userIds = append(userIds, userId)
		}
		returnData["userIds"] = append(returnData["userIds"], userId)
	}

	returnMessage.Code = 10000
	returnMessage.Message = "连接成功"
	// 获取所有在线用户列表
	returnMessage.Data = returnData
	message.Client.WriteJSON(returnMessage)

	// 给其余用户广播用户登陆的消息
	//go u.SendToIds(userIds, 10006, message.Message, message.Method, returnData, 1)
	return 0, returnMessage.Message
}
