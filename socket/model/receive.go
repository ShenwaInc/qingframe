package model

import "github.com/gorilla/websocket"

// 接收定义消息结构
type ReceiveMessage struct {
	// 请求的方法
	Method string `json:"method"`
	// 消息类型(1,text,0:系统消息)
	Type int8 `json:"type"`
	// 消息体
	Message string `json:"message"`
	// 消息来源用户Id
	FromId string `json:"fromId"`
	// 当前连接
	Client *websocket.Conn `json:"client"`
	// 数据参数
	Data map[string]interface{} `json:"data"`
}

// 发送消息结构
type SendMessage struct {
	// 请求的方法
	Method string `json:"method"`
	// 消息类型(1,text,0:系统消息))
	Type uint8 `json:"type"`
	// 消息体
	Message string `json:"message"`
	// Code 0 正确，1 错误
	Code int16 `json:"code"`
	// 数据参数
	Data interface{} `json:"data"`
}

// 所有用户连接辞池子
var Clients = make(map[string]*websocket.Conn) // connected clients

// 所有消息管道
var MessageBroadcast = make(chan ReceiveMessage) // broadcast channel

// 所有处理失败消息管道
var ErrorMessage = []ReceiveMessage{}
