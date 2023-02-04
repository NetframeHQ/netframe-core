<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Tracking
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
            \App\Helpers\TrackingHelper::logStats();
        }

        return $next($request);
    }
}
