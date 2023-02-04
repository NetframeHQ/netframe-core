<?php

namespace App\Http\Middleware;

use Closure;

class CheckOs
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
        $user_agent = getenv("HTTP_USER_AGENT");

        $os = '';
        if (strpos($user_agent, "Win") !== false) {
            $os = 'windows';
        } elseif (strpos($user_agent, "Mac") !== false) {
            $os = 'mac';
        } elseif (strpos($user_agent, "Linux") !== false) {
            $os = 'linux';
        }

        view()->share('userOs', $os);

        return $next($request);
    }
}
