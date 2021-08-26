<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use Illuminate\Console\Command;
use App\Services\CloudService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

        //更新路由
        Artisan::call('route:clear');

        //更新版本信息
        $arguments = $this->argument();
        if (empty($arguments['version'])){
            $system = config('system');
            $arguments['version'] = $system['version'];
            $arguments['release'] = intval($system['release']) + 1;
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

        $this->info('Whotalk framework upgrade successfully.');
        return true;
    }
}
