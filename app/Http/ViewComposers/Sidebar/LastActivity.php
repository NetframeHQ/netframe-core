<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class LastActivity
{
    /**
     * SIDEBAR WIDGET Last Activity
     * Attribute used in composer is $profile
     *
     * @param
     *            (object) profile : value $profile
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        /*
         * $params = $view->getData();
         *
         * return $view->with('profile', $params['profile']);
         */
    }
}
