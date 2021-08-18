// @Time : 2020/9/26 下午2:36
// @Author : xiukang
// @File : httpRouter
// @Software: GoLand

package routers

import (
	"net/http"
)

// @Title GetHttpRouter
// @Description 初始化和手动维护路由
// @return *muxHandler

type muxHandler struct {
	handlers    map[string]http.Handler
	handleFuncs map[string]func(resp http.ResponseWriter, req *http.Request)
}

// @Title NewMuxHandler
// @Description 生成路由对象
// @return *muxHandler

func NewMuxHandler() *muxHandler {
	return &muxHandler{
		make(map[string]http.Handler),
		make(map[string]func(resp http.ResponseWriter, req *http.Request)),
	}
}

// @Title ServeHTTP
// @Description 执行handler的ServeHTTP方法,每个handler里默认实现该方法
// @Param   resp  http.ResponseWriter   返回参数
// @Param   req  *http.Request   请求参数

func (h *muxHandler) ServeHTTP(resp http.ResponseWriter, req *http.Request) {
	urlPath := req.URL.Path
	if hl, ok := h.handlers[urlPath]; ok {
		hl.ServeHTTP(resp, req)
		return
	}
	if fn, ok := h.handleFuncs[urlPath]; ok {
		fn(resp, req)
		return
	}
	http.NotFound(resp, req)
}

// @Title Handle
// @Description 构建将handle名与handle实体map
// @Param   pattern  string  名字
// @Param   req  *http.Request   请求参数

func (h *muxHandler) Handle(pattern string, hl http.Handler) {
	h.handlers[pattern] = hl
}

// @Title HandleFunc
// @Description 构建将handle名与handle实体map
// @Param   resp  http.ResponseWriter   返回参数
// @Param   req  *http.Request   请求参数

func (h *muxHandler) HandleFunc(pattern string, fn func(resp http.ResponseWriter, req *http.Request)) {
	h.handleFuncs[pattern] = fn
}
