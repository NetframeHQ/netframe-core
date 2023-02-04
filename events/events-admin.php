<?php
/**
 *  Events admin file
 *
 *
 */


/**
 * Detect browser language and set netframe in Language user client if
 * application netframe has this language
 */
Event::listen('admin.notifyCrowdfunding', function ($crowdfunding, $valid) {
    //get users to notify
    $arrayNotification = array();
    $users = $crowdfunding->projects->users()->where('roles_id', '<=', 2)->get();
    foreach ($users as $user) {
        $arrayNotification[] = $user;
    }

    $notifJson = [
        'crowdfundingId'   => $crowdfunding->id,
        'result'     => $valid,
    ];

    foreach ($arrayNotification as $userNotif) {
        //add notify to project owners
        $notif = new Notif();
        $notif->author_id = $userNotif->id;
        $notif->author_type = 'User';
        $notif->type = 'crowdfundingValidation';
        $notif->user_from = 1;
        $notif->parameter = json_encode($notifJson);
        $notif->save();

        //send email
        $dataMail = array();
        $dataMail['user'] = $user;
        $dataMail['valid'] = ($valid == 1) ? 'Valid' : 'Refuse';

        Mail::send('emails.admin.crowdfunding-validation', $dataMail, function ($m) use ($user) {
            $m->to($user->email, $user->getNameDisplay())
                ->subject(trans('email.crowdfunding.subject', [], $user->lang));
        });
    }
});
