<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckLang
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

        $availableLangs = config('app.locales');

        if (!auth()->guard('web')->check()) {
            $browserLangs = explode(',', $request->server('HTTP_ACCEPT_LANGUAGE'));
            $matchLang = false;
            foreach ($browserLangs as $browserLang) {
                foreach ($availableLangs as $locLang) {
                    if (strpos($browserLang, $locLang) !== false && !$matchLang) {
                        $matchLang = true;
                        app()->setLocale($locLang);
                        continue;
                    }
                }
            }
        }

        return $next($request);
    }
}
