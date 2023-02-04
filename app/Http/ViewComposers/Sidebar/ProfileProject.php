<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class ProfileProject
{
    public function __construct()
    {
    }

    /**
     * SIDEBAR WIDGET project card
     */
    public function compose(View $view)
    {
        $params = $view->getData();
        
        if (isset($params['prefixTranslate'])) {
            $prefixTranslate = $params['prefixTranslate'];
        } else {
            $prefixTranslate = '';
        }
        
        if (isset($params['routeMore'])) {
            $routeMore = $params['routeMore'];
        } else {
            $routeMore = '';
        }
        
        return $view->with('projects', $params['projects'])
            ->with('routeMore', $routeMore)
            ->with('prefixTranslate', $prefixTranslate);
    }
}
