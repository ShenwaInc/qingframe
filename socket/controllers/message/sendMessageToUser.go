/*
@Time : 2020/9/26 下午3:23
@Author : xiukang
@File : sendMessageToUser
@Software: GoLand
*/
package message

import (
	"net/http"
	"strings"
	message2 "xfy_whotalk_socket/server/message"
)

type SendMessageToUser struct {
	MessageController
}

// @Title ServeHTTP
// @Description http请求 给指定用户发消息
// @Param   method  model.ReceiveMessage   消息类型
// @return code int8
// @return res string

func (m *MessageController) ServeHTTP(resp http.ResponseWriter, req *http.Request) {
	userIds := req.FormValue("userIds")
	message := req.FormValue("message")

	users := strings.Split(userIds, ",")

	messageServer := message2.NewMessageServer()

	var returnData map[string]interface{}
	returnData = make(map[string]interface{})
	returnData["message"] = message
	returnData["fromId"] = 0

	go messageServer.SendToIds(users, 10005, "", "/api/message/SendMessageToUser", returnData, 1)
	resp.Write(m.End(0,"成功",""))

}


