<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class VirtualUser extends Authenticatable
{
    public function instances()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function getNameDisplay()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
