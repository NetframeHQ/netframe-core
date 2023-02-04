<?php

namespace App\Listeners;

use App\Events\CheckUserTag;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notif;

class UserTag
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  UserUpdatedEvent  $event
     * @return void
     */
    public function handle(CheckUserTag $event)
    {
        $post = $event->post;
        $content = $event->content;

        $userTaggued = preg_match_all('~(@\[(.*?)\]\(user:([0-9]*)\))~i', $content, $matchesTaggued);
        if (!empty($matchesTaggued[3])) {
            $fcmUsers = [];

            foreach ($matchesTaggued[3] as $userId) {
                $fcmUsers[] = $userId;

                // build array
                $notifJson = [
                    'post_type' => get_class($post),
                    'post_id' => $post->id,
                ];

                //notify taggued user
                $notif = new Notif();
                $notif->instances_id = session('instanceId');
                $notif->author_id = $userId;
                $notif->author_type = 'App\\User';
                $notif->type = 'userTaggued';
                $notif->user_from = auth()->guard('web')->user()->id;
                $notif->parameter = json_encode($notifJson);
                $notif->save();
            }

            // get last notif text
            if (!empty($fcmUsers)) {
                \App\Helpers\FcmHelper::buildFromNotif($fcmUsers, $notif->id);
            }
        }
    }
}
