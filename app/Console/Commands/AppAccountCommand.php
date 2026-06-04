<?php

/**
 * 轻记账应用自动化脚本，用于通过命令行管理账本与后台
 * 需要将该文件复制到 /app/Console/Commands/ 目录下
 * 可以将默认的 family_id 、uniacid 、uid 、asset_id 参数定义在 env.php 文件中
 * 创建账单命令行：php artisan app:account createBook "用途" 100.00 "类型名" "资产名" --family_id=账本ID --uniacid=租户ID --uid=用户UID
 * 创建资产命令行：php artisan app:account createAsset "资产名" "资产用途" "资产描述" --family_id=账本ID --uniacid=租户ID
 * 创建环境变量（设置默认参数）：php artisan app:account setEnv family_id=账本ID uniacid=租户ID uid=用户UID asset_id=资产ID
*/

use Addons\swa_account\app\Models\AccountAssets;
use Addons\swa_account\app\Models\AccountBooks;
use Addons\swa_account\app\Models\AccountFamily;
use Addons\swa_account\app\Models\AccountType;
use Addons\swa_account\app\Services\BooksService;
use Addons\swa_account\app\Services\UserService;
use App\Http\Middleware\AppRuntime;
use Illuminate\Console\Command;

class AppAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:account {operation} {params?*} {--uniacid=0} {--family_id=0} {--uid=0} {--asset_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '轻记账应用自动化脚本，用于通过命令行管理账本与后台';
    protected $application;
    protected $authToken = null;

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
        global $_W;
        $operation = $this->argument('operation');
        $params = $this->argument('params');
        $family_id = $this->option('family_id');
        $asset_id = 0;
        $envFile = public_path('addons/swa_account/env.php');
        $envs = [];
        if (file_exists($envFile)){
            $envs = require_once $envFile;
        }
        if (!empty($envs['asset_id'])){
            $asset_id = (int)$envs['asset_id'];
        }
        if (empty($family_id)){
            $family_id = $envs['family_id'] ?? 0;
        }
        $uniacid = $this->option('uniacid');
        if (empty($uniacid)){
            $uniacid = $envs['uniacid'] ?? 0;
        }
        $uid = $this->option('uid');
        if (empty($uid)){
            $uid = $envs['uid'] ?? 0;
        }
        if (!empty($uid)){
            // 用户初始化
            $authInfo = UserService::uniAuth($uid);
            if (!empty($authInfo['loginState'])){
                $this->authToken = $authInfo['token'];
                if (empty($uniacid)){
                    $uniacid = $authInfo['uniacid'];
                }
                // 初始化账本
                $family = AccountFamily::getFamilyByUid($uid, 1);
                if (!empty($family)){
                    $family_id = $family['id'];
                    $_W['family'] = $family;
                    BooksService::bookInit();
                }
            }
        }
        if (!empty($uniacid)){
            // 初始化租户
            $this->application = new AppRuntime();
            $this->application->Runtime($uniacid, $this->authToken);
        }

        switch ($operation){
            case 'createBook':
                // 创建账单
                list($title, $amount, $typeName, $assetName) = $params;
                if (empty($title)){
                    $this->error('请输入账单用途');
                    return false;
                }
                if (empty($amount)){
                    $this->error('请输入账单金额');
                    return false;
                }
                if (empty($family_id)){
                    $this->error('无效的账本信息');
                    return false;
                }
                $data = [
                    'name'=>trim($title),
                    'amount'=>(float)$amount
                ];
                $type = AccountType::where('name', $typeName)->where(function ($query) use ($_W) {
                    $query->where('uniacid', 0)
                        ->orWhere('uniacid', $_W['uniacid'])
                        ->orWhere('family_id', $_W['family']['id']);
                })->orderByRaw('family_id DESC, uniacid DESC, id DESC')->first();
                if (empty($type)){
                    $this->error('无效的账单类型');
                    return false;
                }
                $data['type_id'] = $type->id;
                $data['is_income'] = $type->is_income;
                if (!empty($assetName)){
                    $asset = AccountAssets::where('family_id', $family_id)->where(function ($query) use ($assetName) {
                        $query->where('name', 'like', '%'.$assetName.'%')
                            ->orWhere('remark', 'like', '%'.$assetName.'%');
                    })->first();
                    if (!empty($asset)){
                        $asset_id = $asset->id;
                    }
                }
                if (empty($asset_id)){
                    $this->error('未找到关联资产信息');
                    return false;
                }
                $data['asset_id'] = $asset_id;
                $data['billing_at'] = date('Y-m-d H:i:s', TIMESTAMP);
                $data['users'] = serialize([
                    ['uid'=>$_W['member']['uid'], 'nickname'=>$_W['family']['my_nickname']?:$_W['member']['nickname']]
                ]);
                $data['uid'] = $_W['member']['uid'];
                $data['nickname'] = $_W['family']['my_nickname']??$_W['member']['nickname'];
                $data['uniacid'] = $_W['uniacid'];
                $data['billing_state'] = 1;
                $data['family_id'] = $family_id;
                if (AccountBooks::create($data)){
                    $this->info('账单创建成功');
                }else{
                    $this->error('账单创建失败');
                    return false;
                }
                break;
            case 'createAsset' :
                // 创建资产
                break;
            case 'saveEnv' :
                $data = $this->options();
                $envs = array_merge($envs, post_var(['uniacid', 'family_id', 'uid', 'asset_id'], $data));
                $envStr = '';
                foreach ($envs as $key=>$value){
                    $envStr .= "    '$key' => '$value',\n";
                    $this->line("{$key}=>{$value}");
                }
                $envCode = <<<EOD
<?php
defined('QingFrame') or exit('Access Denied');

return [
$envStr];

EOD;
                file_put_contents($envFile, $envCode);
                $this->info('保存成功');
                break;
            default:
                $this->error('无效的操作');
                break;
        }
        return true;
    }
}
