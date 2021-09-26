/*
@Time : 2020/9/26 下午2:52
@Author : xiukang
@File : base
@Software: GoLand
*/
package controllers

import (
	"encoding/json"
)

type BaseController struct {
}

// @Title 返回json数据
// @Description
// @Param   code  int16 true
// @Param   message  String true
// @Param   data  interface true
// @Return  res []byte

func (c *BaseController) End(code int16, message string, data interface{}) (res []byte) {
	if data == nil {
		data = make(map[string]interface{})
	}
	returnData := map[string]interface{}{"code": code, "message": message, "data": data}

	res, _ = json.Marshal(returnData)
	return
}
