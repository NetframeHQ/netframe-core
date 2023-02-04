<?php

namespace App\Http\ViewComposers\Messages;

use Illuminate\View\View;

use App\Notif;

class NewWithList
{
    /**
     * new message with contact list
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();

        $contactList = \App\Http\Controllers\MessageMailController::listContacts();

        return $view->with('contactList', $contactList)
        ->with('types', config('messages.types'))
        ->with('type', 1)
        ->with('overrideType', 0);
    }
}
