<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Instance;

class CheckAppActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $appSlug = null)
    {
        $instance = Instance::find(session('instanceId'));
        $activeApp = $instance->appActive($appSlug);

        if (!$activeApp) {
            return response(view('errors.403'), 403);
        }

        return $next($request);
    }
}
