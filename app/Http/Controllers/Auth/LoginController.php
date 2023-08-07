<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/console';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $appSecurityEntrance = env("APP_SECURITY_ENTRANCE", "/");
        if (!empty($appSecurityEntrance) && $appSecurityEntrance!="/"){
            $securityEntrance = session()->get("securityEntrance");
            if (empty($securityEntrance)){
                abort(413, "Please log in through the secure entrance");
            }
        }
        $this->middleware('app')->except('handle');
        $this->middleware('guest')->except('logout');
    }


    public function showLoginForm(Request $request){
        global $_GPC;
        if (empty($_GPC['referer'])){
            $_GPC['referer'] = 'console';
        }
        return $this->globalView(['auth/loginCustom', 'auth.login']);
    }

    /**
     * Defined username.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }


}
