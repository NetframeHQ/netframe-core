<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SessionHelper;
use App\Helpers\AclHelper;
use App\User;
use App\Netframe;

class CheckAccountent
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
        if (auth()->guard('accountent')->guest()) {
            return redirect()->guest('accountent/login');
        } else {
            if (auth()->guard('accountent')->check()) {
                if (auth()->guard('accountent')->viaRemember()) {
                }
            }
        }
        return $next($request);
    }
}
