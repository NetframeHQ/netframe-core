<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SessionHelper;
use App\Helpers\AclHelper;
use App\User;

class AdminHome
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
        if (auth()->guard('admin')->check()) {
            if (auth()->guard('admin')->viaRemember()) {
            }
            return redirect()->route('admin.home');
        }

        return $next($request);
    }
}
