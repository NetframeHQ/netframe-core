<?php

namespace App\Listeners;

use App\Events\PostAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\NewsFeed;

class NewsFeedUpdate
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
     * @param  PostAction  $event
     * @return void
     */
    public function handle(PostAction $event)
    {
        $newsFeed = NewsFeed::where('post_id', '=', $event->id)
            ->where('post_type', '=', $event->typePost)
            ->update(array('updated_at' => date('Y-m-d H:i:s')));
    }
}
