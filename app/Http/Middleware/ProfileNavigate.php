<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SessionHelper;
use App\Helpers\AclHelper;
use App\User;

class ProfileNavigate
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
        $pageType = $request->segment(2);

        if (auth()->check()) {
            if (in_array($pageType, config('netframe.list_profile'))) {
                $params = $request->route()->parameters();

                if (in_array('id', $params)) {
                    $model = studly_case($pageType);
                    $pageId = intval($route->getParameter('id'));

                    $query = $model::where('id', '=', $pageId)->where('users_id', '=', auth()->user()->id)->first();

                    // if user is property of this page
                    if (!is_null($query)) {
                        SessionHelper::setProfile('current', $query, $pageType);
                    } else {
                        // if user is not property
                        // Stuff "as profile" in "current profile"
                        $asProfile = SessionHelper::profile('as');
                        //SessionHelper::setProfile('current', $asProfile, $asProfile->profile);
                        session(['profiles.current', $asProfile]);
                    }
                }
            }
        }

        return $next($request);
    }
}
