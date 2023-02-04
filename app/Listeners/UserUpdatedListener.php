<?php

namespace App\Listeners;

use App\Events\UserUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Elasticsearch\Client;
use App\Observers\ElasticsearchObserver;

class UserUpdatedListener
{
    public $es;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->es = new ElasticsearchObserver($client);
    }

    /**
     * Handle the event.
     *
     * @param  UserUpdatedEvent  $event
     * @return void
     */
    public function handle(UserUpdatedEvent $event)
    {
        $user = $event->user;
        if ($user->active==1) {
            $this->es->index($user);
        }
    }
}
