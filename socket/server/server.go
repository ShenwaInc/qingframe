package server

import (
	"fmt"
	"github.com/gorilla/websocket"
	"io/ioutil"
	"os"
	"reflect"
	"strings"
	"sync"
	"xfy_whotalk_socket/config"
	"xfy_whotalk_socket/model"
	"xfy_whotalk_socket/runtime"
)

type BaseServer struct {
}

var mutex sync.Mutex

func NewBaseServer() *BaseServer {
	return &BaseServer{}
}

type checkDomain struct {
	Message  string `json:"message"`
	Redirect string `json:"redirect"`
	Type     string `json:"type"`
}

// @Title CheckDomainWhite
// @Description 验证域名白名单
// @Param   domain  string   域名
// @return   code  int16  返回码
// @return   message  string   消息

func (b *BaseServer) CheckDomainWhite(domain string) (code int16, msg string) {

	// 1.域名写死
	//domain1 := strings.Contains(domain, ".wayxq.")
	//domain2 := strings.Contains(domain, ".fu505.")
	//
	//code = 1
	//if domain1 || domain2 {
	//	code = 0
	//}
	//
	//return code, msg

	// 2.调接口获取域名
	//path := config.ApiHost + "?i=4&c=entry&m=swa_supersale&do=api&r=authorize.query&siteroot=" + domain
	//resp, err := http.Get(path)
	//if err != nil {
	//	return
	//}
	//defer resp.Body.Close()
	//body, _ := ioutil.ReadAll(resp.Body)
	//
	//var res checkDomain
	//_ = json.Unmarshal(body, &res)
	//
	//if res.Type != "success" {
	//	code = 1
	//}
	//return code, res.Message

	// 3.获取文件
	domainStr, err := b.ReadFile(config.DomainPath)
	if err != nil {
		return 1, err.Error()
	}
	domainArr := strings.Split(domainStr, ",")
	code = 1
	for _, v := range domainArr {
		if v == "" {
			continue
		}
		if ok := strings.Contains(domain, v); ok {
			fmt.Println("1111")
			return 0, "成功"
		}
	}
	return code, "失败"
}

// @Title SendToUser
// @Description 消息发送给用户
// @Param   userId  uint64   用户id
// @Param   code  int16  返回码
// @Param   message  string   消息
// @Param   method  string   方法路径
// @Param   data  interface   数据
// @Param   types  uint8  消息类型

func (b *BaseServer) SendToId(client *websocket.Conn, userId string, code int16, message string, method string, data interface{}, types uint8) {
	if userId == "" && client == nil {
		runtime.Error.Println("SendToUser--err->:", code, message, method, data, "user is not 0")
		return
	}
	var returnMessage model.SendMessage
	returnMessage.Type = types
	returnMessage.Message = message
	returnMessage.Code = code
	returnMessage.Data = data
	returnMessage.Method = method

	if client != nil {
		err := client.WriteJSON(returnMessage)
		if err != nil {
			runtime.Error.Println("SendToUser--err->:", returnMessage, err)
		}
		return
	}

	if _, ok := model.Clients[userId]; !ok {
		//user Id 不存在
		runtime.Error.Println("SendToUser--err->:", returnMessage, "no user id")
		return
	}

	err := model.Clients[userId].WriteJSON(returnMessage)
	if err != nil {
		runtime.Error.Println("SendToUser--err->:", returnMessage, err)
	}
	runtime.Info.Println("SendToUser:", returnMessage)

}

// @Title SendToIds
// @Description 批量消息发送给用户群
// @Param   userIds  []uint64   用户ids
// @Param   code  int16  返回码
// @Param   message  string   消息
// @Param   method  string   方法路径
// @Param   data  interface   数据
// @Param   types  uint8  消息类型

func (b *BaseServer) SendToIds(userIds []string, code int16, message string, method string, data interface{}, types uint8) {

	var returnMessage model.SendMessage
	returnMessage.Type = types
	returnMessage.Message = message
	returnMessage.Code = code
	returnMessage.Data = data
	returnMessage.Method = method

	number := len(userIds)
	for i := 0; i < number; i++ { //looping from 0 to the length of the array
		userId := userIds[i]
		if _, ok := model.Clients[userId]; !ok {
			//不存在
			runtime.Error.Println("SendToUsers--err->:", returnMessage, "no user id", userId)
			continue
		}
		err := model.Clients[userId].WriteJSON(returnMessage)
		if err != nil {
			runtime.Error.Println("SendToUsers--err->:", returnMessage, err)
		}

		runtime.Info.Println("SendToUsers-->:", returnMessage)
	}
}

// @Title Converts
// @Description 遍历变量里所有数值到一个字符串数组
// @Param   dst  []string  原始数组
// @return   v reflect.Value  变量的reflect.ValueOf() 值
// @return   []string   字符串数组

func (b *BaseServer) Converts(dst []string, v reflect.Value) []string {
	// Drill down to the concrete value
	for v.Kind() == reflect.Interface {
		v = v.Elem()
	}

	if v.Kind() == reflect.Slice {
		// Convert each element of the slice.
		for i := 0; i < v.Len(); i++ {
			dst = b.Converts(dst, v.Index(i))
		}
	} else {
		// Convert value to string and append to result
		dst = append(dst, fmt.Sprint(v.Interface()))
	}
	return dst
}

// @Title WriteFile
// @Description 写入文件
// @Param   file  string  文件名
// @Param   content  string  文件内容

func (b *BaseServer) WriteFile(file string, content string) error {
	f, err := os.OpenFile(file, os.O_WRONLY, 0644)
	if err != nil {
		// 打开文件失败处理
		runtime.Error.Println("打开域名文件失败.....")
		runtime.Error.Println(err)
		return err
	}
	// 查找文件末尾的偏移量
	n, _ := f.Seek(0, 2)

	// 从末尾的偏移量开始写入内容
	_, err = f.WriteAt([]byte(content), n)

	defer f.Close()
	return nil
}

// @Title WriteFile
// @Description 获取文件
// @Param   file  string  文件名
// @Param   content  string  文件内容

func (b *BaseServer) ReadFile(file string) (string, error) {
	f, err := ioutil.ReadFile(file)
	if err != nil {
		return "", err
	}
	return string(f), nil
}
