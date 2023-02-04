<?php

namespace App\Http\ViewComposers\Sidebar;

use Illuminate\View\View;

class ProfileMosaic
{
    public function __construct()
    {
    }

    /**
     * SIDEBAR WIDGET profiles
     *
     * @param
     *            (object) profile : value = $profile->follwers()
     * @param
     *            (string) profileType : value = 'user'
*      @return void
     */
    public function compose(View $view)
    {
        $params = $view->getData();

        if (isset($params['prefixTranslate'])) {
            $prefixTranslate = $params['prefixTranslate'];
        } else {
            $prefixTranslate = '';
        }

        $view->with([
            'profiles' => $params['profiles'],
            'profileType' => $params['profileType'],
            'prefixTranslate' => $prefixTranslate
        ]);
    }
}
