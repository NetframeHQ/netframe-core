<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class ThumbDisplay
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

        $width = (isset($getData['width'])) ? $getData['width'] : 90;
        $height = (isset($getData['height'])) ? $getData['height'] : 90;
        $attributes = (isset($getData['attributes'])) ? $getData['attributes'] : [
            'class' => 'profile-image img-responsive'
        ];
        $defaultSrc = isset($getData['defaultSrc'])
            ? $getData['defaultSrc']
            : asset('assets/img/mosaic-no-image.jpg');

        return $view->with('width', $width)
        ->with('height', $height)
        ->with('attributes', $attributes)
        ->with('defaultSrc', $defaultSrc);
    }
}
