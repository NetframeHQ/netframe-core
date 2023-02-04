<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class Playlist
{
    /**
     * SIDEBAR WIDGET playlist
     *
     * @param
     *            (object) profile : value = $profile->follwers()
     * @param
     *            (string) profileType : value = 'user'
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $params = $view->getData();

        if (isset($params['prefixTranslate'])) {
            $prefixTranslate = $params['prefixTranslate'];
        } else {
            $prefixTranslate = '';
        }

        return $view->with('prefixTranslate', $prefixTranslate);
    }
}
