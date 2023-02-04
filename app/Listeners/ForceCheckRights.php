<?php

namespace App\Listeners;

use App\Events\AddProfile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ForceCheckRights
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
     * @param  AddProfile  $event
     * @return void
     */
    public function handle(AddProfile $event)
    {
        $user = User::findOrFail($event->userId);
        $user->check_rights = 1;
        $user->save();
    }
}
