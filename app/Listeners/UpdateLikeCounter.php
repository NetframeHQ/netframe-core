<?php

namespace App\Listeners;

use App\Events\LikeElement;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\NewsFeed;

class UpdateLikeCounter
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
     * @param  LikeElement  $event
     * @return void
     */
    public function handle(LikeElement $event)
    {
        if (in_array($event->model, config('netframe.model_likables'))) {
            $element = call_user_func(array("App\\".class_basename($event->model), 'find'), $event->where['liked_id']);
            $element->timestamps = false;

            if ($event->crement == 'increment') {
                $element->increment('like', 1);
            } else {
                $element->decrement('like', 1);
            }
        } else {
            if ($event->where['idNewsFeeds'] != 0) {
                $newsFeed = NewsFeed::find($event->where['idNewsFeeds']);
                $newsFeed->timestamps = false;

                if ($event->crement == 'increment') {
                    $newsFeed->increment('like', 1);
                } else {
                    $newsFeed->decrement('like', 1);
                }
            }
        }
    }
}
