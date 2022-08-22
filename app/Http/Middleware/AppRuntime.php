<?php

namespace App\Http\Middleware;

use App\Services\AccountService;
use App\Services\FileService;
use App\Services\MemberService;
use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;

define('IN_MOBILE', true);

class AppRuntime
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        global $_W;
        SettingService::Load();
        $_W['page'] = $_W['setting']['page'];
        $uniacid = $request->input('i',0);
        if (empty($uniacid)) abort(404,'找不到该平台');
        $_W['uniacid'] = intval($uniacid);
        $_W['account'] = AccountService::FetchUni($uniacid);
        $_W['openid'] = session()->get("openid$uniacid",'');
        $_W['member'] = array('uid'=>0);
        //自动登录
        $authToken = $request->header('x-auth-token');
        if (!empty($authToken)){
            MemberService::UniAuth($authToken);
        }
        if (!$_W['member']['uid'] && !empty($_W['openid'])){
            MemberService::AuthFetch($_W['openid']);
        }
        if (!$_W['member']['uid']){
            $member = session("_app_member_session_{$uniacid}_",array());
            if (!empty($member)){
                MemberService::AuthLogin($member, false);
            }
        }
        $_W['attachurl'] = FileService::SetAttachUrl();
        return $next($request);
    }
}
