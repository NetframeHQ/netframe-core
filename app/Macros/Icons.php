<?php

/**
 *  profiles icons
 */
HTML::macro('profileIcon', function ($profile, $state = null, $notif = null) {
    $tpl = [];
    $tpl['profile'] = $profile;
    $tpl['state'] = $state;
    $tpl['notif'] = $notif;
    $tpl['withSpan'] = true;

    return view('macros.svg-icons.'.$profile, $tpl)->render();
});

HTML::macro('userAvatar', function ($user, $size, $additionnalClass = '') {
    $tpl = [];
    $tpl['initials'] = $user->initials();
    $tpl['color'] = $user->initialsToColor();
    $tpl['size'] = 'size-' . $size;
    $tpl['additionnalClass'] = $additionnalClass;

    return view('macros.user-avatar', $tpl)->render();
});
