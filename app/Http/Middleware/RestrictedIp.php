<?php

namespace App\Http\Middleware;

use Closure;

class RestrictedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array($request->ip(), config('netframe.allowedIps'))) {
            return response(view('errors.403'), 403);
        }

        return $next($request);
    }
}
