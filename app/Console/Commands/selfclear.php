<?php

namespace App\Console\Commands;

use App\Services\FileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class selfclear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Whotalk framework clean';

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
        //清理无用文件
        $unused = array(
            app_path('Console/Commands/repwd.php'),
            database_path('migrations/2021_08_10_113449_create_uni_settings_table.php'),
            base_path('bootstrap/helpers.php'),
            app_path('Services/AttachmentService.php'),
            resource_path('views/console/socket.blade.php')
        );
        foreach ($unused as $file){
            if (file_exists($file)){
                @unlink($file);
            }
        }
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
            $file = database_path("migrations/2021_08_10_113449_create_{$dorpTable}_table.php");
            if (file_exists($file)){
                @unlink($file);
            }
        }
        //清理无用文件夹
        $undirs = array(
            base_path('socket/'),
            base_path('bootstrap/functions/'),
            base_path('bootstrap/wemod/'),
            resource_path('views/console/extra/'),
            resource_path('views/console/set/')
        );
        foreach ($undirs as $dir){
            if (is_dir($dir)){
                FileService::rmdirs($dir);
            }
        }
        $this->info('FrameWork Clean successfully.');
        return true;
    }
}
