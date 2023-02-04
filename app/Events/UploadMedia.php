<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\User;
use App\Instance;

class UploadMedia
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     * @user : object user
     * @instanceId : id of concerned instance
     *
     * @return void
     */
    public function __construct(User $user, $instanceId)
    {
        $this->instance = Instance::find($instanceId);
        $user->current_instance_id = $instanceId;
        $this->user = $user;
    }
}
