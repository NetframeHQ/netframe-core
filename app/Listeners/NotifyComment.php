<?php

namespace App\Listeners;

use App\Events\NewComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Comment;
use App\Notif;

class NotifyComment
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
     * @param  NewComment  $event
     * @return void
     */
    public function handle(NewComment $event)
    {
        $news = $event->comment->post;
        $com = Comment::where(['post_id' => $event->comment->post_id, 'post_type' => $event->comment->post_type])
            ->where('users_id', '!=', $event->comment->users_id)
            ->Where('users_id', '!=', $news->users_id)
            ->groupBy('users_id')->get();

        $newJson = [
            'post_type'      => $event->comment->post_type,
            'post_id'        => $event->comment->post_id,
            'id_comment'     => $event->comment->id,
            'text_comment'   => substr($event->comment->content, 0, 50)];
        $notifArray = array();

        if ($event->comment->users_id != $news->users_id) {
            array_push($notifArray, array(
                //'id_foreign'     => $news->users_id,
                //'type_foreign'   => 'user',
                'instances_id'   => session('instanceId'),
                'author_id'      => $news->users_id,
                'author_type'    => 'App\\User',
                'type'           => 'comment',
                'user_from'      => $event->comment->users_id,
                'parameter'      => json_encode($newJson),
                'read'           => 0,
                'created_at'     => new \DateTime(),
                'updated_at'     => new \DateTime()
            ));
        }

        foreach ($com as $item) {
            array_push($notifArray, array(
                'instances_id'  => session('instanceId'),
                'author_id'     => $item->users_id,
                'author_type'   => 'App\\User',
                'type'          => 'comment',
                'user_from'     => $event->comment->users_id,
                'parameter'     => json_encode($newJson),
                'read'          => 0,
                'created_at'    => new \DateTime(),
                'updated_at'    => new \DateTime()
            ));
        }

        if (!empty($notifArray)) {
            foreach ($notifArray as $notifUnit) {
                $notif = new Notif();
                $notif->instances_id = $notifUnit['instances_id'];
                $notif->author_id = $notifUnit['author_id'];
                $notif->author_type = $notifUnit['author_type'];
                $notif->type = $notifUnit['type'];
                $notif->user_from = $notifUnit['user_from'];
                $notif->parameter = $notifUnit['parameter'];
                $notif->read = 0;
                $notif->save();

                \App\Helpers\FcmHelper::buildFromNotif([$notifUnit['author_id']], $notif->id);
            }
            //Notif::insert($notifArray);
        }
    }
}
