<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\CloudService;
use App\Services\HttpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

define('REPORTAPI', env('APP_REPORT_API', 'https://v3.whotalk.com.cn/api/m/workorder'));
define('REPORTSECRET', env('APP_REPORT_SECRET', 'G5M5RYtn7fb5rPagKxmXe5Rr2RDQ9nIJ'));

class ReportController extends Controller {

    public $sourceName = "";

    public function httpReq(Request $request, $option='index'){
        global $_W;
        $_W['inReport'] = true;
        return $this->$option($request);
    }

    public function getSource(){
        if (!empty($this->sourceName)) return $this->sourceName;
        $cloudState = CloudService::CloudActive();
        if ($cloudState['status']!=1){
            return $this->message("请先激活云服务", url('console/active'));
        }
        $this->sourceName = $cloudState['name']."（站点ID：{$cloudState['siteid']}）";
        return $this->sourceName;
    }

    public function attach(Request $request){
        if (!$request->hasFile('file')) return error(-1,'请选择要上传的文件');
        $service = serv('storage');
        $settings = $service->settings['upload'];
        $Upload = $request->file('file');
        $ext = strtolower($Upload->getClientOriginalExtension());
        $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        if (in_array($ext, $harmtype)){
            return $this->message("不允许上传此类文件($ext)");
        }
        $fileType = "";
        foreach ($settings as $key=>$item){
            if (in_array($ext, $item['extentions'])){
                $fileType = $key;
                break;
            }
        }
        if (empty($fileType)){
            return $this->message("不允许上传此类文件($ext)");
        }
        $size = $Upload->getSize();
        $fileLimit = $settings[$fileType]['limit'] * 1024;
        if ($fileLimit>0 && $size>$fileLimit){
            return $this->message("超出文件大小限制({$size}byte)");
        }
        $type = $fileType=='media' ? 'audio' : $fileType;
        $path = $Upload->store("{$type}s/".$service->uniacid."/".date('Y/m'));
        if (!$path) return error(-1,'上传失败，请重试');
        if ($size>1048576){
            $resize = sprintf("%.1f", ($size / 1048576)) . 'Mb';
        }else{
            $resize = sprintf("%.1f", ($size / 1024)) . 'Kb';
        }
        $result = array(
            "path"=>$path,
            "name"=>$Upload->getClientOriginalName(),
            "size"=>$resize,
            "message"=>"OK",
            "isremote"=>false
        );
        if (!empty($service->settings['remote']['type'])) {
            $remoteState = $service->remoteUpload($result['path']);
            if (is_error($remoteState)) {
                $result['message'] = '远程附件上传失败，请检查配置并重新上传';
            }else{
                $result['isremote'] = true;
                if (file_exists(ATTACHMENT_ROOT . $result['path'])) {
                    @unlink(ATTACHMENT_ROOT . $result['path']);
                }
            }
        }
        $result['url'] = tomedia($result['path']);
        return $this->message($result, "", "success");
    }

    public function index(Request $request){
        $curPage = $request->input('page', 1);
        $sourceName = $this->getSource();
        if (!is_string($sourceName)) return $sourceName;
        $res = $this->reportCloud('order/list', array(
            'page' => $curPage,
            'keyword' => $request->input('keyword', ""),
            'source' => $sourceName
        ));
        if (is_error($res)){
            return $this->message($res['message']);
        }
        $pager = pagination($res['total'], $curPage);
        return $this->globalView('console.report.index', array(
            'list' => $res['list'],
            'total'=> $res['total'],
            'pager'=>$pager,
            'badges'=>['orange', 'orange', 'blue', 'red', 'blue','black', 'green', 'gray'],
            'title'=> '工单服务中心'
        ));
    }

    public function feedback(Request $request){
        $orderId = (int)$request->input('id');
        if (empty($orderId)) return $this->message('无效的工单信息');
        if ($request->isMethod('post')){
            $data = $request->post('data');
            $fileList = [];
            $attachs = $request->post('attach');
            if (!empty($attachs)){
                foreach ($attachs as $key=>$attach){
                    $fileList[] = array(
                        'name'=>$request->input('attachName')[$key],
                        'path'=>tomedia($attach)
                    );
                }
            }
            if (empty($data['content']) && empty($fileList)) return $this->message("请简单描述您遇到的问题");
            $data['fileList'] = $fileList;
            $data['source'] = $this->getSource();
            $data['order_id'] = $orderId;
            $data['sign'] = $this->genSignature($data);
            $res = $this->reportCloud("orderFeedback/add", $data, false);
            if (is_error($res)) return $this->message($res['message']);
            if (!$request->ajax()){
                return $this->success("已收到您的反馈，请耐心等待工作人员处理");
            }
            return $this->success(['response'=>$res, 'input'=>$data]);
        }
        return $this->globalView('console.report.feedback', array(
            'id'=>$orderId
        ));
    }

    public function detail(Request $request){
        $data = $this->reportCloud('order/details', array('id'=>$request->input('id'),'source'=>$this->getSource()));
        if (is_error($data)) return $this->message($data['message']);
        return $this->globalView('console.report.detail', array(
            'orderInfo'=>$data,
            'title'=>$data['title']
        ));
    }

    public function rmAttach(Request $request){
        $res = serv('storage')->removeFile($request->input('file'));
        if (is_error($res)) return $this->message($res['message']);
        return $this->message("操作成功！", "", "success");
    }

    public function post(Request $request){
        if ($request->isMethod('post')){
            $data = $request->post('data');
            $fileList = [];
            $attachs = $request->post('attach');
            if (!empty($attachs)){
                foreach ($attachs as $key=>$attach){
                    $fileList[] = array(
                        'name'=>$request->input('attachName')[$key],
                        'path'=>tomedia($attach)
                    );
                }
            }
            $cateId = (int)$request->input('cateId', 0);
            $data['category_id'] = (int)$request->input('subCate')[$cateId];
            if (empty($data['category_id'])){
                $data['category_id'] = $cateId;
            }
            if (empty($data['category_id'])) return $this->message("无效的工单分类");
            if (empty($data['content'])) return $this->message("请简单描述您遇到的问题");
            if (empty($data['mobile'])) return $this->message("联系方式不能为空");
            $data['fileList'] = $fileList;
            $data['source'] = $this->getSource();
            $data['name'] = mb_substr($data['content'], 0, 12, 'utf8') . "...";
            if (is_error($data['source'])) return $this->message($data['source']['message']);
            $res = $this->reportCloud("order/save", $data, false);
            CacheService::flush();
            return $this->success(['response'=>$res, 'input'=>$data]);
        }
        $cates = $this->reportCloud("orderCategory/list");
        if (is_error($cates)) return $this->message($cates['message']);
        $subcates = [];
        if (!empty($cates)){
            foreach ($cates as $cate){
                if (!empty($cate['children'])){
                    $subcates[$cate['id']] = $cate['children'];
                }
            }
        }
        return $this->globalView('console.report.post', array(
            'cates'=>$cates,
            'subcates'=>$subcates,
            'title'=>"提交工单",
            'cloudState'=>CloudService::CloudActive()
        ));
    }

    /**
     * 云端工单接口
     * @param string $api 接口
     * @param string|null|array $data 数据
     * @return array
    */
    public function reportCloud($api, $data='', $isCache=true){
        global $_W;
        $demoData = $this->demoCloud($api);
        if (!empty($demoData)) return $demoData;
        $postData = is_array($data) ? $data : [];
        $cacheKey = "QingFrameworkReport$api{$postData['id']}{$postData['page']}";
        if ($isCache){
            $cacheData = Cache::get($cacheKey, []);
            if (!empty($cacheData)){
                return is_error($cacheData) ? [] : $cacheData;
            }
        }
        $postData['r'] = str_replace('/','.', $api);
        $postData['i'] = env('APP_REPORT_ACID', 1);
        $postData['source_url'] = $_W['siteroot'];
        $postData['sign'] = $this->genSignature($postData);
        $res = HttpService::ihttp_request(REPORTAPI, $postData);
        if (is_error($res)) return $res;
        $result = json_decode($res['content'], true);
        if (empty($result) || !isset($result['status'])) return error(-1, "请求失败，请重试");
        if ($result['status']!='success') return error(-1, $result['message']);
        if (!isset($result['data'])){
            return array('type'=>'success', 'message'=>$result['message'], 'redirect'=>'');
        }
        if ($isCache){
            Cache::put($cacheKey, empty($result['data'])?error(-1, "Empty"):$result['data'], 3600);
        }
        return $result['data'];
    }

    public function genSignature($data) {
        // 对参数进行升序排序
        ksort($data);
        // 将参数连接起来
        $dataString = http_build_query($data, '', '&');
        // 使用HMAC-SHA256算法生成签名
        return hash_hmac('sha256', $dataString, REPORTSECRET, false);
    }

    public function demoCloud($api){
        $result = [];
        switch ($api){
            case "order/listsss" :
                $result = array(
                    'list'=>json_decode('{"0":{"id":1,"title":"智慧社区-名称-202304141050","ordersn":"OW20230414105034473","category_id":"1","content":"","secret":"","status":"0","executor_id":"0","create_id":"0","created_at":"2023-04-14 10:50","executorPerson":"","statusName":"待验证","categoryName":"智慧社区"}}', true),
                    'total'=>1
                );
                break;
            case "order/detailssss" :
                $result = json_decode('{"id":1,"uniacid":"1","title":"智慧社区-名称-202304141050","ordersn":"OW20230414105034473","source":"UN7852125","category_id":"1","content":"","secret":"","status":"0","create_id":"0","executor_id":"0","created_at":"2023-04-14 10:50","updated_at":"2023-04-14 10:53:46","categoryName":"智慧社区","fileList":[{"path":"/file/45d5adad.jpg","name":"附件名称","url":"http://local.lingchen.com/storage//file/45d5adad.jpg"}],"executorName":"","statusName":"待验证","name":"名称"}', true);
                break;
            case "orderCategory/listsss":
                $result = json_decode('[{"id":1,"name":"智慧社区","pid":"","created_at":"2023-04-14 09:56","children":[{"id":2,"name":"停车场子模块","pid":1,"created_at":"2023-04-14 09:56"}]}]', true);
                break;
        }
        return $result;
    }

}
