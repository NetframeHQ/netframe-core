<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewPost
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
    public function __construct($typePost, $data, $id = null, $mediasId = null, $oldMediasIds = [], $fromUpload = false)
    {
        $this->data = $data;
        $this->typePost = $typePost;
        $this->id = $id;
        $this->mediasId = $mediasId;
        $this->oldMediasList = $oldMediasIds;
        $this->fromUpload = $fromUpload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
