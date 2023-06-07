## 安装

### 准备工作

- 已备案域名（未备案域名无法解析到国内服务器）
- 云服务器（配置建议4核8G5M带宽及以上，需开放80、22、443、3000及其它WEB常用端口）
- 安装操作系统（建议使用Linux CentOS 7.8及以上）
- 安装远程命令行SSH或集成管理面板（如宝塔、WDCP等）

### 运行环境
- Nginx1.20+ 或者 Apache2.4.0+ 
- PHP7.2+
- MySQL5.6+

### 创建站点
通过宝塔面板等方式在满足上述要求的服务器和运行环境创建好站点后，需要注意以下事项：
- 创建的站点或容器必须指定7.2以上的PHP版本
- 站点所指定的PHP必须安装这些拓展(需重启PHP)：fileinfo、exif
- 站点所指定的PHP必须要确保这些函数没有被禁用(需重启PHP)：putenv、proc_open、symlink
- 建议关闭站点的CDN加速或第三方代理，安装完成后再开启
- Nginx的运行环境需要给站点添加以下的伪静态规则
```
location / {  
    try_files $uri $uri/ /index.php$is_args$query_string;  
}  
```

### 安装与部署
1. 拉取项目源码
```shell
git clone https://github.com/ShenwaInc/qingwork.git
```
2. 在根目录运行如下命令来安装依赖包
```shell
cd qingwork
composer update
```
3. 拷贝根目录下的 .example.env 文件并重命名为 .env
4. 将根目录下的 /public/ 文件夹设为运行目录
5. 设置以下目录权限为755（所有者为www）： app、resources、storage、servers、routes、public
6. 在根目录运行如下命令来创建附件映射
```shell
php artisan storage:link
```
7. 在根目录运行如下命令来清理无用文件和目录
```shell
php artisan self:clear
```
8. 输入网址运行安装向导，完成软件的安装


### 开发者模式
- 编辑.env文件，添加环境变量 APP_DEVELOPMENT=1
