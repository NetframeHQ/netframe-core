<?php

namespace App\Listeners;

use App\Events\PostNotif;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notif;

class PostNotifEvent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PostNotif  $event
     * @return void
     */
    public function handle(PostNotif $event)
    {
        $testNotif = Notif::select()->where($event->notification);
        if (count($testNotif->get()) == 0) {
            $notif = new Notif();
            $notif->instances_id = $event->notification['instances_id'];
            $notif->author_id = $event->notification['author_id'];
            $notif->author_type = $event->notification['author_type'];
            $notif->type = $event->notification['type'];
            $notif->user_from = $event->notification['user_from'];
            $notif->parameter = $event->notification['parameter'];
            $notif->read = 0;
            $notif->save();

            \App\Helpers\FcmHelper::buildFromNotif([$event->notification['author_id']], $notif->id);
        }
    }
}
