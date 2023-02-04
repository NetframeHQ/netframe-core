<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CheckUserTag
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    /**
     *  @typePost (string) Give type post ex event, offer, news, media...
     *  @data (object) Attempt object Model
     *  @id id of newsfeed model
     */
    public function __construct($post, $content)
    {
        $this->post = $post;
        $this->content = $content;
    }
}
