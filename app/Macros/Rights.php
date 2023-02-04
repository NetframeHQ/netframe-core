<?php

/**
 *  Manage rights for instance, profiles and users
 */
 HTML::macro('userRights', function ($mainProfile, $relatedProfile, $domTargetRelaod, $fromInvite) {
    $tpl = [];
    $tpl['profile'] = (class_basename($mainProfile) == 'User') ? $relatedProfile : $mainProfile;
    $tpl['user'] = (class_basename($mainProfile) == 'User') ? $mainProfile : $relatedProfile;

    $tpl['fromProfile'] = class_basename($mainProfile);
    //get dynamicaly user role on profile or instance
    $userRights = $tpl['profile']->users()->where('users_id', $tpl['user']->id)->first();
    $tpl['right'] = ($userRights) ? $userRights->pivot->roles_id : -1;
    $tpl['status'] = ($userRights) ? $userRights->pivot->status : -1;
    $tpl['domTargetRelaod'] = $domTargetRelaod;
    $tpl['fromInvite'] = ($fromInvite) ? 1 : 0;

    return view('macros.user-rights', $tpl)->render();
 });
