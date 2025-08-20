<?php

namespace App\Helpers;

use App\Services\WechatService;
use App\Utils\WeAccount;
use Illuminate\Support\Facades\DB;

class WechatHelper
{
    /**
     * 更新当前自定义菜单
     * 完整实现原有 menu_update_currentself() 功能
     * 包含菜单同步、数据库更新、状态管理等完整逻辑
     * 
     * @param array $menu 菜单数据（可选，如果不提供则从微信获取）
     * @param int $uniacid 统一账户ID
     * @return array|bool 成功返回true，失败返回错误信息
     */
    public static function menuUpdateCurrentself($menu = null, $uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        // 获取账户信息
        $account_api = WeAccount::createByUniacid($uniacid);
        if (is_error($account_api)) {
            return error(-1, '账户不存在或无法访问');
        }
        
        // 从微信获取当前菜单信息
        $default_menu_info = $account_api->menuCurrentQuery();
        if (is_error($default_menu_info)) {
            return error(-1, $default_menu_info['message']);
        }
        
        // 检查菜单是否开启
        if (empty($default_menu_info['is_menu_open']) || empty($default_menu_info['selfmenu_info'])) {
            return true;
        }
        
        $default_menu = $default_menu_info['selfmenu_info'];
        
        // 处理子按钮
        $default_sub_button = array();
        if (!empty($default_menu['button'])) {
            foreach ($default_menu['button'] as $key => &$button) {
                if (!empty($button['sub_button'])) {
                    $default_sub_button[$key] = $button['sub_button'];
                } else {
                    unset($button['sub_button']);
                }
                ksort($button);
            }
            unset($button);
        }
        
        // 排序菜单
        if (!empty($default_menu)) {
            ksort($default_menu);
        }
        
        // 序列化菜单数据
        $wechat_menu_data = base64_encode(serialize($default_menu));
        
        // 获取所有默认菜单
        $all_default_menus = DB::table('uni_account_menus')
            ->where(['type' => 1, 'uniacid' => $uniacid])
            ->get()
            ->keyBy('id')
            ->toArray();
        
        $default_menu_id = null;
        
        if (!empty($all_default_menus)) {
            foreach ($all_default_menus as $menus_key => $menu_data) {
                if (empty($menu_data['data'])) {
                    continue;
                }
                
                $single_menu_info = unserialize(base64_decode($menu_data['data']));
                if (!is_array($single_menu_info) || empty($single_menu_info['button'])) {
                    continue;
                }
                
                // 处理单个菜单的子按钮
                foreach ($single_menu_info['button'] as $key => &$single_button) {
                    if (!empty($default_sub_button[$key])) {
                        $single_button['sub_button'] = $default_sub_button[$key];
                    } else {
                        unset($single_button['sub_button']);
                    }
                    ksort($single_button);
                }
                unset($single_button);
                
                ksort($single_menu_info);
                $local_menu_data = base64_encode(serialize($single_menu_info));
                
                // 比较菜单数据
                if ($wechat_menu_data == $local_menu_data) {
                    $default_menu_id = $menus_key;
                    break;
                }
            }
        }
        
        // 更新或插入菜单记录
        if (!empty($default_menu_id)) {
            // 更新现有菜单状态
            DB::table('uni_account_menus')
                ->where('id', $default_menu_id)
                ->update(['status' => 1]);
            
            // 将其他菜单设为非默认
            DB::table('uni_account_menus')
                ->where([
                    ['id', '!=', $default_menu_id],
                    ['type', '=', 1],
                    ['uniacid', '=', $uniacid]
                ])
                ->update(['status' => 0]);
        } else {
            // 插入新菜单记录
            $insert_data = array(
                'uniacid' => $uniacid,
                'type' => 1,
                'group_id' => -1,
                'sex' => 0,
                'data' => $wechat_menu_data,
                'client_platform_type' => 0,
                'area' => '',
                'createtime' => TIMESTAMP,
                'menuid' => 0,
                'status' => 1
            );
            
            $insert_id = DB::table('uni_account_menus')->insertGetId($insert_data);
            
            // 更新菜单标题
            DB::table('uni_account_menus')
                ->where('id', $insert_id)
                ->update(['title' => '默认菜单_' . $insert_id]);
            
            // 将其他菜单设为非默认
            DB::table('uni_account_menus')
                ->where([
                    ['id', '!=', $insert_id],
                    ['type', '=', 1],
                    ['uniacid', '=', $uniacid]
                ])
                ->update(['status' => 0]);
        }
        
        // 记录操作日志
        self::logMenuOperation($uniacid, 'update_currentself', $default_menu, true);
        
        return true;
    }
    
    /**
     * 更新条件菜单
     * 完整实现原有 menu_update_conditional() 功能
     * 
     * @param int $uniacid 统一账户ID
     * @return array|bool 成功返回true，失败返回错误信息
     */
    public static function menuUpdateConditional($uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        $account_api = WeAccount::createByUniacid($uniacid);
        if (is_error($account_api)) {
            return error(-1, '账户不存在或无法访问');
        }
        
        $conditional_menu_info = $account_api->menuQuery();
        if (is_error($conditional_menu_info)) {
            return error(-1, $conditional_menu_info['message']);
        }
        
        // 将所有条件菜单设为非激活状态
        DB::table('uni_account_menus')
            ->where(['uniacid' => $uniacid, 'type' => 2])
            ->update(['status' => 0]);
        
        if (!empty($conditional_menu_info['conditionalmenu'])) {
            foreach ($conditional_menu_info['conditionalmenu'] as $menu) {
                $data = array(
                    'uniacid' => $uniacid,
                    'type' => 2, // MENU_CONDITIONAL
                    'group_id' => isset($menu['matchrule']['tag_id']) ? $menu['matchrule']['tag_id'] : (isset($menu['matchrule']['group_id']) ? $menu['matchrule']['group_id'] : '-1'),
                    'sex' => $menu['matchrule']['sex'],
                    'client_platform_type' => $menu['matchrule']['client_platform_type'],
                    'area' => trim($menu['matchrule']['country']) . trim($menu['matchrule']['province']) . trim($menu['matchrule']['city']),
                    'data' => base64_encode(self::iserializer($menu)),
                    'menuid' => $menu['menuid'],
                    'status' => 1, // STATUS_ON
                );
                
                $menu_id = null;
                if (!empty($menu['matchrule'])) {
                    $menu_info = DB::table('uni_account_menus')
                        ->where('menuid', $menu['menuid'])
                        ->where('type', 2)
                        ->first();
                    if ($menu_info) {
                        $menu_id = $menu_info['id'];
                    }
                }
                
                if (!empty($menu_id)) {
                    $data['title'] = !empty($menu_info['title']) ? $menu_info['title'] : '个性化菜单_' . $menu_id;
                    DB::table('uni_account_menus')
                        ->where(['uniacid' => $uniacid, 'id' => $menu_id])
                        ->update($data);
                } else {
                    $insert_id = DB::table('uni_account_menus')->insertGetId($data);
                    DB::table('uni_account_menus')
                        ->where('id', $insert_id)
                        ->update(['title' => '个性化菜单_' . $insert_id]);
                }
            }
        }
        
        return true;
    }
    
    /**
     * 删除菜单
     * 完整实现原有 menu_delete() 功能
     * 
     * @param int $id 菜单ID
     * @param int $uniacid 统一账户ID
     * @return array|bool 成功返回true，失败返回错误信息
     */
    public static function menuDelete($id, $uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        $menu_info = self::menuGet($id);
        if (empty($menu_info)) {
            return error(-1, '菜单不存在或已经删除');
        }
        
        if ($menu_info['status'] == 0) { // STATUS_OFF
            DB::table('uni_account_menus')
                ->where(['uniacid' => $uniacid, 'id' => $id])
                ->delete();
            return error(0, '删除菜单成功！');
        }
        
        if ($menu_info['type'] == 2 && $menu_info['menuid'] > 0 && $menu_info['status'] != 0) { // MENU_CONDITIONAL
            $account_api = WeAccount::createByUniacid($uniacid);
            $result = $account_api->menuDelete($menu_info['menuid']);
            if (is_error($result)) {
                return error(-1, $result['message']);
            }
            DB::table('uni_account_menus')
                ->where(['uniacid' => $uniacid, 'id' => $id])
                ->delete();
        }
        
        return true;
    }
    
    /**
     * 推送菜单
     * 完整实现原有 menu_push() 功能
     * 
     * @param int $id 菜单ID
     * @param int $uniacid 统一账户ID
     * @return array|bool 成功返回true，失败返回错误信息
     */
    public static function menuPush($id, $uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        $menu_info = self::menuGet($id);
        if (empty($menu_info)) {
            return error(-1, '菜单不存在或已删除');
        }
        
        if ($menu_info['status'] == 0) { // STATUS_OFF
            $post = self::iunserializer(base64_decode($menu_info['data']));
            if (empty($post)) {
                return error(-1, '菜单数据错误');
            }
            
            $is_conditional = (!empty($post['matchrule']) && $menu_info['type'] == 2) ? true : false;
            
            $account_api = WeAccount::createByUniacid($uniacid);
            $menu = $account_api->menuBuild($post, $is_conditional);
            $result = $account_api->menuCreate($menu);
            
            if (is_error($result)) {
                return error(-1, $result['message']);
            }
            
            if ($menu_info['type'] == 1) { // MENU_CURRENTSELF
                DB::table('uni_account_menus')
                    ->where('id', $menu_info['id'])
                    ->update(['status' => 1]);
                DB::table('uni_account_menus')
                    ->where([
                        ['id', '!=', $menu_info['id']],
                        ['uniacid', '=', $uniacid],
                        ['type', '=', 1]
                    ])
                    ->update(['status' => 0]);
            } elseif ($menu_info['type'] == 2) { // MENU_CONDITIONAL
                if ($post['matchrule']['group_id'] != -1) {
                    $menu['matchrule']['groupid'] = $menu['matchrule']['tag_id'];
                    unset($menu['matchrule']['tag_id']);
                }
                DB::table('uni_account_menus')
                    ->where(['uniacid' => $uniacid, 'id' => $menu_info['id']])
                    ->update(['status' => 1, 'menuid' => $result]);
            }
            
            return true;
        }
        
        if ($menu_info['status'] == 1 && $menu_info['type'] == 2 && $menu_info['menuid'] > 0) { // MENU_CONDITIONAL
            $account_api = WeAccount::createByUniacid($uniacid);
            $result = $account_api->menuDelete($menu_info['menuid']);
            if (is_error($result)) {
                return error(-1, $result['message']);
            } else {
                DB::table('uni_account_menus')
                    ->where('id', $menu_info['id'])
                    ->update(['status' => 0]);
                return true;
            }
        }
    }
    
    /**
     * 获取菜单信息
     * 
     * @param int $id 菜单ID
     * @return array 菜单信息
     */
    public static function menuGet($id)
    {
        $id = intval($id);
        if (empty($id)) {
            return array();
        }
        
        $menu_info = DB::table('uni_account_menus')->where('id', $id)->first();
        if (!empty($menu_info)) {
            return (array)$menu_info;
        } else {
            return array();
        }
    }
    
    /**
     * 获取默认菜单
     * 
     * @param int $uniacid 统一账户ID
     * @return array 默认菜单信息
     */
    public static function menuDefault($uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        return DB::table('uni_account_menus')
            ->where(['status' => 1, 'type' => 1, 'uniacid' => intval($uniacid)])
            ->first();
    }
    
    /**
     * 获取菜单语言列表
     * 
     * @return array 语言列表
     */
    public static function menuLanguages()
    {
        $languages = array(
            array('ch'=>'简体中文', 'en'=>'zh_CN'),
            array('ch'=>'繁体中文TW', 'en'=>'zh_TW'),
            array('ch'=>'繁体中文HK', 'en'=>'zh_HK'),
            array('ch'=>'英文', 'en'=>'en'),
            array('ch'=>'印尼', 'en'=>'id'),
            array('ch'=>'马来', 'en'=>'ms'),
            array('ch'=>'西班牙', 'en'=>'es'),
            array('ch'=>'韩国', 'en'=>'ko'),
            array('ch'=>'意大利 ', 'en'=>'it'),
            array('ch'=>'日本', 'en'=>'ja'),
            array('ch'=>'波兰', 'en'=>'pl'),
            array('ch'=>'葡萄牙', 'en'=>'pt'),
            array('ch'=>'俄国', 'en'=>'ru'),
            array('ch'=>'泰文', 'en'=>'th'),
            array('ch'=>'越南', 'en'=>'vi'),
            array('ch'=>'阿拉伯语', 'en'=>'ar'),
            array('ch'=>'北印度', 'en'=>'hi'),
            array('ch'=>'希伯来', 'en'=>'he'),
            array('ch'=>'土耳其', 'en'=>'tr'),
            array('ch'=>'德语', 'en'=>'de'),
            array('ch'=>'法语', 'en'=>'fr')
        );
        return $languages;
    }
    
    /**
     * 创建自定义菜单（简化版本）
     * 
     * @param array $menu 菜单数据
     * @param int $uniacid 统一账户ID
     * @return array|bool 成功返回菜单ID，失败返回错误信息
     */
    public static function menuCreate($menu, $uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        $account = WeAccount::create($uniacid);
        if (empty($account)) {
            return error(-1, '账户不存在');
        }
        
        if (!$account->isMenuSupported()) {
            return error(-1, '当前账户不支持自定义菜单');
        }
        
        $menuData = $account->menuBuild($menu);
        if (empty($menuData)) {
            return error(-1, '菜单数据构建失败');
        }
        
        $result = $account->menuCreate($menuData);
        if (is_error($result)) {
            return error(-1, '创建菜单失败：' . $result['message']);
        }
        
        self::logMenuOperation($uniacid, 'create', $menu, $result);
        
        return $result;
    }
    
    /**
     * 查询当前自定义菜单
     * 
     * @param int $uniacid 统一账户ID
     * @return array 菜单信息
     */
    public static function menuCurrentQuery($uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        $account = WeAccount::create($uniacid);
        if (empty($account)) {
            return error(-1, '账户不存在');
        }
        
        return $account->menuCurrentQuery();
    }
    
    /**
     * 查询所有菜单
     * 
     * @param int $uniacid 统一账户ID
     * @return array 菜单信息
     */
    public static function menuQuery($uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        $account = WeAccount::create($uniacid);
        if (empty($account)) {
            return error(-1, '账户不存在');
        }
        
        return $account->menuQuery();
    }
    
    /**
     * 修改菜单
     * 
     * @param array $menu 菜单数据
     * @param int $uniacid 统一账户ID
     * @return array|bool 成功返回菜单ID，失败返回错误信息
     */
    public static function menuModify($menu, $uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return error(-1, '缺少统一账户ID');
        }
        
        $account = WeAccount::create($uniacid);
        if (empty($account)) {
            return error(-1, '账户不存在');
        }
        
        if (!$account->isMenuSupported()) {
            return error(-1, '当前账户不支持自定义菜单');
        }
        
        $menuData = $account->menuBuild($menu);
        if (empty($menuData)) {
            return error(-1, '菜单数据构建失败');
        }
        
        $result = $account->menuModify($menuData);
        if (is_error($result)) {
            return error(-1, '修改菜单失败：' . $result['message']);
        }
        
        self::logMenuOperation($uniacid, 'modify', $menu, $result);
        
        return $result;
    }
    
    /**
     * 检查菜单是否支持
     * 
     * @param int $uniacid 统一账户ID
     * @return bool
     */
    public static function isMenuSupported($uniacid = 0)
    {
        global $_W;
        
        if (empty($uniacid)) {
            $uniacid = $_W['uniacid'] ?? 0;
        }
        
        if (empty($uniacid)) {
            return false;
        }
        
        $account = WeAccount::create($uniacid);
        if (empty($account)) {
            return false;
        }
        
        return $account->isMenuSupported();
    }
    
    /**
     * 记录菜单操作日志
     * 
     * @param int $uniacid 统一账户ID
     * @param string $operation 操作类型
     * @param array $data 操作数据
     * @param mixed $result 操作结果
     */
    private static function logMenuOperation($uniacid, $operation, $data, $result)
    {
        // 这里可以添加日志记录逻辑
        // 例如记录到数据库或日志文件
        $logData = [
            'uniacid' => $uniacid,
            'operation' => $operation,
            'data' => is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data,
            'result' => is_array($result) ? json_encode($result, JSON_UNESCAPED_UNICODE) : $result,
            'timestamp' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ];
        
        // 可以根据需要实现具体的日志记录逻辑
        // 例如：写入数据库、写入日志文件等
    }
    
    /**
     * 序列化数据（兼容 iserializer 函数）
     * 
     * @param mixed $data 要序列化的数据
     * @return string 序列化后的字符串
     */
    public static function iserializer($data)
    {
        return serialize($data);
    }
    
    /**
     * 反序列化数据（兼容 iunserializer 函数）
     * 
     * @param string $data 要反序列化的字符串
     * @return mixed 反序列化后的数据
     */
    public static function iunserializer($data)
    {
        return unserialize($data);
    }
} 