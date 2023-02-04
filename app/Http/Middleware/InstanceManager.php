<?php

namespace App\Http\Middleware;

use Closure;
use App\Instance;

class InstanceManager
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
            $instance = auth('web')->user()->instances()->where('id', '=', session('instanceId'))->first();
        }

        if (!session()->has('instanceId') || !isset($instance->pivot) || $instance->pivot->roles_id > 2) {
            return redirect()->route('user.timeline');
        }

        return $next($request);
    }
}
