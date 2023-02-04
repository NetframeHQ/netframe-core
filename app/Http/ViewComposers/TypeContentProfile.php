<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class TypeContentProfile
{
    /**
     * share profile card
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();

        $profile = $getData['profile'];

        $spokenLanguages = null;

        if ($profile->getType() == 'user') {
            $spokenLanguages = $profile->getSpokenLanguages();
            $spokenLanguages = (count($spokenLanguages) > 0) ? $profile->getSpokenLanguages() : null;
        }

        return $view->with('spokenLanguages', $spokenLanguages);
    }
}
