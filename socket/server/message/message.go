package message

import (
	"reflect"
	"xfy_whotalk_socket/model"
	"xfy_whotalk_socket/server"
)

type Message struct {
	server.BaseServer
}

func NewMessageServer() *Message {
	return &Message{}
}

// @Title SendToUsers
// @Description 批量消息发送给用户群
// @Param   message    用户消息
// @return   code  int16  返回码
// @return   message  string   消息

func (m *Message) SendToUsers(message model.ReceiveMessage) (code int16, res string) {
	//var data userData
	info := message.Data

	userIds := info["userIds"]

	var list []string
	userIdsStr := m.Converts(nil, reflect.ValueOf(userIds))
	for _, userIdStr := range userIdsStr {
		//userId, _ := strconv.ParseInt(userIdStr, 10, 64)
		list = append(list, userIdStr)
	}

	var returnData map[string]interface{}
	returnData = make(map[string]interface{})
	returnData["message"] = message.Message
	returnData["fromId"] = message.FromId
	go m.SendToIds(list, 10005, "", message.Method, returnData, 1)
	return
}

// @Title SendToAll
// @Description 批量消息发送给所有在线用户
// @Param   message    用户消息
// @return   code  int16  返回码
// @return   message  string   消息

func (m *Message) SendToAll(message model.ReceiveMessage) (code int16, res string) {
	var userIds []string
	for userId, _ := range model.Clients {
		if userId != message.FromId {
			userIds = append(userIds, userId)
		}
	}

	var returnData map[string]interface{}
	returnData = make(map[string]interface{})
	returnData["message"] = message.Message
	returnData["fromId"] = message.FromId

	go m.SendToIds(userIds, 10005, "", message.Method, returnData, 1)
	return
}

