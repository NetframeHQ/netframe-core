<?php

namespace App\Helpers;

use App\Tracking;

class TrackingHelper
{
    public static function logStats()
    {

        if (auth()->guard('web')->check()) {
            $trackConf = config('tracking');

            $crawler = (new \App\Helpers\CrawlerHelper)->isCrawler();
            if (!$crawler) {
                $excludedRoutes = $trackConf['exclusion-routes-tracking'];
                foreach ($excludedRoutes as $exclude) {
                    if (request()->is($exclude)) {
                        return;
                    }
                }

                // discard base domains
                $baseDomain = env('APP_BASE_DOMAIN');
                $requestedDomain = request()->getHttpHost();
                $instanceSlug = str_replace('.'.$baseDomain, '', $requestedDomain);
                if (in_array($instanceSlug, $trackConf['exclusion-subdomain-tracking'])) {
                    return;
                }

                if (getenv('HTTP_CLIENT_IP')) {
                    $ipaddress = getenv('HTTP_CLIENT_IP');
                } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                } elseif (getenv('HTTP_X_FORWARDED')) {
                    $ipaddress = getenv('HTTP_X_FORWARDED');
                } elseif (getenv('HTTP_FORWARDED_FOR')) {
                    $ipaddress = getenv('HTTP_FORWARDED_FOR');
                } elseif (getenv('HTTP_FORWARDED')) {
                    $ipaddress = getenv('HTTP_FORWARDED');
                } elseif (getenv('REMOTE_ADDR')) {
                    $ipaddress = getenv('REMOTE_ADDR');
                } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                }

                if (session()->has('instanceId')) {
                    //push tracking
                    if (session()->exists('ghostId')) {
                        $userId = session('ghostId');
                        $ipaddress = '127.0.0.1';
                        $userType = 'App\\Ghost';
                        $location = 'hidden';
                    } else {
                        $userId = auth()->guard('web')->user()->id;
                        $userType = 'App\\User';
                        $location = session("lat").','.session("lng");
                    }

                    $tracking = new Tracking();
                    $tracking->instances_id = session('instanceId');
                    $tracking->location = $location;
                    $tracking->users_type = $userType;
                    $tracking->users_id = $userId;
                    $tracking->referer = substr(request()->server('HTTP_REFERER'), 0, 250);
                    $tracking->url = request()->url();
                    $tracking->method = request()->server('REQUEST_METHOD');
                    $tracking->language = \Lang::locale();
                    $tracking->true_language = substr(request()->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
                    $tracking->ip = $ipaddress;
                    $tracking->user_agent = request()->server('HTTP_USER_AGENT');
                    $tracking->save();
                }
            }
        }
    }
}
