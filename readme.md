##安装

###准备工作

  a. 已备案域名（未备案域名无法解析到国内服务器，同时无法生成封装APP）
  b. 云服务器（配置建议2核4G5M及以上，需开放80、22、443、3000及其它WEB常用端口）
  c. 安装操作系统（建议使用Linux CentOS7.8及以上）
  d. 安装远程命令行SSH或集成管理面板（如宝塔、WDCP等）

###运行环境
  a. Apache2.4.0+ 或者 Nginx1.20+
  b. PHP7.2+
  c. MySQL5.7+

###创建站点
通过宝塔面板等方式在满足上述要求的服务器和运行环境创建好站点后，需要注意以下事项：
  a. 创建的站点必须指定7.2以上的PHP版本
  b. 站点所指定的PHP必须安装这些拓展(需重启PHP)：sg11、fileinfo、exif
  c. 站点所指定的PHP必须要确保这些函数没有被禁用(需重启PHP)：putenv、proc_open、symlink
  d. 建议关闭站点的CDN加速或第三方代理，安装完成后再开启
  e. 创建好站点后，需要给站点添加以下的伪静态规则


`
location / {  
	try_files $uri $uri/ /index.php$is_args$query_string;  
}  
`