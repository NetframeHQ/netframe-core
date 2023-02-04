<?php

namespace App\Listeners;

use App\Events\UploadMedia;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ComputeMediaSize
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
    public function handle(UploadMedia $event)
    {
        // get instance and user total media size
        $instanceMedias = $event->instance->medias->sum('file_size');
        $userMedias = $event->user->allMedias->sum('file_size');

        // update instance and user parameter
        $event->user->setParameter('medias_size', $userMedias);
        $event->instance->setParameter('medias_size', $instanceMedias);
    }
}
