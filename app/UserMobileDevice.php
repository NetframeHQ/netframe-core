<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMobileDevice extends Model
{
    protected $table = 'user_mobile_device';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tokensFcm()
    {
        return $this->belongsTo('App\DeviceFcmToken', 'duuid', 'device_uuid');
    }
}
