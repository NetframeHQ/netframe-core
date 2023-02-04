<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class NotificationUser
{

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();

        $notifications = $getData['notifications'];

        return $view->with('results', $notifications);
    }
}
