<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AccountService;
use App\Models\UniAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;

class AccountManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:manage {operation} {--uniacid=} {--name=} {--description=} {--logo=} {--uid=0} {--keyword=} {--expire=} {--role=manager} {--domain=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '多平台（租户）管理工具，operation可选值：create(创建租户),edit（编辑租户信息）,delete（删除租户）,display（展示所有租户）,detail（查看租户详情）,setExpireTime（设置到期时间）,setFounderUid（设置所有者）,setRole（增加管理角色）,removeRole（移除管理角色），setDomain（绑定域名）';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $operation = $this->argument('operation');
        $uniacid = $this->option('uniacid') ?? 0;
        $uid = $this->option('uid') ?? config('system.setting.founder', 1);
        switch ($operation) {
            case 'create':
                // 创建租户
                $data = post_var(['name', 'description', 'logo'], $this->options());
                if (empty($data['name'])){
                    $this->error('请输入平台名称');
                    return;
                }
                if (empty($data['logo'])){
                    $data['logo'] = '/static/icon200.jpg';
                }
                if (empty($data['description'])){
                    $data['description'] = 'Artisan创建于' . date('Y-m-d H:i');
                }
                $res = AccountService::createAccount($data, $uid);
                if (is_error($res)){
                    $this->error($res['message']);
                }else{
                    $this->info('创建成功，租户ID为' . $res['uniacid']);
                }
                break;
            case 'edit':
                // 编辑租户资料
                $data = post_var(['name', 'description', 'logo'], $this->options());
                if (empty($uniacid)){
                    $this->error('租户ID不能为空');
                    return;
                }
                if (isset($data['name']) && empty($data['name'])){
                    $this->error(__('platformNameEmpty'));
                    return;
                }
                if (isset($data['logo']) && empty($data['logo'])){
                    unset($data['logo']);
                }
                if (empty($data)){
                    $this->error(__('operationFailed'));
                    return;
                }
                if (UniAccount::where('uniacid', $uniacid)->update($data)){
                    $this->info(__('savedSuccessfully'));
                }else{
                    $this->error(__('operationFailed'));
                }
                break;
            case 'delete':
                // 删除租户
                if (empty($uniacid)){
                    $this->error('租户ID不能为空');
                    return;
                }
                AccountService::remoteAccount($uniacid);
                $this->info(__('successful'));
                $this->info("租户（ID：{$uniacid}）已删除");
                break;
            case 'display':
                // 展示所有租户（未删除的）
                $keyword = $this->option('keyword') ?? '';
                $condition = [];
                if (!empty($keyword)){
                    $condition[] = ['uni_account.name','like',"%{$keyword}%"];
                }
                $uid = $this->option('uid') ?: config('system.setting.founder', 1);
                $list = UniAccount::searchAccountQuery(false, 1, false, $uid)->where($condition)->get()->toArray();
                //Log::info("租户列表（UID：{$uid}）：", $list);
                if (!empty($list)){
                    $rows = array_map(function ($account){
                        return [$account['uniacid'], $account['name'], $account['description'], date('Y-m-d', $account['createtime']), $account['expire_time']>0? date('Y-m-d', $account['expire_time']) : __('长期')];
                    }, $list);
                    $this->table(['uniacid', 'name', 'description', 'created_at', 'expired_at'], $rows);
                }else{
                    $this->info("用户（UID：{$uid}）没有可用租户");
                }
                break;
            case 'detail':
                # code...
                break;
            case 'setExpireTime':
                // 设置平台到期时间
                if (empty($uniacid)){
                    $this->error('租户ID不能为空');
                    return;
                }
                $expire = $this->option('expire') ?: '';
                $expire_time = (int)($expire ? strtotime($expire . ' 23:59:59') : 0);
                DB::table('account')->where('uniacid',$uniacid)->update(['endtime' => $expire_time]);
                $this->info("租户（ID：{$uniacid}）到期时间已更新为：" . ($expire_time>0? date('Y-m-d H:i:s', $expire_time) : __('长期')));
                break;
            case 'setFounderUid':
                // 设置平台所有者
                if (empty($uniacid)){
                    $this->error('租户ID不能为空');
                    return;
                }
                if (empty($uid)){
                    $this->error('用户ID不能为空');
                    return;
                }
                DB::table('uni_account_users')->where(array('role'=>'owner','uniacid'=>$uniacid))->delete();
                if (UserService::AccountRoleUpdate($uniacid, $uid)){
                    $this->info("租户（ID：{$uniacid}）所有者已更新");
                }else{
                    $this->error(__('operationFailed'));
                }
                break;
            case 'setRole' :
                // 添加或修改租户操作员角色
                $role = $this->option('role') ?: '';
                if (empty($uniacid)){
                    $this->error('租户ID不能为空');
                    return;
                }
                if (empty($uid)){
                    $this->error('用户ID不能为空');
                    return;
                }
                if (!in_array($role, ['operator', 'manager'])){
                    $this->error('角色参数错误');
                }
                if (UserService::AccountRoleUpdate($uniacid, $uid, $role)){
                    $this->info("租户（ID：{$uniacid}）操作员角色已更新");
                }else{
                    $this->error(__('operationFailed'));
                }
                break;
            case 'removeRole':
                // 删除租户操作员角色
                if (empty($uniacid)){
                    $this->error('租户ID不能为空');
                    return;
                }
                if (empty($uid)){
                    $this->error('用户ID不能为空');
                    return;
                }
                $complete = DB::table('uni_account_users')->where(array('uid'=>$uid,'uniacid'=>$uniacid))->delete();
                if ($complete){
                    $this->info("租户（ID：{$uniacid}）操作员角色已删除");
                }else{
                    $this->error(__('operationFailed'));
                }
                break;
            case 'setDomain':
                // 设置租户绑定域名
                if (empty($uniacid)){
                    $this->error('租户ID不能为空');
                    return;
                }
                // 域名为空时表示解除绑定
                $domain = trim($this->option('domain'));
                $uni_settings = DB::table('uni_settings')->where('uniacid', $uniacid)->select(['jsauth_acid', 'bind_domain'])->first();
                if ($domain==$uni_settings['bind_domain']){
                    $this->info(__('successful'));
                    return;
                }
                if (!empty($domain)){
                    if (!preg_match('/^(?:[a-zA-Z\d_-]+\.)*[a-z]{2,6}$/i', $domain)){
                        return $this->message('请输入正确格式的域名(只输入host部分)');
                    }
                    if (DB::table('uni_settings')->where('bind_domain', $domain)->exists()){
                        return $this->message('该域名已被其它平台绑定');
                    }
                }
                $res = DB::table('uni_settings')->where('uniacid', $uniacid)->update(['bind_domain'=>$domain]);
                if ($res){
                    $this->info(__('successful'));
                }else{
                    $this->error(__('operationFailed'));
                }
                break;
        }
        return;
    }
}
