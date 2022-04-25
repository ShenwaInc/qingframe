##安装

###准备工作

- 已备案域名（未备案域名无法解析到国内服务器，同时无法生成封装APP）
- 云服务器（配置建议2核4G5M及以上，需开放80、22、443、3000及其它WEB常用端口）
- 安装操作系统（建议使用Linux CentOS7.8及以上）
- 安装远程命令行SSH或集成管理面板（如宝塔、WDCP等）

###运行环境
- Apache2.4.0+ 或者 Nginx1.20+
- PHP7.2+
- MySQL5.7+

###创建站点
通过宝塔面板等方式在满足上述要求的服务器和运行环境创建好站点后，需要注意以下事项：
- 创建的站点必须指定7.2以上的PHP版本
- 站点所指定的PHP必须安装这些拓展(需重启PHP)：sg11、fileinfo、exif
- 站点所指定的PHP必须要确保这些函数没有被禁用(需重启PHP)：putenv、proc_open、symlink
- 建议关闭站点的CDN加速或第三方代理，安装完成后再开启
- Nginx的运行环境需要给站点添加以下的伪静态规则
```
location / {  
	try_files $uri $uri/ /index.php$is_args$query_string;  
}  
````

###拉取代码
- 将仓库源码拉取到站点根目录中
- 在根目录运行如下命令来安装依赖包
```
composer update
```


###安装向导
- 将根目录下的.env.temp文件复制并重命名为 .env
- 将根目录下的 /public/ 文件夹设为运行目录
- 设置以下目录权限为755（目录的所有者最好为www）： app、resources、storage、servers
- 输入网址运行安装向导，完成软件的安装


###开发模式
- 编辑.env文件，将 APP_DEVELOPMENT 的值0改为1