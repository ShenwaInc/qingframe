框架默认支持laravel的[Blade模板](https://learnku.com/docs/laravel/6.x/blade/5147)引擎，为了兼容其它开发框架及快速开发，轻如云提供了一套类似Smarty引擎的HTML服务端编译模板引擎。



## 规范
### 模板位置
HTML模板文件默认存放在 `/resources/template/`目录下，后缀名为`.html`格式，文件内即为HTML页面的模板代码，以[模板标签语法](#qvtSF)来提供编译引擎动态展示数据。

微服务的HTML模板存放路径为：`/servers/**{$service}**/template/**{$platform}/{$templateName}**.html`，详见微服务[响应](https://www.yuque.com/shenwa/qingru/dsz8uazm1m1rk3h0#vRllC)

`**{$service}**`：微服务标识

`**{$platform}**`：当前路由通道名称，该名称根据当前的请求决定。后台请求固定为`**web**`，前台请求可以根据业务在[生成URL](https://www.yuque.com/shenwa/qingru/vnyayi#AzfjH)时自定义，默认为`**app**`。

`**{$templateName}**`：模板名称

应用模块的HTML模板存放路径为：`/public/addons/**{$moduleName}**/template/**{$templateName}**.html`,详见应用模块[规范](https://www.yuque.com/shenwa/qingru/dae7wpppwv8lhzrz#hkNdh)

`**{$moduleName}**`：应用标识

`**{$templateName}**`：模板名称

### 快速开始
```php
//编译渲染 /resources/template/web/composer.html 的模板并返回视图

if (!function_exists('tpl_include')){
    //引用模板引擎
    include_once app_path("Helpers/smarty.php");
}
//渲染模板文件并返回HTML视图
return include tpl_include("web/composer");
```



## 模板标签语法
### <font style="color:rgb(36, 41, 46);">输出变量</font>
```html
{$foo} 
```

<font style="color:rgb(36, 41, 46);">输出标签是由一对花括号做为定界符的，不支持输出数组，相对于php中的 echo</font>

#### <font style="color:rgb(36, 41, 46);">示例</font>
```html
<div class="user-head ellipsis">{$_W['fans']['nickname']}</div> 
// 对等于  <div class="user-head ellipsis"><?php echo $_W['fans']['nickname']; ?></div> 
```

### <font style="color:rgb(36, 41, 46);">条件判断</font>
```html
{if condition}
//Todo something
{elseif condition}
//Todo something
{else}
//Todo something
{/if} 
```

<font style="color:rgb(36, 41, 46);">条件选择分支</font>

#### <font style="color:rgb(36, 41, 46);">示例</font>
<font style="color:rgb(36, 41, 46);">如果 $do 等于 record 则显示下面的卡券领取记录，否则显示查看卡券</font>

```html
{if $do == 'record'}
<li class="active"><a href="javascript:;">卡券领取记录</a></li>
{else}
<li class="active"><a href="javascript:;">查看卡券</a></li>
{/if}
```

<font style="color:rgb(36, 41, 46);">if 和 elseif 配合使用</font>

```html
{if $dca['status'] == 1}
	<span class="label label-success">未使用</span>
{elseif $dca['status'] == 2}
	<span class="label label-warning">已失效</span>
{elseif $dca['status'] == 3}
	<span class="label label-danger">已核销</span>
{elseif $dca['status'] == 4}
	<span class="label label-default">已删除</span>
{else}
	<span class="label label-default">全部</span>
{/if}
```

### <font style="color:rgb(36, 41, 46);">循环语句</font>
```html
{loop $result $key $value}
{/loop}
```

<font style="color:rgb(36, 41, 46);">循环遍历语句，相当于PHP中的</font>

```php
foreach ($result as $key => $value) {

}
```

#### <font style="color:rgb(36, 41, 46);">参数</font>
+ **<font style="color:rgb(36, 41, 46);">$key</font>**<font style="color:rgb(36, 41, 46);"> </font><font style="color:rgb(36, 41, 46);">可以使用其它名称，将存储数组中每一成员的键值</font>
+ **<font style="color:rgb(36, 41, 46);">$value</font>**<font style="color:rgb(36, 41, 46);"> </font><font style="color:rgb(36, 41, 46);">可以使用其它名称，将存储数组中每一成员的值</font>

#### <font style="color:rgb(36, 41, 46);">示例</font>
<font style="color:rgb(36, 41, 46);">循环一个数组用 loop 标签，与php中的 foreach 函数类似，第一个参数为数组的索引，第二个参数为数组第一项的值，只要标签成对匹配，模板中的标签是可以嵌套使用的。</font>

```html
{loop $list $index $item}
<tr>
  <td>{$index}（显示数组的索引）：</td>
  <td>{$item['user']['nickname']}</td>
  <td>
    {if $item['follow'] == '1'}
    <span class="label label-success">已关注 </span> 
    {elseif $item['unfollowtime'] <> '0'}
      <span class="label label-warning" >取消关注 </span>
      {else}
      <span class="label label-danger">未关注 </span>
      {/if}
  </td>
</tr>
{/loop}
```

### <font style="color:rgb(36, 41, 46);">php解释</font>
```html
{php expression} 
```

<font style="color:rgb(36, 41, 46);">运行一个PHP原生代码，不支持多行php语句</font>

#### <font style="color:rgb(36, 41, 46);">参数</font>
+ **<font style="color:rgb(36, 41, 46);">expression</font>**<font style="color:rgb(36, 41, 46);"> </font><font style="color:rgb(36, 41, 46);">符合PHP语法的表达式</font>

#### <font style="color:rgb(36, 41, 46);">示例</font>
<font style="color:rgb(36, 41, 46);">由于某些时候需要使用一些php来输出内容，比如格式化时间戳为日期时，此写法并不支持多行php语句，例如：</font>

```html
<span class="help-block">{ php echo date('Y-m-d H:i:s', $row['followtime'])}</span> 
```

<font style="color:rgb(36, 41, 46);">或者</font>

```php
<？php echo 1; ?> 
```

### <font style="color:rgb(36, 41, 46);">模板嵌套</font>
```html
{template '$templatename'}
```

<font style="color:rgb(36, 41, 46);">引用一个模板文件</font>

#### <font style="color:rgb(36, 41, 46);">参数</font>
+ **<font style="color:rgb(36, 41, 46);">$templatename</font>**<font style="color:rgb(36, 41, 46);"> </font><font style="color:rgb(36, 41, 46);">模板名称或是路径+模板名称</font>

#### <font style="color:rgb(36, 41, 46);">示例</font>
<font style="color:rgb(36, 41, 46);">在模板中如果需要引用其它模板文件可以使用以下的方法：</font>

```html
//模块中的使用方法，不需要添加目录信息
{template 'header'}

//系统的模板引用的方法，需要添加目录信息
{template 'common/header-base'}

//引用微服务的模板
{template 'server/ucenter:header'}
```



### <font style="color:rgb(36, 41, 46);">*特殊标签</font>
```html
//输出表单的token文本域，便于兼容不同系统和框架的表单CSRF验证功能
{csrftoken}

//自动输出页内弹窗的ID，便于处理弹窗的异步重载功能
{ajaxhash}
```



