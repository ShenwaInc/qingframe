# xfy_whotalk_socket 
#### start todo
```
git clone xxxxxx.git
go mod init
go mod tidy
go mod vendor
```

#### set GoPrpoxy
```
GOPROXY=https://mirrors.aliyun.com/goproxy/
``` 
 
#### Example

在开发web的时候，如果项目不支持热重启，每添加或修改个接口都需要重启项目才能测试，会很麻烦。都知道beego有bee工具，bee run启动项目即可，而在rizla项目中热重启方法如下

```
# 安装rizla包
$ go get -u github.com/kataras/rizla
# 热重启方式启动iris项目
$ rizla main.go
```