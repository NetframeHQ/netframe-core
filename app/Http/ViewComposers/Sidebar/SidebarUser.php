<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;
use App\User;
use App\UsersReference;
use App\Helpers\AclHelper;

class SidebarUser
{
    public function __construct()
    {
        $this->Acl = new AclHelper();
    }

    public function compose(View $view)
    {
        $getData = $view->getData();

        $profile = $getData['profile'];

        if (auth()->guard('web')->check() && auth()->guard('web')->user()->id == $profile->id) {
            $prefixTranslate = "own";
        } else {
            $prefixTranslate = "";
        }

        $data = array();
        $user = User::find($profile->id);
        $zoomMapBox = config('location.zoom-map-sidebar');
        $sidebarProfiles = [];
        $sidebarPages = [];

        $data['playlistsuser'] = $user->playlistsuser->take(3);

        //subscriptions
        $subscriptions = $user->subscriptionsList->take(4);
        $subscribeProfiles = [];
        foreach ($subscriptions as $suscribe) {
            if ($suscribe->profile->active == 1) {
                $subscribeProfiles[] = $suscribe->profile;
            }
        }

        $sidebarProfiles[] = [ 'type'=>'subscriptions', 'profiles' => $subscribeProfiles ];
        $sidebarProfiles[] = [ 'type'=>'friends', 'profiles' => $user->friendsList(4) ];
        $sidebarProfiles[] = [ 'type'=>'followers', 'profiles' => $user->followers()->take(4) ];
        $sidebarPages[] = [ 'type'=>'house', 'profiles' => $user->houses->where('active', '=', 1)->take(4) ];
        $sidebarPages[] = [ 'type'=>'community', 'profiles' => $user->community->where('active', '=', 1)->take(4) ];
        $sidebarPages[] = [ 'type'=>'project', 'profiles' => $user->project->where('active', '=', 1)->take(4) ];
        $sidebarPages[] = [ 'type'=>'channel', 'profiles' => $user->channels->where('active', '=', 1)->take(4) ];
        $rights = $this->Acl->getRights('user', $user->id);

        //get references liked by showing user
        $userLikedReferences = UsersReference::select('users_references.id')
            ->where('users_references.instances_id', '=', session('instanceId'))
            ->join('likes as l', 'l.liked_id', '=', 'users_references.id')
            ->where('l.liked_type', '=', 'App\\UsersReference')
            ->where('l.users_id', '=', auth()->guard('web')->user()->id)
            ->where('users_references.users_id', '=', $profile->id)
            ->pluck('users_references.id')->toArray();

        $displayUserPages = true;
        foreach ($sidebarPages as $pageType) {
            if (count($pageType['profiles']) > 0) {
                $displayUserPages = true;
            }
        }

        return $view->with('prefixTranslate', $prefixTranslate)
            ->with('displayUserPages', $displayUserPages)
            ->with('userLikedReferences', $userLikedReferences)
            ->with('dataUser', $user)
            ->with('profileSidebar', $user)
            ->with('zoomMapBox', $zoomMapBox)
            ->with('sidebarProfiles', $sidebarProfiles)
            ->with('sidebarPages', $sidebarPages)
            ->with('profile', $user)
            ->with('rights', $rights);
    }
}
