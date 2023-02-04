<?php

namespace App\Listeners;

use App\Events\NewPost;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\NewsFeed;

class NewsFeedInsertUpdate
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
     * @param  NewsPost  $event
     * @return void
     */
    public function handle(NewPost $event)
    {
        $newsFeed = new NewsFeed;

        if (is_null($event->id)) {
            // Insert
            $newsFeed->users_id = $event->data->users_id;
            $newsFeed->instances_id = session('instanceId');
            $newsFeed->author_id = $event->data->author_id;
            $newsFeed->author_type = studly_case($event->data->author_type);
            $newsFeed->true_author_id = $event->data->true_author_id;
            $newsFeed->true_author_type = studly_case($event->data->true_author_type);
            $newsFeed->post_id = $event->data->id;
            $newsFeed->confidentiality = $event->data->confidentiality;
            $newsFeed->post_type = "App\\".studly_case($event->typePost);
            $newsFeed->save();
        } else {
            // Update
            $newsFeed = NewsFeed::where('post_id', '=', $event->id)
                ->where('post_type', '=', get_class($event->data))
                ->first();

            $newsFeed->author_id = $event->data->author_id;
            $newsFeed->author_type = studly_case($event->data->author_type);
            $newsFeed->true_author_id = $event->data->true_author_id;
            $newsFeed->true_author_type = studly_case($event->data->true_author_type);
            $newsFeed->users_id = $event->data->users_id;
            $newsFeed->confidentiality = $event->data->confidentiality;
            $newsFeed->updated_at = $event->data->updated_at;
            $newsFeed->post_type = "App\\".studly_case($event->typePost);
            $newsFeed->save();
        }

        $newsFeed->author->touch();

        if ($event->mediasId != null && $newsFeed->author != $newsFeed->true_author && !$event->fromUpload) {
            //detach old medias
            foreach ($event->oldMediasList as $oldMedia) {
                $newsFeed->author->medias()->detach($oldMedia);
            }

            // get default post folder
            $pivotFields = [];
            if (class_basename($newsFeed->author) != 'Channel') {
                $mediaFolder = $newsFeed->author->getDefaultFolder('__posts_medias');
                $pivotFields = ['medias_folders_id' => $mediaFolder];
            }

            foreach ($event->mediasId as $mediaId) {
                // test if media already attached
                if (!$newsFeed->author->medias->contains($mediaId)) {
                    $newsFeed->author->medias()->attach($mediaId, $pivotFields);
                }
            }
        }
    }
}
