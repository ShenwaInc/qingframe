<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

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
    public function __construct(Request $request)
    {
        global $_GPC;
        $this->middleware('guest')->except('logout');
        $_GPC = $request->all();
        if (empty($_GPC['referer'])){
            $_GPC['referer'] = 'console';
        }
        View::share('_GPC',$_GPC);
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
