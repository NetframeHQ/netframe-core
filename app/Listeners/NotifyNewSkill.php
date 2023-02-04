<?php

namespace App\Listeners;

use App\Events\AddUserSkill;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notif;

class NotifyNewSkill
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
     * @param  AddUserSkill  $event
     * @return void
     */
    public function handle(AddUserSkill $event)
    {
        $notifJson = [
            'referenceId'   => $event->reference->id,
            'referenceName'     => $event->reference->reference->name,
        ];

        //notify owners
        $notif = new Notif();
        $notif->instances_id = session('instanceId');
        $notif->author_id = $event->user->id;
        $notif->author_type = 'App\\User';
        $notif->type = 'userNewReferenceByUser';
        $notif->user_from = auth()->guard('web')->user()->id;
        $notif->parameter = json_encode($notifJson);
        $notif->save();

        \App\Helpers\FcmHelper::buildFromNotif([$event->user->id], $notif->id);
    }
}
