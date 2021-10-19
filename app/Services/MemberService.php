<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MemberService
{

    public static $profile_fields = array('realname'=>'真实姓名','nickname'=>'昵称','avatar'=>'头像','qq'=>'QQ号','mobile'=>'手机号码','vip'=>'VIP级别','gender'=>'性别','birthyear'=>'出生生日','constellation'=>'星座','zodiac'=>'生肖','telephone'=>'固定电话','idcard'=>'证件号码','studentid'=>'学号','grade'=>'班级','address'=>'邮寄地址','zipcode'=>'邮编','nationality'=>'国籍','resideprovince'=>'居住地址','graduateschool'=>'毕业学校','company'=>'公司','education'=>'学历','occupation'=>'职业','position'=>'职位','revenue'=>'年收入','affectivestatus'=>'情感状态','lookingfor'=>'交友目的','bloodtype'=>'血型','height'=>'身高','weight'=>'体重','alipay'=>'支付宝帐号','msn'=>'MSN','email'=>'电子邮箱','taobao'=>'阿里旺旺','site'=>'主页','bio'=>'自我介绍','interest'=>'兴趣爱好');

    public static function getone(){

    }

    public static function AuthLogin($member,$remember=true){
        global $_W;
        if (!empty($member) && !empty($member['uid'])) {
            $member = pdo_get('mc_members', array('uid' => $member['uid'], 'uniacid' => $_W['uniacid']), array('uid', 'nickname', 'realname', 'mobile', 'email', 'groupid', 'credit1', 'credit2', 'credit6'));
            if (!empty($member) && (!empty($member['mobile']) || !empty($member['email']))) {
                if($_W['openid']!=$member['uid']){
                    $openid = pdo_getcolumn('mc_mapping_fans', array('uid' => $member['uid'], 'uniacid' => $_W['uniacid']), 'openid');
                    $_W['openid'] = empty($openid) ? $member['uid'] : $openid;
                }
                $_W['member'] = $member;
                $_W['member']['groupname'] = $_W['uniaccount']['groups'][$member['groupid']]['title'];
                self::GroupUpdate();
                if($remember){
                    session()->put('openid',$_W['openid']);
                    session()->put("_app_member_session_{$_W['uniacid']}_",$member);
                    session()->save();
                }
                return true;
            }
        }
        return false;
    }

    public static function GroupUpdate($uid = 0){
        global $_W;
        if(!$_W['uniaccount']['grouplevel']) {
            $_W['uniaccount']['grouplevel'] = (int)pdo_getcolumn('uni_settings', array('uniacid' => $_W['uniacid']), 'grouplevel');
            if (empty($_W['uniaccount']['grouplevel'])) {
                return true;
            }
        }
        $uid = intval($uid);
        if($uid <= 0) {
            $uid = $_W['member']['uid'];
            $user = $_W['member'];
            $user['openid'] = $_W['openid'];
        } else {
            $user = pdo_get('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid), array('uid', 'realname', 'credit1', 'credit6', 'groupid'));
            $user['openid'] = pdo_getcolumn('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'uid' => $uid), 'openid');
        }
        if(empty($user)) {
            return false;
        }
        $groupid = $user['groupid'];
        $credit = $user['credit1'] + $user['credit6'];
        $groups = pdo_getall('mc_groups', array('uniacid' => $_W['uniacid']), array(), 'groupid', 'credit');
        if(empty($groups)) {
            return false;
        }
        $data = array();
        foreach($groups as $group) {
            $data[$group['groupid']] = $group['credit'];
        }
        asort($data);
        if($_W['uniaccount']['grouplevel'] == 1) {
            foreach($data as $k => $da) {
                if($credit >= $da) {
                    $groupid = $k;
                }
            }
        } else {
            $now_group_credit = $data[$user['groupid']];
            if($now_group_credit < $credit) {
                foreach($data as $k => $da) {
                    if($credit >= $da) {
                        $groupid = $k;
                    }
                }
            }
        }
        if($groupid > 0 && $groupid != $user['groupid']) {
            pdo_update('mc_members', array('groupid' => $groupid), array('uniacid' => $_W['uniacid'], 'uid' => $uid));
        }
        $user['groupid'] = $groupid;
        $_W['member']['groupid'] = $groupid;
        $_W['member']['groupname'] = $_W['uniaccount']['groups'][$groupid]['title'];
        return $user['groupid'];
    }

    public static function UniAuth($authtoken,$expir=false){
        global $_W;
        $session = json_decode(base64_decode($authtoken), true);
        if ($session['uid']){
            if($session['expire']<TIMESTAMP && $expir) return false;
            $member = DB::table('mc_members')->where(['uid'=>$session['uid'],'uniacid'=>$_W['uniacid']])->first();
            if (empty($member)) return false;
            if ($session['hash'] == md5($member['password'].$member['salt'].$session['expire'])){
                $_W['member'] = $member;
                $openid = DB::table('mc_mapping_fans')->where('uid',$member['uid'])->value('openid');
                $_W['openid'] = !empty($openid) ? $openid : $member['uid'];
                $_W['member']['openid'] = $_W['openid'];
                return true;
            }
        }
        return false;
    }

    public static function AuthFetch($openid,$auth=true){
        if (empty($openid)) return array('uid'=>0);
        global $_W;
        $member = pdo_fetch("select m.*,mc.openid from ".tablename('mc_members')." as m left join ".tablename("mc_mapping_fans")." as mc on mc.uid=m.uid where m.uniacid=:uniacid and (m.uid=:uid or mc.openid=:openid) limit 1", array('uniacid'=>$_W['uniacid'],'uid'=>intval($openid),'openid'=>trim($openid)));
        if (empty($member)) return array('uid'=>0);
        if ($auth){
            if (empty($member['openid'])) $member['openid'] = $member['uid'];
            $_W['member'] = $member;
            $_W['openid'] = $member['openid'];
        }
        return $member;
    }

}
