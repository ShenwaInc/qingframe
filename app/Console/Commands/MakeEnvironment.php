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
    protected $signature = 'make:environment {--path=docker.env}';

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
        $inputPath = $this->option('path');
        $envDatas = $this->getEnvironments($this->envs, $inputPath);
        if (empty($replaces)){
            $this->error("Failed to get environment variables.");
            return false;
        }
        $envStub = file_get_contents(resource_path('stub/env.stub'));
        if (empty($envStub)){
            $this->error("Failed to visit env.stub.");
            return false;
        }
        $searchs = array(
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
            $envDatas['APP_DEBUG'],
            env('APP_URL'),
            env('APP_FOUNDER'),
            env('APP_VERSION'),
            env('APP_RELEASE'),
            $envDatas['DB_HOST'],
            $envDatas['DB_PORT'],
            $envDatas['DB_DATABASE'],
            $envDatas['DB_USERNAME'],
            $envDatas['DB_PASSWORD'],
            env('DB_PREFIX'),
            $envDatas['SESSION_DRIVER'],
            $envDatas['REDIS_HOST'],
            $envDatas['REDIS_PASSWORD'],
            $envDatas['REDIS_PORT']
        );
        if (!file_put_contents(base_path(".env"), str_replace($searchs, $replaces, $envStub))){
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
        $envDatas = array();
        $envData = file_get_contents(base_path($inputPath));
        if (!$envData) return [];
        foreach ($envs as $key){
            preg_match("/^".$key."=(.+)/m", $envData, $matchs);
            $envDatas[$key] = empty($matchs) ? "" : $matchs[1];
        }
        return $envDatas;
    }

}
