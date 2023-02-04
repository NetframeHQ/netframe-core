<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeleteChannelPost implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channelId;
    public $deletedTarget;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($channelId, $postId, $post)
    {
        $this->channelId = $channelId;
        $this->postId = $postId;
        $this->post = $post;
        $this->deletedTarget = class_basename($post)."-Channel-".$post->id;
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
