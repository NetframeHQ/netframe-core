<?php

/**
 *  Button Like Post
 */
HTML::macro('fileValidateAsk', function ($workflowAction, $media, $notif) {
    $tpl = [];
    $tpl['media'] = $media;
    $tpl['wfAction'] = $workflowAction;
    $tpl['notif'] = $notif;

    return view('workflow.macros.validate-file-ask', $tpl)->render();
});
