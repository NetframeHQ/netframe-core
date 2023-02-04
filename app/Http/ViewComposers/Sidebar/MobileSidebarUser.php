<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class MobileSidebarUser
{
    public function __construct()
    {
    }

    /**
     * MOBILE TOOLBAR USER
     */
    public function compose(View $view)
    {
        $getData = $view->getData();

        if (auth()->guard('web')->check() && auth()->guard('web')->user()->id == $getData['profile']->id) {
            $prefixTranslate = "own";
        } else {
            $prefixTranslate = "";
        }

        return $view->with('prefixTranslate', $prefixTranslate);
    }
}
