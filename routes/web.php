<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Middleware\App;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\ConsolePermission;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$appSecurityEntrance = env("APP_SECURITY_ENTRANCE");
if (!empty($appSecurityEntrance) && $appSecurityEntrance!="/"){
    Route::get("/$appSecurityEntrance", function (Request $request){
        $request->session()->put("securityEntrance", random(12));
        return redirect("/login");
    });
}

Route::get('/', function (Request $request){
    try {
        $installed = Schema::hasTable("account");
    }catch (\Exception $exception){
        if(in_array($exception->getCode(), [1044, 1045, 2002])){
            $installed = false;
        }else{
            throw $exception;
        }
    }
    if (!$installed){
        return response()->redirectTo('installer');
    }

    global $_W;
    $App = new App();
    $App->initialize($request);

    $uniacid = (int)DB::table('uni_settings')->where('bind_domain', $request->server('HTTP_HOST'))->value('uniacid');
    if (!empty($uniacid) && env('APP_FORCE_DOMAIN', false)){
        if (Auth::check()){
            return redirect("/console/account/{$uniacid}");
        }else{
            return redirect("/login/{$uniacid}");
        }
    }
    $locale = $request->input('lang', $_W['locale']);
    if (!empty($locale) && $locale!=$_W['locale']){
        \Illuminate\Support\Facades\App::setLocale($locale);
        session()->put('FRAME_LOCALE', $locale);
    }
    SettingService::Load();
    $language = serv('language');
    $views = ['welcomeCustom', 'welcome'];
    if (!empty($uniacid)){
        $views = ['welcomeCustom'.$uniacid, 'welcomeCustom', 'welcome'];
    }
    return response()->view($views, array('title'=>__($_W['setting']['page']['title']), 'Multilingual'=>$language->enabled, 'locale'=>$locale));
});

Route::group(['prefix' => 'auth','namespace'=>'Auth', 'middleware'=>['app']],function (){
    Route::post('/login', 'AuthController@Login');
    Route::post('/logout', 'AuthController@Logout');
});

Route::group(['namespace'=>'Auth', 'middleware'=>['app']],function (){
    Route::get('/login/{uniacid}', 'AuthController@Entry')->where('uniacid','[0-9]+');
});

Route::group(['prefix' => 'wem','namespace' => 'App', 'middleware'=>['app','runtime']],function (){
    Route::match(['get', 'post'],'/{modulename}/{do?}', 'ModuleController@entry');
    Route::post('/subscribe/{action}', 'ModuleController@subscribe');
});

Route::group(['prefix' => 'console', 'namespace' => 'Console', 'middleware'=>[Authenticate::class, 'app', ConsolePermission::class]], function () {
    Route::get('/', 'PlatformController@index');
    Route::get('/util/{op?}', 'UtilController@index');
    Route::post('/util/{op?}', 'UtilController@save');
    Route::post('/setting', 'SettingController@save');
    Route::match(['get', 'post'],'/active', 'SettingController@active');
    Route::match(['get', 'post'], '/setting/{op?}', 'SettingController@index');
    Route::get('/account/{uniacid}', 'PlatformController@checkout')->where('uniacid','[0-9]+');
    Route::match(['get', 'post'],'/account/{action}', 'AccountController@index')->where('action','[a-z]+');
    Route::match(['get', 'post'],'/user/{op?}', 'UserController@index');
    Route::match(['get', 'post'],'/m/{modulename}/{do?}', 'ModuleController@entry');
    Route::match(['get', 'post'],'/module/{option?}', 'ModuleController@index');
    Route::get('/server', 'ServerController@index');
    Route::get('/server/account', 'ServerController@checkout');
    Route::get('/server/apis/{server}', 'ServerController@Apis');
    Route::get('/server/methods/{server}', 'ServerController@Methods');
    Route::match(['get', 'post'], '/report/{option?}', 'ReportController@httpReq');
});

Route::group(['prefix'=>'server', 'namespace' =>'Console', 'middleware'=>[Authenticate::class, 'app', ConsolePermission::class]],function (){
    Route::any('/{server}/{segment1?}/{segment2?}', 'ServerController@HttpRequest');
});

Route::group(['prefix'=>'payment', 'namespace' => 'App', 'middleware'=>['app']],function (){
    Route::any('/{payment}', 'PaymentController@notify')->where('payment','[a-z]+');
    Route::match(['get', 'post', 'option'], '/return/{payment}', 'PaymentController@response');
});

Route::group(['prefix'=>'installer', 'middleware'=>['app']],function (){
    Route::get('/', 'InstallController@index');
    Route::post('/agreement', 'InstallController@agreement');
    Route::get('/database', 'InstallController@database');
    Route::post('/database', 'InstallController@dbDetect');
    Route::get('/render', 'InstallController@render');
    Route::post('/render', 'InstallController@install');
    Route::get('/complete', 'InstallController@complete');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin/{modulename}', function (Request $request, $moduleName){
    if (empty($request->user())){
        $referer = "/login?referer=console/m/$moduleName";
        return response()->redirectTo($referer);
    }
    return response()->redirectTo("console/m/$moduleName");
});
