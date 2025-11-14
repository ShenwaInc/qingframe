<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MemberService
{

    public static $profile_fields = array('realname'=>'真实姓名','nickname'=>'昵称','avatar'=>'头像','qq'=>'QQ号','mobile'=>'手机号码','vip'=>'VIP级别','gender'=>'性别','birthyear'=>'出生年', 'birthmonth'=>'出生月', 'birthday'=>'出生日','constellation'=>'星座','zodiac'=>'生肖','telephone'=>'固定电话','idcard'=>'证件号码','studentid'=>'学号','grade'=>'班级','address'=>'邮寄地址','zipcode'=>'邮编','nationality'=>'国籍','resideprovince'=>'居住地址','graduateschool'=>'毕业学校','company'=>'公司','education'=>'学历','occupation'=>'职业','position'=>'职位','revenue'=>'年收入','affectivestatus'=>'情感状态','lookingfor'=>'交友目的','bloodtype'=>'血型','height'=>'身高','weight'=>'体重','alipay'=>'支付宝帐号','msn'=>'MSN','email'=>'电子邮箱','taobao'=>'阿里旺旺','site'=>'主页','bio'=>'自我介绍','interest'=>'兴趣爱好');

    public static function getone(){

    }

    public static function AuthLogin($member,$remember=true){
        global $_W;
        if (!empty($member) && !empty($member['uid'])) {
            $ucenter = serv('ucenter');
            if (!$ucenter->enabled){
                return false;
            }
            $member = $ucenter->getUser($member['uid'], array('uid', 'nickname', 'realname', 'mobile', 'email', 'groupid', 'credit1', 'credit2', 'credit6'));
            if (!empty($member) && (!empty($member['mobile']) || !empty($member['email']))) {
                if($_W['openid']!=$member['uid'] && serv("wechat")->enabled){
                    $openid = pdo_getcolumn('mc_mapping_fans', array('uid' => $member['uid'], 'uniacid' => $_W['uniacid']), 'openid');
                    $_W['openid'] = empty($openid) ? $member['uid'] : $openid;
                    $member['openid'] = $_W['openid'];
                }
                $_W['member'] = $member;
                $_W['member']['groupname'] = $_W['uniaccount']['groups'][$member['groupid']]['title'];
                @$ucenter->UpdateGroupId($member['uid']);
                if($remember){
                    session()->put("openid{$_W['uniacid']}",$_W['openid']);
                    session()->put("_app_member_session_{$_W['uniacid']}_",$member);
                    session()->save();
                }
                return true;
            }
        }
        return false;
    }

    public static function AuthLogout(){
        global $_W;
        session()->forget("openid{$_W['uniacid']}");
        session()->forget("_app_member_session_{$_W['uniacid']}_");
        session()->save();
    }

    public static function GroupUpdate($uid = 0){
        global $_W;
        $uid = empty($uid) ? $_W['member']['uid'] : $uid;
        $ucenter = serv('ucenter');
        if ($ucenter->enabled){
            return $ucenter->UpdateGroupId($uid);
        }
        return false;
    }

    public static function UniAuth($authToken, $expire=false){
        global $_W;
        $session = json_decode(base64_decode($authToken), true);
        if ($session['uid']){
            if($session['expire']<TIMESTAMP && $expire) return false;
            $member = serv('ucenter')->getUser($session['uid']);
            if (empty($member) || is_error($member)) return false;
            if ($session['hash'] == md5($member['password'].$member['salt'].$session['expire'])){
                $_W['member'] = $member;
                if (empty($_W['openid'])){
                    return self::AuthLogin($member);
                }
                $openid =  serv("wechat")->enabled ? DB::table('mc_mapping_fans')->where('uid',$member['uid'])->value('openid') : 0;
                $_W['openid'] = !empty($openid) ? $openid : $member['uid'];
                $_W['member']['openid'] = $_W['openid'];
                return true;
            }
        }
        return false;
    }

    public static function AuthFetch($openid, $auth=true){
        if (empty($openid)) return array('uid'=>0);
        global $_W;
        $wechat = serv("wechat");
        if ($wechat->enabled){
            $member = $wechat->getUserByOpenid($openid);
        }else{
            $member = serv('ucenter')->getUser(intval($openid));
            $member['openid'] = $member['uid'];
        }
        if (empty($member) || is_error($member)) return array('uid'=>0);
        if ($auth){
            if (empty($member['openid'])) $member['openid'] = $member['uid'];
            $_W['member'] = $member;
            $_W['openid'] = $member['openid'];
        }
        return $member;
    }

}
