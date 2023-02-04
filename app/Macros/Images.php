<?php

/**
 * generate tumbnail by media object
 */
HTML::macro('thumbnail', function (
    /*\App\Media*/ $media = null,
    $width = 120,
    $height = 120,
    array $attributes = array(),
    $defaultSrc = null,
    $style = null,
    $profileType = ''
) {
    if ($width != '' && $height != '') {
        $style = "style=\"max-width:".$width."px;max-height:".$height."px;width:100%;height:100%;\"";
    } else {
        $style = '';
    }

        return view('macros.thumbnail', array(
            'media' => $media,
            'width' => $width,
            'height' => $height,
            'attributes' => $attributes,
            'defaultSrc' => $defaultSrc,
            'style' => $style,
            'profileType' => $profileType
        ))->render();
});

/**
 * generate tumbnail by media object
 */
HTML::macro('thumbImage', function (
    $mediaId = null,
    $width = 120,
    $height = 120,
    array $attributes = array(),
    $defaultSrc = null,
    $spanStyle = null,
    $profile = null
) {
    if ($width != '' && $height != '') {
        $style = "style=\"max-width:".$width."px;max-height:".$height."px;width:100%;height:100%;\"";
    } else {
        $style = '';
    }
        return view('macros.thumb-image', array(
            'mediaId' => $mediaId,
            'width' => $width,
            'height' => $height,
            'attributes' => $attributes,
            'defaultSrc' => $defaultSrc,
            'style' => $style,
            'spanStyle' => $spanStyle,
            'profile' => $profile,
        ))->render();
});




/**
 * display mosaic
 * input tab with list of object profiles
 *
 * @DEPRECATED
 */
HTML::macro('miniMosaic', function (
    $profiles = null,
    $profileType = '',
    $width = 90,
    $height = 90,
    array $attributes = array(),
    $defaultSrc = null,
    $style = null
) {
        $output = '';

        return view('macros.mini-mosaic', array(
            'profiles' => $profiles,
            'profileType' => $profileType,
            'width' => $width,
            'height' => $height,
            'attributes' => $attributes,
            'defaultSrc' => $defaultSrc,
            'style' => $style
        ))->render();
});


/**
 * Show mini Mosaic thumbnail
 *
 * @param (object) $loopObject loop on object
 * @param (int) $colSize number display column size for responsive thumbnail
 */
HTML::macro('thumbMosaic', function ($loopObject, $colSize = 3) {
    return view('macros.thumb-mosaic', array(
        'listProfile' => $loopObject,
        'colSize' => $colSize
    ))->render();
});
