<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SubscribeToProfile
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $profile_id, $profile_type)
    {
        $this->user_id = $user_id;
        $this->profile_type = $profile_type;
        $this->profile_id = $profile_id;
    }
}
