<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountWechat;
use App\Services\FileService;
use App\Utils\WeEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WechatController extends Controller
{
    //
    public function recived(Request $request, $uniacid){
        global $_W,$_GPC;
        $hash = $request->input('hash','');
        if(!empty($hash)) {
            $id = DB::table('account')->where(array('hash'=>trim($hash)))->value('acid');
        }
        if(!empty($_GPC['appid'])) {
            $appid = ltrim($_GPC['appid'], '/');
            $id = AccountWechat::where('key','=',$appid)->value('acid');
        }
        if(empty($id)) {
            $id = intval($uniacid);
        }
        if (!empty($id)) {
            $uniacid = pdo_getcolumn('account', array('acid' => $id), 'uniacid');
            $_W['account'] = $_W['uniaccount'] = uni_fetch($uniacid);
        }
        if(empty($_W['account'])) {
            exit('initial error hash or id');
        }
        if(empty($_W['account']['token'])) {
            exit('initial missing token');
        }
        $_W['debug'] = intval($_GPC['debug']);
        $_W['emulator'] = intval($_GPC['emulator']);
        $_W['acid'] = $_W['account']['acid'];
        $_W['uniacid'] = $_W['account']['uniacid'];
        $_W['account']['groupid'] = $_W['uniaccount']['groupid'];
        $_W['account']['qrcode'] = $_W['attachurl'].'qrcode_'.$_W['acid'].'.jpg?time='.$_W['timestamp'];
        $_W['account']['avatar'] = $_W['attachurl'].'headimg_'.$_W['acid'].'.jpg?time='.$_W['timestamp'];
        $_W['attachurl'] = FileService::SetAttachUrl();

        $engine = new WeEngine();
        if($_W['isajax'] && $_W['ispost'] && $_GPC['flag'] == 1) {
            $engine->encrypt();
        }
        if($_W['isajax'] && $_W['ispost'] && $_GPC['flag'] == 2) {
            $engine->decrypt();
        }
        $_W['isajax'] = false;
        $engine->start();
    }

}
