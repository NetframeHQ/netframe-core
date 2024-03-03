<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Instance;
use App\Application;

class CheckInstance
{
    public function handle($request, Closure $next)
    {
        if (!auth()->guard('web')->check()) {
            $langHTTP = $request->server('HTTP_ACCEPT_LANGUAGE');
            $langBrowser = substr($langHTTP, 0, 2);
            if (in_array($langBrowser, config()->get('app.locales'))) {
                app()->setLocale($langBrowser);
                session(['userLang' => $langBrowser]);
            }
        }
        /*
        else{
            app()->setLocale(auth()->guard('web')->user()->lang);
            session(['userLang' => auth()->guard('web')->user()->lang]);
        }
        */

        $defaultSubDomain = env('DEFAULT_SUBDOMAIN', 'work');

        $baseProtocol = env('APP_BASE_PROTOCOL');
        $baseDomain = env('APP_BASE_DOMAIN');
        $requestedDomain = $request->getHttpHost();
        $instanceSlug = str_replace('.'.$baseDomain, '', $requestedDomain);

        if ($requestedDomain != $baseDomain &&
            !in_array($instanceSlug, [$defaultSubDomain, "broadcast", "drive-connect"])) {
            //check instance

            session(['withoutInstance' => false]);
            $instance = Instance::where('slug', '=', $instanceSlug)->first();
            if ($instance != null) {
                //instance match
                view()->share('globalInstanceName', $instance->name);

                foreach (Application::get() as $app) {
                    if ($instance->apps->contains($app->id)) {
                        view()->share('active'.ucfirst($app->slug), true);
                    } else {
                        view()->share('active'.ucfirst($app->slug), false);
                    }
                }

                //make gobal vars for views
                // check instance theme
                $instanceCssTheme = 'standard';
                $instanceCssTheme = $instance->getParameter('css_theme');
                if ($instanceCssTheme != null && $instanceCssTheme != 'standard') {
                    $themePath = config('themes.themes.' . $instanceCssTheme . '.path');
                    if ($themePath != null) {
                        $timestamp = \File::lastModified(public_path($themePath));
                        view()->share('instanceThemeCss', $themePath.'?v='.$timestamp);
                    }
                }

                // logo instance light
                $instanceLogo = $instance->getParameter('main_logo_2018', true);
                if ($instanceLogo != null && $instanceLogo->parameter_value != null) {
                    $logoParams = json_decode($instanceLogo->parameter_value, true);
                    $mainLogoUrl = url()->route('instance.download', [
                        'parametername' => 'main_logo_2018',
                        'filename' => $logoParams['filename']
                    ]);
                    view()->share('instanceLogo', $mainLogoUrl);
                    view()->share('instanceLogoDark', $mainLogoUrl);
                }

                // logo instance dark
                $instanceLogoDark = $instance->getParameter('main_logo_2018_dark', true);
                if ($instanceLogoDark != null && $instanceLogoDark->parameter_value != null) {
                    $logoParamsDark = json_decode($instanceLogoDark->parameter_value, true);
                    $mainLogoDarkUrl = url()->route('instance.download', [
                        'parametername' => 'main_logo_2018_dark',
                        'filename' => $logoParamsDark['filename']
                    ]);
                    view()->share('instanceLogoDark', $mainLogoDarkUrl);
                }

                // logo menu light
                $instanceMenuLogo = $instance->getParameter('menu_logo_2018', true);
                if ($instanceMenuLogo != null && $instanceMenuLogo->parameter_value != null) {
                    $logoParams = json_decode($instanceMenuLogo->parameter_value, true);
                    $menuLogoUrl = url()->route('instance.download', [
                        'parametername' => 'menu_logo_2018',
                        'filename' => $logoParams['filename']
                    ]);
                    view()->share('menuLogo', $menuLogoUrl);
                    view()->share('menuLogoDark', $menuLogoUrl);
                }

                // logo menu dark
                $instanceMenuLogoDark = $instance->getParameter('menu_logo_2018_dark', true);
                if ($instanceMenuLogoDark != null && $instanceMenuLogoDark->parameter_value != null) {
                    $logoDarkParams = json_decode($instanceMenuLogoDark->parameter_value, true);
                    $menuLogoDarkUrl = url()->route('instance.download', [
                        'parametername' => 'menu_logo_2018_dark',
                        'filename' => $logoDarkParams['filename']
                    ]);
                    view()->share('menuLogoDark', $menuLogoDarkUrl);
                }

                $instanceCoverImage = $instance->getParameter('cover_image', true);
                if ($instanceCoverImage != null && $instanceCoverImage->parameter_value != null) {
                    $logoParams = json_decode($instanceCoverImage->parameter_value, true);
                    $instanceCoverUrl = url()->route('instance.download', [
                        'parametername' => 'cover_image',
                        'filename' => $logoParams['filename']
                    ]);
                    view()->share('instanceCoverUrl', $instanceCoverUrl);
                }

                // check custom css for current theme

                $instanceCssParameters = $instance->getParameter('css_colors_2018', true);
                if ($instanceCssParameters != null && $instanceCssParameters->parameter_value != null) {
                    $cssParameters = json_decode($instanceCssParameters->parameter_value, true);

                    // test force dark or light mode
                    if (isset($cssParameters[$instanceCssTheme]['disableMode']) &&
                        !empty($cssParameters[$instanceCssTheme]['disableMode'])) {
                        view()->share('disableCssMode', 'disable-' . $cssParameters[$instanceCssTheme]['disableMode']);
                    }

                    $customCss = $instance->getParameter('custom_css_2018');
                    $customCssFilePath = env('NETFRAME_DATA_PATH', base_path())
                        . '/storage/uploads/instances-css/'
                        . $instance->id . '-' . $instance->slug . '.css';

                    if ($customCss == 1 &&
                        file_exists($customCssFilePath) &&
                        isset($cssParameters[$instanceCssTheme])
                        ) {
                        view()->share(
                            'customAdditionnalCss',
                            url()->route('instance.download', ['parametername' => 'instance_css'])
                        );
                    }
                }

                // mono profile parameter
                $instanceMonoProfile = $instance->getParameter('monoprofile');
                session(['instanceMonoProfile' => $instanceMonoProfile]);

                // custom likes emojis
                $cusomEmojis = $instance->getLikeEmojis();
                view()->share('customLikesEmojis', $cusomEmojis);

                $bgScreenInstance = $instance->getParameter('background_login_2018');
                if ($bgScreenInstance != null) {
                    $bgScreen = url()->route('instance.download', ['parametername' => 'background_login_2018']);
                    view()->share('customBackground', "style=\"background-image:url('".$bgScreen."')\"");
                }

                // nav theme
                $navThemeInstance = $instance->getParameter('nav_theme');
                $navThemeInstance = ($navThemeInstance == null || $customCss != 1) ? '' : $navThemeInstance;
                view()->share('navThemeInstance', $navThemeInstance);

                //billing and quotas
                $billingOffer = $instance->getParameter('billing_offer');
                $instanceMediaSize = $instance->getMediaSize();
                $offerQuota = config('billing.offer.'.$billingOffer.'.globalQuota');
                if ($offerQuota > 0 && $instanceMediaSize >= $offerQuota) {
                    $reachInstanceQuota = true;
                } else {
                    $reachInstanceQuota = false;
                }
                session([
                    'reachInstanceQuota' => $reachInstanceQuota,
                    'instanceOffer' => $billingOffer,
                    'activeChannels' => false
                ]);

                if (!auth()->guard('web')->check()) {
                    session([
                        'instanceId' => $instance->id,
                        //'instance' => $instance
                    ]);
                } elseif (session()->has('instanceId') && $instance->id != session('instanceId')) {
                    auth()->guard('web')->logout();
                    session([
                        'instanceId' => $instance->id,
                        //'instance' => $instance
                    ]);
                    return redirect()->to($instance->getUrl().'/'.$request->path());
                } elseif (!session()->has('instanceId')) {
                    session([
                        'instanceId' => $instance->id,
                        //'instance' => $instance
                    ]);
                }

                if (!session()->has('inCreation')) {
                    $user = \Auth::user();
                    $subscribeValid = $instance->subscribeValid();

                    if ($subscribeValid == 2) {
                        \App\Helpers\SessionHelper::destroyProfile();
                        auth()->guard('web')->logout();
                        session()->flush();
                        $redirectUrl = $baseProtocol .
                            '://' .
                            $defaultSubDomain .
                            '.' .
                            $baseDomain .
                            '/static/instance-closed';
                        return redirect()->to($redirectUrl);
                        //return redirect()->to(env('APP_URL').'/static/instance-closed');
                    } elseif (isset($user) && $subscribeValid != 1) {
                        $uhi = \DB::table('users_has_instances')
                            ->where([
                                'instances_id'=> session('instanceId'),
                                'users_id' => $user->id
                            ])
                            ->first();

                        if ($uhi->roles_id < 3) {
                            if (!session()->has('to_subscription')
                                || (in_array($subscribeValid, [0,4])
                                && request()->route()->getName()!='instance.subscription')) {
                                session(['to_subscription' => 0]);
                                return redirect()->route('instance.subscription');
                            }
                        } elseif (in_array($subscribeValid, [0,4])) {
                            // dd(request());
                            // if(!in_array(request()->route()->getName(), ['auth.login', 'auth.logout']))
                            \App\Helpers\SessionHelper::destroyProfile();
                            auth()->guard('web')->logout();
                            session()->flush();
                            return redirect()->route('login', ['messageLogin' => 'expiration']);
                        }
                    }
                }
            } else {
                //not found instance
                //session()->forget(['instanceId', 'instance']);
                return redirect()->to($baseProtocol.'://' . $defaultSubDomain . '.' . $baseDomain);
            }
        } elseif (auth()->guard('web')->check() && !in_array($instanceSlug, ["broadcast", "drive-connect"])) {
            $user = auth()->guard('web')->user();
            $instance = $user->instances()->first();
            session(['instanceId' => $instance->id]);

            // reimplement full session
            $authAuto = app('App\Http\Controllers\AuthController')->autoLogin();
            if (class_basename($authAuto) == 'RedirectResponse') {
                return $authAuto;
            }

            return redirect()->to($instance->getUrl());
        } elseif ($instanceSlug == 'drive-connect') {
            if (!auth()->guard('web')->check()) {
                return redirect()->to($baseProtocol.'://' . $defaultSubDomain . '.'.$baseDomain);
            }
        } elseif (!in_array($instanceSlug, ["broadcast"])) {
            auth()->guard('web')->logout();
            session()->forget(['instance', 'instanceId']);
            session(['withoutInstance' => true]);
        }


        return $next($request);
    }
}
