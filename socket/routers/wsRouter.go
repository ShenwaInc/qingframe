package routers

import (
	"reflect"
	"strings"
	"xfy_whotalk_socket/model"
	"xfy_whotalk_socket/runtime"
	"xfy_whotalk_socket/server"
	"xfy_whotalk_socket/server/message"
	"xfy_whotalk_socket/server/user"
)

var regStruct map[string]interface{}

func init() {
	// 初始化服务map(必须要手动维护)
	regStruct = make(map[string]interface{})
	regStruct["User"] = user.NewUserServer()
	regStruct["Message"] = message.NewMessageServer()
}

// @Title GetRouter
// @Description 系统路由
// @Param   method  model.ReceiveMessage   消息类型
// @return code int8
// @return res string

func GetRouter(method model.ReceiveMessage) (code int16, res string) {
	server := server.NewBaseServer()
	methods := strings.Split(method.Method, "/")
	if len(methods) != 2 {
		go server.SendToId(method.Client, method.FromId, 10003, "api参数有误", method.Method, "", 0)
		return
	}
	serverName := methods[0]
	methodName := methods[1]
	if _, ok := regStruct[serverName]; !ok {
		// 不存在
		go server.SendToId(method.Client, method.FromId, 10004, "url not found", method.Method, "", 0)
		return
	}

	code, res = execute(serverName, methodName, method)
	if code > 0 {
		go server.SendToId(method.Client, method.FromId, 10004, res, method.Method, "", 0)
	}
	return
}

// @Title execute
// @Description 通过反射调用对象的操作方法
// @Param   ruleClassName  string   服务名
// @Param   methodName  methodName   方法名
// @Param   message  model.ReceiveMessage   消息结构
// @return code int8
// @return res string

func execute(ruleClassName string, methodName string, message model.ReceiveMessage) (code int16, res string) {
	t := reflect.TypeOf(regStruct[ruleClassName])
	value := reflect.ValueOf(regStruct[ruleClassName])
	if _, ok := t.MethodByName(methodName); !ok {
		return 1, "no method"
	}

	// 所有方法need args 必须要
	args := []reflect.Value{reflect.ValueOf(message)}
	response := value.MethodByName(methodName).Call(args)

	for i := range response {
		if i == 0 {
			x := response[i].Int()
			code = int16(x)
		}
		if i == 1 {
			res = response[i].String()
		}
	}
	runtime.Info.Println("execute--->:", response, code, res)
	return
}
