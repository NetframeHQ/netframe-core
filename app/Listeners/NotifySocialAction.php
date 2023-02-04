<?php

namespace App\Listeners;

use App\Events\SocialAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notif;

class NotifySocialAction
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
     * @param  SocialAction  $event
     * @return void
     */
    public function handle(SocialAction $event)
    {
        if ($event->to_user != auth()->guard('web')->user()->id) {
            $newJson = [
                'element_type'      => $event->elementType,
                'element_id'        => $event->elementId,
            ];

            $notifArray = array(
                'instances_id'   => session('instanceId'),
                'author_id'      => $event->to_user,
                'author_type'    => 'App\\User',
                'type'           => $event->typeNotification,
                'user_from'      => auth()->guard('web')->user()->id,
                'parameter'      => json_encode($newJson),
            );
            $testNotif = Notif::select()->where($notifArray);
            if (count($testNotif->get()) == 0) {
                $notif = new Notif();
                $notif->instances_id = session('instanceId');
                $notif->author_id = $event->to_user;
                $notif->author_type = 'App\\User';
                $notif->type = $event->typeNotification;
                $notif->user_from = auth()->guard('web')->user()->id;
                $notif->parameter = json_encode($newJson);
                $notif->read = 0;
                $notif->save();

                \App\Helpers\FcmHelper::buildFromNotif([$event->to_user], $notif->id);
            }
        }
    }
}
