<?php

namespace App\Http\ViewComposers\Toolbar;

use Illuminate\View\View;

class MobileSidebar
{
    /**
     * Mobile toolbar event card btn xs
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();
        $profile = $getData['profile'];
        $nextEvent = $profile->nextEvent->first();
        return $view->with('nextEvent', $nextEvent);
    }
}
