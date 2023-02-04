<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostChannel implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channelId;
    public $post;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($channelId, $post = null)
    {
        $this->channelId = $channelId;
        $this->postId = $post['id'];
        $this->post = $post;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Channel-'.$this->channelId);
    }
}
