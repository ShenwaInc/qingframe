<?php

namespace App\Console\Commands;

use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class selfmigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whotalk framework migrate';

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
        //操作数据库迁移
        try {
            if (!Schema::hasColumn('uni_settings','notice')){
                Schema::table('uni_settings',function (Blueprint $table){
                    $table->addColumn('text','notice',array('comment'=>'消息通知'));
                });
            }
            if (Schema::hasTable('account_aliapp')){
                $DorpTables = array(
                    'account_aliapp',
                    'account_baiduapp',
                    'account_wxapp',
                    'account_phoneapp',
                    'account_toutiaoapp',
                    'account_webapp',
                    'account_xzapp',
                    'activity_clerk_menu',
                    'article_category',
                    'article_comment',
                    'article_news',
                    'article_notice',
                    'article_unread_notice',
                    'basic_reply',
                    'business',
                    'core_cron',
                    'core_cron_record',
                    'core_job',
                    'core_menu',
                    'core_menu_shortcut',
                    'core_performance',
                    'core_queue',
                    'core_refundlog',
                    'core_resource',
                    'core_sendsms_log',
                    'coupon_location',
                    'cover_reply',
                    'custom_reply',
                    'images_reply',
                    'mc_cash_record',
                    'mc_chats_record',
                    'mc_credits_recharge',
                    'mc_fans_groups',
                    'mc_fans_tag',
                    'mc_fans_tag_mapping',
                    'mc_handsel',
                    'mc_mass_record',
                    'mc_member_address',
                    'mc_member_property',
                    'mc_oauth_fans',
                    'menu_event',
                    'message_notice_log',
                    'mobilenumber',
                    'modules_bindings',
                    'modules_cloud',
                    'modules_ignore',
                    'modules_plugin',
                    'modules_plugin_rank',
                    'modules_rank',
                    'music_reply',
                    'news_reply',
                    'phoneapp_versions',
                    'profile_fields',
                    'qrcode',
                    'qrcode_stat',
                    'rule',
                    'rule_keyword',
                    'site_article',
                    'site_article_comment',
                    'site_category',
                    'site_nav',
                    'site_page',
                    'site_slide',
                    'site_store_cash_log',
                    'site_store_cash_order',
                    'site_store_goods',
                    'site_store_goods_cloud',
                    'site_store_order',
                    'site_styles',
                    'site_styles_vars',
                    'site_templates',
                    'stat_keyword',
                    'stat_msg_history',
                    'stat_rule',
                    'system_stat_visit',
                    'uni_link_uniacid',
                    'userapi_cache',
                    'userapi_reply',
                    'users_extra_templates',
                    'users_invitation',
                    'users_lastuse',
                    'video_reply',
                    'voice_reply',
                    'wechat_attachment',
                    'wechat_news',
                    'wxapp_general_analysis',
                    'wxapp_register_version',
                    'wxapp_reply',
                    'wxapp_undocodeaudit_log',
                    'wxapp_versions',
                    'wxcard_reply'
                );
                foreach ($DorpTables as $dorpTable){
                    Schema::drop($dorpTable);
                }
            }
            if(is_dir(base_path('socket'))){
                FileService::rmdirs(base_path('socket'));
                DB::table('gxswa_cloud')->where(array('identity'=>'laravel_whotalk_socket'))->update(array('rootpath'=>'swasocket/'));
            }
            $this->info('Whotalk framework migrate successfully.');
        } catch (\Exception $exception){
            $this->error("Migrate fail:".$exception->getMessage());;
        }

        return true;
    }
}
