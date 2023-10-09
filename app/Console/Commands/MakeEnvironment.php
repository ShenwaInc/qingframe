<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use Illuminate\Console\Command;

class MakeEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:env {options?} {--path=docker.env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make environment';
    protected $application;
    protected $envs = array(
        'APP_DEBUG',
        'APP_ID',
        'APP_SECRET',
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD',
        'SESSION_DRIVER',
        'REDIS_HOST',
        'REDIS_PASSWORD',
        'REDIS_PORT'
    );

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->application = new App();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $options = $this->argument('options');
        if (!empty($options)){
            return $this->MakeEnvironmentFromOptions($options);
        }else{
            $inputPath = $this->option('path');
            $envData = $this->getEnvironments($this->envs, $inputPath);
        }
        if (empty($envData)){
            $this->error("Failed to get environment variables.");
            return false;
        }
        $envStub = file_get_contents(resource_path('stub/env.stub'));
        if (empty($envStub)){
            $this->error("Failed to visit env.stub.");
            return false;
        }
        $search = array(
            '{AUTHKEY}',
            '{APP_DEBUG}',
            '{BASEURL}',
            '{FOUNDER}',
            '{APP_VERSION}',
            '{APP_RELEASE}',
            '{DB_HOST}',
            '{DB_PORT}',
            '{DB_DATABASE}',
            '{DB_USERNAME}',
            '{DB_PASSWORD}',
            '{DB_PREFIX}',
            '{SESSION_DRIVER}',
            '{REDIS_HOST}',
            '{REDIS_PASSWORD}',
            '{REDIS_PORT}'
        );
        $replaces = array(
            env('APP_AUTHKEY'),
            $envData['APP_DEBUG'],
            env('APP_URL'),
            env('APP_FOUNDER'),
            env('APP_VERSION'),
            env('APP_RELEASE'),
            $envData['DB_HOST'],
            $envData['DB_PORT'],
            $envData['DB_DATABASE'],
            $envData['DB_USERNAME'],
            $envData['DB_PASSWORD'],
            env('DB_PREFIX'),
            $envData['SESSION_DRIVER'],
            $envData['REDIS_HOST'],
            $envData['REDIS_PASSWORD'],
            $envData['REDIS_PORT']
        );
        if (!file_put_contents(base_path(".env"), str_replace($search, $replaces, $envStub))){
            $this->error('Build environment failed');
            return false;
        }
        $this->info("Build environment successfully.");
        return true;
    }

    public function getEnvironments($envs, $inputPath="docker.env"){
        if (empty($envs)) return [];
        if (!is_array($envs)) $envs = [$envs];
        if (!file_exists(base_path($inputPath))) return [];
        $data = array();
        $envData = file_get_contents(base_path($inputPath));
        if (!$envData) return [];
        foreach ($envs as $name){
            preg_match("/^".$name."=(.+)/m", $envData, $matches);
            $data[$name] = empty($matches) ? "" : $matches[1];
        }
        return $data;
    }

    public function MakeEnvironmentFromOptions($options=""){
        $replaces = [];
        if (empty($options)){
            return false;
        }
        $envs = array(
            'de'=>'APP_DEBUG',
            'ai'=>'APP_ID',
            'as'=>'APP_SECRET',
            'mh'=>'DB_HOST',
            'mp'=>'DB_PORT',
            'mu'=>'DB_USERNAME',
            'mpwd'=>'DB_PASSWORD',
            'sd'=>'SESSION_DRIVER',
            'rh'=>'REDIS_HOST',
            'rp'=>'REDIS_PASSWORD',
            'rpo'=>'REDIS_PORT',
            'pk'=>'MIX_PUSHER_APP_KEY',
            'pc'=>'MIX_PUSHER_APP_CLUSTER',
            'cs'=>'SESSION_SECURE_COOKIE',
            'ac'=>'APP_RUNNING_IN_W7_CD'
        );
        $_options = explode("-", $options);
        $envText = file_get_contents(base_path(".env"));
        foreach ($_options as $option){
            list($key, $value) = explode(":", $option);
            $name = $envs[$key]?:$key;
            if (empty($name)) continue;
            $envText = preg_replace("/^".$name."=(.+)/m", "$name=".trim($value), $envText);
            if ($name=='DB_USERNAME'){
                $envText = preg_replace("/^DB_DATABASE=(.+)/m", "DB_DATABASE=".trim($value), $envText);
            }
            if ($name=='SESSION_DRIVER'){
                $envText = preg_replace("/^CACHE_DRIVER=(.+)/m", "CACHE_DRIVER=".trim($value), $envText);
            }
        }
        if (!file_put_contents(base_path(".env"), $envText)){
            $this->error('Build environment failed');
            return false;
        }
        $this->info("Build environment successfully.");
        return true;
    }

}
