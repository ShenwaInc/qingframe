<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Module extends Model
{
    //
    protected $table = 'modules';

    static function Initializer(){
        $self = DB::table('modules');
        $initialized = $self->where('mid','>',0)->first();
        if (!$initialized){
            $self->insert(array(
                ['name'=>'basic','application_type'=>0,'type'=>'system','title'=>'基本文字回复','version'=>'1.0','ability'=>'基本文字回复','description'=>'基本文字回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'news','application_type'=>0,'type'=>'system','title'=>'基本混合图文回复','version'=>'1.0','ability'=>'基本混合图文回复','description'=>'基本混合图文回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'music','application_type'=>0,'type'=>'system','title'=>'基本音乐回复','version'=>'1.0','ability'=>'基本音乐回复','description'=>'基本音乐回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'userapi','application_type'=>0,'type'=>'system','title'=>'自定义接口回复','version'=>'1.0','ability'=>'自定义接口回复','description'=>'自定义接口回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'userapi','application_type'=>0,'type'=>'system','title'=>'会员中心充值模块','version'=>'1.0','ability'=>'会员中心充值模块','description'=>'会员中心充值模块','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'custom','application_type'=>0,'type'=>'system','title'=>'多客服转接','version'=>'1.0','ability'=>'多客服转接','description'=>'多客服转接','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'images','application_type'=>0,'type'=>'system','title'=>'基本图片回复','version'=>'1.0','ability'=>'基本图片回复','description'=>'基本图片回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'video','application_type'=>0,'type'=>'system','title'=>'基本视频回复','version'=>'1.0','ability'=>'基本视频回复','description'=>'基本视频回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'voice','application_type'=>0,'type'=>'system','title'=>'基本语音回复','version'=>'1.0','ability'=>'基本语音回复','description'=>'基本语音回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'chats','application_type'=>0,'type'=>'system','title'=>'发送客服消息','version'=>'1.0','ability'=>'发送客服消息','description'=>'公众号可以在粉丝最后发送消息的48小时内无限制发送消息','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'wxcard','application_type'=>0,'type'=>'system','title'=>'微信卡券回复','version'=>'1.0','ability'=>'微信卡券回复','description'=>'微信卡券回复','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'default','application_type'=>2,'type'=>'system','title'=>'微站默认模板','version'=>'1.0','ability'=>'微站默认模板','description'=>'微站默认模板','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1],
                ['name'=>'store','application_type'=>0,'type'=>'business','title'=>'站内商城','version'=>'1.0','ability'=>'站内商城','description'=>'站内商城','author'=>'WeEngine Team','isrulefields'=>1,'issystem'=>1,'wxapp_support'=>1,'welcome_support'=>1,'oauth_type'=>1,'webapp_support'=>1,'phoneapp_support'=>1,'account_support'=>2,'xzapp_support'=>1,'aliapp_support'=>1,'baiduapp_support'=>1,'toutiaoapp_support'=>1]
            ));
            $module_exist = $self->where('name','xfy_whotalk')->exists();
            if(!$module_exist){
                $whotalk = array(
                    'name'=>'xfy_whotalk',
                    'application_type'=>1,
                    'type'=>'business',
                    'title'=>'Whotalk即时通讯',
                    'version'=>'3.0.45',
                    'ability'=>'Whotalk即时通讯系统',
                    'description'=>'Whotalk聊天IM即时通讯系统',
                    'author'=>'Shenwa Studio.',
                    'url'=>'https://www.whotalk.com.cn/',
                    'subscribes'=>'a:14:{i:0;s:4:"text";i:1;s:5:"image";i:2;s:5:"voice";i:3;s:5:"video";i:4;s:10:"shortvideo";i:5;s:8:"location";i:6;s:4:"link";i:7;s:9:"subscribe";i:8;s:11:"unsubscribe";i:9;s:2:"qr";i:10;s:5:"trace";i:11;s:5:"click";i:12;s:4:"view";i:13;s:14:"merchant_order";}',
                    'handles'=>'a:12:{i:0;s:4:"text";i:1;s:5:"image";i:2;s:5:"voice";i:3;s:5:"video";i:4;s:10:"shortvideo";i:5;s:8:"location";i:6;s:4:"link";i:7;s:9:"subscribe";i:8;s:2:"qr";i:9;s:5:"trace";i:10;s:5:"click";i:11;s:14:"merchant_order";}',
                    'isrulefields'=>0,
                    'issystem'=>0,
                    'title_initial'=>'W',
                    'wxapp_support'=>1,
                    'welcome_support'=>1,
                    'oauth_type'=>1,
                    'webapp_support'=>1,
                    'phoneapp_support'=>1,
                    'account_support'=>2,
                    'xzapp_support'=>1,
                    'aliapp_support'=>1,
                    'baiduapp_support'=>1,
                    'toutiaoapp_support'=>1,
                    'from'=>'local',
                    'logo'=>'/static/icon200.jpg'
                );
                $self->insert($whotalk);
            }
        }
        return true;
    }
}
