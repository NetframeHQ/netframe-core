<?php

namespace App\Listeners;

use App\Events\UserUpdateEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Observers\ElasticsearchObserver;
use Elasticsearch\Client;

class UserUpdateListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Handle the event.
     *
     * @param  UserUpdateEvent  $event
     * @return void
     */
    public function handle(UserUpdateEvent $event)
    {
        $user = $event->user;
        $esObserver = new ElasticsearchObserver($this->client);
        $esObserver->index($user);
    }
}
