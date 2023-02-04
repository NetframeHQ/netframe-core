<?php

namespace App\Listeners;

use App\Events\RemoveAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\NetframeAction;

class NetframeActionDelete
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
     * @param  RemoveAction  $event
     * @return void
     */
    public function handle(RemoveAction $event)
    {
        $actionArray = array(
            'users_id' => auth()->guard('web')->user()->id,
            'type_action' => $event->type_action,
            'author_id' => $event->id_foreign_action,
            'author_type' => "App\\".studly_case($event->type_foreign_action)
        );
        $testAction = NetframeAction::select()->where($actionArray)->first();
        if ($testAction != null) {
            $newsFeedAction = $testAction->posts()->first();
            if ($newsFeedAction != null) {
                $newsFeedAction->active = 0;
                $newsFeedAction->save();
            }
        }
    }
}
