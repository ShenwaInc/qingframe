<?php

namespace App\Services;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class WeauthService extends EloquentUserProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        //账户已删除
        if ($user->status!=2) return false;
        $config = config('system');
        $plain = $credentials['password'];
        $authPassword = $user->getAuthPassword();
        return sha1("{$plain}-{$authPassword['salt']}-{$config['setting']['authkey']}") == $authPassword['password'];
    }

}
