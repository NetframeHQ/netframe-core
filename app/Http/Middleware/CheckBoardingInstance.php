<?php

namespace App\Http\Middleware;

use Closure;
use App\Boarding;

class CheckBoardingInstance
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
        if (!session()->has('boarding.waitingInstanceId')) {
            return redirect()->route('boarding.home');
        }

        return $next($request);
    }
}
