<?php


namespace App\Services;


use App\Utils\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FileService
{

    public static function SetAttachUrl() {
        global $_W;
        if(empty($_W['setting']['remote_complete_info'])){
            $_W['setting']['remote_complete_info'] = $_W['setting']['remote'];
        }
        if (!empty($_W['uniacid'])) {
            $uni_remote_setting = SettingService::uni_load('remote');
            if (!empty($uni_remote_setting['remote']['type'])) {
                $_W['setting']['remote'] = $uni_remote_setting['remote'];
            }
        }
        $attach_url = $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
        if (!empty($_W['setting']['remote']['type'])) {
            if ($_W['setting']['remote']['type'] == 1) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 2) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 3) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
            } elseif ($_W['setting']['remote']['type'] == 4) {
                $attach_url = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
            }
        }
        return $attach_url;
    }

    public static function file_write($filename, $data) {
        $uri = ATTACHMENT_ROOT . '/' . $filename;
        $path = dirname($uri);
        self::mkdirs($path);
        Storage::put($filename, $data);
        return is_file($uri);
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


    public static function mkdirs($path, $perm=0777, $rec=false) {
        if (!is_dir($path)) {
            self::mkdirs(dirname($path));
            mkdir($path, $perm, $rec);
        }

        return is_dir($path);
    }


    public static function rmdirs($path, $clean = false) {
        if (!is_dir($path)) {
            return true;
        }
        $files = glob($path . '/*');
        if ($files) {
            foreach ($files as $file) {
                is_dir($file) ? self::rmdirs($file) : @unlink($file);
            }
        }

        return $clean || @rmdir($path);
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

    public static function file_remote_upload($filename, $auto_delete_local = true) {
        $result = serv('storage')->remoteUpload($filename);
        if (is_error($result)) return $result;
        if ($auto_delete_local) {
            self::file_delete($filename);
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
