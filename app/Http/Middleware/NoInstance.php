<?php

namespace App\Http\Middleware;

use Closure;
use App\Instance;

class NoInstance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('instanceId')) {
            $instance = Instance::find(session('instanceId'));
            return redirect($instance->getUrl());
        }

        return $next($request);
    }
}
