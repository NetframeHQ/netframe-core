<?php

/**
 * Macro HTML pour appeler le type d'alert et le message d'alert
 * pour jquery growl bootstrap javascript
 * type message (null, 'info', 'danger', 'success')
 * envoyer une variable session portant le nom 'growl' ex: session()->flash('growl', '<type>|<message>')
 *
 */
HTML::macro('growl', function ($key) {
    $data = explode('|', session('growl'));
    return $data[$key];
});

/**
 *
 * Macro HTML pour gérer les messages erreurs, success, alerts...
 * Dans le controlleur renvoyer un message flash de la form
 * ex: session()->flash('messageForm', "danger | <mon message>");
 *
 */
Form::macro('message', function () {
    if (session()->has('messageForm')) {
        // exemple dans le controller renvoyer une valeur en session
        // ayant le nom messageForm, contenu du message ex: "success|<mon message>"
        $data = explode('|', session('messageForm'));

        return view('macros.form-message')->with(array(
            'alertType' => trim($data[0]),
            'content' => trim($data[1])
        ))->render();
    }
});

/**
 * number of notifications for left menu
 */
HTML::macro('notifyNumber', function () {
    $count = App\Notif::where(array(
        'author_id' => auth()->guard('web')->user()->id,
        'read' => 0
    ))
    ->count();

    return $count;
});

/**
 * number of messages for left menu
 */
HTML::macro('messagesNumber', function () {
    $count = session('wmm');
    return $count;
});

/**
 * number of messages and notifications for left menu
 *
 * @depcreated
 */
HTML::macro('messageNotifNumber', function () {
    $countM = session('wmm');
    $countN = Notif::where(array(
        'author_id' => auth()->guard('web')->user()->id,
        'read' => 0
    ))
    ->count();

    $count = $countN + $countM;

    $output = $count;

    return $output;
});

/**
 *
 * number of itemn in instant playlist
 * @deprecated
 */
HTML::macro('instantPlaylistNumber', function () {
    if (auth()->guard('web')->check()) {
        $count = Playlist::where('users_id', '=', auth()->guard('web')->user()->id)
        ->where('instant_playlist', '=', 1)
        ->first();
        if ($count != null) {
            $count = $count->items()
            ->where('read_owner', '=', 0)
            ->count();
        }
    } else {
        $count = 0;
    }

    //$output = ( $count > 0 ) ? '<span class="badge-notif">'.$count.'</span>' : '';
    $output = $count;

    return $output;
});
