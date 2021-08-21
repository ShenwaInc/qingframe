<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\Request;

class UtilController extends Controller
{
    //
    public function save(Request $request,$op='index'){
        global $_W,$_GPC;
        if ($op=='upload'){
            $type = $request->input('type', 'image');
            $path = FileService::Upload($request,$type);
            if (is_error($path)){
                return $this->message($path['message']);
            }
            pdo_insert('core_attachment', array(
                'uniacid' => $_W['uniacid'],
                'uid' => $_W['uid'],
                'filename' => htmlspecialchars_decode($request->file('file')->getClientOriginalName(), ENT_QUOTES),
                'attachment' => $path,
                //1图片2媒体3附件
                'type' => 'image' == $type ? 1 : ('media' == $type ? 2 : 3),
                'createtime' => TIMESTAMP,
                'module_upload_dir' => trim($_GPC['dest_dir']),
                'group_id' => intval($_GPC['group_id']),
            ));
            return $this->message(array('url'=>asset("storage/{$path}"),'path'=>$path),'','success');
        }
    }

}
