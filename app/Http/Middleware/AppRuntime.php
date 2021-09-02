<?php

namespace App\Http\Middleware;

use App\Services\AttachmentService;
use App\Services\MemberService;
use App\Services\SettingService;
use Closure;

define('IN_MOBILE', true);

class AppRuntime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        global $_W;
        SettingService::Load();
        $_W['page'] = $_W['setting']['page'];
        $uniacid = $request->input('i',0);
        if (empty($uniacid)) abort(404,'找不到该平台');
        $_W['uniacid'] = intval($uniacid);
        $_W['account'] = uni_fetch($uniacid);
        $_W['openid'] = session()->get('openid','');
        $_W['member'] = array('uid'=>0);
        //自动登录
        $authtoken = $request->header('x-auth-token');
        if (!empty($authtoken)){
            MemberService::UniAuth($authtoken);
        }
        if (!$_W['member']['uid'] && !empty($_W['openid'])){
            MemberService::AuthFetch($_W['openid']);
        }
        $_W['attachurl'] = AttachmentService::SetAttachUrl();
        include_once base_path("bootstrap/functions/app.func.php");
        return $next($request);
    }
}
