<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AccountManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:manage {operation} {--uniacid=} {--name=} {--description=} {--logo=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '租户管理工具，operation可选值：create(创建租户),edit（编辑租户信息）,delete（删除租户）,display（展示所有租户）,detail（查看租户详情）,setExpireTime（设置到期时间）,setFounderUid（设置创始人）,addRole（增加管理角色）,removeRole（移除管理角色）';

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
    }
}
