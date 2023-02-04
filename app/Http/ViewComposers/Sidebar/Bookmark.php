<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class Bookmark
{
    /**
     * SIDEBAR WIDGET Bookmark
     * Attribute used in composer is $project->bookmarks
     *
     * @param
     *            (object) project : value $profile
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $params = $view->getData();

        return $view->with('project', $params['project']);
    }
}
