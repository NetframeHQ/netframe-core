<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class LikeElement
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    /**
     * Increment or Decrement value +1 for like in table
     *
     * @param (string) $table
     * @param (array) $where array associative $field => $value
     * @param (string) $crement 'increment' or 'decrement' param
     */
    public function __construct($model, array $where, $crement = 'increment')
    {
        $this->model = $model;
        $this->where = $where;
        $this->crement = $crement;
    }
}
