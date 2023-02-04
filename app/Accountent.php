<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Accountent extends Authenticatable //implements UserInterface, RemindableInterface
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accountents';


    public function instances()
    {
        return $this->belongsToMany('App\Instance', 'accountents_has_instances', 'accountents_id', 'instances_id');
    }
}
