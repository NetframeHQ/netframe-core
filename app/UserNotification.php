<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{

    protected $table = "user_notifications";
    protected $fillable = ['instances_id', 'device', 'frequency'];

    public function instances()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
