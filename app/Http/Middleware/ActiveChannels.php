<?php

namespace App\Http\Middleware;

use Closure;

class ActiveChannels
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
        session(['activeChannels' => true]);

        return $next($request);
    }
}
