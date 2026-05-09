<?php

namespace App\Http\Middleware;

use App\Services\AccountService;
use App\Services\FileService;
use App\Services\MemberService;
use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        if ($this->isBlacklisted()) {
            abort(403, 'Forbidden');
        }
        $uniacid = $request->input('i', SITEACID);
        $this->Runtime($uniacid, $request->header('x-auth-token'));
        return $next($request);
    }

    public function Runtime($uniacid, $authToken=null){
        if (empty($uniacid)) abort(404,'找不到该平台');
        global $_W;
        SettingService::Load();
        if($state = \request()->input("state", "")){
            if (!empty($state) && \Str::startsWith($state, "we7sid-")){
                $_W['session_id'] = str_replace("we7sid-", "", $state);
                session()->setId($_W['session_id']);
            }
            session()->start();
        }
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

    /**
     * 检测当前 IP 是否在黑名单中
     */
    protected function isBlacklisted($ip=''): bool
    {
        if (empty($ip)){
            global $_W;
            $ip = $_W['clientip'];
        }
        $file = storage_path('Blacklist.txt');
        if (!file_exists($file) || empty($ip)) {
            return false;
        }
        // 获取文件修改时间，用于缓存失效
        $mtime = filemtime($file);
        $cacheKey = 'blacklist_rules_' . $mtime;
        // 从缓存读取规则，若不存在则解析文件并缓存
        $rules = Cache::remember($cacheKey, 3600, function () use ($file) {
            return $this->parseBlacklistFile($file);
        });
        return $this->ipMatchesRules($ip, $rules);
    }

    /**
     * 解析黑名单文件
     */
    protected function parseBlacklistFile(string $file): array
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $exact = [];
        $prefixes = [];

        foreach ($lines as $line) {
            $line = trim($line);
            // 跳过注释行（可选）
            if (strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '*') !== false) {
                // 通配符规则：转换为前缀（去掉 * 并补充点）
                $prefix = rtrim(str_replace('*', '', $line), '.');
                $prefixes[] = $prefix . '.'; // 确保以点结尾
            } else {
                // 精确规则：存入哈希表
                $exact[$line] = true;
            }
        }

        return ['exact' => $exact, 'prefixes' => $prefixes];
    }

    /**
     * 检查 IP 是否匹配任意规则
     */
    protected function ipMatchesRules(string $ip, array $rules): bool
    {
        // 1. 精确匹配
        if (isset($rules['exact'][$ip])) {
            return true;
        }

        // 2. 前缀匹配（通配符）
        foreach ($rules['prefixes'] as $prefix) {
            if (strpos($ip, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

}
