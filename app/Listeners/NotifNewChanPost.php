<?php
namespace App\Listeners;

use App\Events\CheckUserTag;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\PostChannel;
use App\Channel;
use App\News;
use App\Helpers\FcmHelper;

class NotifNewChanPost
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
     * @param UserUpdatedEvent $event
     * @return void
     */
    public function handle(PostChannel $event)
    {
        // get all users of channel except @author julien
        $channel = Channel::find($event->channelId);
        $post = News::find($event->postId);
        $author = $post->author;
        $title = $author->getNameDisplay() . ' ' . trans('notifications.on') . ' ' . $channel->getNameDisplay();
        $message = \App\Helpers\StringHelper::resumeContent($post->content, 200) . '...';
        $link = $channel->getUrl();
        $usersIds = $channel->users()
            ->wherePivot('status', '=', '1')
            ->where('users.id', '!=', $author->id)
            ->pluck('users.id')
            ->toArray();

        \App\Helpers\FcmHelper::sendFcm($usersIds, $title, $message, $link);
    }
}
