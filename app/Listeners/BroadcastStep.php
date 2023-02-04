<?php

namespace App\Listeners;

use App\Events\CollabStep;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BroadcastStep
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
     * @param  CollabStep  $event
     * @return void
     */
    public function handle(CollabStep $event)
    {
        //
    }
}
