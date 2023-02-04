<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewAction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        $type_action,
        $id_foreign_action,
        $type_foreign_action,
        $id_foreign_nf,
        $type_foreign_nf,
        $expert_action = 0
    ) {
        $this->type_action = $type_action;
        $this->id_foreign_action = $id_foreign_action;
        $this->type_foreign_action = $type_foreign_action;
        $this->id_foreign_nf = $id_foreign_nf;
        $this->type_foreign_nf = $type_foreign_nf;
        $this->expert_action = $expert_action;
    }
}
