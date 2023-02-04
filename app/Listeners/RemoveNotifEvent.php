<?php

namespace App\Listeners;

use App\Events\RemoveNotif;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notif;

class RemoveNotifEvent
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
     * @param  RemoveNotif  $event
     * @return void
     */
    public function handle(RemoveNotif $event)
    {
        $testNotif = Notif::select()->where($event->notification);
        if (count($testNotif->get()) != 0) {
            $notif = $testNotif->get()->first();
            $notif->delete();
        }
    }
}
