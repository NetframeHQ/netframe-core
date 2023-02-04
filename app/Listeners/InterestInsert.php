<?php

namespace App\Listeners;

use App\Events\InterestAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Interest;

class InterestInsert
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
     * @param  InterestAction  $event
     * @return void
     */
    public function handle(InterestAction $event)
    {
        $proccedInterests = false;
        if (!is_array($event->tags) && class_basename($event->tags) == 'Tag') {
            $tags = [];
            $tags[] = $event->tags;
            $proccedInterests = true;
        } elseif ($event->tags instanceof \Illuminate\Database\Eloquent\Collection) {
            $tags = $event->tags;
            $proccedInterests = true;
        }

        if ($proccedInterests) {
            foreach ($tags as $tag) {
                //check if user has this interest
                $hasInterest = $event->user->interests()->where('tags_id', '=', $tag->id)->first();

                if ($hasInterest != null) {
                    $hasInterest->weight = $hasInterest->weight * config('interests.'.$event->type.'.exists');
                    $hasInterest->save();
                } else {
                    $interest = new Interest();
                    $interest->instances_id = session('instanceId');
                    $interest->users_id = $event->user->id;
                    $interest->tags_id = $tag->id;
                    $interest->weight = config('interests.'.$event->type.'.new');
                    $interest->save();
                }
            }
        }
    }
}
