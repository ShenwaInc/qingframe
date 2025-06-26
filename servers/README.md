<h2 id="Wpmq3">微服务概述</h2>
<h3 id="ajsG8">什么是微服务</h3>
微服务是将某一类事务的常用方法进行封装并向整个系统提供对外服务的公共方法的一种轻量型应用，可以理解为是脚手架的完整封装形式。

以存储服务为例，Laravel本身提供了 Flysystem 拓展包和 Storage 对象来对文件的各项操作，但是并没有提供可视化的文件统一管理的功能，虽然提供了 Amazon S3 和 FTP 驱动的对接功能，但是并没有提供可视化的对接配置项功能，其在 `config/filesystems.php` 中配置的方式不支持多租户的系统，因此轻如云系统提供了一个完整封装的存储服务，管理员可以在后台便捷的管理各种配置项，包括文件上传的参数配置项（例如后缀名约束、文件体积约束以及其它第三方驱动、云存储的密钥等等）、文件管理等功能，同时向整个系统提供对外的服务接口和内置方法（例如文件上传、文件删除、文件选择器等）。

轻如云系统提倡将单一应用程序划分成一组小的服务，服务之间互相协调、互相配合，为用户提供最终价值。每个服务运行在其独立的进程中，服务与服务间采用轻量级的通信机制互相沟通（通常是基于HTTP的RESTful API）。



<h3 id="klEsZ">为什么要微服务</h3>
在开发大型应用时，我们往往会把某一类的常用函数创建一个服务或者模型来处理相关的逻辑，便于我们在控制器内随时引用。但问题是，这些模型或者服务更像是把辅助函数进行归类堆砌，无法处理更复杂的业务逻辑，也不具备独立运行的条件，更不能提高业务逻辑的复用性。即便是通过composer安装的完整业务逻辑依赖包，很多时候也不得不开发对应的配置功能来结合才能使用。

尤其在开发多拓展应用的软件时，我们常常因为某些功能相近的代码块的维护和迭代感到力不从心，也常常因为反复造轮子而感到疲倦。这些问题在云市场上的一些应用表现得特别明显，往往客户只需要一只蚊子腿，却不得不为了烹饪蚊子而架设炉子甚至建厨房，每个独立的应用最终都变成了全家桶，几乎每个不同品类、不同用途的软件里面，相重合的功能高达60%以上。因此而来的巨额成本只能由客户来承担，而开发者同样为了开发和维护这些功能而浪费了很大的精力。

为了解决这些问题，微服务应运而生。



<h3 id="zzApW">何时需要微服务</h3>
任何的业务逻辑，都可以作为一个微服务来**向不同应用模块、其它微服务、甚至是框架提供服务**。每个微服务都自成体系，有自己的框架和规范，只需要提供统一的接入标准来告知开发者如何引用即可。

和传统的服务或者模型不同，微服务更偏向于业务型的逻辑服务，通常会将某些在应用开发者必不可少，但又和软件主要业务逻辑关联不大的功能块作为微服务来运行，便于向多个不同的应用和软件提供对应的服务，例如用户服务、支付服务、权限服务、消息推送服务等等。

当开发一个新应用时，我们通常会专注于应用自身的业务逻辑开发，其它大量的同质性的开发工作（例如用户相关功能、文件相关功能、支付相关功能、短信相关功能）均交给微服务来实现。



<h3 id="blcqx">微服务能带来什么</h3>
1. 提高软件功能块的复用性、耦合性、灵活性，降低软件主体底层功能的维护成本；
2. 提高软件可拓展性，和主体功能的高可用性；
3. 使用者接入轻如云市场，可低成本快速对接海量优质云服务；
4. 即装即用，符合前沿的优秀程序设计逻辑；
5. 开发者更专注于业务逻辑本身，大幅度降低开发周期和开发成本；
6. 开发者避免重复造轮子，还可以减轻多个软件同一个轮子的迭代压力；
7. 开发者可将自身的开发积累快速发布为微服务，技术积累同时变现增收；
8. 解决市场上开发能力过剩，而需求方却始终找不到满意产品和服务的问题；
9. 需求方可以在鱼龙混杂的开发者市场中低成本快速定制符合需求的服务，也可以让开发者快速将现有服务应用到产品中；
10. 微服务市场诞生后，大家会看到许多有意思的微服务。



<h2 id="dLplj">快速构建</h2>
使用轻如云框架可以通过 Artisan 工具一键生成微服务安装包，详情请参考[开发者模式](https://www.yuque.com/shenwa/qingru/xsi1e1p9d59k5981)

```shell
php artisan make:service 'identity'
#其中identity为微服务标识
```

<h2 id="FL5n7"></h2>
<h2 id="dlKPe">安装包路径</h2>
> 所有的微服务统一放在站点下的同一目录，该目录位置并非固定路径，可按实际情况存放，一般是站点根目录的 **/servers/ **目录下，该路径定义在常量 **<font style="color:#cc0000;background-color:#f7faff;">MICRO_SERVER</font>** ，以下简称微服务目录
>

```php
<?php
/**
 * 常量定义
 *
 * @author 神蛙科技
 * @url
 */
defined('IN_IA') or exit('Access Denied');


//定义微擎框架下的微服务目录
define('MICRO_SERVER', IA_ROOT."/swa_microserver/");
```





<h2 id="rhkul">微服务文件结构</h2>
> 每个微服务都需要作为一个单独的文件夹放在微服务目录下。原则上该文件夹**<font style="color:#F5222D;">必含的文件只有描述文件和模型文件</font>**，其它的文件结构、引用方式甚至是路由都由服务提供者自行定义和设计。
>

/servers/identity/

------------------manifest.json

------------------IdentityService.php





<h2 id="gVANq">描述文件</h2>
> 微服务的描述文件存放在对应服务的根目录下，统一命名 **<font style="color:#F5222D;">manifest.json</font>** 。描述文件必须严格按照下方示例的规范，否则将无法解析。
>

```json
{
    "application": {
        "identity": "ucenter",			//服务标识，唯一且必须
        "name": "用户管理",
        "cover": "server/ucenter/icon.png",
        "summary": "提供统一用户管理功能，包括登录、注册、会员资料、会员等级等会员相关管理功能",
        "version": "1.0.7",
        "releases": "2021123101"
    },
    "drive": "php",					//服务的主要运行语言，默认为PHP
    "entrance": "member",		//微服务后端入口路由，也可以在模型文件的 getEntry() 方法返回，支持第三方链接
    "require":[
        {"id":"weengine","summary":"WeEngine框架兼容"}	 //依赖的微服务，安装微服务时将自动判断是否已安装对应服务
    ],
    "apis": {
        "wiki": "https://ucenter-shenwa.doc.coding.io",	//微服务API接口文档
        "schemas": []
    },
    "methods": {
        "wiki": "",								//内置方法统一说明文档
        "register": {
            "name": "用户注册",
            "summary": "统一用户注册方法",
            "wiki": "",
            "listener": "uc.register",
            "params": {
                "username":["用户名，仅限手机号或邮箱","string"],
                "password": ["登录密码","string|null"],
                "profile": ["会员资料","array|null"]
            },
            "return": ["会员信息","array|error"]
        }
    },
    "components": {
        "wiki": ""
    },
    "resources": [],
    "install": {
        "content": "install.php",				//微服务安装脚本
        "drive": "php"							//微服务安装脚本类型，可选php（PHP文件）、phpscript（PHP脚本，慎用）、sql（SQL脚本）、shell（shell文件）、shellscript（shell脚本，慎用）
    },
    "upgrade": {
        "content": "upgrade.php",				//升级脚本
        "drive": "php"							//升级脚本类型，同安装脚本类型
    },
    "uninstall": {
        "content": "",							//卸载脚本
        "drive": "sql"							//卸载脚本类型，同安装脚本类型
    }
}
```

<h3 id="SQXJa">依赖</h3>
如果微服务需要依赖其它服务，请在**<font style="color:#660e7a;">require</font>**字段申明，该字段结构请参考以上示例。如果系统未安装其依赖的服务时，会自动进入微服务安装引导流程。

<h3 id="FZkCD">API接口说明(APIS)</h3>
微服务可以在安装包的描述文件内申明该服务所提供的API接口，只需要在 **<font style="color:#660e7a;">apis</font>** 字段申明即可，支持外部的API说明文档（**<font style="color:#660e7a;">wiki</font>**字段），也支持提供API接口的实例结构（**<font style="color:#660e7a;">schemas</font>**字段），系统将自动按照该描述文件的接口实例生成API接口文档。

当API接口较多时，可以通过服务模型文件的 [getApis()](https://www.yuque.com/shenwa/qingru/kh3of6#iaWa2) 方法返回接口文档和实例。	

<h3 id="HdPfa">内置方法说明(METHODS)</h3>
微服务需要对外提供内置的方法时，可以在描述文件的 methods 节点说明各个方法的详细信息。 methods 节点是一个由多个方法对象组成的对象，对象的每个元素表示一个方法，元素的Key即方法名。每个元素的结构大致如下：

name：String类型，方法的对外名称

summary：String类型，方法的概要说明

wiki：String类型，该方法的外部说明文档

listener：String类型，该方法会触发哪个监听器

params：Object类型，该方法各项参数的详解，对象的每个元素表示一个参数，元素的Key即参数名，每个元素是一个包含两个元素的数组组成，分别表示参数的对外名称和数据类型

return：Array类型，包含两个元素的数组，分别表示返回的信息说明以及返回的数据类型

<h3 id="lDuOc">注意事项</h3>
1. 微服务的描述文件必须严格按照上方的示例的结构，否则可能无法正常解析（请务必去掉注释）；
2. 微服务升级时，只需要将版本号及发布号调高，并编写好升级脚本后，到后台更新即可；
3. 在填写依赖时，<font style="color:#E8323C;">请注意避免服务相互依赖的问题</font>；
4. 内置方法的<font style="color:#E8323C;">广播事件名请务必确保唯一性</font>。





<h2 id="SOz8M">模型文件</h2>
```php
<?php

namespace Server\storage;

class UcenterService extends MicroService {

    //定义微服务标识符
    public $identity = 'ucenter';

    public function __construct($uniacid=0){
        parent::__construct($this->identity);
        // Todo something
    }

    public static function foo(...$any){
        // Todo something
    }

    //获取服务的内置方法（如使用manifest.json的方法列表则可去掉该方法）
    public function getMethods($data=array()){
        return parent::getMethods($data);
    }

    //自定义服务的API接口文档（如使用manifest.json的接口列表则可去掉该方法）
    public function getApis($data = array()){
        return parent::getApis($data);
    }

    /**
  * 自定义接管路由（如使用默认路由则可去掉该方法）
  * @param string|null $platform 路由通道，可选web、app、api及自定义通道
  * @param string|null $route 路由名称
  * @return array|error 返回数据
  */
    public function HttpRequest($platform="web", $route=""){
        // Todo something
        return parent::HttpRequest($platform, $route);
    }

}
```

<h3 id="zAq5m">继承</h3>
微服务的模型必需要继承 **<font style="color:#0000ff;background-color:#f7faff;">MicroService</font>** 类，并在构造函数<font style="color:#0000ff;background-color:#f7faff;">__construct</font> 中初始化该类。**<font style="color:#0000ff;background-color:#f7faff;">MicroService</font>** 类提供了许多内置的公共方法，便于在管理服务、调用服务时达到统一规范的目的。相关的内置公共方法详解请参考：[父类公共方法](https://www.yuque.com/shenwa/evz0h9/kh3of6)。

<h3 id="yOmEq">路由</h3>
API路由

```php
Route::group(['prefix'=>'server', 'middleware'=>['app']],function (){
    Route::any('/run/{server}/{segment1?}', 'HttpController@ServerRun')->where('server','[a-z]+');
    Route::any('/{server}/{segment1?}/{segment2?}', 'HttpController@ServerApi')->where('server','[a-z]+');
});

//例如 /api/server/ucenter/route1 将会运行控制器 HttpController 的 ServerApi() 方法，在该方法中将会执行 UcenterService->HttpRequest('api', 'route1');
//如果微服务没有重定义 HttpRequest() 方法，按默认的微服务路由将会运行控制器 Server\ucenter\api\Route1Controller 的 main() 方法。
```

所有以<font style="color:#E8323C;">HTTP的方式访问该服务</font>都会运行该服务模型文件的 **<font style="color:#0000ff;background-color:#f7faff;">HttpRequest()</font>**  方法，并通过 <font style="color:#0066ff;background-color:#f7faff;">$platform</font> 参数区分通道，支持web（后台页面，默认）、app（前台页面）、api（前台接口）及自定义通道。

微服务提供者可以在该方法内定义自己的路由规则，来实现任何功能。

为了便于快速开发，我们在该模型文件所继承的 **<font style="color:#0000ff;background-color:#f7faff;">MicroService</font>** 类内也添加了该方法，并编写了一套贴近MVC框架的路由规则，这意味着在正常情况下，您不需要重新定义路由规则，只需要在该服务目录下创建对应通道的文件夹（如web/），并在对应的文件夹内创建控制器来实现需要的功能即可。

如果要访问默认路由指定的控制器，则对应的URL需要使用 **<font style="color:#0000ff;background-color:#f7faff;">MicroService</font>** 类的 [**<font style="background-color:#f7faff;">url()</font>** 方法](https://www.yuque.com/shenwa/evz0h9/kh3of6#aa8dR)和 [**<font style="background-color:#f7faff;">api() </font>**方法](https://www.yuque.com/shenwa/evz0h9/kh3of6#RPJq2) 来生成，查看[详细说明](https://www.yuque.com/shenwa/evz0h9/vnyayi)。





<h2 id="aMiIX">其它规范</h2>
<h3 id="QjXKT">PSR-4</h3>
微服务目录内的所有PHP文件都支持通过PSR-4（<font style="color:rgba(0, 0, 0, 0.9);">PHP Standard Recommendations 4</font>）规范来实现自动引用，具体说明请参考：[PHP: Hypertext Preprocessor](https://www.php.net/manual/zh/language.namespaces.rationale.php)

<h3 id="kXvUk">静态文件</h3>
考虑到微服务目录可能会放在外部无法访问的目录内（如laravel、phpcmf等框架内），服务目录建议不要包含静态文件，所有需要的静态文件请放在云存储等第三方云平台上，便于统一引用、管理。如果必须要放在微服务目录下时，建议使用软链接的方式关联到站点的开放目录下。

如果必须要将静态资源文件或依赖库等放在项目内且可以通过外部访问，可把它们放在微服务目录下的 `res` 目录下，并通过 `serv($identity)->res($path)` 方法来获得单个资源文件的URL。

```php
$avatar = serv("ucenter")->res("avatar.png");

dd($avatar);
//https://yourdomain.com/resource/server/ucenter/avatar.png
```

![](https://cdn.nlark.com/yuque/0/2024/png/1333431/1728546344405-5e900a18-3cda-4114-9b8d-3b899474800f.png)







