<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAuthLogger extends Authenticatable //implements UserInterface, RemindableInterface
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_auth_logger';


    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }
}
