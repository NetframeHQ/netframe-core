<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class Event
{
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
