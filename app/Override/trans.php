<?php

function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
{

    if (is_null($id)) {
        return app('translator');
    }

    if (session()->has('instanceId')) {
        $overrideConf = config('override-trans.'.session('instanceId').'.'.$id);
        if ($overrideConf != null) {
            return $overrideConf;
        }
    }
    $translatedValue = app('translator')->trans($id, $parameters, $domain, $locale);
    return $translatedValue;
}
