<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/';

    /**
     * Get the post-login redirect path based on user type.
     * Admin seeder account goes directly to admin dashboard.
     * Shop owners (registered admins) can access the shop.
     *
     * @return string
     */
    public function redirectTo()
    {
        $user = auth()->user();
        
        // Check if this is the main admin seeder account
        if ($user->isMainAdmin()) {
            return '/admin';
        }
        
        // All other users (including shop owners) go to the shop
        return '/';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
