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
    public function handle(Request $request, Closure $next){
        $uniacid = $request->input('i', SITEACID);
        $this->Runtime($uniacid, $request->header('x-auth-token'));
        return $next($request);
    }

    public function Runtime($uniacid, $authToken=null){
        if (empty($uniacid)) abort(404,'找不到该平台');
        global $_W;
        $_W['session_id'] = "";
        $state = \request()->input("state", "");
        if (!empty($state) && \Str::startsWith($state, "we7sid-")){
            $_W['session_id'] = str_replace("we7sid-", "", $state);
            session()->setId($_W['session_id']);
        }else{
            $_W['session_id'] = session()->getId();
        }
        session()->start();
        $_W['uniacid'] = intval($uniacid);
        $_W['account'] = AccountService::FetchUni($uniacid);
        $_W['acid'] = intval($_W['account']['acid']) ?? $_W['uniacid'];
        $_W['openid'] = session()->get("openid".$uniacid,'');
        $_W['member'] = array('uid'=>0);
        $_W['oauth_account'] = $_W['account']['oauth'] = array(
            'key' => $_W['account']['key'],
            'secret' => $_W['account']['secret'],
            'acid' => $_W['account']['acid'],
            'type' => $_W['account']['type'],
            'level' => $_W['account']['level'],
            'support_oauthinfo' => $_W['account']->supportOauthInfo,
            'support_jssdk' => $_W['account']->supportJssdk,
        );
        if (!empty($authToken)){
            //自动登录
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
    }
}
