<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\CloudService;
use App\Services\HttpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilController extends Controller
{

    public function index(Request $request,$op='index'){
        $method = "do" . ucfirst($op);
        if (method_exists($this,$method)){
            return $this->$method($request);
        }
        return $this->message();
    }

    /**
     * @throws \Exception
     */
    public function doWxcode(Request $request){
        $attach = $request->get('attach');
        $type = $request->get('a');
        if ($type=='image'){
            $content = HttpService::ihttp_post($attach,"");
            if (is_error($content)){
                throw new \Exception($content['message']);
            }
            session_exit($content['content']);
        }
        return "";
    }

    public function doCache(Request $request){
        //清理系统缓存
        try {
            CacheService::flush();
        }catch (\Exception $exception){
            return $this->message($exception->getMessage());
        }
        return $this->message('清理完成！','','success');
    }

    public function doFile(Request $request){
        global $_W,$_GPC;
        $do = $request->input('do');
        if (empty($do)) $do = 'image';
        $islocal = 'local' == $_GPC['local'];
        $module_upload_dir = '';
        $dest_dir = $_GPC['dest_dir'];
        $uniacid = $_W['uniacid'];
        if ('' != $dest_dir) {
            $module_upload_dir = sha1($dest_dir);
        }

        $attachment_by_uid = 0;
        if (!empty($_W['setting']['upload']['attachment_by_uid']) && !empty($uniacid) && in_array($_W['role'], array('clerk', 'operator', 'manager'))) {
            $attachment_by_uid = 1;
        }

        if ('image' == $do) {
            $year = $_GPC['year'];
            $month = $_GPC['month'];
            $page = max(1, intval($_GPC['page']));
            $groupid = intval($_GPC['group_id']);
            $keyword = trim($_GPC['keyword']);
            $order = trim($_GPC['order']);
            $page_size = 15;
            $page = max(1, $page);
            $condition = array();
            if ($islocal) {
                $query = DB::table('core_attachment');
                if (1 == $attachment_by_uid) {
                    $condition['uid'] = $_W['uid'];
                }
            } else {
                $query = DB::table('wechat_attachment');
            }
            $condition['uniacid'] = $uniacid;
            $condition['module_upload_dir'] = $module_upload_dir;

            if (empty($uniacid)) {
                $condition['uid'] = $_W['uid'];
            }
            if ($groupid > 0) {
                $condition['group_id'] = $groupid;
            }

            if (0 == $groupid) {
                $condition['group_id'] = -1;
            }

            if ($year || $month) {
                $start_time = strtotime("{$year}-{$month}-01");
                $end_time = strtotime('+1 month', $start_time);
                $condition[] = ['createtime','>=',$start_time];
                $condition[] = ['createtime','<=',$end_time];
            }
            if ($islocal) {
                $condition['type'] = 1;
            } else {
                $condition['type'] = 'image';
            }

            if (!empty($keyword)) {
                $condition[] = ['filename','like',"%$keyword%"];
            }

            $query = $query->where($condition);

            if (!empty($order)) {
                if (in_array($order, array('asc', 'desc'))) {
                    $query = $query->orderBy('id',$order);
                }
                if (in_array($order, array('filename_asc', 'filename_desc'))) {
                    $order = $order == 'filename_asc' ? 'asc' : 'desc';
                    $query = $query->orderBy('filename_asc',$order);
                }
            }
            $total = $query->count();

            $list = $query->offset(($page-1)*$page_size)->limit($page_size)->get()->toArray();
            if (!empty($list)) {
                foreach ($list as &$meterial) {
                    if ($islocal) {
                        $meterial['url'] = tomedia($meterial['attachment']);
                        unset($meterial['uid']);
                    } else {
                        if (!empty($_W['setting']['remote']['type'])) {
                            $meterial['attach'] = tomedia($meterial['attachment']);
                        } else {
                            $meterial['attach'] = tomedia($meterial['attachment'], true);
                        }
                        $meterial['url'] = $meterial['attach'];
                    }
                }
            }

            serv("weengine")->func("web");

            $pager = pagination($total, $page, $page_size, '', $context = array('before' => 5, 'after' => 4, 'isajax' => $_W['isajax']));
            $result = array(
                'list' => $list,
                'total' => $total,
                'page' => $page,
                'page_size' => $page_size,
                'pager' => $pager,
                'items' => $list,
            );
            return $this->message(error(0,$result),'','success');
        }
        if ('keyword' == $do) {
            $keyword = addslashes($_GPC['keyword']);
            $pindex = max(1, $_GPC['page']);
            $psize = 24;
            $condition = array('uniacid' => $uniacid, 'status' => 1);
            $offset = ($pindex-1)*$psize;
            if (!empty($keyword)) {
                $condition['content like'] = '%' . $keyword . '%';
            }

            $query = DB::table('rule_keyword')->where($condition);
            $total = $query->count();
            $keyword_lists = $query->limit($psize)->offset($offset)->get()->keyBy('id')->toArray();
            $result = array(
                'items' => $keyword_lists,
                'pager' => pagination($total, $pindex, $psize, '', array('before' => '2', 'after' => '3', 'ajaxcallback' => 'null', 'isajax' => 1)),
            );
            return $this->message(error(0,$result),'','ajax');
        }

        return $this->message(error(-1,'操作失败，请重试'));
    }

    //
    public function save(Request $request,$op='index'){
        global $_W,$_GPC;
        if ($op=='cloudcode'){
            if (checksubmit('sendcode')){
                $mobile = $request->input('mobile');
                if (empty($mobile) || !preg_match('/^(\+)?(86)?0?1\d{10}$/', $mobile)) return $this->message("请输入正确的手机号");
                $data = array('r'=>'util.code', 'token'=>1,'mobile'=>$mobile,"sendcode"=>"1","from"=>"autocheck");
                $res = CloudService::CloudApi("", $data);
                if (is_error($res)){
                    return $this->message($res['message']);
                }
                return $this->message($res['message'], "", $res["type"]);
            }
            return $this->message();
        }
        if ($op=='file'){
            $do = $request->input('do');
            if ('delete' == $do){
                $id = $_GPC['id'];
                $condition = array();
                $query = DB::table('core_attachment')->whereIn('id', $id);
                if (empty($_W['uniacid'])){
                    $condition['uid'] = $_W['uid'];
                }else{
                    $condition['uniacid'] = $_W['uniacid'];
                }
                $attachments = $query->where($condition)->get()->toArray();
                if (!empty($attachments)){
                    foreach ($attachments as $key=>$value){
                        serv('storage')->removeFile($value['attachment']);
                    }
                    $query->where($condition)->delete();
                }
                return $this->message(error(0,"删除成功！"),'','success');
            }
            if ($do=='wechat_upload'){
                $type = trim($_GPC['upload_type']);
                $mode = trim($_GPC['mode']);
                $acceptMime = $type=='voice' ? 'audio' : $type;
                $result = serv('storage')->saveFile('file', $acceptMime);
                if (is_error($result)) return $this->message($result, referer(), 'error');
                $res = serv('wechat')->uploadMaterial($result['path'], $acceptMime, $result['name']);
                if (is_error($res)) return $this->message($res, referer());
                $size = intval($_FILES['file']['size']);
                $res['error'] = 0;
                if ($type=='image'){
                    $res['width'] = $size[0];
                    $res['hieght'] = $size[1];
                }
                $res['type'] = $type;
                $res['url'] = tomedia($result['path']);
                $res['mode'] = $mode;
                die(json_encode($res));
            }
        }
        if ($op=='upload'){
            $type = $request->input('type', 'image');
            $path = serv('storage')->putFile('file');
            if (is_error($path)){
                return $this->message($path['message']);
            }
            $group_id = intval($_GPC['group_id']);
            $module_upload_dir = '';
            $dest_dir = $_GPC['dest_dir'];
            if ('' != $dest_dir) {
                $module_upload_dir = sha1($dest_dir);
            }
            $info = array(
                'name' => htmlspecialchars_decode($request->file('file')->getClientOriginalName(), ENT_QUOTES),
                'ext' => $request->file('file')->getClientOriginalExtension(),
                'filename' => $path['name'],
                'attachment' => $path['path'],
                'url' => tomedia($path['path']),
                'is_image' => 'image' == $type ? 1 : 0,
                'filesize' => $request->file('file')->getSize(),
                'group_id' => $group_id
            );
            pdo_insert('core_attachment', array(
                'uniacid' => $_W['uniacid'],
                'uid' => $_W['uid'],
                'filename' => $info['name'],
                'attachment' => $path['path'],
                //1图片2媒体3附件
                'type' => 'image' == $type ? 1 : ('media' == $type ? 2 : 3),
                'createtime' => TIMESTAMP,
                'module_upload_dir' => $module_upload_dir,
                'group_id' => $group_id
            ));
            if ('image' == $type) {
                $info['width'] = 0;
                $info['height'] = 0;
            } else {
                $info['size'] = $info['filesize'];
            }
            $info['state'] = 'SUCCESS';
            session()->save();
            die(json_encode($info));
        }
        return $this->message();
    }

}
