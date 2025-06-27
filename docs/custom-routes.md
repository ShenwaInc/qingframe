# 模块自定义路由使用指南

## 概述

本系统允许模块和微服务定义完全自定义的路由，不受 `/m/{moduleName}` 前缀限制。这些路由将直接注册到主路由系统中。

## 使用方法

### 1. 创建自定义路由文件

#### 为模块创建
```bash
php artisan module:custom-route mymodule
```

#### 为微服务创建
```bash
php artisan module:custom-route ucenter --server
```

### 2. 编辑自定义路由文件

创建完成后，您会在以下位置找到自定义路由文件：
- 模块：`public/addons/mymodule/custom_routes.php`
- 微服务：`servers/ucenter/custom_routes.php`

### 3. 定义自定义路由

在 `custom_routes.php` 文件中，您可以定义任何类型的路由：

```php
<?php

use Illuminate\Support\Facades\Route;

// 简单的自定义页面
Route::get('/my-custom-page', function () {
    return 'This is my custom page!';
});

// 带控制器的路由
Route::get('/custom/{id}', 'CustomController@show')->name('custom.show');

// API路由
Route::group(['prefix' => 'api/v1'], function () {
    Route::get('/data', 'ApiController@getData');
    Route::post('/data', 'ApiController@storeData');
});

// 需要认证的路由
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', 'DashboardController@index');
    Route::get('/profile', 'ProfileController@show');
});

// RESTful路由
Route::resource('items', 'ItemController');

// 带中间件的路由
Route::get('/admin', 'AdminController@index')->middleware(['auth', 'admin']);
```

## 路由类型示例

### 1. 简单页面路由
```php
Route::get('/about', function () {
    return view('about');
});
```

### 2. 带参数的路由
```php
Route::get('/user/{id}/posts/{post_id}', 'UserPostController@show');
```

### 3. 多种HTTP方法
```php
Route::match(['get', 'post'], '/contact', 'ContactController@handle');
```

### 4. 路由组
```php
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', 'AdminController@index');
    Route::get('/users', 'AdminController@users');
    Route::get('/settings', 'AdminController@settings');
});
```

### 5. API路由组
```php
Route::group(['prefix' => 'api/v1', 'middleware' => 'auth:api'], function () {
    Route::get('/users', 'UserApiController@index');
    Route::post('/users', 'UserApiController@store');
    Route::get('/users/{id}', 'UserApiController@show');
    Route::put('/users/{id}', 'UserApiController@update');
    Route::delete('/users/{id}', 'UserApiController@destroy');
});
```

### 6. 域名限制路由
```php
Route::domain('api.example.com')->group(function () {
    Route::get('/status', 'StatusController@index');
});
```

## 控制器示例

### 基础控制器
```php
<?php

namespace Addons\mymodule\Controllers;

use Illuminate\Http\Request;

class CustomController extends BaseController
{
    public function show($id)
    {
        return view('custom.show', ['id' => $id]);
    }
    
    public function store(Request $request)
    {
        // 处理数据存储
        return response()->json(['success' => true]);
    }
}
```

### API控制器
```php
<?php

namespace Addons\mymodule\Controllers;

use Illuminate\Http\Request;

class ApiController extends BaseController
{
    public function getData()
    {
        return response()->json([
            'data' => 'Your data here',
            'timestamp' => now()
        ]);
    }
    
    public function storeData(Request $request)
    {
        // 验证和处理数据
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email'
        ]);
        
        // 存储数据
        return response()->json(['success' => true]);
    }
}
```

## 注意事项

### 1. 路由名称冲突
由于自定义路由直接注册到主路由系统，请确保路由名称不冲突。建议使用模块名称作为前缀：

```php
// 推荐：使用模块名称作为前缀
Route::get('/mymodule/custom-page', 'CustomController@index')->name('mymodule.custom.page');

// 避免：可能冲突的路由名称
Route::get('/custom-page', 'CustomController@index')->name('custom.page');
```

### 2. 控制器命名空间
控制器应该放在模块的 `Controllers` 目录下，并使用正确的命名空间：

```php
// 模块控制器
namespace Addons\mymodule\Controllers;

// 微服务控制器
namespace Server\ucenter\Controllers;
```

### 3. 视图路径
视图应该放在模块的 `views` 目录下：

```php
// 引用模块视图
return view('mymodule::custom.show', $data);
```

### 4. 中间件使用
可以使用任何Laravel中间件：

```php
Route::group(['middleware' => ['auth', 'admin', 'throttle:60,1']], function () {
    // 需要认证、管理员权限和限流的路由
});
```

## 最佳实践

### 1. 路由组织
按功能组织路由：

```php
// 公开路由
Route::get('/public', 'PublicController@index');

// 用户路由
Route::group(['middleware' => 'auth'], function () {
    Route::get('/user/profile', 'UserController@profile');
    Route::post('/user/profile', 'UserController@updateProfile');
});

// 管理员路由
Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('/admin/dashboard', 'AdminController@dashboard');
    Route::resource('/admin/users', 'AdminUserController');
});
```

### 2. 错误处理
在控制器中统一处理错误：

```php
public function store(Request $request)
{
    try {
        // 业务逻辑
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
```

### 3. 路由缓存
在生产环境中，记得缓存路由：

```bash
php artisan route:cache
```

## 故障排除

### 路由不生效
1. 检查 `custom_routes.php` 文件是否存在
2. 清除路由缓存：`php artisan route:clear`
3. 检查路由语法是否正确

### 控制器找不到
1. 检查命名空间是否正确
2. 运行 `composer dump-autoload`
3. 检查控制器文件是否存在

### 权限问题
1. 检查中间件配置
2. 确保用户有相应权限
3. 查看权限中间件日志 