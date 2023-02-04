<?php

/**
 *  Macro pour générer l'url de l'utilisateur
 */
HTML::macro('urlUser', function ($uri = null) {
    if (auth()->guard('web')->check()) {
        $uriUser = auth()->guard('web')->user()->slug . '/' . strtolower(
            auth()->guard('web')->user()->firstname . '-' . auth()->guard('web')->user()->name
        );

        // segement supplémentaire pour l'url si besoin
        $segment = (isset($uri)) ? '/'. $uri : null;

        return url()->to('user/' . $uriUser . $segment);
    }
});

/**
 * return online status of user or talent attach to user
 */
HTML::macro('online', function ($profile, $small = false, $cssClass = '') {
    if ($profile->getType() == "user") {
        $output = '';

        if ($profile->isOnline()) {
            $output .= '<span class="status-online '.$cssClass.'">';

        /* } elseif ($small) {
            return '';
        } */
        } else {
            $output .= '<span class="status-offline '.$cssClass.'">';
        }

        if ($small) {
            $output .= '<i class="icon ticon-dot"></i>';
        } else {
            $output .= '<i class="glyphicon glyphicon-user"></i>';
        }
        $output .= '</span>';

        return $output;
    } else {
        return '';
    }
});
