package message

import (
	"net/http"
	"xfy_whotalk_socket/config"
	"xfy_whotalk_socket/server"
)

type AddDomainWhite struct {
	MessageController
}

// @Title ServeHTTP
// @Description http请求 给指定用户发消息
// @Param   method  model.ReceiveMessage   消息类型
// @return code int8
// @return res string

func (m *AddDomainWhite) ServeHTTP(resp http.ResponseWriter, req *http.Request) {
	url := req.FormValue("url")
	if url == "" {
		resp.Write(m.End(1, "url为空", ""))
		return
	}
	baseServer := server.NewBaseServer()

	err := baseServer.WriteFile(config.DomainPath, url)
	if err != nil {
		resp.Write(m.End(1, err.Error(), ""))
		return
	}
	resp.Write(m.End(0, "成功", ""))
	return

}
