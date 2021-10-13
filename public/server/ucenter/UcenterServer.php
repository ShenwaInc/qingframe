<?php

use App\Services\SettingService;
use Illuminate\Support\Facades\DB;

class UcenterServer
{

    public $identity = 'ucenter';
    public $servername = '会员中心';
    public $Apis = [];
    public $Sdks = [];
    public $setting = true;

    function __construct(){
        if (defined('IN_SYS') && !function_exists('message')){
            require_once base_path('bootstrap/functions/web.func.php');
        }
        if (!function_exists('mc_credit_update')){
            require_once base_path("bootstrap/wemod/mc.mod.php");
        }
    }

    public function doAdminTrade($request){
        global $_GPC,$_W;
        $dos = array('consume', 'user', 'modal', 'credit', 'card', 'cardsn', 'tpl', 'cardconsume');
        $do = in_array($_GPC['op'], $dos) ? $_GPC['op'] : 'tpl';

        if ('user' == $do) {
            $type = trim($_GPC['type']);
            if (!in_array($type, array('uid', 'mobile'))) {
                $type = 'mobile';
            }
            $username = trim($_GPC['username']);
            $data = DB::table('mc_members')
                ->where(array(
                    'uniacid' => $_W['uniacid'],
                    $type => $username
                ))
                ->get()->toArray();
            if (empty($data)) {
                exit(json_encode(array('error' => 'empty', 'message' => '没有找到对应用户')));
            } elseif (count($data) > 1) {
                exit(json_encode(array('error' => 'not-unique', 'message' => '用户不唯一,请重新输入用户信息')));
            } else {
                $card = array();
                $user = $data[0];
                $user['groupname'] = $_W['account']['groups'][$user['groupid']]['title'];

                $html = "用户昵称:{$user['nickname']},会员组:{$user['groupname']}<br>";
                if (!empty($we7_coupon_info)) {
                    $html .= "{$user['discount_cn']}<br>";
                }
                $html .= "余额:{$user['credit2']}元,积分:{$user['credit1']}<br>";
                if (!empty($we7_coupon_info) && !empty($card) && $card['offset_rate'] > 0 && $card['offset_max'] > 0) {
                    $html .= "{$card['offset_rate']}积分可抵消1元。最多可抵消{$card['offset_max']}元";
                }

                session_exit(json_encode(array('error' => 'none', 'user' => $user, 'html' => $html, 'card' => $card, 'group' => $_W['account']['groups'], 'grouplevel' => $_W['account']['grouplevel'])));
            }
        }

        if ('credit' == $do) {
            $setting = SettingService::uni_load('creditnames',$_W['uniacid']);
            $creditnames = $setting['creditnames'];
            $uid = intval($_GPC['uid']);
            $type = trim($_GPC['type']);
            $num = floatval($_GPC['num']);
            $names = array('credit1' => $creditnames['credit1']['title'], 'credit2' => $creditnames['credit2']['title']);
            $credits = mc_credit_fetch($uid);
            if ($num < 0 && abs($num) > $credits[$type]) {
                exit("会员账户{$names[$type]}不够");
            }
            $status = mc_credit_update($uid, $type, $num, array($_W['user']['uid'], trim($_GPC['remark']), 'system', $_W['user']['clerk_id'], $_W['user']['store_id'], $_W['user']['clerk_type']));
            if (is_error($status)) {
                exit($status['message']);
            }
            if ('credit1' == $type) {
                mc_group_update($uid);
            }
            $openid = DB::table('mc_mapping_fans')
                ->where(array(
                    'uniacid' => $_W['uniacid'],
                    'uid' => $uid
                ))
                ->value('openid');
            if (!empty($openid)) {
                if ('credit1' == $type) {
                    mc_notice_credit1($openid, $uid, $num, '管理员后台操作' . $creditnames['credit1']['title']);
                }
                if ('credit2' == $type) {
                    if ($num > 0) {
                        mc_notice_recharge($openid, $uid, $num, '', "管理员后台操作{$creditnames['credit1']['title']},增加{$creditnames['credit2']['title']}");
                    } else {
                        mc_notice_credit2($openid, $uid, $num, 0, '', '', "管理员后台操作{$creditnames['credit1']['title']},减少{$creditnames['credit2']['title']}");
                    }
                }
            }
            exit('success');
        }
        session_exit('操作失败，请重试');
    }

}
