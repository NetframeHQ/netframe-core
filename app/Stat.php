<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
