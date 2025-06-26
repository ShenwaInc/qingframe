除了以下约束的文件外，模块的所有目录结构和文件均由模块开发者自由设计。

注：除了运营产生的附件外，模块运行相关的所有源码和静态文件都应存放在模块目录下或者云存储中，应有清晰的目录结构和层次。

<h2 id="XDc9x">1 安装包</h2>
<h3 id="vywH0">1.1 安装路径</h3>
轻如云系统的模块安装包放在项目的` **/public/addons/** `目录下，以模块标识命名。

![](https://cdn.nlark.com/yuque/0/2023/png/1333431/1677729032002-21b08aef-4447-425e-8e4b-22d9de148198.png)



<h3 id="g7ubO">1.2 描述文件（manifest.json）</h3>
模块的描述文件以JSON格式保存在模块根目录下的  `**manifest.json**` 文件，其固定格式如下：

```json
{
  "application": {
    "name": "多人实用记账本",
    "identifie": "xfy_account",
    "version": "1.0.1",
    "releasedate": "202303011001",
    "type": "business",
    "module_type": "1",
    "ability": "Your Module",
    "description": "Your Module",
    "author": "神蛙科技",
    "url": "https://www.gxit.org/",
    "logo": "/static/images/microserver.png"
  },
  "servers": [
    {
      "id": "ucenter",
      "version": "",
      "description": "用户相关服务"
    }
  ],
  "permissions": [
    {
      "name": "会员管理",
      "route": "member",
      "subPerm": [
        {"name": "新增会员", "route": "post"},
        {"name": "会员等级", "route": "group"},
        {"name": "会员数据", "route": "data"}
      ]
    },
    {
      "name": "系统管理",
      "route": "system",
      "subPerm": [
        {"name": "参数设置", "route": "setting"},
        {"name": "消息通知", "route": "notice"},
        {"name": "广告管理", "route": "advertise"}
      ]
    }
  ],
  "install": {
    "content": "install.php",
    "drive": "php"
  },
  "upgrade": {
    "content": "upgrade.php",
    "drive": "php"
  },
  "uninstall": {
    "content": "uninstall.php",
    "drive": "php"
  }
}
```

<h4 id="a0Gtn">字段说明</h4>
+ `**application**`：模块基础信息，如果发布了新版本的应用，需要修改提升对应的版本名称和版本号。
    - `identifie`：唯一标识
    - `name`：应用名称
    - `**module_type**`：应用类型，1为内置应用，2为第三方应用
    - `version`：版本名称，如1.0.1
    - `releasedate`：版本号
    - `type`：应用分类
    - `ability`：一句话说明
    - `description`：应用描述
    - `author`：应用作者
+ `**servers**`：模块依赖的微服务，如果不指定版本号则表示任意版本都支持，安装应用时将会自动安装或更新依赖的微服务。<font style="color:#DF2A3F;">请注意：此模式下安装的微服务自动安装Composer依赖时可能出现超时现象，建议非必须的微服务仅在需要用到时才提示安装，或安装好模块后在后台引导安装相应的微服务。</font>
+ `**permissions**`：模块的权限清单，支持2级权限设置。该字段同样也表示模块的默认主菜单。
+ `**install**`：指定模块的安装脚本，其中 `content` 支持文件路径（绝对路径相对于模块根目录的路径）或脚本代码；`drive` 支持php（PHP文件）、phpscript（PHP脚本，不建议）、sql（MySQL脚本）、shell（相对于模块目录的.sh文件路径）、shellscript（shell脚本，不建议）
+ `**upgrade**`：指定模块的升级脚本，规则内容同上
+ `**uninstall**`：指定模块的卸载脚本，规则内容同上



<h3 id="CuWYB">1.3 入口文件（site.php）</h3>
该文件用于定义应用模块的后台和前台控制器，所有的HTTP请求和内置的调用都会先运行该文件的对应方法。

`**site.php**`文件内需要定义模块控制器主类（`<font style="color:#74B602;">class</font> Addons\identifie\site`），开发者可以在这个类编写任何方法逻辑。在命名文件、类名、方法名、路由时，请注意区分大小写，并注意遵循PSR-4规范。

<h4 id="nVHK2">内置应用</h4>
内置应用的模块主类必须继承内置应用基类`**App\Utils\WeModule**`，该基类定义了模块的初始化方法、模块URL生成方法、内置的视图模板编译方法等，便于应用的快速开发。

```php
namespace Addons\identifie;

use App\Utils\WeModule;

class site extends WeModule{

    public function doWebIndex(){
        //后台默认入口
        //Todo something
        return "Hello World.";
    }

    public function doWebRoute2(){
        //后台自定义入口
        //Todo something
        return "Hello World.";
    }

    public function doMobileIndex(){
        //前台默认入口
        //Todo something
        return "Hello World.";
    }

    /**
     * 支付结果通知回调函数
    */
    public function payResult($params){
        //Todo something
    }

    /**
     * 退款结果通知回调函数
    */
    public function refundResult($params){
        //Todo something 处理退款订单
        return true;
    }

    public function foo(){
        //自定义方法
        return "Hello World.";
    }

}
```



<h4 id="lyQFY">第三方应用</h4>
第三方应用的入口文件必须继承第三方应用基类 `**App\Utils\QuickModule**` ，该基类主要实现轻如云系统与第三方应用的跳转和鉴权功能。

<font style="color:#DF2A3F;">原则上第三方英文的入口文件不需要写任何入口方法和控制器，只需要默认生成的文件即可，除非您需要调整自动授权和跳转逻辑。</font>

<font style="color:#DF2A3F;">第三方应用的请求和响应逻辑请参考下文。</font>

```php
<?php

namespace Addons\identifie;

use App\Utils\QuickModule;

class site extends QuickModule{

    public $WebIndex = ''; //默认入口链接，如https://example.com/
    public $SsoMaster = true;
    
}


?>

```

**变量说明**

| 参数 | 类型 | 描述 |
| --- | --- | --- |
| `**$WebIndex**` | String | 第三方系统入口链接，支持带参数，不支持含#号的链接。为空则表示需要后台设置 |
| `**$SsoMaster**` | Boolen | 是否使用<font style="color:rgb(53, 53, 53);">masterSecret进行签名验证，详细请参考下文说明，默认为true</font> |




<h3 id="Gj2mq">1.4 composer.json（非必须）</h3>
当模块安装包存在 composer.json 文件且不存在 composer.lock 文件时，将自动运行composer依赖包的安装。

开发者需要自行在适当位置引用模块内的 /vendor/autoload.php 文件。



<h2 id="idStT">2 请求</h2>
模块的所有HTTP请求最终都会运行模块的主类文件 `**site.php**` ，您需要根据模块功能在此文件内定义对应的方法，每个方法都可以视为一个控制器。

您还可以在此文件内创建一个公共方法，然后在此方法根据URL参数来重定义路由-控制器规则。

更多关于请求的说明请参考：[请求](https://learnku.com/docs/laravel/6.x/requests/5139)

<h3 id="bqshO">2.1 内置应用</h3>
<h4 id="Fw09B">后台路由</h4>
模块后台请求的路由规则是 `/console/m/<font style="color:#DF2A3F;">{module}</font>/<font style="color:#ED740C;">{route?}</font>` ，访问的是模块主类的 `doWeb<font style="color:#ED740C;">Route</font>()` 方法，默认是 `doWebIndex()` 方法。如果找不到该方法时，系统会尝试读取模块目录下的 `inc/web/<font style="color:#ED740C;">route</font>.inc.php` 文件，如果该文件存在则直接运行该文件。

<h4 id="K6jPb">前台路由</h4>
模块前台请求的路由规则是 `/app/m/<font style="color:#DF2A3F;">{module}</font>/<font style="color:#ED740C;">{route?}</font>` ，访问的是模块主类的 `doMobile<font style="color:#ED740C;">Route</font>()` 方法，默认是 `doMobileIndex()` 方法。如果找不到该方法时，系统会尝试读取模块目录下的 `inc/mobile/<font style="color:#ED740C;">route</font>.inc.php` 文件，如果该文件存在则直接运行该文件。

<h4 id="ll8BD">接口路由</h4>
模块前台的请求路由规则是`/api/m/<font style="color:#DF2A3F;">{module}</font>/<font style="color:#ED740C;">{route?}</font>`，当`<font style="color:#ED740C;">{route?}</font>`不为空时，运行的是模块主类的`doApi<font style="color:#ED740C;">Route</font>()`方法，否则访问的是`doMobileApi()`方法。

<h4 id="jDkAr">生成URL</h4>
在所有继承了内置应用基类`**App\Utils\WeModule**`的方法和控制器中，都可以用`**$this->createWebUrl()**`方法来生成后台URL，用`**$this->createMobileUrl()**`方法来生成前台URL

```php
/**
 * 生成前台URL
 * @param string $do 路由名称
 * @param array|null $query URL参数
 * @param bool|null $noredirect 微信内跳转标识
 * @param bool|null $addhost 是否返回完整URL（带域名和协议头）
 * @return string 前台URL
*/
protected function createMobileUrl($do, $query = array(), $noredirect = true, $addhost=false) {
    ......
    return $url;
}


/**
 * 生成后台URL
 * @param string $do 路由名称
 * @param array|null $query URL参数
 * @return string 后台URL
*/
protected function createWebUrl($do, $query = array()) {
    $module_name = strtolower($this->modulename);
    return wurl("m/{$module_name}".($do?'/'.$do:''), $query);
}
```

```php
//生成模块后台URL
$webUrl = $this->createWebUrl("web", array("r"=>"system.index"));
dd($webUrl);
// http://yourdomain.com/console/m/whotalk/web?r=system.index
// 对应 /public/addons/whotalk/site.php 的 doWebWeb() 方法


//生成模块前台URL
$payUrl = $this->createMobileUrl("pay", array("tid"=>"SY20230710903089"));
dd($payUrl);
// http://yourdomain.com/wem/whotalk/pay?i=1&tid=SY20230710903089
// 对应 /public/addons/whotalk/site.php 的 doMobilePay() 方法
```



<h4 id="bwC8q">默认控制器</h4>
**doWebIndex()**

后台默认控制器

**doWebMenu()**

后台菜单控制器，默认将根据当前管理员的权限配置返回后台JSON格式的管理菜单数据（二级），可根据实际需要重写此方法。

:::tips
+ **URL**：`/console/m/<font style="color:#DF2A3F;">{module}</font>/menu`
+ **Method**：`GET`
+ **需要登录**：<font style="background:#C0DDFC;color:#00346B">是</font>
+ **需要鉴权**：<font style="background:#C0DDFC;color:#00346B">是</font>

:::

```json
{
	"message":"OK",
    "data":{
        "menus":[
            {
                "title":"标题1",
                "route":"路由1",
                "url":"跳转链接，当没有子级菜单时点击直接调整，否则请忽略",
                "icon":"图标1",
                "subNavs":[
                    {
                        "title":"子菜单标题",
                        "route":"子菜单路由",
                        "url":"跳转链接",
                        "icon":"子菜单图标"
                    }
                ]
            }
        ]
    },
    "code":0,
    "type":"success",
    "redirect":""
}
```

```json
{
    "message":"请先登录",
    "data":[],
    "code":"-2",
    "type":"error",
    "redirect":"/console/login"
}
```

**doMobileIndex()**

前台默认控制器

**payResult()**

用户支付结果回调事件，只有在支付成功后才会执行，一般会执行两次（分别是同步通知用户、异步接口通知），应该模块需要在该方法实现用户支付完成后的业务逻辑。该方法的详细说明请参考下文：[支付结果通知](#xE8E4)

**refundResult()**

系统退款结果回调事件，只有在退款成功才会执行。该方法的更多说明请参考下文：[退款结果通知](#WopAT)

<h3 id="L8KPF">2.2 第三方应用</h3>
第三方应用只提供一个默认的控制器方法`<font style="color:#DF2A3F;">doWebIndex()</font>`（路由 /console/m/<font style="color:#DF2A3F;">{module}</font>/index），该方法定义在模块主类继承的系统应用基类`**App\Utils\QuickModule**`内，用于实现轻如云系统与第三方应用的跳转和鉴权功能，默认情况下您无需对该方法做任何改动实现对应功能；前台由第三方应用实现，因此不接收任何请求。

一般情况下，您只需要定义模块主类的公共变量`**$WebIndex**`和`**$SsoMaster**`就可以完成平台对接到第三方系统的工作，只需要在第三方系统实现单点登录的鉴权功能即可实现轻如云系统与第三方系统的无缝对接。

**doWebIndex()**

该方法将自动获取[单点登录code](https://www.yuque.com/shenwa/qingwork-dev/co6qmcz2kicd9wms#rbDqq)，然后把它和主类文件定义的`**$WebIndex**`变量拼接成第三方应用的自动登录入口，并自动跳转到该入口。该入口接收到code后，需要实现自动登录和鉴权等正确的响应流程，具体实现流程请查看[单点登录服务](https://www.yuque.com/shenwa/qingwork-dev/co6qmcz2kicd9wms#JALBT)的说明，或参考第三方应用示例。

**$WebIndex**

第三方系统入口链接，可携带参数跳转，不支持含#号的链接。该变量可以为空，如果为空则表示需要后台动态设置入口链接，可登录超管账号通过【平台管理】→【应用与服务】单独为每个平台设置。后台跳转到第三方应用时，第三方链接需要把获取的code通过单独登录接口请求鉴权信息。

**$SsoMaster**

请求鉴权的接口要求使用 `secret` 进行接口签名验证，该秘钥可通过单点登录服务获取和设置。

默认情况下，第三方应用使用的是系统唯一的`<font style="color:rgb(53, 53, 53);">masterSecret</font>`<font style="color:rgb(53, 53, 53);">进行签名验证，如果您需要每个平台单独使用不同的</font>`<font style="color:rgb(53, 53, 53);">secret</font>`<font style="color:rgb(53, 53, 53);">进行接口签名验证，可在 site.php 将公共变量</font>`**$SsoMaster**`<font style="color:rgb(53, 53, 53);">设为</font>`<font style="color:rgb(53, 53, 53);">false</font>`<font style="color:rgb(53, 53, 53);">即可。</font>

```php
namespace Addons\identifie;

class site extends QuickModule{

    public $WebIndex = 'https://www.baidu.com/sso';
    public $SsoMaster = false;

}
```





<h2 id="DqnXS">3 响应</h2>
所有的请求都要求按照 [响应](https://www.yuque.com/shenwa/qingru/gm56u4) 的规范返回数据，可以是视图、HTML/字符串、JSON、重定向或者其它《[响应](https://www.yuque.com/shenwa/qingru/gm56u4)》中约束的实例。

如果是必须要在方法内部强制跳转其它URL或者中断运行（exit()或die()）的情形，请务必在代码中断前运行` **session()->save();** `来保存会话，否则当前会话信息会丢失。

<font style="color:#DF2A3F;">本文档的响应内容仅对内置应用而言，第三方应用的响应由第三方实现。</font>

<h3 id="hkNdh">3.1 视图响应</h3>
在内置应用的控制器内，通过向请求的外部控制器返回`$this->View($data, $template)`方法来响应[Blade视图](https://learnku.com/docs/laravel/6.x/blade/5147)，通过`include $this->template($template);`方法来引用[HTML视图](https://www.yuque.com/shenwa/qingru/ts8lla)。

<h4 id="MCa8I">引用模块子视图（Blade）</h4>
Blade 的 `@include` 指令允许你从其它视图中引入 Blade 视图，但该视图仅限系统的公共视图（位于`/resources/views`目录下的视图）。

使用`@moduleView('**{$module}:{$platform}.{$view}**')`指令可以引入模块自带的其它视图。

`**{$module}**`：模块唯一标识，通过该参数可以让你引用其它模块的视图，如果是本模块内引用则不需要提供

`**{$view}**`：视图名称，与`@include`的参数规范一致

`**{$platform}**`：通道名称，可选值为“**web**”、“**app**”，表示前台视图和后台视图，默认会根据请求的路由自动匹配

```php
@moduleView('whotalk:web.header')
//等同于 @moduleView('whotalk:web/header')
//引用的是模块后台模板 /public/addons/whotalk/views/web/header.blade.php

//当后台请求时可缺省通道名称web：
@moduleView('whotalk:header')

//在本模块内的子视图引用时，可直接写作：
@moduleView('header')
```

<h3 id="DlHXS">3.2 接口响应</h3>
在内置应用的控制器内可以通过 `return response()->json($data);`指令直接返回接口数据，强烈建议返回统一格式的JSON数据，请参考：[统一响应结构](https://www.yuque.com/shenwa/qingru/gm56u4#wNjkT)。

<h3 id="oxMrW">3.3 抛出响应</h3>
应用模块的抛出响应请自行实现，或参考框架的[抛出响应](https://www.yuque.com/shenwa/qingru/gm56u4#SL5ze)。



<h2 id="k0bvM">4 用户机制</h2>
模块的用户机制和积分机制必须严格使用【用户服务】相关的方法和接口，确保各模块间的用户数据实时同步，同时可以通过总后台管理用户的基本信息。

详细的用户开发文档请参考：[用户服务开发文档](https://www.yuque.com/shenwa/qingwork-dev/wbnmkqyr631e6dut)



<h2 id="UyhdS">5 支付</h2>
模块的支付依赖微服务【支付服务】，该服务提供多个方法和接口，包括H5支付、APP支付、扫码支付（商户）、刷脸支付、支付分以及退款业务等，同时支持微信支付和支付宝，具备开发能力的开发者可以基于该服务开发更多支付接口，支付服务相关开发文档请参考：[支付服务](https://www.yuque.com/shenwa/tt5ahr/kp6e8h1cw4rxlgb8)。

**<font style="color:#DF2A3F;">第三方应用也可以使用支付服务的统一下单接口快速实现支付功能，详情请参考：</font>**[<font style="color:#117CEE;">统一下单接口</font>](https://www.yuque.com/shenwa/qingwork-dev/kp6e8h1cw4rxlgb8#jBhDt)

<h3 id="B5dhF">5.1 支付流程</h3>
1. 生成订单（选其一）
    1. [生成系统订单](https://www.yuque.com/shenwa/qingwork-dev/kp6e8h1cw4rxlgb8#SIUX4)（内置应用）
    2. 应用直付（内置应用H5端）
    3. [统一下单接口](https://www.yuque.com/shenwa/qingwork-dev/kp6e8h1cw4rxlgb8#jBhDt)（第三方应用）
2. 唤起支付工具（选其一）
    1. 跳转收银台（H5端）
    2. [微信支付下单](https://www.yuque.com/shenwa/qingwork-dev/kp6e8h1cw4rxlgb8#DjF5m)
    3. [支付宝下单](https://www.yuque.com/shenwa/qingwork-dev/kp6e8h1cw4rxlgb8#GjhFF)
3. [支付结果回调](#xE8E4)、[退款结果回调](#WopAT)

<h3 id="AIszb">5.2 应用直付（内置应用）</h3>
内置应用**在H5端内**可以直接通过 `return $this->pay($params)` 方法一键唤起收银台进入支付流程，只需要提供简单的参数即可。

该方法最终运行[支付服务的统一收银台](https://www.yuque.com/shenwa/qingwork-dev/kp6e8h1cw4rxlgb8#vZXhT)，成功时会返回一个[视图响应对象](https://learnku.com/docs/laravel/6.x/responses/5140#586a91)，将该对象直接返回给Laravel最外层控制器即可进入统一收银台。

```php
//site.php
class IdentifieModuleSite extends \App\Utils\WeModule{

	public function doMobilePay(){
		......
		$params = array(
			'tid'=>$paylog['tid'],			//内部订单号，必须
			'fee'=>$paylog['amount'],		//订单金额，必须
			'title'=>"测试充值{$paylog['amount']}元",					//商品描述，必须
			'user'=>$_W['member']['uid'],	//用户信息，非必须
			'openid'=>$_W['openid']			//微信JS-SDK（微信网页和小程序）内必须提供
		);
		return $this->pay($params);
	}
	
}
```

**参数说明**

| 参数 | 类型 | 说明 |
| --- | --- | --- |
| `<font style="color:#DF2A3F;">$params</font>` | Array | 订单数据：<br/>+ `tid`：内部订单号，必须<br/>+ `amount`：订单金额，创建时必须<br/>+ `subject`：商品描述，创建时必须<br/>+ `openid`：非必须，微信JS-SDK下单时需要提供<br/>+ `uniontid`：外部订单号，创建时如未提供将自动生成<br/>+ `isRecharge`：是否是充值行为，非必须 |


<font style="color:#DF2A3F;">注：应用的模块主类必须继承系统应用基类 </font>`<font style="color:#DF2A3F;">\App\Utils\WeModule</font>`

<h3 id="xE8E4">5.3 支付结果通知</h3>
通过支付服务唤起支付的订单，用户支付完成后，将通过应用模块主类的 `payResult($params)` 方法通知应用处理支付结果，只有支付成功才会调用该方法。

```php
/**
* 处理模块支付结果
* @param array $params 支付参数（result-结果, from-回调方式, type-支付方式, tid-内部订单号）
*/
public function payResult($params){
    global $_W,$_S;
    if ('success' == $params['result'] && 'notify' == $params['from']) {
        //支付回调（异步），处理支付结果
        //Todo something
    }

    if ('return' == $params['from']) {
        //支付回调（同步），处理支付结果
        //Todo something
        $redirect = ""; //此处可根据订单号查询出支付成功后跳转地址，如订单详情等
        if ('success' == $params['result']) {
            message('成功支付', $redirect, 'success');
        } else {
            message('支付失败，请重试', referer(), 'error');
        }
    }

    return $params;
}
```

`**payResult()**`方法传递一个数组参数 `**$params**`，内部大致结构如下：

+ `**$params['result']**`：支付结果；只有当值为 success 的时候表示支付成功
+ `**$params['from']**`：回调方式，可能值为 return 和 notify ,当值为return是表示是URL同步通知，此时只需要页面提示用户支付成功，不需要处理任何数据。当值为 notify 时，表示是接口的异步通知，此时需要做好支付成功后的数据和业务处理
+ `**$params['tid']**`：内部订单号
+ `**$params['type']**`：支付方式，可能值为 wechat（微信支付）、alipay（支付宝）、credit（余额/积分）

<h3 id="WopAT">5.4 退款结果通知</h3>
通过支付服务唤起支付的订单，退款成功后，将通过应用模块主类的 `refundResult($params)` 方法通知应用处理支付结果，只有退款成功才会调用该方法。

<font style="color:#DF2A3F;">为避免退款信息无法通知到该方法，请务必使用支付服务的 </font>`**<font style="color:#DF2A3F;">Refund()</font>**`<font style="color:#DF2A3F;"> 方法发起退款。</font>

```php
/**
 * 退款结果通知
 * @param array $params 退款结果数据（out_trade_no支付单号、tid内部订单号、total_amount支付金额、refund_amount退款金额）
 * @return bool
*/
public function refundResult($params){
    //Todo something 处理退款订单
    return true;
}
```

	`**refundResult()**`方法传递一个数组参数 `**$params**`，内部大致结构如下：

+ `**$params['out_trade_no']**`：外部订单号
+ `**$params['tid']**`：内部订单号
+ `**$params['total_amount']**`：支付金额
+ `**$params['refund_amount']**`：退款金额

