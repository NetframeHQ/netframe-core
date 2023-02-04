<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\MessageMail;
use App\Friends;
use App\Notif;

class Navigation
{
    /**
     * set var for main navigation
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();

        if (auth()->guard('web')->check()) {
            $unreadedMsg = MessageMail::getUnreadNotification();
            $hasMessages = MessageMail::hasMessages();
            $nbFriends = Friends::countFriends();

            $countNotifs = Notif::where(array(
                'author_id' => auth()->guard('web')->user()->id,
                'read' => 0
            ))->count();

            session([
                "hasMessages" => $hasMessages,
                "wmm" => $unreadedMsg,
                "NetframeFriends" => $nbFriends
            ]);
            $totalNotifications = $countNotifs + $unreadedMsg;
        } else {
            session([
                "hasMessages" => false,
                "wmm" => 0,
                "NetframeFriends" => 0
            ]);
            $totalNotifications = 0;
            $avatar = null;
        }

        return $view->with('totalNotifications', $totalNotifications);
    }
}
