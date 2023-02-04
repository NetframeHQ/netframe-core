<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SocialAction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($to_user, $elementId, $elementType, $typeNotification)
    {
        $this->to_user = $to_user;
        $this->elementId = $elementId;
        $this->elementType = $elementType;
        $this->typeNotification = $typeNotification;
    }
}
