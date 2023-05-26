<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenAuthKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:authkey {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application auth key';

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
    public function handle(){
        //
        $options = $this->options();
        $oldKey = env('APP_AUTHKEY');
        $environmentPath = base_path(".env");
        $environment = file_get_contents($environmentPath);
        $instLock = base_path('storage/installed.bin');
        $newKey = \Str::random(12);
        if (file_exists($instLock)){
            $lockData = file_get_contents($instLock);
            $instData = json_decode(base64_decode($lockData), true);
            if ($options['force']){
                //重置密码
                $founderPwd = $this->secret('Enter founder password:');
                $founderUid = (int)env('APP_FOUNDER', 1);
                $founder = DB::table('users')->where('uid', $founderUid)->first(['salt','password']);
                $pwdhash = sha1("{$founderPwd}-{$founder['salt']}-{$oldKey}");
                if ($pwdhash!=$founder['password']){
                    $this->error("Incorrect password.");
                    return false;
                }
                $salt = \Str::random(8);
                $pwdHashNew = sha1("{$founderPwd}-{$salt}-{$newKey}");
                if (!DB::table('users')->where('uid', $founderUid)->update(array('salt'=>$salt,'password'=>$pwdHashNew))){
                    $this->error("Generate auth key faild.");
                    return false;
                }
                $instData['authkey'] = $newKey;
                file_put_contents($instLock, base64_encode(json_encode($instData, 320)));
            }else{
                $newKey = $instData['authkey'];
            }
        }
        if (!file_put_contents($environmentPath, str_replace('APP_AUTHKEY='.$oldKey, 'APP_AUTHKEY=' . $newKey, $environment))){
            $this->error("Permission denied.");
            return false;
        }
        $this->info("Generate auth key successfully.");
        return true;
    }


}
