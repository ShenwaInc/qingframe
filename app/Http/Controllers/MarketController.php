<?php

namespace App\Http\Controllers;

use App\Services\CloudService;
use App\Services\MSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    //
    /**
     * @url /market
     * @method GET|POST
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $cacheKey = "cloud:module_list:1" . urlencode($keyword);
        $res = Cache::get($cacheKey, array());
        if (empty($res)){
            $data = array(
                'r'=>'cloud.packages',
                'pidentity'=>config('system.identity'),
                'page'=>1,
                'category'=>1,
                'authorize'=>1,
                'keyword'=>$keyword
            );
            $res = CloudService::CloudApi("", $data);
            Cache::put($cacheKey, $res, 600);
        }
        $plugins = $plugin_features = [];
        if (!is_error($res) && !empty($res['servers'])){
            $plugins = $res['servers'];
        }
        $category = array(
            ['title'=>'人工智能', 'list'=>[], 'id'=>'ai', 'icon'=>'fas fa-brain'],
            ['title'=>'电商系统', 'list'=>[], 'id'=>'ecommerce', 'icon'=>'fas fa-shopping-cart'],
            ['title'=>'大健康', 'list'=>[], 'id'=>'health', 'icon'=>'fas fa-heartbeat'],
            ['title'=>'智能硬件', 'list'=>[], 'id'=>'iot', 'icon'=>'fas fa-microchip'],
            ['title'=>'财务系统', 'list'=>[], 'id'=>'finance', 'icon'=>'fas fa-money-bill-wave'],
            ['title'=>'实用工具', 'list'=>[], 'id'=>'tools', 'icon'=>'fas fa-tools'],
            ['title'=>'社交群聊', 'list'=>[], 'id'=>'social', 'icon'=>'fas fa-users'],
            ['title'=>'其他应用', 'list'=>[], 'id'=>'other', 'icon'=>'fas fa-ellipsis-h']
        );
        if (!empty($plugins)){
            $plugin_features = array_filter($plugins, function ($item) {
                return $item['featured'] == 1;
            });
            foreach ($category as &$item){
                $item['list'] = array_filter($plugins, function ($plugin) use ($item) {
                    return strexists($plugin['tags']??$plugin['name'], $item['title']);
                });
            }
        }
        $user = $request->user();
        if (!empty($user)){
            global $_W;
            $profile = DB::table('users_profile')->where('uid', $user->uid)->select('avatar','gender','mobile','email', 'realname')->first();
            $_W['user'] = array_merge($user->toArray(), $profile?:[]);
            $_W['uid'] = $user->uid;
        }
        $cloudServers = MSService::cloudServers(1, $keyword, true);
        return $this->globalView('market', ['title'=>'轻如云市场', 'plugins'=>$plugin_features, 'category'=>$category, 'curPage'=>'market', 'servers'=>$cloudServers?:[]]);
    }

    /**
     * @url /market/search
     * @method GET|POST
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $page = (int)$request->input('page', 1);
        $featured = $request->input('featured');
        $apiData = array(
            'r'=>'cloud.packages',
            'pidentity'=>config('system.identity'),
            'page'=>$page,
            'category'=>1,
            'authorize'=>1,
            'keyword'=>$keyword
        );
        if (!is_null($featured)){
            $apiData['featured'] = (int)$featured;
        }
        $res = CloudService::CloudApi("", $apiData);
        if (is_error($res)){
            return $this->message($res['message']);
        }
        return $this->success([
            'plugins'=>$res['servers'],
            'total'=>$res['total'],
            'page'=>$res['page'],
            'pageSize'=>$res['pageSize']
        ]);
    }
}
