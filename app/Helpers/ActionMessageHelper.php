<?php

namespace App\Helpers;

class ActionMessageHelper
{
    public static function success($message)
    {
        session()->flash('notifActionStatus', 'success');
        session()->flash('notifActionMessage', $message);
    }

    public static function show()
    {
        if (session()->has('notifActionMessage')) {
            return view('notifications.action-message')->render();
        } else {
            return '';
        }
    }
}
