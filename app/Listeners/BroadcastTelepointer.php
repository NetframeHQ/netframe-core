<?php

namespace App\Listeners;

use App\Events\CollabTelepointer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BroadcastTelepointer
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
     * @param  CollabTelepointer  $event
     * @return void
     */
    public function handle(CollabTelepointer $event)
    {
        //
    }
}
