<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;
use App\Subscription;
use App\Like;

class SidebarProfile
{
    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();

        $profile = $getData['profile'];

        $joined = $profile->users()->where('users_id', '=', auth()->guard('web')->user()->id)->first();
        if ($joined != null) {
            $joined = $joined->pivot->status;
        }
        $liked = Like::isLiked(['liked_id'=>$profile->id, 'liked_type'=>get_class($profile)]);

        $nextEvents = $profile->posts()
            ->leftJoin('events', 'events.id', '=', 'news_feeds.post_id')
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where(function ($dates) {
                $dates->orWhere(function ($allDay) {
                    $allDay->whereNull('events.time')
                        ->where('events.date', '>=', date('Y-m-d'));
                })
                ->orWhere(function ($allDay) {
                    $allDay->whereNotNull('events.time')
                        ->where(\DB::raw("CONCAT(date, ' ', 'time')"), '>=', date('Y-m-d H:i:s'));
                });
            })
            ->take(3)->get();

        $nextEvents = $profile->posts()
            ->leftJoin('events', 'events.id', '=', 'news_feeds.post_id')
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where(function ($dates) {
                $dates->orWhere(function ($allDay) {
                    $allDay->whereNull('events.time')
                        ->where('events.date', '>=', date('Y-m-d'));
                })
                ->orWhere(function ($allDay) {
                    $allDay->whereNotNull('events.time')
                        ->where(\DB::raw("CONCAT(date, ' ', 'time')"), '>=', date('Y-m-d H:i:s'));
                });
            })
            ->take(3)->get();

        $followed = Subscription::checkSubscribe($profile->id, get_class($profile));
        $sidebarProfiles = array();
        $sidebarProfiles[] = [ 'type'=>'task', 'profiles' => $profile->tasks->take(10) ];
        $sidebarProfiles[] = [ 'type'=>'house', 'profiles' => $profile->houses->take(4) ];
        $sidebarProfiles[] = [ 'type'=>'community', 'profiles' => $profile->communities->take(4) ];
        $sidebarProfiles[] = [ 'type'=>'project', 'profiles' => $profile->projects->take(4) ];
        $sidebarProfiles[] = [ 'type'=>'channel', 'profiles' => $profile->channels->take(10) ];
        $sidebarProfiles[] = [
            'type'=>'members',
            'profiles' => $profile->users()->wherePivot('status', '=', 1)->take(10)->get()
        ];
        $starMedias = $profile->getFavoriteOrLastMedia();

        return $view->with('followed', $followed)
            ->with('nextEvents', $nextEvents)
            ->with('liked', $liked)
            ->with('joined', $joined)
            ->with('nextEvents', $nextEvents)
            ->with('sidebarProfiles', $sidebarProfiles)
            ->with('starMedias', $starMedias);
    }
}
