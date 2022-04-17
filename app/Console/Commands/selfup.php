<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
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

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whotalk framework upgrade';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $bulidapp = new App();
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
        //数据库升级
        //Artisan::call('self:migrate');
        $setupsql = "";
        if (!Schema::hasTable("microserver")){
            $tablename = tablename("microserver");
            $setupsql .= <<<EOF
CREATE TABLE IF NOT EXISTS $tablename (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `identity` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `cover` varchar(255) NOT NULL DEFAULT '',
  `summary` text,
  `version` varchar(10) NOT NULL DEFAULT '',
  `releases` varchar(20) NOT NULL DEFAULT '',
  `drive` varchar(10) NOT NULL DEFAULT '',
  `entrance` varchar(255) NOT NULL,
  `datas` mediumtext,
  `configs` mediumtext,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `addtime` int(10) NOT NULL DEFAULT '0',
  `dateline` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='服务表';
EOF;
        }
        if (!empty($setupsql)){
            DB::statement($setupsql);
        }

        //更新路由
        Artisan::call('route:clear');

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

        $this->info('Whotalk framework upgrade successfully.');
        return true;
    }
}
