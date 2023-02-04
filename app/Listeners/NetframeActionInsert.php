<?php

namespace App\Listeners;

use App\Events\NewAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\NewsFeed;
use App\NetframeAction;

class NetframeActionInsert
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
     * @param  NewAction  $event
     * @return void
     */
    public function handle(NewAction $event)
    {
        if (auth()->guard('web')->check()) {
            $loadAction = true;
            if (in_array($event->type_action, ['bookmark', 'follow', 'like', 'participant_event'])) {
                //test if action already exists

                $event->type_foreign_action = str_replace('App\\', '', $event->type_foreign_action);

                $tlActionArray = array(
                    'instances_id' => session('instanceId'),
                    'users_id' => auth()->guard('web')->user()->id,
                    'type_action' => $event->type_action,
                    'author_id' => $event->id_foreign_action,
                    'author_type' => 'App\\'.studly_case($event->type_foreign_action)
                );
                $testAction = NetframeAction::select()->where($tlActionArray);
                if (count($testAction->get()) != 0) {
                    $loadAction = false;

                    //if newsfeed netframeaction is inactive, reactivate
                    $newsFeedAction = $testAction->first()->posts->first();
                    if ($newsFeedAction != null) {
                        $newsFeedAction->active = 1;
                        $newsFeedAction->save();
                    }
                }
            }

            if ($loadAction) {
                $event->type_foreign_action = str_replace('App\\', '', $event->type_foreign_action);
                $action = new NetframeAction();
                $action->instances_id = session('instanceId');
                $action->users_id = auth()->guard('web')->user()->id;
                $action->type_action = $event->type_action;
                $action->expert_action = $event->expert_action;
                $action->author_id = $event->id_foreign_action;
                $action->author_type = "App\\".studly_case($event->type_foreign_action);
                $action->save();

                if (in_array(class_basename($action->author), ['Channel', 'House', 'Community', 'Project'])
                    && $action->author->confidentiality == 0) {
                    $confidentiality = 0;
                    $privateProfile = 1;
                } else {
                    $confidentiality = 1;
                    $privateProfile = 0;
                }

                $event->type_foreign_nf = str_replace('App\\', '', $event->type_foreign_nf);
                //add action in newsfeed newsfeeds with $action->id
                $newsFeed = new NewsFeed;
                $newsFeed->instances_id = session('instanceId');
                $newsFeed->users_id = auth()->guard('web')->user()->id;
                $newsFeed->author_id = $event->id_foreign_nf;
                $newsFeed->author_type = "App\\".studly_case($event->type_foreign_nf);
                $newsFeed->true_author_id = $event->id_foreign_nf;
                $newsFeed->true_author_type = "App\\".studly_case($event->type_foreign_nf);
                $newsFeed->post_id = $action->id;
                $newsFeed->post_type = 'App\\NetframeAction';
                $newsFeed->invisible_owner = 1;
                $newsFeed->confidentiality = $confidentiality;
                $newsFeed->private_profile = $privateProfile;
                $newsFeed->save();
            }
        }
    }
}
