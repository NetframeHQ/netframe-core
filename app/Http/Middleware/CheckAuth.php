<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SessionHelper;
use App\Helpers\AclHelper;
use App\User;
use App\Netframe;
use App\Instance;

class CheckAuth
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
        if (auth()->guard('web')->guest()) {
            return redirect()->guest('login');
        } else {
            // Get object GeoIP location
            $this->NetframeGeoIp = SessionHelper::getLocation();

            // Share Variable in all application prefixed by Netframe
            if (!session()->has('userTimezone') && isset($this->NetframeGeoIp->timezone)) {
                session(['userTimezone' => $this->NetframeGeoIp->timezone]);
            } elseif (!session()->has('userTimezone')) {
                session(['userTimezone' => 'Europe/paris']);
            }

            view()->share(array(
                'NetframeGeoip'  => $this->NetframeGeoIp,
            ));

            if (auth()->guard('web')->check()) {
                app()->setLocale(auth()->guard('web')->user()->lang);

                //get user instance
                $userInstance = auth()
                    ->guard('web')
                    ->user()
                    ->instances()
                    ->where('id', '=', session('instanceId'))
                    ->first();

                if (auth()->guard('web')->viaRemember()
                    && !session()->exists("allProfiles")) { // && !session("allProfiles")){
                    if (!auth()->guard('web')->user()->active) {
                        auth()->guard('web')->logout();
                        return redirect()->route('login');
                    }

                    //if sessions not exists, create them
                    $authAuto = app('App\Http\Controllers\AuthController')->autoLogin();
                    if (class_basename($authAuto) == 'RedirectResponse') {
                        return $authAuto;
                    }

                    // redirect to requested page
                    if (request()->is('/')) {
                        return redirect()->route('netframe.workspace.home');
                    } else {
//                        return redirect($userInstance->getUrl().request()->getRequestUri());
                    }
                }

                //check if connected on instance
                if (session('withoutInstance')) {
                    //redirect to first user instance
                    if (session()->exists('instanceId')) {
                        //redirect to same page on instance
                        $instance = Instance::find(session('instanceId'));
                        return redirect()->to($instance->getUrl().'/'.$request->path());
                    } else {
                        // redirect to login page
                        return redirect()->route('login');
                    }
                }

                // user quota
                $userMediaSize = auth()->guard('web')->user()->getMediaSize();
                $offerQuota = config('billing.offer.'.session('instanceOffer').'.userQuota');
                if ($offerQuota > 0 && $userMediaSize >= $offerQuota) {
                    $reachUserQuota = true;
                } else {
                    $reachUserQuota = false;
                }
                session(['reachUserQuota' => $reachUserQuota]);

                // instance locale consent
                $needLocalConsent = ($userInstance->getParameter('local_consent_state') &&
                    !auth()->guard('web')->user()->getParameter('local_consent_state')
                    ) ? true : false;
                view()->share(['need_local_consent' => $needLocalConsent]);

                if ($needLocalConsent) {
                    view()->share([
                        'need_local_consent_content' => $userInstance->getParameter('local_consent_content')
                    ]);
                }


                // GDPR consent
                if (auth()->guard('web')->user()->modal_gdpr == 1) {
                    if (!$needLocalConsent) {
                        $user = User::find(auth()->guard('web')->user()->id);
                        $user->modal_gdpr = 0;
                        $user->save();
                    }
                    $gdpr_modal = view('account.gdpr-modal', [
                        'gdpr' => auth()->guard('web')->user()->gdpr_agrement
                    ])->render();
                    $gdpr_modal = mb_ereg_replace(PHP_EOL, '', $gdpr_modal);
                    $gdpr_modal = mb_ereg_replace('\r\n', '', $gdpr_modal);
                    $gdpr_modal = mb_ereg_replace('\n', '', $gdpr_modal);
                    $gdpr_modal = mb_ereg_replace('\r', '', $gdpr_modal);
                    view()->share(['modal_gdpr' => $gdpr_modal]);
                }

                view()->share('gdpr_agrement', auth()->guard('web')->user()->gdpr_agrement);

                if (auth()->guard('web')->user()->gdpr_agrement == 0 && !session()->exists('ghostId')) {
                    session(['ghostId' => rand()]);
                } elseif (auth()->guard('web')->user()->gdpr_agrement == 1 && session()->exists('ghostId')) {
                    session()->forget('ghostId');
                }

                if (auth()->guard('web')->user()->check_rights == 1 || !session()->has('acl')) {
                    // regenerate instance session var
                    $instance = auth()
                        ->guard('web')
                        ->user()
                        ->instances()
                        ->where('id', '=', session('instanceId'))
                        ->first();

                    session([
                        "acl" =>  Netframe::getAcl(auth()->guard('web')->user()->id),
                        "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id),
                        "instance" => $instance,
                        "instanceRoleId" => auth()->guard('web')->user()->getInstanceRoleId(),
                        'instanceRole' => auth()->guard('web')->user()->getInstanceRole(),
                        "allFeeds" => auth()
                            ->guard('web')
                            ->user()
                            ->channels()
                            ->where('active', '=', 1)
                            ->orderBy('name')
                            ->pluck('name', 'id'),
                    ]);
                    // get and store profiles creation authorizations
                    $profileAuth = auth()->guard('web')->user()->storeInstanceProfile($instance);
                    session(['profileAuth' => $profileAuth]);

                    $user = User::find(auth()->guard('web')->user()->id);
                    $user->check_rights = 0;
                    $user->save();
                    /*
                        ->getQuery()
                        ->update(['check_rights' => 0]);
                        */
                }

                // Share Variable in all application prefixed by Netframe
                view()->share('NetframeProfiles', session('allProfiles'));
                view()->share('UserChannels', session('allFeeds'));
                //session(['NetframeListCategories' => config('listCategories')]);

                auth()->guard('web')->user()->updateLastConnect();

                //sidebar state
                $sidebarstate = auth()->guard('web')->user()->getParameter('sidebarstate');
                $sidebarView = ($sidebarstate == null || $sidebarstate == 'open') ? 'show-content-sidebar' : '';
                view()->share('sidebarState', $sidebarView);
            } else {
                view()->share([
                    'NetframeProfiles' => [],
                ]);
            }

            // if user is disabled during navigation
            if (!auth()->guard('web')->user()->active) {
                auth()->guard('web')->logout();
                return redirect()->route('login');
            }

            //Check l'url si l'utilisateur existe à partir du segment user et slug
            /**
             * @TODO migrate in filters
             */
            if (request()->segment(1) === 'user' && request()->route()->parameter('slug')) {
                $userFound = User::where('slug', request()->route()->parameter('slug'))->first();
                if ($userFound) {
                    //view()->share('userData', $userFound);
                } else {
                    \App::abort(404);
                }
            }

            $location = $this->NetframeGeoIp;
            session([
                "lat" => $location->lat,
                "lng" => $location->lon
            ]);


            if (isset($userInstance) && $userInstance != null) {
                if ($userInstance->getParameter('default_latlng') != null) {
                    $defaultLatLng = json_decode($userInstance->getParameter('default_latlng'));
                    session([
                        "lat" => $defaultLatLng->lat,
                        "lng" => $defaultLatLng->lng
                    ]);
                }
            }
        }

        return $next($request);
    }
}
