<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Services\CacheService;
use Illuminate\Console\Command;
use App\Services\CloudService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class selfup extends Command
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
        if (empty($_W['siteroot'])){
            $appurl = env('APP_URL');
            if (empty($appurl)) return $this->error('Invaild website url.') || "";
            $_W['siteroot'] = $appurl . "/";
        }
        $component = DB::table('gxswa_cloud')->where('type',0)->first(['id','identity','modulename','type','releasedate','rootpath']);
        $cloudupdate = CloudService::CloudUpdate($component['identity'],base_path().'/');
        if (is_error($cloudupdate)) return $this->error($cloudupdate['message']) || "";

        //更新版本信息
        $arguments = $this->argument();
        $system = config('system');
        if (empty($arguments['version'])){
            $ugradeinfo = CloudService::CloudApi('structure',array('identity'=>$component['identity']));
            if (is_error($ugradeinfo)){
                $arguments['version'] = $system['version'];
                $arguments['release'] = intval($system['release']) + 1;
            }else{
                $arguments['version'] = $ugradeinfo['version'];
                $arguments['release'] = $ugradeinfo['releasedate'];
            }
        }
        DB::table('gxswa_cloud')->where('id',$component['id'])->update(array(
            'version'=>$arguments['version'],
            'updatetime'=>TIMESTAMP,
            'dateline'=>TIMESTAMP,
            'releasedate'=>intval($arguments['release']),
            'online'=>serialize(array(
                'isnew'=>false,
                'version'=>$arguments['version'],
                'releasedate'=>intval($arguments['release'])
            ))
        ));
        CloudService::CloudEnv(array("APP_VERSION={$system['version']}","APP_RELEASE={$system['release']}"), array("APP_VERSION={$arguments['version']}","APP_RELEASE={$arguments['release']}"));

        $this->info('Framework upgrade successfully.');
        return true;
    }
}
