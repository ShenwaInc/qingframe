<?php


namespace App\Services;


use App\Utils\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FileService
{

    public static function file_write($filename, $data) {
        global $_W;
        $filename = ATTACHMENT_ROOT . '/' . $filename;
        self::mkdirs(dirname($filename));
        file_put_contents($filename, $data);
        @chmod($filename, $_W['config']['setting']['filemode']);

        return is_file($filename);
    }

    function file_read($filename) {
        $filename = ATTACHMENT_ROOT . '/' . $filename;
        if (!is_file($filename)) {
            return false;
        }

        return file_get_contents($filename);
    }

    public static function file_move($filename, $dest) {
        global $_W;
        self::mkdirs(dirname($dest));
        if (is_uploaded_file($filename)) {
            move_uploaded_file($filename, $dest);
        } else {
            rename($filename, $dest);
        }
        @chmod($filename, $_W['config']['setting']['filemode']);

        return is_file($dest);
    }


    static function file_tree($path, $include = array()) {
        $files = array();
        if (!empty($include)) {
            $ds = glob($path . '/{' . implode(',', $include) . '}', GLOB_BRACE);
        } else {
            $ds = glob($path . '/*');
        }
        if (is_array($ds)) {
            foreach ($ds as $entry) {
                if (is_file($entry)) {
                    $files[] = $entry;
                }
                if (is_dir($entry)) {
                    $rs = self::file_tree($entry);
                    foreach ($rs as $f) {
                        $files[] = $f;
                    }
                }
            }
        }

        return $files;
    }


    function file_tree_limit($path, $limit = 0, $acquired_files_count = 0) {
        $files = array();
        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (false !== ($file = readdir($dir))) {
                    if (in_array($file, array('.', '..'))) {
                        continue;
                    }
                    if (is_file($path . '/' . $file)) {
                        $files[] = $path . '/' . $file;
                        ++$acquired_files_count;
                        if ($limit > 0 && $acquired_files_count >= $limit) {
                            closedir($dir);

                            return $files;
                        }
                    }
                    if (is_dir($path . '/' . $file)) {
                        $rs = $this->file_tree_limit($path . '/' . $file, $limit, $acquired_files_count);
                        foreach ($rs as $f) {
                            $files[] = $f;
                            ++$acquired_files_count;
                            if ($limit > 0 && $acquired_files_count >= $limit) {
                                closedir($dir);

                                return $files;
                            }
                        }
                    }
                }
                closedir($dir);
            }
        }

        return $files;
    }


    function file_dir_exist_image($path) {
        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while (false !== ($file = readdir($dir))) {
                    if (in_array($file, array('.', '..'))) {
                        continue;
                    }
                    if (is_file($path . '/' . $file) && $path != (ATTACHMENT_ROOT . 'images') && $this->file_is_image($path . '/' . $file)) {
                        if (0 === strpos($path, ATTACHMENT_ROOT)) {
                            $attachment = str_replace(ATTACHMENT_ROOT . 'images/', '', $path . '/' . $file);
                            list($file_account) = explode('/', $attachment);
                            if ('global' == $file_account) {
                                continue;
                            }
                        }
                        closedir($dir);

                        return true;
                    }
                    if (is_dir($path . '/' . $file) && $this->file_dir_exist_image($path . '/' . $file)) {
                        closedir($dir);

                        return true;
                    }
                }
                closedir($dir);
            }
        }

        return false;
    }


    public static function mkdirs($path, $perm=0777, $rec=false) {
        if (!is_dir($path)) {
            self::mkdirs(dirname($path));
            mkdir($path, $perm, $rec);
        }

        return is_dir($path);
    }


    function file_copy($src, $des, $filter) {
        $dir = opendir($src);
        @mkdir($des);
        while (false !== ($file = readdir($dir))) {
            if (('.' != $file) && ('..' != $file)) {
                if (is_dir($src . '/' . $file)) {
                    $this->file_copy($src . '/' . $file, $des . '/' . $file, $filter);
                } elseif (!in_array(substr($file, strrpos($file, '.') + 1), $filter)) {
                    copy($src . '/' . $file, $des . '/' . $file);
                }
            }
        }
        closedir($dir);
    }


    public static function rmdirs($path, $clean = false) {
        if (!is_dir($path)) {
            return false;
        }
        $files = glob($path . '/*');
        if ($files) {
            foreach ($files as $file) {
                is_dir($file) ? self::rmdirs($file) : @unlink($file);
            }
        }

        return $clean ? true : @rmdir($path);
    }

    static function file_upload_path($type='image'){
        global $_W;
        $uniacid = intval($_W['uniacid']);
        $path = "{$type}s/{$uniacid}/" . date('Y/m/');
        if (!is_dir(ATTACHMENT_ROOT.$path)){
            self::mkdirs(ATTACHMENT_ROOT.$path);
        }
        return $path;
    }

    static function Upload(Request $request,$type='image',$field='file'){
        global $_W;
        if (!$request->hasFile($field)) return error(-1,'没有上传内容');
        if (!in_array($type, array('image', 'media', 'attach'))) {
            return error(-2, '未知的上传类型');
        }
        $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        $Upload = $request->file($field);
        $ext = $Upload->getClientOriginalExtension();
        $size = $Upload->getSize();
        $setting = SettingService::Load('upload');
        if (in_array($ext, $harmtype)){
            return error(-3, '不允许上传此类文件');
        }
        if ($type!='attach'){
            $allowExt = $setting['upload'][$type]['extentions'];
            $limit = $setting['upload'][$type]['limit'];
            if (!in_array($ext, $allowExt)) {
                return error(-3, '不允许上传此类文件');
            }
            if (!empty($limit) && $limit * 1024 < $size) {
                return error(-4, "上传的文件超过大小限制({$size}byte)");
            }
        }
        $path = $Upload->store("{$type}s/{$_W['uniacid']}/".date('Y/m'));
        if (!$path) return error(-1,'上传失败，请重试');
        //图片压缩
        if ($type=='image'){
            $quality = intval($setting['upload']['image']['zip_percentage']);
            if ($quality>0 && $quality<100){
                $savepath = ATTACHMENT_ROOT . $path;
                self::file_image_quality($savepath, $savepath, $ext);
            }
        }
        return array(
            "path"=>$path,
            "success"=>true
        );
    }

    static function file_upload($file, $type = 'image', $name = '', $compress = false) {
        $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        if (empty($file)) {
            return error(-1, '没有上传内容');
        }
        if (!in_array($type, array('image', 'thumb', 'voice', 'video', 'audio'))) {
            return error(-2, '未知的上传类型');
        }
        global $_W;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $setting = SettingService::Load('upload');
        switch ($type) {
            case 'image':
            case 'thumb':
                $allowExt = array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'ico');
                $limit = $setting['upload']['image']['limit'];
                break;
            case 'voice':
            case 'audio':
                $allowExt = array('mp3', 'wma', 'wav', 'amr');
                $limit = $setting['upload']['audio']['limit'];
                break;
            case 'video':
                $allowExt = array('rm', 'rmvb', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4');
                $limit = $setting['upload']['audio']['limit'];
                break;
        }
        $type_setting = in_array($type, array('image', 'thumb')) ? 'image' : 'audio';
        $setting = $_W['setting']['upload'][$type_setting];

        if (!empty($setting['extentions'])) {
            $allowExt = $setting['extentions'];
        }
        if (!in_array(strtolower($ext), $allowExt) || in_array(strtolower($ext), $harmtype)) {
            return error(-3, '不允许上传此类文件');
        }
        if (!empty($limit) && $limit * 1024 < filesize($file['tmp_name'])) {
            return error(-4, "上传的文件超过大小限制，请上传小于 {$limit}k 的文件");
        }

        $result = array();
        if (empty($name) || 'auto' == $name) {
            $uniacid = intval($_W['uniacid']);
            $path = "{$type}s/{$uniacid}/" . date('Y/m/');
            self::mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = self::file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);

            $result['path'] = $path . $filename;
        } else {
            self::mkdirs(dirname(ATTACHMENT_ROOT . '/' . $name));
            if (!strexists($name, $ext)) {
                $name .= '.' . $ext;
            }
            $result['path'] = $name;
        }

        $save_path = ATTACHMENT_ROOT . '/' . $result['path'];

        $image = '';
        if (isset($setting['zip_percentage']) && $setting['zip_percentage'] == 100 && SettingService::check_php_ext('exif')) {
            $exif = exif_read_data($file['tmp_name']);
            if (!empty($exif['THUMBNAIL']['Orientation'])) {
                $image = imagecreatefromstring(file_get_contents($file['tmp_name']));
                switch($exif['THUMBNAIL']['Orientation']) {
                    case 8:
                        $image = imagerotate($image,0,0);
                        break;
                    case 3:
                        $image = imagerotate($image,180,0);
                        break;
                    case 6:
                        $image = imagerotate($image,-90,0);
                        break;
                    default:
                        $image = imagerotate($image,0,0);
                        break;
                }
            }
        }
        if (empty($image)) {
            $newimage = self::file_move($file['tmp_name'], $save_path);
        } else {
            $newimage = imagejpeg($image,$save_path);
            imagedestroy($image);
        }
        if (empty($newimage)) {
            return error(-1, '文件上传失败, 请将 attachment 目录权限先777 <br> (如果777上传失败,可尝试将目录设置为755)');
        }

        if ('image' == $type && $compress) {
            self::file_image_quality($save_path, $save_path, $ext);
        }

        if (self::file_is_uni_attach($save_path)) {
            $check_result = self::file_check_uni_space($save_path);
            if (is_error($check_result)) {
                @unlink($save_path);

                return $check_result;
            }
            $uni_remote_setting = uni_setting_load('remote');
            if (empty($uni_remote_setting['remote']) && empty($_W['setting']['remote']['type'])) {
                self::file_change_uni_attchsize($save_path);
            }
        }

        $result['success'] = true;

        return $result;
    }

    function file_wechat_upload($file, $type = 'image', $name = '') {
        $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        if (empty($file)) {
            return error(-1, '没有上传内容');
        }
        if (!in_array($type, array('image', 'thumb', 'voice', 'video', 'audio'))) {
            return error(-2, '未知的上传类型');
        }

        global $_W;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if (in_array(strtolower($ext), $harmtype)) {
            return error(-3, '不允许上传此类文件');
        }

        $result = array();
        if (empty($name) || 'auto' == $name) {
            $uniacid = intval($_W['uniacid']);
            $path = "{$type}s/{$uniacid}/" . date('Y/m/');
            $this->mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = $this->file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);
            $result['path'] = $path . $filename;
        } else {
            $this->mkdirs(dirname(ATTACHMENT_ROOT . '/' . $name));
            if (!strexists($name, $ext)) {
                $name .= '.' . $ext;
            }
            $result['path'] = $name;
        }
        $save_path = ATTACHMENT_ROOT . '/' . $result['path'];
        if (!$this->file_move($file['tmp_name'], $save_path)) {
            return error(-1, '保存上传文件失败');
        }

        if ('image' == $type) {
            $this->file_image_quality($save_path, $save_path, $ext);
        }
        $result['success'] = true;

        return $result;
    }

    public static function file_remote_upload($filename, $auto_delete_local = true) {
        global $_W;
        if (empty($_W['setting']['remote']['type'])) {
            return false;
        }
        if ($_W['setting']['remote']['type'] == 1) {
            return error(1, '远程附件上传失败，暂不支持FTP');
        } elseif ($_W['setting']['remote']['type'] == 2) {
            try {
                AttachmentService::alioss_upload($filename, ATTACHMENT_ROOT . $filename);
                if ($auto_delete_local) {
                    self::file_delete($filename);
                }
            }catch (\Exception $exception){
                return error(-1,$exception->getMessage());
            }
        } elseif ($_W['setting']['remote']['type'] == 3) {
            return error(1, '远程附件上传失败，暂不支持七牛云存储');
        } elseif ($_W['setting']['remote']['type'] == 4) {
            $result = AttachmentService::cos_upload($filename,$_W['setting']['remote']['cos']);
            if (is_error($result)) return $result;
            if ($auto_delete_local) {
                self::file_delete($filename);
            }
        }
        return true;
    }

    function file_dir_remote_upload($dir_path, $limit = 200) {
        global $_W;
        if (empty($_W['setting']['remote']['type'])) {
            return error(1, '未开启远程附件');
        }
        if (!empty($dir_path)) {
            $local_attachment = $this->file_tree_limit($dir_path, $limit);
        } else {
            $local_attachment = array();
        }
        if (is_array($local_attachment) && !empty($local_attachment)) {
            foreach ($local_attachment as $attachment) {
                $filename = str_replace(ATTACHMENT_ROOT, '', $attachment);
                list($image_dir, $file_account) = explode('/', $filename);
                if ('global' == $file_account || !$this->file_is_image($attachment)) {
                    continue;
                }
                $result = $this->file_remote_upload($filename);
                if (is_error($result)) {
                    return $result;
                }
            }
        }

        return true;
    }

    public static function file_random_name($dir, $ext) {
        do {
            $filename = random(30) . '.' . $ext;
        } while (file_exists($dir . $filename));

        return $filename;
    }

    public static function file_delete($file) {
        global $_W;
        if (empty($file)) {
            return false;
        }
        $file_extension = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($file_extension, array('php', 'html', 'js', 'css', 'ttf', 'otf', 'eot', 'svg', 'woff'))) {
            return false;
        }

        $uni_remote_setting = uni_setting_load('remote');
        if (empty($uni_remote_setting['remote']) && empty($_W['setting']['remote']['type'])) {
            if (file_exists(ATTACHMENT_ROOT . '/' . $file) && self::file_is_uni_attach(ATTACHMENT_ROOT . '/' . $file)) {
                self::file_change_uni_attchsize(ATTACHMENT_ROOT . '/' . $file, false);
            }
        }
        if (file_exists($file)) {
            @unlink($file);
        }
        if (file_exists(ATTACHMENT_ROOT . '/' . $file)) {
            @unlink(ATTACHMENT_ROOT . '/' . $file);
        }

        return true;
    }

    public static function file_remote_delete($file) {
        global $_W;
        if (empty($file)) {
            return true;
        }
        if ($_W['setting']['remote']['type'] == '1') {
            return error(1, '删除附件失败，暂不支持FTP');
        } elseif ($_W['setting']['remote']['type'] == '2') {
            $loadoss = CloudService::LoadCom('alioss');
            if (is_error($loadoss)) return $loadoss;
            $buckets = AttachmentService::alioss_buctkets($_W['setting']['remote']['alioss']['key'], $_W['setting']['remote']['alioss']['secret']);
            $endpoint = 'http://' . $buckets[$_W['setting']['remote']['alioss']['bucket']]['location'] . '.aliyuncs.com';
            try {
                $ossClient = new \OSS\OssClient($_W['setting']['remote']['alioss']['key'], $_W['setting']['remote']['alioss']['secret'], $endpoint);
                $ossClient->deleteObject($_W['setting']['remote']['alioss']['bucket'], $file);
            } catch (\OSS\Core\OssException $e) {
                return error(1, '删除oss远程文件失败');
            }
        } elseif ($_W['setting']['remote']['type'] == '3') {
            return error(1, '删除七牛远程文件失败');
        } elseif ($_W['setting']['remote']['type'] == '4') {
            $loadcos = CloudService::LoadCom('cosv5');
            if (is_error($loadcos)) return $loadcos;
            try {
                $key = $file;
                $bucket = $_W['setting']['remote']['cos']['bucket'] . '-' . $_W['setting']['remote']['cos']['appid'];
                $cosClient = new Client(
                    array(
                        'region' => $_W['setting']['remote']['cos']['local'],
                        'credentials'=> array(
                            'secretId'  => $_W['setting']['remote']['cos']['secretid'],
                            'secretKey' => $_W['setting']['remote']['cos']['secretkey'])));
                $cosClient->deleteObjects(array(
                    'Bucket' => $bucket,
                    'Objects' => array(array('Key' => $key))
                ));
            } catch (\Exception $e) {
                return error(1, '删除cos远程文件失败');
            }
        }
        return true;
    }

    function file_image_thumb($srcfile, $desfile = '', $width = 0) {
        global $_W;

        if (0 == intval($width)) {
            $width = intval($_W['setting']['upload']['image']['width']);
        }
        if (empty($desfile)) {
            $ext = pathinfo($srcfile, PATHINFO_EXTENSION);
            $srcdir = dirname($srcfile);
            do {
                $desfile = $srcdir . '/' . random(30) . ".{$ext}";
            } while (file_exists($desfile));
        }

        $des = dirname($desfile);
        if (!file_exists($des)) {
            if (!$this->mkdirs($des)) {
                return error('-1', '创建目录失败');
            }
        } elseif (!is_writable($des)) {
            return error('-1', '目录无法写入');
        }
        $org_info = @getimagesize($srcfile);
        if ($org_info) {
            if (0 == $width || $width > $org_info[0]) {
                copy($srcfile, $desfile);

                return str_replace(ATTACHMENT_ROOT . '/', '', $desfile);
            }
        }
        $scale_org = $org_info[0] / $org_info[1];
        $height = $width / $scale_org;
        $desfile = Image::create($srcfile)->resize($width, $height)->saveTo($desfile);
        if (!$desfile) {
            return false;
        }

        return str_replace(ATTACHMENT_ROOT . '/', '', $desfile);
    }

    function file_image_crop($src, $desfile, $width = 400, $height = 300, $position = 1) {
        $des = dirname($desfile);
        if (!file_exists($des)) {
            if (!$this->mkdirs($des)) {
                return error('-1', '创建目录失败');
            }
        } elseif (!is_writable($des)) {
            return error('-1', '目录无法写入');
        }

        return Image::create($src)
            ->crop($width, $height, $position)
            ->saveTo($desfile);
    }

    function file_lists($filepath, $subdir = 1, $ex = '', $isdir = 0, $md5 = 0, $enforcement = 0) {
        static $file_list = array();
        if ($enforcement) {
            $file_list = array();
        }
        $flags = $isdir ? GLOB_ONLYDIR : 0;
        $list = glob($filepath . '*' . (!empty($ex) && empty($subdir) ? '.' . $ex : ''), $flags);
        if (!empty($ex)) {
            $ex_num = strlen($ex);
        }
        foreach ($list as $k => $v) {
            $v = str_replace('\\', '/', $v);
            $v1 = str_replace(IA_ROOT . '/', '', $v);
            if ($subdir && is_dir($v)) {
                $this->file_lists($v . '/', $subdir, $ex, $isdir, $md5);
                continue;
            }
            if (!empty($ex) && strtolower(substr($v, -$ex_num, $ex_num)) == $ex) {
                if ($md5) {
                    $file_list[$v1] = md5_file($v);
                } else {
                    $file_list[] = $v1;
                }
                continue;
            } elseif (!empty($ex) && strtolower(substr($v, -$ex_num, $ex_num)) != $ex) {
                unset($list[$k]);
                continue;
            }
        }

        return $file_list;
    }

    function file_remote_attach_fetch($url, $limit = 0, $path = '') {
        global $_W;
        $url = trim($url);
        if (empty($url)) {
            return error(-1, '文件地址不存在');
        }
        $resp = HttpService::ihttp_get($url);

        if (is_error($resp)) {
            return error(-1, '提取文件失败, 错误信息: ' . $resp['message']);
        }
        if (200 != intval($resp['code'])) {
            return error(-1, '提取文件失败: 未找到该资源文件.');
        }
        $get_headers = $this->file_media_content_type($url);
        if (empty($get_headers)) {
            return error(-1, '提取资源失败, 资源文件类型错误.');
        } else {
            $ext = $get_headers['ext'];
            $type = $get_headers['type'];
        }

        if (empty($path)) {
            $path = $type . "/{$_W['uniacid']}/" . date('Y/m/');
        } else {
            $path = parse_path($path);
        }
        if (!$path) {
            return error(-1, '提取文件失败: 上传路径配置有误.');
        }

        if (!is_dir(ATTACHMENT_ROOT . $path)) {
            if (!$this->mkdirs(ATTACHMENT_ROOT . $path, 0755, true)) {
                return error(-1, '提取文件失败: 权限不足.');
            }
        }


        if (!$limit) {
            if ('images' == $type) {
                $limit = $_W['setting']['upload']['image']['limit'] * 1024;
            } else {
                $limit = $_W['setting']['upload']['audio']['limit'] * 1024;
            }
        } else {
            $limit = $limit * 1024;
        }
        if (intval($resp['headers']['Content-Length']) > $limit) {
            return error(-1, '上传的媒体文件过大(' . sizecount($resp['headers']['Content-Length']) . ' > ' . sizecount($limit));
        }
        $filename = $this->file_random_name(ATTACHMENT_ROOT . $path, $ext);
        $pathname = $path . $filename;
        $fullname = ATTACHMENT_ROOT . $pathname;
        if (false == file_put_contents($fullname, $resp['content'])) {
            return error(-1, '提取失败.');
        }

        return $pathname;
    }

    public static function file_media_content_type($url) {
        $file_header = iget_headers($url, 1);
        if (empty($url) || !is_array($file_header)) {
            return false;
        }
        switch ($file_header['Content-Type']) {
            case 'application/x-jpg':
            case 'image/jpg':
            case 'image/jpeg':
                $ext = 'jpg';
                $type = 'images';
                break;
            case 'image/png':
                $ext = 'png';
                $type = 'images';
                break;
            case 'image/gif':
                $ext = 'gif';
                $type = 'images';
                break;
            case 'video/mp4':
            case 'video/mpeg4':
                $ext = 'mp4';
                $type = 'videos';
                break;
            case 'video/x-ms-wmv':
                $ext = 'wmv';
                $type = 'videos';
                break;
            case 'audio/mpeg':
                $ext = 'mp3';
                $type = 'audios';
                break;
            case 'audio/mp4':
                $ext = 'mp4';
                $type = 'audios';
                break;
            case 'audio/x-ms-wma':
                $ext = 'wma';
                $type = 'audios';
                break;
            default:
                return false;
                break;
        }

        return array('ext' => $ext, 'type' => $type);
    }

    function file_allowed_media($type) {
        global $_W;
        if (!in_array($type, array('image', 'audio'))) {
            return array();
        }
        if (empty($_W['setting']['upload'][$type]['extention']) || !is_array($_W['setting']['upload'][$type]['extention'])) {
            return $_W['config']['upload'][$type]['extentions'];
        }

        return $_W['setting']['upload'][$type]['extention'];
    }

    function file_is_image($url) {
        global $_W;
        $allowed_media = $this->file_allowed_media('image');

        if ('//' == substr($url, 0, 2)) {
            $url = 'http:' . $url;
        }
        if (0 == strpos($url, $_W['siteroot'] . 'attachment/')) {
            $url = str_replace($_W['siteroot'] . 'attachment/', ATTACHMENT_ROOT, $url);
        }
        $lower_url = strtolower($url);
        if (('http://' == substr($lower_url, 0, 7)) || ('https://' == substr($lower_url, 0, 8))) {
            $analysis_url = parse_url($lower_url);
            $preg_str = '/.*(\.' . implode('|\.', $allowed_media) . ')$/';
            if (!empty($analysis_url['query']) || !preg_match($preg_str, $lower_url) || !preg_match($preg_str, $analysis_url['path'])) {
                return false;
            }
            $img_headers = $this->file_media_content_type($url);
            if (empty($img_headers) || !in_array($img_headers['ext'], $allowed_media)) {
                return false;
            }
        }

        $info = igetimagesize($url);
        if (!is_array($info)) {
            return false;
        }

        return true;
    }

    public static function file_image_quality($src, $to_path, $ext) {
        global $_W;
        if ('gif' == strtolower($ext)) {
            return false;
        }
        $quality = intval($_W['setting']['upload']['image']['zip_percentage']);
        if ($quality <= 0 || $quality >= 100) {
            return false;
        }

        if (filesize($src) / 1024 > 5120) {
            return false;
        }

        $result = Image::create($src, $ext)->saveTo($to_path, $quality);

        return $result;
    }

    public static function file_is_uni_attach($file) {
        global $_W;
        if (!is_file($file) || !$_W['uniacid']) {
            return false;
        }

        return strpos($file, "/{$_W['uniacid']}/") > 0;
    }

    public static function file_check_uni_space($file) {
        global $_W;
        if (!is_file($file)) {
            return error(-1, '未找到上传的文件。');
        }
        $uni_remote_setting = SettingService::uni_load('remote');
        if (empty($uni_remote_setting['remote']['type'])) {
            $uni_setting = SettingService::uni_load(array('attachment_limit', 'attachment_size'));

            $attachment_limit = intval($uni_setting['attachment_limit']);
            if (0 == $attachment_limit) {
                $upload = SettingService::Load('upload');
                $attachment_limit = empty($upload['upload']['attachment_limit']) ? 0 : intval($upload['upload']['attachment_limit']);
            }

            if ($attachment_limit > 0) {
                $file_size = max(1, round(filesize($file) / 1024));
                if (($file_size + $uni_setting['attachment_size']) > ($attachment_limit * 1024)) {
                    return error(-1, '上传失败，可使用的附件空间不足！');
                }
            }
        }

        return true;
    }

    public static function file_change_uni_attchsize($file, $is_add = true) {
        global $_W;
        if (!is_file($file)) {
            return error(-1, '未找到的文件。');
        }
        $file_size = round(filesize($file) / 1024);
        $file_size = max(1, $file_size);

        $result = true;
        $uni_remote_setting = uni_setting_load('remote');
        if (empty($uni_remote_setting['remote']['type']) && !empty($_W['uniacid'])) {
            $uni_settings = pdo_get('uni_settings', array('uniacid' => $_W['uniacid']), array('uniacid','attachment_size'));
            if (empty($uni_settings)) {
                $result = pdo_insert('uni_settings', array('attachment_size' => $file_size, 'uniacid' => $_W['uniacid']));
            } else {
                if (!$is_add) {
                    $file_size = -$file_size;
                }
                $result = pdo_update('uni_settings', array('attachment_size' => (intval($uni_settings['attachment_size']) +$file_size)), array('uniacid' => $_W['uniacid']));
            }
            $uniacid = $_W['uniacid'];

            $cachekey = CacheService::system_key('unisetting', array('uniacid' => $uniacid));
            $unisetting = Cache::get($cachekey);
            $unisetting['attachment_size'] += $file_size;
            $unisetting['attachment_size'] = max(0, $unisetting['attachment_size']);
            Cache::put($cachekey, $unisetting,86400*7);
        }

        return $result;
    }

}
