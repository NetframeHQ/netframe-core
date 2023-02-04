<?php

namespace App\Http\Middleware;

use Closure;
use App\Boarding;

class CheckBoarding
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
        if (!session()->has('boarding.email-user') && !session()->has('boarding.boarding')) {
            return redirect()->route('boarding.home');
        }

        return $next($request);
    }
}
