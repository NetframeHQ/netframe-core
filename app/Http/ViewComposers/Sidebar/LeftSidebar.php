<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class LeftSidebar
{
    public function __construct()
    {
    }

    /**
     * Left sidebar probiles
     */
    public function compose(View $view)
    {
        $getData = $view->getData();

        $profilesTypes = config('netframe.list_profile');

        if (session('instanceMonoProfile')) {
            $profilesTypes = config('netframe.list_profile_mono');
        } else {
            $profilesTypes = config('netframe.list_profile');
        }

        /*
         * HARD DIRTY OVERRIDE FILTER
         */
        if (session('instanceId') == 406) {
            $profilesTypes = array(
                "house",
                "community",
                "project",
            );
        }

        $userProfiles = [];
        foreach ($profilesTypes as $keyProfile) {
            $user = auth()->guard('web')->user();
            $userProfiles[$keyProfile] = $user
                ->$keyProfile()
                ->where('active', '1', 1)
                ->orderBy('updated_at', 'DESC')
                ->with(['profileImage'])
                ->get();
        }

        return $view->with('profilesTypes', $profilesTypes)
            ->with('userProfiles', $userProfiles);
    }
}
