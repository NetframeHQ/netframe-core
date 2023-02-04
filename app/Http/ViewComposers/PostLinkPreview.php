<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Link;

class PostLinkPreview
{
    /**
     * give default params for thumb
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();

        $linkId = $getData['id'];
        $link = Link::find($linkId);

        return $view->with('link', $link);
    }
}
