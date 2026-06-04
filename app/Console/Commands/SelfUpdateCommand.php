<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\CacheService;
use Illuminate\Console\Command;
use App\Services\CloudService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SelfUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:update {version?} {release?}';
    public $Application;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Framework upgrade';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->Application = new App();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //框架升级
        global $_W;
        $arguments = $this->argument();
        if (empty($_W['siteroot'])){
            $appUrl = config('system.url');
            if (empty($appUrl)) return $this->error('Invaild website url.') || "";
            $_W['siteroot'] = $appUrl . "/";
        }
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','modulename','type','version_code','rootpath']);
        if (!empty($arguments['version']) && $arguments['version']!='local'){
            //从云端升级
            $cloudUpdate = CloudService::CloudUpdate($component['identity'],base_path().'/');
            if (is_error($cloudUpdate)) return $this->error($cloudUpdate['message']) || "";
        }

        // 清理系统缓存
        CacheService::flush();

        //运行升级脚本
        self::call('self:migrate');
        self::call('route:clear');
        self::call('update:service');
        self::call('self:clear');

        //更新版本信息
        $system = array(
            'version'=>config('system.version'),
            'release'=>(int)config('system.versionCode')
        );
        if (empty($arguments['version'])){
            $upgradeInfo = CloudService::CloudApi('structure',array('identity'=>$component['identity']));
            if (is_error($upgradeInfo)){
                $arguments['version'] = $system['version'];
                $arguments['release'] = $system['release'] + 1;
            }else{
                $arguments['version'] = $upgradeInfo['version'];
                $arguments['release'] = $upgradeInfo['version_code'] ?? $upgradeInfo['releasedate'];
            }
        }
        if ($arguments['version']=='local'){
            //从本地升级
            $arguments['version'] = $system['version'];
            $arguments['release'] = $system['release'];
        }
        $version_code = intval($arguments['release']);
        DB::table('gxswa_cloud')->where('id',$component['id'])->update(array(
            'version'=>$arguments['version'],
            'updatetime'=>TIMESTAMP,
            'dateline'=>TIMESTAMP,
            'version_code'=>$version_code,
            'online'=>serialize(array(
                'isnew'=>false,
                'version'=>$arguments['version'],
                'version_code'=>$version_code,
            ))
        ));
        CloudService::CloudEnv(array("APP_VERSION={$system['version']}","APP_RELEASE={$system['release']}"), array("APP_VERSION={$arguments['version']}","APP_RELEASE={$arguments['release']}"));

        $this->info('Framework upgrade successfully.');
        return true;
    }
}
