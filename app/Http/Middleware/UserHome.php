<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SessionHelper;
use App\Helpers\AclHelper;
use App\User;

class UserHome
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        if (auth()->guard('web')->check()) {
            if (auth()->guard('web')->viaRemember()) {
                //if sessions not exists, create them
                $authAuto = app('App\Http\Controllers\AuthController')->autoLogin();
                //get user instance
            }
            $userInstance = auth()->guard('web')->user()->instances()->first();

            // if($userInstance->subscribeValid()!=1){
            //     return redirect()->route('instance.subscription');
            // }

            if ($userInstance != null && $userInstance->id == session('instanceId')) {
                return redirect()->route('user.timeline');
            } else {
                return redirect()->to($userInstance->getUrl().'/user/timeline');
            }
        }

        return $next($request);
    }
}
